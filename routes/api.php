<?php

use App\Http\Controllers\AuthController;
use App\Models\Poll;
use App\Http\Controllers\PollController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::get('/polls', [PollController::class, 'index']);
Route::get('/polls/{id}', [PollController::class, 'show']);
Route::get('/polls/search/{title}', [PollController::class, 'search']);

// Route::get('/polls', function () {
//   return Poll::create([
//     'user_id' => '99',
//     'title' => 'poll 1',
//     'description' => 'product 1 balblabla',
//     'deadline' => 'deadline 1',
//     'status' => 'terbuka'
//   ]); 
// });

// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::post('/polls', [PollController::class, 'create']);
  Route::put('/polls/{id}', [PollController::class, 'update']);
  Route::delete('/polls/{id}', [PollController::class, 'destroy']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});
