<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedFileResource;
use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class FileController extends Controller
{
    public function show(File $file): JsonResource
    {
        $file->load('batch');

        return DetailedFileResource::make($file);
    }
}
