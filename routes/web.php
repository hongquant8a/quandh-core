<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\UserController;
use App\Modules\Post\PostController;

Route::get('/', function () {
    return view('welcome');
});


