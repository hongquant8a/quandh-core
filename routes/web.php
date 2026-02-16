<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\UserController;
use App\Modules\Post\PostController;

Route::get('/', function () {
    return view('welcome');
});


