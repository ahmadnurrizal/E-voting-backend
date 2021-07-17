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
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);
Route::get('/v1/users/{id}', [AuthController::class, 'show']);
Route::get('/v1/polls', [PollController::class, 'index']);
Route::get('/v1/polls/{id}', [PollController::class, 'show']);
Route::get('/v1/polls/search/{title}', [PollController::class, 'search']);


// Route::get('/poll-options', [PollOptionController::class, 'index']);

// Protected Routes (need a valid token to access)
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::put('/v1/users/{id}', [AuthController::class, 'update']);
  Route::post('/v1/polls', [PollController::class, 'store']);
  Route::put('/v1/polls/{id}', [PollController::class, 'update']);
  Route::delete('/v1/polls/{id}', [PollController::class, 'destroy']);
  Route::post('/v1/logout', [AuthController::class, 'logout']);
  Route::post('/v1/polls/{id}/vote', [VoterController::class, 'store']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});
