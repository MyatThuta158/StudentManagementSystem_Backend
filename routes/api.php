<?php

use App\Http\Controllers\Admin\ReAllocateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllocationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user/login', [App\Http\Controllers\AuthController::class,'login']);
Route::post('/user/register',[App\Http\Controllers\RegisterController::class,'register']);


Route::get('/admin/allocate/list',[ReAllocateController::class,'list_allocate']);
Route::post('/admin/allocate/{allocateId}',[ReAllocateController::class,'reallocate']);
// allocation start
Route::get('allocations', [AllocationController::class, 'index']); // Retrieve all allocations
Route::get('allocations/{id}', [AllocationController::class, 'show']); // Retrieve a single allocation
Route::post('allocations', [AllocationController::class, 'store']); // Store allocation
//allocation end
