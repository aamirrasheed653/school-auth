<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|min:6',
        ]);
        if ($user = User::where('email', $request->email)->first()) {
            return response([
                "message" => "Email already exists",
                "status" => "Failed"
            ]);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken($request->email)->plainTextToken;
        return response([
            "message" => "Register Successfully!",
            "token" => $token,
            "status" => "Successful",
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        ($user = User::where('email', $request->email)->first());
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;
            return ([
                "message" => "Login Successfully",
                "token" => $token,
                "status" => "OK"
            ]);

        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(["message" => "Logged out successfully"]);
    }

    public function logged()
    {
        $logged = auth()->user();
        return response([
            "logged user" => $logged,
            "Status" => "Ok"
        ], 200);
    }

    public function changepassword(Request $request)
    {
        //Validate the request
        $request->validate([
            'password' => 'required|confirmed'
        ]);
        //Checking if the password is correct
        $changepass = auth()->user();
        $changepass->password = Hash::make($request->password);
        $changepass->save();
        return response([
            "message" => "password changed successfully"
        ], 200);
    }



}
