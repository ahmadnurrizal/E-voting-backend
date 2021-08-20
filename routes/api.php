<?php

// require 'vendor/autoload.php';

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewPasswordController;
use App\Models\Poll;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollOptionController;
use App\Http\Controllers\VoterController;
use App\Models\PollOption;
use App\Models\Voter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon; // managing date and time in PHP much easier
// use Illuminate\Support\Facades\Redis;
use Performance\Performance;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::resource('polls', PollController::class);

// Public Routes 
Route::post('/v1/register', [AuthController::class, 'register']); // register ///////////////////////////////////////
Route::post('/v1/login', [AuthController::class, 'login']); // login //////////////////////////////////////////////
Route::get('/v1/polls', [PollController::class, 'index']); // get all poll
Route::get('/v1/users', [AuthController::class, 'index']); // gell all user
Route::get('/v1/polls/trending', [PollController::class, 'trending']); // get trending polls /////////////////////////////////////
Route::get('/v1/polls/newest', [PollController::class, 'newest']); // get newst polls //////////////////////////////////
Route::get('/v1/polls/{id}', [PollController::class, 'show']); // get poll by id ///////////////////////////////////////
Route::get('/v1/polls/discover/{title}', [PollController::class, 'discover']); // get poll by title /////////////////////////////////////
Route::get('/v1/polls/result/{id}', [PollController::class, 'result']); // get poll result by id ////////////////////////////////////////////
Route::get('/v1/users/{id}', [AuthController::class, 'show']); // get user by id ////////////////////////////////////
Route::get('/v1/poll-options/{id}', [PollOptionController::class, 'show']); // get poll options by poll id ///////////////////////////////////
Route::get('/v1/polls/user-poll/{id}', [PollController::class, 'otherUserPoll']); // get poll by id user ////////////////////////////////
Route::post('/v1/forgot-password', [NewPasswordController::class, 'forgotPassword']); // send verivication email and get token
Route::post('/v1/reset-password', [NewPasswordController::class, 'reset']); // reset password

Route::get('/v1/all', function () {
  if ($poll = Redis::get('polls.all')) {
    return $poll;
  }

  // get all post
  $poll = Poll::all();

  // store into redis
  Redis::set('polls.all', $poll);

  // return all posts
  return $poll;
});
// routes which contain {} (wildcard) have to put in back order



// Protected Routes (need a valid token to access)
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::post('/v1/logout', [AuthController::class, 'logout']); // logout //////////////////////////////////////////////
  Route::get('/v1/user', [AuthController::class, 'userShow']); // get user by id user's login //////////////////////////////////
  Route::get('/v1/user-poll', [PollController::class, 'userPoll']); // get all poll created by id user's login /////////////////////////////
  Route::put('/v1/users', [AuthController::class, 'update']); // update user by id user's login /////////////////////////////////
  Route::put('/v1/users/change-password', [AuthController::class, 'changePassword']); // change password by id user's login ////////////////////////////////
  Route::post('/v1/upload-image', function (Request $request) {
    $request->validate([
      'image' => 'mimes:png,jpg,jpeg|max:1024,' // max size = 1024 kb, accepted formats : png,jpg,jpeg
    ]);

    $image = $request->file('image');
    $file_path = $image->getPathName();
    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST', 'https://api.imgur.com/3/image', [
      'headers' => [
        'authorization' => 'Client-ID ' . env('IMGUR_CLIENT_ID'),
        'content-type' => 'application/x-www-form-urlencoded',
      ],
      'form_params' => [
        'image' => base64_encode(file_get_contents($request->file('image')->path($file_path)))
      ],
    ]);
    $data = json_decode($response->getBody());

    return response()->json([
      "status" => "success",
      "imageURL" => $data->data->link
    ]);
  });

  Route::delete('/v1/users', [AuthController::class, 'destroy']); // delete user by id user's login ///////////////////////////////////////
  Route::post('/v1/polls', [PollController::class, 'store']); // create poll ///////////////////////////////////////////////
  Route::delete('/v1/polls/{id}', [PollController::class, 'destroy']); // delete poll by id ///////////////////////////////////////
  Route::delete('/v1/polls/{id}/reset', [VoterController::class, 'destroy']); // delete voters by poll_id //////////////////////////
  Route::post('/v1/polls/{id}/vote', [VoterController::class, 'store']); // vote option ////////////////////////////////////
  Route::put('/v1/polls/{id}', [PollController::class, 'update']); // update poll by id
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});
