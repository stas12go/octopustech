<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;

// TODO refactor it
Route::apiResource('batches', BatchController::class)->only(['show', 'store']);

Route::apiResource('files', FileController::class)->only(['show']);
