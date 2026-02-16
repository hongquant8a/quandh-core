<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Post\PostController;

Route::get('/export', [PostController::class, 'export']);
Route::post('/import', [PostController::class, 'import']);

Route::post('/bulk-delete', [PostController::class, 'bulkDestroy']);
Route::patch('/bulk-status', [PostController::class, 'bulkUpdateStatus']);
Route::get('/', [PostController::class, 'index']);
Route::get('/{post}', [PostController::class, 'show']);
Route::post('/', [PostController::class, 'store']);
Route::put('/{post}', [PostController::class, 'update']);
Route::patch('/{post}', [PostController::class, 'update']);
Route::delete('/{post}', [PostController::class, 'destroy']);
