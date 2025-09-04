<?php

namespace App\Http\Controllers\Api;

use App\Enums\BatchStatusEnum;
use App\Enums\FileStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Resources\DetailedBatchResource;
use App\Jobs\ProcessImage;
use App\Models\Batch;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BatchController extends Controller
{
    public function store(StoreBatchRequest $request): JsonResponse
    {
        try {
            /** @var Batch $batch */
            $batch = Batch::query()->create([
                'user_id' => 1,// тестовое решение
                'status'  => BatchStatusEnum::PENDING,
            ]);

            foreach ($request->file('files') as $index => $item) {
                $this->processFile($item, $batch, $request->processing_options[$index]);
            }

            return response()->json([
                'message'     => 'Пакет файлов сохранён',
                'batch_id'    => $batch->id,
                'total_files' => $batch->files->count(),
                'status'      => $batch->status->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Ошибка сохранения пакета файлов',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function processFile(UploadedFile $file, Batch $batch, array $processingOptions)
    {
        $extension = $file->getClientOriginalExtension();
        $originalPath = Str::uuid()->toString();

        Storage::disk('public')->put(File::UPLOADED_DIR . $originalPath . '.' . $extension, file_get_contents($file));

        $attributes = [
            'batch_id'           => $batch->id,
            'original_name'      => $file->getClientOriginalName(),
            'extension'          => $extension,
            'original_path'      => $originalPath,
            'status'             => FileStatusEnum::PENDING,
            'processing_options' => $processingOptions,
        ];

        //        dd($attributes);

        $batchFile = File::query()->create($attributes);

        ProcessImage::dispatch($batchFile->id)->onQueue('image-processing');
    }

    public function show(Batch $batch): JsonResource
    {
        $batch->load(['files', 'user']);

        return DetailedBatchResource::make($batch);
    }
}
