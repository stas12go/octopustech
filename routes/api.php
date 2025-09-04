<?php

use App\Http\Controllers\Api\BatchController;
use Illuminate\Support\Facades\Route;

Route::resource('batches', BatchController::class)->only(['show', 'store']);
