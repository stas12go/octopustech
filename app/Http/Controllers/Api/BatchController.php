<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Resources\DetailedBatchResource;
use App\Models\Batch;
use App\Services\BatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class BatchController extends Controller
{
    /** Сохранение нового пакета. */
    public function store(StoreBatchRequest $request, BatchService $batchService): JsonResponse
    {
        try {
            $batch = $batchService->process($request);

            return response()->json([
                'message'     => 'Пакет файлов сохранён',
                'batch_id'    => $batch->id,
                'total_files' => $batch->files()->count(),
                'status'      => $batch->status->name,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Ошибка сохранения пакета файлов', [
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            return response()->json([
                'error'   => 'Ошибка сохранения пакета файлов',
                'message' => 'Пожалуйста, повторите позже',
            ], 500);
        }
    }

    /** Отображение информации о пакете. */
    public function show(Batch $batch): JsonResource
    {
        $batch->loadMissing(['files', 'user']);

        return DetailedBatchResource::make($batch);
    }
}
