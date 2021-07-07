<?php

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

Route::resource('polls', PollController::class);
Route::get('polls/search/{title}', [PollController::class, 'search']);

// Route::get('/polls', function () {
//   return Poll::create([
//     'user_id' => '99',
//     'title' => 'poll 1',
//     'description' => 'product 1 balblabla',
//     'deadline' => 'deadline 1',
//     'status' => 'terbuka'
//   ]);
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});
