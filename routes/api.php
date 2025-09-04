<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\BatchFileController;
use Illuminate\Support\Facades\Route;

// TODO refactor it
Route::apiResource('batches', BatchController::class)->only(['show', 'store']);

Route::apiResource('files', BatchFileController::class)->only(['show']);
