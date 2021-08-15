<?php

namespace App\Http\Controllers;

use App\Models\PollOption;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PollOptionController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // return PollOption::create($request->all()); // create data
  }

  public function uploadImage(Request $request)
  {
    $request->validate([
      'image' => 'mimes:png,jpg,jpeg|max:1024,' // max size = 1024 kb, accepted formats : png,jpg,jpeg
    ]);

    if ($request->hasFile('image')) {
      // create new uniq name of image
      $newImageName = time() . '.' . $request->image->extension();

      // upload poll image to Google drive/LARAVEL/images/options
      $dir = '/';
      $recursive = true; // Get subdirectories also?
      $contents = collect(Storage::disk('google')->listContents($dir, $recursive));

      $dir = $contents->where('type', '=', 'dir')
        ->where('filename', '=', 'options')
        ->first(); // There could be duplicate directory names!

      if (!$dir) {
        return 'Directory does not exist!';
      }

      // upload file to google drive LARAVEL/images/polls
      Storage::disk("google")->putFileAs($dir['path'], $request->file('image'), $newImageName);
      sleep(1); // sleep 1 second to make sure newImageName is unique
    } else {
      return 'no image';
    }

    return response()->json([
      "status" => "success",
      "data" => $newImageName
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $pollOptions = PollOption::where('poll_id', $id)->get();

    if ($pollOptions->isEmpty()) {
      return response()->json([
        "status" => "error",
        "message" => "poll option not found"
      ]);
    }

    $dir = '/';
    $recursive = true; // Get subdirectories also?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));

    foreach ($pollOptions as $pollOption) {
      $filename = $pollOption->image_path;
      // dd($filename);
      $file = $contents
        ->where('type', '=', 'file')
        ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
        ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))->first();

      if (!$file) {
        $imageURL = ''; // user doesn't have profile image
      } else {
        $imageURL[] = Storage::disk('google')->url($file['path']); // create URL for user's profile image
      }
    }

    return response()->json([
      "status" => "success",
      "data" => $pollOption,
      "imageURL" => $imageURL
    ]);
  }


  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
