<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\RoleController;

Route::get('/export', [RoleController::class, 'export']);
Route::post('/import', [RoleController::class, 'import']);
Route::post('/bulk-delete', [RoleController::class, 'bulkDestroy']);
Route::patch('/bulk-status', [RoleController::class, 'bulkUpdateStatus']);
Route::get('/stats', [RoleController::class, 'stats']);
Route::get('/', [RoleController::class, 'index']);
Route::get('/{role}', [RoleController::class, 'show']);
Route::post('/', [RoleController::class, 'store']);
Route::put('/{role}', [RoleController::class, 'update']);
Route::patch('/{role}', [RoleController::class, 'update']);
Route::delete('/{role}', [RoleController::class, 'destroy']);
Route::patch('/{role}/status', [RoleController::class, 'changeStatus']);
