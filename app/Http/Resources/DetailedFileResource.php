<?php

namespace App\Http\Resources;

use App\Enums\FileStatusEnum;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\File
 */
class DetailedFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'original_name'      => $this->original_name,
            'status'             => $this->status->name,
            'processing_options' => $this->processing_options,
            'error_message'      => $this->error_message,
            'file_size'          => $this->original_path && Storage::disk('public')->exists($this->original_path)
                ? Storage::disk('public')->size($this->original_path)
                : null,
            'download_url'       => $this->when($this->status === FileStatusEnum::COMPLETED && $this->processed_path !== null, function () {
                return Storage::disk('public')->url(File::PROCESSED_PATH . $this->processed_path . '.' . $this->extension);
            }),
            'original_url'       => $this->when($this->original_path !== null, function () {
                return Storage::disk('public')->url(File::UPLOADED_PATH . $this->original_path . '.' . $this->extension);
            }),

            'batch' => BatchResource::make($this->batch),

            'created_at'   => $this->created_at->format('d/m/y H:i:s'),
            'processed_at' => $this->processed_at?->format('d/m/y H:i:s'),
        ];
    }
}
