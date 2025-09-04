<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\File
 */
class DetailedFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'original_name'       => $this->original_name,
            'status'              => $this->status->name,
            'processing_options'  => $this->processing_options,
            'error_message'       => $this->error_message,
            'original_url'        => $this->original_url,
            'original_file_size'  => $this->original_file_size,
            'processed_url'       => $this->processed_url,
            'processed_file_size' => $this->processed_file_size,

            'batch' => BatchResource::make($this->batch),

            'created_at'   => $this->created_at->format('d/m/y H:i:s'),
            'processed_at' => $this->processed_at?->format('d/m/y H:i:s'),
        ];
    }
}
