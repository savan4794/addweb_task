<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::get('/home',[HomeController::class,'index']);
Route::resource('posts', PostController::class)->middleware('auth');
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::resource('users', UserController::class);
});

