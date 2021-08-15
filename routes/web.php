<?php


use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});

Route::get('test', function () {
  Storage::disk('google')->put('test.txt', 'Hello World');
  dd("done");
});

Route::get('users/{id}', function ($id) {
  $filename = '11.jpg';

  $dir = '/';
  $recursive = false; // Get subdirectories also?
  $contents = collect(Storage::disk('google')->listContents($dir, $recursive));

  $file = $contents
    ->where('type', '=', 'file')
    ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
    ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
    ->first(); // there can be duplicate file names!

  //return $file; // array with file info

  $rawData = Storage::disk('google')->url($file['path']);
  return ($rawData);
  // dd($rawData);
  return response($rawData, 200)
    ->header('ContentType', $file['mimetype'])
    ->header('Content-Disposition', "attachment; filename=$filename");
});
