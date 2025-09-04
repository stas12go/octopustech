<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedFileResource;
use App\Models\BatchFile;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchFileController extends Controller
{
    public function show(BatchFile $batchFile): JsonResource
    {
        $batchFile->load('batch');

        return DetailedFileResource::make($batchFile);
    }
}
