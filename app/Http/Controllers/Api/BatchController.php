<?php

namespace App\Http\Controllers\Api;

use App\Enums\BatchFileStatusEnum;
use App\Enums\BatchStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use App\Models\BatchFile;
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
                'user_id'            => 1,// тестовое решение
                'status'             => BatchStatusEnum::PENDING,
                'total_files'        => count($request->file('files')),
                'processing_options' => $request->processing_options ?? [],
            ]);

            foreach ($request->file('files') as $index => $item) {
                $this->processFile($item, $batch, $index, $request->processing_options[$index]);
            }

            return response()->json([
                'message'     => 'Пакет файлов сохранён',
                'batch_id'    => $batch->id,
                'total_files' => $batch->total_files,
                'status'      => $batch->status->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Ошибка сохранения пакета файлов',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function processFile(UploadedFile $file, Batch $batch, int|string $index, array $processingOptions)
    {
        $uuid = Str::uuid();
        $extension = $file->getClientOriginalExtension();
        $originalPath = "uploads/{$uuid}.{$extension}";

        Storage::disk('public')->put($originalPath, file_get_contents($file));

        $batchFile = BatchFile::query()->create([
            'batch_id'           => $batch->id,
            'original_name'      => $file->getClientOriginalName(),
            'original_path'      => $originalPath,
            'status'             => BatchFileStatusEnum::PENDING,
            'processing_options' => $processingOptions,
        ]);
        // TODO ProcessImage::dispatch($batchFile->id)->onQueue('image-processing');
    }

    public function show(string $id): JsonResource
    {
        $batch = Batch::with(['files'])->findOrFail($id);

        return BatchResource::make($batch);
    }
}
