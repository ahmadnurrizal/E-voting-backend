<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Voter;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; // managing date and time in PHP much easier
use Illuminate\Support\Facades\Storage;

class PollController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    // get all data
    $poll =  Poll::all();

    return response()->json([
      "status" => "success",
      "data" => $poll
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  // create poll
  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required',
      'description' => 'required',
      'deadline' => 'required',
    ]);

    // insert data to polls table
    $user = auth()->user();
    $data = $request->all();
    $data['user_id'] = $user->id; //auto fill user_id according on user who create
    $poll = Poll::create($data);

    foreach ($data['poll_options'] as $option) {
      $option['poll_id'] = $poll->id;
      $pollOption[] = PollOption::create($option);
    }

    return response()->json([
      "status" => "success",
      "poll" => $poll,
      "pollOption" => $pollOption
    ]);
  }

  public function uploadImage(Request $request)
  {
    $request->validate([
      'image' => 'mimes:png,jpg,jpeg|max:1024,' // max size = 1024 kb, accepted formats : png,jpg,jpeg
    ]);

    if ($request->hasFile('image')) {
      // create new uniq name of image
      $newImageName = time() . '.' . $request->image->extension();

      // upload poll image to Google drive/LARAVEL/images/polls
      $dir = '/';
      $recursive = true; // Get subdirectories also?
      $contents = collect(Storage::disk('google')->listContents($dir, $recursive));

      $dir = $contents->where('type', '=', 'dir')
        ->where('filename', '=', 'polls')
        ->first(); // There could be duplicate directory names!

      if (!$dir) {
        return 'Directory does not exist!';
      }

      // upload file to google drive LARAVEL/images/polls
      Storage::disk("google")->putFileAs($dir['path'], $request->file('image'), $newImageName);
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
    $poll = Poll::find($id); // find data by id

    if (!$poll) {
      return response()->json([
        "status" => "error",
        "message" => "poll not found"
      ], 404);
    }

    $filename = $poll->image_path;
    $dir = '/';
    $recursive = true; // Get subdirectories also?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));

    $file = $contents
      ->where('type', '=', 'file')
      ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
      ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
      ->first(); // there can be duplicate file names!
    //return $file; // array with file info


    if (!$file) {
      $imageURL = ''; // user doesn't have profile image
    } else {
      $imageURL = Storage::disk('google')->url($file['path']); // create URL for user's profile image
    }

    return response()->json([
      "status" => "success",
      "data" => $poll,
      "imageURL" => $imageURL
    ]);
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
    $poll = Poll::find($id);
    if (!$poll) {
      return response()->json([
        "status" => "error",
        "message" => "poll not found"
      ], 404);
    }

    $user = auth()->user();
    $user_id = $poll->user_id;

    if ($user_id != $user->id) { // check user can update poll or not (only user which create the poll can update)
      return response()->json([
        "status" => "error",
        "message" => "user can't update poll"
      ], 404);
    }

    $poll->update($request->all()); // update  data

    $pollOption = PollOption::where('poll_id', '=', $id); // find pollOption by poll_id
    // update poll-options table
    $countOptions = 2; // just create 2 option
    for ($i = 1; $i <= $countOptions; $i += 1) {
      $pollOption->update([
        'option' => $request->input("option{$i}"),
        'image_path' => $request->input("image_path{$i}")
        // 'poll_id' => $poll->id
      ]);
    }
    return response()->json([
      "status" => "success",
      "data" => $poll
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $poll = Poll::find($id);

    if (!$poll) {
      return response()->json([
        "status" => "error",
        "message" => "poll not found",
      ], 404);
    }

    $poll->delete();

    return response()->json([
      "status" => "success",
      "message" => "poll deleted"
    ]);
  }

  /**
   * Search data by title.
   *
   * @param  string  $title
   * @return \Illuminate\Http\Response
   */
  public function discover($title)
  {
    $poll = Poll::where('title', 'like', '%' . $title . '%')
      ->where('status', 'public')->get(); // search data by title

    if ($poll->isEmpty()) {
      return response()->json([
        "status" => "error",
        "message" => "poll not found",
      ], 404);
    }

    return response()->json([
      "status" => "success",
      "message" => $poll
    ]);
  }

  public function trending()
  {
    // according to count voters on current month
    $poll = Poll::withCount(['voters' => function ($query) {
      $query->whereMonth('created_at', Carbon::now()->month);
    }])->where('status', 'public')->orderBy('voters_count', 'DESC')->take(6)->get();

    if ($poll->isEmpty()) {
      return response()->json([
        "status" => "error",
        "message" => "poll not found",
      ], 404);
    }

    return response()->json([
      "status" => "success",
      "message" => $poll
    ]);
  }

  public function newest()
  {
    $poll = Poll::orderBy('created_at', 'DESC')->take(6)
      ->where('status', 'public')->get();

    if ($poll->isEmpty()) {
      return response()->json([
        "status" => "error",
        "message" => "poll not found",
      ], 404);
    }

    return response()->json([
      "status" => "success",
      "message" => $poll
    ]);
  }

  public function userPoll()
  {
    $id = auth()->user()->id; // get id current user
    $poll = Poll::where('user_id', $id)->get(); // get all user's poll by user_id
    if ($poll->isEmpty()) {
      return response()->json([
        "status" => "error",
        "message" => "poll not found",
      ], 404);
    }

    return response()->json([
      "status" => "success",
      "message" => $poll
    ]);
  }

  public function otherUserPoll($id)
  {
    $poll = Poll::where('user_id', '=', $id)->get();
    if ($poll->isEmpty()) {
      return response()->json([
        "message" => "poll not found"
      ]);
    }

    return response()->json([
      "status" => "success",
      "message" => $poll
    ]);
  }

  public function result($id)
  {
    $countOption = PollOption::where('poll_options.poll_id', '=', $id)->count(); // get value countOption
    /*  
        list value of poll_option_id : 
        A=1,
        B=2,
        C=3, dst
    */
    for ($i = 1; $i <= $countOption; $i++) {
      $data[$i] = Voter::where('voters.poll_id', '=', $id)->where('voters.poll_option_id', '=', $i)
        ->join('users', 'users.id', '=', 'voters.user_id')
        ->join('polls', 'polls.id', '=', 'voters.poll_id')
        ->join('poll_options', 'poll_options.id', '=', 'voters.poll_id')
        ->count();
      // ->get(['polls.id', 'polls.title', 'poll_options.option', 'users.name', 'voters.poll_option_id']); // return spesific column   
    }

    /*
        $data["1"] >> count of option A
        $data["2"] >> count of option B
        $data["3"] >> count of option C
        dst
    */
    return $data;
  }
}
