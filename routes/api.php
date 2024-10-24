<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->middleware('permission:manage posts'); // Create a new post
    Route::put('/posts/{id}', [PostController::class, 'update'])->middleware('permission:manage posts'); // Update a post
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->middleware('permission:manage posts'); // Delete a post
});

Route::get('/posts', [PostController::class, 'index']); // List all posts
Route::get('/posts/{id}', [PostController::class, 'show']); // Show a single post

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']); // List all users (Admin only)
    Route::get('/users/{id}', [UserController::class, 'show']); // Show a single user (Admin only)
});
