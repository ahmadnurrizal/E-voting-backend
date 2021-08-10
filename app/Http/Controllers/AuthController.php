<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

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
    $id = auth()->user()->id; // get id current user
    $user = User::find($id);

    $request->validate([
      'email' => [
        'required',
        Rule::unique('users')->ignore($user->id), // uunique email and ignore email for users itself
      ],
      'image' => 'mimes:png,jpg,jpeg|max:1024,' // max size = 1024 kb, accepted formats : png,jpg,jpeg
    ]);

    if ($request->hasFile('image')) {
      // checking user profile image exist or not
      $filename = $user->profil_path;
      $dir = '/';
      $recursive = true; // Get subdirectories also?
      $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
      $file = $contents
        ->where('type', '=', 'file')
        ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
        ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
        ->first();

      $exist = !$file;
      if (!$exist && $user->profil_path != '') {
        Storage::disk("google")->delete($file['path']); // if exist delete old profile image
      }

      // create new uniq name of image
      $newImageName = time() . '.' . $request->image->extension();

      // upload new profile image to Google drive/LARAVEL/images/profiles
      $dir = '/';
      $recursive = true; // Get subdirectories also?
      $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
      $dir = $contents->where('type', '=', 'dir')
        ->where('filename', '=', 'profiles') // target folder = profiles
        ->first(); // There could be duplicate directory names!

      if (!$dir) {
        return 'Directory does not exist!';
      }
      Storage::disk("google")->putFileAs($dir['path'], $request->file('image'), $newImageName); // upload image
      $user->profil_path = $newImageName;
    }

    // update all request
    $user->update($request->all());

    return response()->json([
      "status" => "success",
      "data" => $user
    ]);
  }

  public function userShow()
  {
    $id = auth()->user()->id; // get id current user
    $user = User::find($id);

    if (!$user) {
      return response()->json([
        "status" => "error",
        "message" => "user not found"
      ], 404);
    }

    $filename = $user->profil_path;

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
      "data" => $user,
      "profileURL" => $imageURL
    ]);
  }

  public function changePassword(Request $request)
  {
    $request->validate([
      'current_password' => 'required|string',
      'new_password' => 'required|string',
      'confirm_new_password' => 'required|string'
    ]);

    $id = auth()->user()->id; // get id user's login
    $user = User::find($id);

    $currentPassword = auth()->user()->password;
    $inputPassword = $request->current_password;

    if (Hash::check($inputPassword, $currentPassword)) {
      if ($request->new_password == $request->confirm_new_password) {
        $user->update([
          'password' => bcrypt($request->new_password)
        ]);

        return response()->json([
          "status" => "success",
          "message" => 'change password is success'
        ]);
      } else {
        return response()->json([
          "status" => "error",
          "message" => "New password and confirm new password doesn't match"
        ]);
      }
    } else {
      return response()->json([
        "status" => "error",
        "message" => "You have to input old password"
      ]);
    }
  }

  public function show($id)
  {
    $user = User::find($id); // search data by id

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

  public function destroy(Request $request)
  {
    $id = auth()->user()->id; // get id user's login
    $user = User::find($id);

    if (!$user) {
      return response()->json([
        "status" => "error",
        "message" => "user not found",
      ], 404);
    }

    $request->validate([
      'password' => 'required|string'
    ]);

    if (!Hash::check($request->password, $user->password)) {
      return response()->json([
        "status" => "error",
        "message" => "Password doesn't match",
      ]);
    }

    $user->delete();

    return response()->json([
      "status" => "success",
      "message" => "user deleted"
    ]);
  }

  public function logout()
  {
    auth()->user()->tokens()->delete(); // Delete token (code is good, ignore error)
    return response(['message' => 'logged out']);
  }
}
