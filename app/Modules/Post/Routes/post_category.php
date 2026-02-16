<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Post\PostCategoryController;

Route::get('/export', [PostCategoryController::class, 'export']);
Route::post('/import', [PostCategoryController::class, 'import']);

Route::post('/bulk-delete', [PostCategoryController::class, 'bulkDestroy']);
Route::patch('/bulk-status', [PostCategoryController::class, 'bulkUpdateStatus']);
Route::get('/stats', [PostCategoryController::class, 'stats']);
Route::get('/tree', [PostCategoryController::class, 'tree']);
Route::get('/', [PostCategoryController::class, 'index']);
Route::get('/{category}', [PostCategoryController::class, 'show']);
Route::post('/', [PostCategoryController::class, 'store']);
Route::put('/{category}', [PostCategoryController::class, 'update']);
Route::patch('/{category}', [PostCategoryController::class, 'update']);
Route::delete('/{category}', [PostCategoryController::class, 'destroy']);
Route::patch('/{category}/status', [PostCategoryController::class, 'changeStatus']);
