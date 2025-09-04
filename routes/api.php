<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;

Route::apiResource('batches', BatchController::class)->only(['show', 'store'])->middleware('throttle:10,1');
Route::prefix('batches/{batch}')->group(function () {
    Route::get('files/{file}', [FileController::class, 'show'])->middleware('throttle:30,1');
});

Route::fallback(function () {
    return response()->json([
        'error'   => 'Endpoint not found',
        'message' => 'The requested API endpoint does not exist',
    ], 404);
});
