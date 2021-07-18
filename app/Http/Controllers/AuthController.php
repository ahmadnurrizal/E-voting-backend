<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $fields = $request->validate([
      'name' => 'required|string',
      'email' => 'required|string|unique:users,email',
      'password' => 'required|string|confirmed'
    ]);

    $user = User::create([
      'name' => $fields['name'],
      'email' => $fields['email'],
      'password' => bcrypt($fields['password'])
    ]);

    // creating token
    $token = $user->createToken('myapptoken')->plainTextToken;

    $response = [
      'user' => $user,
      'token' => $token
    ];

    return response($response, 201);
  }

  public function login(Request $request)
  {
    $fields = $request->validate([
      'email' => 'required|string',
      'password' => 'required|string'
    ]);

    // check email
    $user = User::where('email', $fields['email'])->first();

    // dd($user->password);

    // check password
    if (!$user || !Hash::check($fields['password'], $user->password)) {

      return response([
        'message' => 'Bed Creds'
      ], 401);
    }

    // creating token
    $token = $user->createToken('myapptoken')->plainTextToken;

    $response = [
      'user' => $user,
      'token' => $token
    ];

    return response($response, 201);
  }

  public function update(Request $request)
  {

    $request->validate([
      'email' => 'string|unique:users,email',
    ]);

    $id = auth()->user()->id; // get id current user
    $user = User::find($id);
    $user->update($request->all()); // update  data
    $user['password'] = bcrypt($user->password);

    $user->update(array('password' => $user['password']));

    return $user;
  }

  public function show($id)
  {
    return User::where('id', '=', $id)->get(); // search data by id
  }

  public function logout(Request $request)
  {
    auth()->user()->tokens()->delete(); // Delete token (code is good, ignore error)
    return response(['message' => 'logged out']);
  }
}
