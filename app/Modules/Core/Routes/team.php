<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\TeamController;

Route::get('/export', [TeamController::class, 'export']);
Route::post('/import', [TeamController::class, 'import']);
Route::post('/bulk-delete', [TeamController::class, 'bulkDestroy']);
Route::patch('/bulk-status', [TeamController::class, 'bulkUpdateStatus']);
Route::get('/stats', [TeamController::class, 'stats']);
Route::get('/tree', [TeamController::class, 'tree']);
Route::get('/', [TeamController::class, 'index']);
Route::get('/{team}', [TeamController::class, 'show']);
Route::post('/', [TeamController::class, 'store']);
Route::put('/{team}', [TeamController::class, 'update']);
Route::patch('/{team}', [TeamController::class, 'update']);
Route::delete('/{team}', [TeamController::class, 'destroy']);
Route::patch('/{team}/status', [TeamController::class, 'changeStatus']);
