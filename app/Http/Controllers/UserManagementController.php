<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class UserManagementController extends Controller
{
    public function createUser(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4|max:20',
            'role' => 'sometimes|in:admin,guest,scanner,organizer'
        ],
    [
        'email.unique' => 'email already exists please use another email',
        'role.in' => 'role must be in [admin,guest,scanner,organizer]'
    ]);


    $id = DB::table('users')->insertGetId([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role ?? 'guest',
        'email_verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);
   
    $user = User::find($id);
    $token = $user->createToken('register-token')->plainTextToken;
    $updateToken = DB::table('users')->where('id', $id)->update([
        'remember_token' => $token
    ]);
    $userwithToken = DB::table('users')->where('id', $id)->first();

    return response()->json([
        'status' => 'success',
        'message' => 'user created successfully!',
        'token' => $token,
        'user' => $user

    ], 201);
    }

    public function updateUser(Request  $request, $userId){
       $user = User::find($userId);

       if(!$user){
        return response()->json(['message' => 'user not found please enter valid user Id'], 404);
       }
        $fields = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email',
            'password' => 'sometimes|string|min:4|max:20',
            'role' => 'sometimes|string|in:guest,admin,organizer,scanner'
        ],
       [
        'email.unique' => 'email already existis please enter another email'
       ] );

       if(isset($fields['password'])){
            $fields['password']= Hash::make($fields['password']);
       }

      $user->update($fields);

      return response()->json([
        'status' => 'success',
        'message' => 'updated successfully!',
        'fieds updated' => array_keys($fields)
      ]);


    }


    public function removeUser($userId){
        $user = DB::table('users')->where('id', $userId)->first();

        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ]);
        }

       $delete =  DB::table('users')->where('id', $user->id)->delete();

        return response()->json([
            'status' => 'success',
        'message' => $delete . ' user deleted successfully!'
        ]);
    }
}
