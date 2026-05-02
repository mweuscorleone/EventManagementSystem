<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/create/user', [UserManagementController::class, 'createUser']);
Route::put('update/user/{userId}', [UserManagementController::class, 'updateUser']);
Route::delete('/delete/user/{userId}', [UserManagementController::class, 'removeUser']);
Route::post('user/login', [AuthController::class, 'login']);
Route::post('/reset/password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/user/logout', [AuthController::class, 'logout']);
});

        