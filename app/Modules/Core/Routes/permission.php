<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\PermissionController;

Route::get('/export', [PermissionController::class, 'export']);
Route::post('/import', [PermissionController::class, 'import']);
Route::post('/bulk-delete', [PermissionController::class, 'bulkDestroy']);
Route::get('/stats', [PermissionController::class, 'stats']);
Route::get('/', [PermissionController::class, 'index']);
Route::get('/{permission}', [PermissionController::class, 'show']);
Route::post('/', [PermissionController::class, 'store']);
Route::put('/{permission}', [PermissionController::class, 'update']);
Route::patch('/{permission}', [PermissionController::class, 'update']);
Route::delete('/{permission}', [PermissionController::class, 'destroy']);
