<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\BatchFileController;
use Illuminate\Support\Facades\Route;

// TODO refactor it
Route::resource('batches', BatchController::class)->only(['show', 'store']);

Route::prefix('batches/{batch}')->group(function () {
    Route::get('files', [BatchFileController::class, 'index']);
    Route::get('files/{file}', [BatchFileController::class, 'show']);
    Route::get('results', [BatchController::class, 'results']);
});
