<?php

namespace App\Http\Resources;

use App\Enums\FileStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Batch
 */
class DetailedBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'status'          => $this->status->name,
            'progress'        => $this->progress,
            'total_files'     => $this->files->count(),
            'processed_files' => $this->getFilesCountByStatus(FileStatusEnum::COMPLETED),
            'failed_files'    => $this->getFilesCountByStatus(FileStatusEnum::FAILED),

            'files' => FileResource::collection($this->files),
            'user'  => UserResource::make($this->user),

            'created_at'   => $this->created_at->format('d/m/y H:i:s'),
            'processed_at' => $this->processed_at?->format('d/m/y H:i:s'),
        ];
    }
}
