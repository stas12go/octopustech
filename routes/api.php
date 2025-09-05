<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;

Route::prefix('batches')->group(function () {
    Route::post('/', [BatchController::class, 'store'])->middleware('throttle:10,1');

    Route::prefix('{batch}')->group(function () {
        Route::get('/', [BatchController::class, 'show']);
        Route::get('files/{file}', [FileController::class, 'show']);
    })->middleware('throttle:30,1');
});

Route::fallback(function () {
    return response()->json([
        'error'   => 'Endpoint not found',
        'message' => 'The requested API endpoint does not exist',
    ], 404);
});
