<?php

namespace App\Services;

use App\Enums\BatchStatusEnum;
use App\Http\Requests\StoreBatchRequest;
use App\Models\Batch;

class BatchService
{
    public function __construct(protected FileService $fileService)
    {
    }

    /** Обработка пакета. */
    public function process(StoreBatchRequest $request): Batch
    {
        /** @var Batch $batch */
        $batch = Batch::query()->create([
            'user_id' => 1,// тестовое решение
            'status'  => BatchStatusEnum::PENDING,
        ]);

        foreach ($request->file('files') as $index => $item) {
            $this->fileService->process($item, $batch->id, $request->processing_options[$index]);
        }

        return $batch;
    }
}
