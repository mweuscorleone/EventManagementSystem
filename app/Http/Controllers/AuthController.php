<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4|max:20'
        ]);

        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('login-token')->plainTextToken;


        return response()->json([
            'status' => 'success',
            'message' => 'login successully!',
            'token' => $token
        ], 200);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:4|max:20'
        ],
        [
            'exists.email' => 'email address you entered is not exist, please try again'
        ]);

        DB::table('users')->where('email', $request->email)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'password changed successfully!'
        ], 200);
    }

    public function logout(Request $request){
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'logout successfully'
        ]);
    }
}
