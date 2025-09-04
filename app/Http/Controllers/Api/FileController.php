<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedFileResource;
use App\Models\Batch;
use App\Models\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\JsonResource;

class FileController extends Controller
{
    /** Получение информации о файле. */
    public function show(Batch $batch, File $file): JsonResource
    {
        $file->load('batch');

        if ($file->batch->id !== $batch->id) {
            throw new ModelNotFoundException();
        }

        return DetailedFileResource::make($file);
    }
}
