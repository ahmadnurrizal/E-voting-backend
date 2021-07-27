<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Http\Request;

class VoterController extends Controller
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
  public function store(Request $request, $id)
  {
    $user = auth()->user();
    $voter = Voter::create([
      'user_id' => $user->id,
      'poll_id' => $id,
      'poll_option_id' => $request->poll_option_id
    ]);

    return response()->json([
      "status" => "success",
      "data" => $voter
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Voter  $voter
   * @return \Illuminate\Http\Response
   */
  public function show(Voter $voter)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Voter  $voter
   * @return \Illuminate\Http\Response
   */
  public function edit(Voter $voter)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Voter  $voter
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Voter $voter)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Voter  $voter
   * @return \Illuminate\Http\Response
   */
  public function destroy(Voter $voter)
  {
    //
  }
}
