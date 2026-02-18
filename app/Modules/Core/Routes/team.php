<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Core\TeamController;

Route::get('/export', [TeamController::class, 'export'])->middleware('permission:teams.export,web');
Route::post('/import', [TeamController::class, 'import'])->middleware('permission:teams.import,web');
Route::post('/bulk-delete', [TeamController::class, 'bulkDestroy'])->middleware('permission:teams.bulkDestroy,web');
Route::patch('/bulk-status', [TeamController::class, 'bulkUpdateStatus'])->middleware('permission:teams.bulkUpdateStatus,web');
Route::get('/stats', [TeamController::class, 'stats'])->middleware('permission:teams.stats,web');
Route::get('/tree', [TeamController::class, 'tree'])->middleware('permission:teams.tree,web');
Route::get('/', [TeamController::class, 'index'])->middleware('permission:teams.index,web');
Route::get('/{team}', [TeamController::class, 'show'])->middleware('permission:teams.show,web');
Route::post('/', [TeamController::class, 'store'])->middleware('permission:teams.store,web');
Route::put('/{team}', [TeamController::class, 'update'])->middleware('permission:teams.update,web');
Route::patch('/{team}', [TeamController::class, 'update'])->middleware('permission:teams.update,web');
Route::delete('/{team}', [TeamController::class, 'destroy'])->middleware('permission:teams.destroy,web');
Route::patch('/{team}/status', [TeamController::class, 'changeStatus'])->middleware('permission:teams.changeStatus,web');
