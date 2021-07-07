<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;

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

    return Poll::create($request->all()); // create data
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
    return $poll;
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
