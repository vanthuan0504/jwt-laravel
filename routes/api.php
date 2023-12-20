<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group([
    'middleware' => ['cors', 'json.response'],
    'prefix' => 'auth'
], function ($router) {
    // Apis that do not need a token to authentication
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::middleware(['requireToken', 'jwt.auth'])->group(function () {
        Route::get('/me', [AuthController::class, 'getUserProfile'])->name('getUserProfile');
        Route::put('/me', [AuthController::class, 'updateUserProfile'])->name('updateUserProfile');
        Route::patch('/me/password', [AuthController::class, 'changePassword'])->name('changePassword');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    });
});


Route::group([
    'middleware' => ['cors', 'json.response', 'requireToken', 'jwt.auth'],
    'prefix' => 'users'
], function($router) {
    Route::get('/', [UserController::class, 'getAllUsers']);
});