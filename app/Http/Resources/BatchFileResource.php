<?php

namespace App\Http\Resources;

use App\Enums\BatchFileStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\BatchFile
 */
class BatchFileResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
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
            'download_url'       => $this->when($this->status === BatchFileStatusEnum::COMPLETED && $this->processed_path, function () {
                return Storage::disk('public')->url($this->processed_path);
            }),
            'original_url'       => $this->when($this->original_path, function () {
                return Storage::disk('public')->url($this->original_path);
            }),

            'created_at'         => $this->created_at->toISOString(),
            'processed_at'       => $this->processed_at?->toISOString(),
        ];
    }
}
