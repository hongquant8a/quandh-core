<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\UserController;

Route::get('/export', [UserController::class, 'export']);
Route::post('/import', [UserController::class, 'import']);

Route::post('/bulk-delete', [UserController::class, 'bulkDestroy']);
Route::patch('/bulk-status', [UserController::class, 'bulkUpdateStatus']);
Route::get('/', [UserController::class, 'index']);
Route::get('/{user}', [UserController::class, 'show']);
Route::post('/', [UserController::class, 'store']);
Route::put('/{user}', [UserController::class, 'update']);
Route::patch('/{user}', [UserController::class, 'update']);
Route::delete('/{user}', [UserController::class, 'destroy']);
