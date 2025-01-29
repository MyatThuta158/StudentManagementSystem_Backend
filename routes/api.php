<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user/login', [App\Http\Controllers\AuthController::class,'login']);
Route::post('/user/register',[App\Http\Controllers\RegisterController::class,'register']);
