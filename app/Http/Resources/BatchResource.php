<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Batch
 */
class BatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'status'          => $this->status->name,
            'progress'        => $this->progress,
            'total_files'     => $this->total_files,
            'processed_files' => $this->processed_files,
            'failed_files'    => $this->failed_files,
            'error_message'   => $this->error_message,

            'files' => BatchFileResource::collection($this->files),
            'user'  => UserResource::make($this->user),

            'created_at'   => $this->created_at->format('d/m/y H:i:s'),
            'processed_at' => $this->processed_at?->format('d/m/y H:i:s'),
        ];
    }
}
