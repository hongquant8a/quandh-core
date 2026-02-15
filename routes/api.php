<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    
Route::apiResource('users', UserController::class);
Route::post('users/bulk-delete', [UserController::class, 'bulkDestroy']);
Route::patch('users/bulk-status', [UserController::class, 'bulkUpdateStatus']);

Route::apiResource('posts', PostController::class);
Route::post('posts/bulk-delete', [PostController::class, 'bulkDestroy']);
Route::patch('posts/bulk-status', [PostController::class, 'bulkUpdateStatus']);
