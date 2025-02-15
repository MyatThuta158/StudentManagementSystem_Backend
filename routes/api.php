<?php

use App\Http\Controllers\Admin\ReAllocateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user/login', [App\Http\Controllers\AuthController::class,'login']);
Route::post('/user/register',[App\Http\Controllers\RegisterController::class,'register']);


Route::get('/admin/allocate/list',[ReAllocateController::class,'list_allocate']);
Route::post('/admin/allocate/{allocateId}',[ReAllocateController::class,'reallocate']);
