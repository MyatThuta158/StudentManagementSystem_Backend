<?php

use App\Http\Controllers\BulkAllocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\Admin\ReAllocateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/user/register', [App\Http\Controllers\RegisterController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/allocations/{allocateId}', [ReAllocateController::class, 'reallocate']);
    // allocation start
    Route::get('allocations', [AllocationController::class, 'index']);
    Route::get('allocations/{id}', [AllocationController::class, 'show']);
    Route::post('allocations', [AllocationController::class, 'store']);
    Route::get('/allocations/search', [AllocationController::class, 'search']);
    Route::post('bulk/allocations', [BulkAllocationController::class, 'allocate']);

    //allocation end

    // show non-allocated student
    Route::get('student/nonallocate', [BulkAllocationController::class, 'nonAllocatedStudentList']);
    // end non-allocated student
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/comments', [CommentController::class, 'index']);     // List all comments
    Route::post('/comments', [CommentController::class, 'store']);   // Add a comment
    Route::get('/comments/{id}', [CommentController::class, 'show']); // Get a single comment
    Route::put('/comments/{id}', [CommentController::class, 'update']); // Update a comment
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']); // Delete a comment
});
