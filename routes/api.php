<?php

use App\Http\Controllers\AuthController;
use App\Models\Poll;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollOptionController;
use App\Http\Controllers\VoterController;
use App\Models\PollOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('/v1/register', [AuthController::class, 'register']); // register
Route::post('/v1/login', [AuthController::class, 'login']); // login
Route::get('/v1/polls', [PollController::class, 'index']); // get all poll
Route::get('/v1/users', [AuthController::class, 'index']); // gell all user
Route::get('/v1/polls/trending', [PollController::class, 'trending']); // get trending polls
Route::get('/v1/polls/newst', [PollController::class, 'newst']); // get newst polls
Route::get('/v1/polls/{id}', [PollController::class, 'show']); // get poll by id
Route::get('/v1/polls/discover/{title}', [PollController::class, 'discover']); // get poll by title
Route::get('/v1/polls/result/{id}', [PollController::class, 'result']); // get poll result by id
Route::get('/v1/users/{id}', [AuthController::class, 'show']); // get user by id
// routes which contain {} (wildcard) have to put in back order



// Protected Routes (need a valid token to access)
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::post('/v1/logout', [AuthController::class, 'logout']); // logout
  Route::get('/v1/user', [AuthController::class, 'userShow']); // get user by id user's login
  Route::get('/v1/user-poll', [PollController::class, 'userPoll']); // get all poll created by id user's login

  Route::post('/v1/users', [AuthController::class, 'update']); // update user by id user's login
  Route::put('/v1/users/change-password', [AuthController::class, 'changePassword']); // change password by id user's login
  Route::delete('/v1/users', [AuthController::class, 'destroy']); // delete user by id user's login

  Route::post('/v1/polls', [PollController::class, 'store']); // create poll
  Route::put('/v1/polls/{id}', [PollController::class, 'update']); // update poll by id
  Route::delete('/v1/polls/{id}', [PollController::class, 'destroy']); // delete poll by id
  Route::post('/v1/polls/{id}/vote', [VoterController::class, 'store']); // vote option
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});
