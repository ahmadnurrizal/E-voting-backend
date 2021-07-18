<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;

class PollController extends Controller
{
  public function userPoll()
  {
    $id = auth()->user()->id; // get id current user
    $poll = Poll::where('user_id', $id)->get(); // get all user's poll by user_id

    return $poll;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function index()
  {
    // get all data
    return Poll::all();
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
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
    $data['user_id'] = $user->id; //auto fill user_id base on user when creating polling
    $poll = Poll::create($data);

    // insert data to poll-options table
    $countOptions = 2; // just create 2 option
    for ($i = 1; $i <= $countOptions; $i += 1) {
      $pollOption = PollOption::create([
        'option' => $request->input("option{$i}"),
        'image_path' => $request->input("image_path{$i}"),
        'poll_id' => $poll->id
      ]);
    }

    // return Poll::create($request->all()); // create data
    return [$pollOption, $poll]; // create data
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    return Poll::find($id); // find data by id
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
      return $poll;
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    return Poll::destroy($id);
  }

  /**
   * Search data by title.
   *
   * @param  string  $title
   * @return \Illuminate\Http\Response
   */
  public function search($title)
  {
    return Poll::where('title', 'like', '%' . $title . '%')->get(); // search data by title
  }
}
