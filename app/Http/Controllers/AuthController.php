<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AuthController extends Controller
{
  public function index()
  {
    $user = User::all();
    if (!$user) {
      return response()->json([
        "status" => "error",
        "message" => "User not found"
      ], 404);
    }

    return response()->json([
      "status" => "success",
      "data" => $user
    ]);
  }

  public function register(Request $request)
  {
    $fields = $request->validate([
      'name' => 'required|string',
      'email' => 'required|string|unique:users,email',
      'password' => 'required|string|confirmed',
      'gender' => 'required|string',
      'date_of_birth' => 'required'
    ]);


    $user = User::create([
      'name' => $fields['name'],
      'email' => $fields['email'],
      'password' => bcrypt($fields['password']),
      'gender' => $fields['gender'],
      // 'date_of_birth' => \Carbon\Carbon::createFromFormat('d/m/Y', $fields['date_of_birth'])->format('Y-m-d') // date format convert to yyyy/mm/dd
      'date_of_birth' => $fields['date_of_birth']
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
      'image' => 'mimes:png,jpg,jpeg|max:1024,' // max size = 1024 kb, accepted formats : png,jpg,jpeg
    ]);

    $id = auth()->user()->id; // get id current user
    $user = User::find($id);

    $exist = File::exists(public_path('images/profils/' . $user->profil_path)); // checking an old image. return true/false
    if ($exist && $user->profil_path != '') {
      unlink('images/profils/' . $user->profil_path); // remove old image
    }


    if ($request->hasFile('image')) {
      // create new uniq name of image
      $newImageName = time() . '-' . $id . '.' . $request->image->extension();

      // saving image to /public/image/profils directory
      $request->image->move(public_path('images/profils'), $newImageName);
      $request->profil_path = $newImageName;
    } else {
      $newImageName = null;
    }

    // update all request
    $user->update($request->all());

    // manual update because we need to process first then input to database
    $user->update([
      'password' => bcrypt($user->password),
      'profil_path' => $newImageName,
    ]);

    return response()->json([
      "status" => "success",
      "data" => $user
    ]);
  }

  public function userShow()
  {
    // $user = User::where('id', '=', $id)->get(); // search data by id
    $id = auth()->user()->id; // get id current user
    $user = User::find($id);

    if (!$user) {
      return response()->json([
        "status" => "error",
        "message" => "user not found"
      ], 404);
    }

    return response()->json([
      "status" => "success",
      "data" => $user
    ]);
  }

  public function show($id)
  {
    $user = User::where('id', '=', $id)->get(); // search data by id

    if (!$user) {
      return response()->json([
        "status" => "error",
        "message" => "user not found"
      ], 404);
    }

    return response()->json([
      "status" => "success",
      "data" => $user
    ]);
  }

  public function logout(Request $request)
  {
    auth()->user()->tokens()->delete(); // Delete token (code is good, ignore error)
    return response(['message' => 'logged out']);
  }
}
