<?php

namespace App\Http\Controllers;

use App\Models\PollOption;
use App\Models\Poll;
use Illuminate\Http\Request;

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

      // saving image to /public/image/options directory
      $request->image->move(public_path('images/options'), $newImageName);
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
    $pollOption = PollOption::where('poll_id', $id)->get();

    if ($pollOption->isEmpty()) {
      return response()->json([
        "status" => "error",
        "message" => "poll option not found"
      ]);
    }

    return response()->json([
      "status" => "success",
      "data" => $pollOption
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
