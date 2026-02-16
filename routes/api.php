<?php

use Illuminate\Support\Facades\Route;

// Auth module - public routes
Route::prefix('auth')->group(function () {
    require base_path('app/Modules/Auth/Routes/auth.php');
});

// Protected routes
// Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user());

    Route::prefix('users')->group(function () {
        require base_path('app/Modules/User/Routes/user.php');
    });
    Route::prefix('posts')->group(function () {
        require base_path('app/Modules/Post/Routes/post.php');
    });
    Route::prefix('post-categories')->group(function () {
        require base_path('app/Modules/Post/Routes/post_category.php');
    });
// });
