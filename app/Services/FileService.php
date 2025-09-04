<?php

namespace App\Services;

use App\Enums\FileStatusEnum;
use App\Jobs\ProcessImage;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /** Обработка файла. */
    public function process(UploadedFile $uploadedFile, int $batchID, array $processingOptions): File
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        $originalPath = Str::uuid()->toString();

        Storage::disk('public')->put(File::UPLOADED_DIR . $originalPath . '.' . $extension, file_get_contents($uploadedFile));

        $attributes = [
            'batch_id'           => $batchID,
            'original_name'      => $uploadedFile->getClientOriginalName(),
            'extension'          => $extension,
            'original_path'      => $originalPath,
            'status'             => FileStatusEnum::PENDING,
            'processing_options' => $processingOptions,
        ];

        $file = File::query()->create($attributes);

        ProcessImage::dispatch($file->id)->onQueue('image-processing');

        return $file;
    }
}
