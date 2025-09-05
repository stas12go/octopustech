<?php

namespace App\Http\Resources;

use App\Enums\FileStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'DetailedBatchResource', description: 'Детальная информация о пакете обработки', properties: [
    new OAT\Property(property: 'id', type: 'integer', example: 1),
    new OAT\Property(property: 'status', type: 'string', enum: ['pending', 'processing', 'completed', 'failed', 'partial'], example: 'processing'),
    new OAT\Property(property: 'progress', type: 'number', format: 'float', example: 50.5),
    new OAT\Property(property: 'total_files', type: 'integer', example: 10),
    new OAT\Property(property: 'processed_files', type: 'integer', example: 5),
    new OAT\Property(property: 'failed_files', type: 'integer', example: 1),
    new OAT\Property(property: 'error_message', type: 'string', nullable: true),
    new OAT\Property(property: 'files', type: 'array', items: new OAT\Items(ref: '#/components/schemas/FileResource')),
    new OAT\Property(property: 'user', ref: '#/components/schemas/UserResource'),
    new OAT\Property(property: 'created_at', type: 'string', example: '15/01/24 10:30:00'),
    new OAT\Property(property: 'processed_at', type: 'string', example: '15/01/24 10:35:00', nullable: true),
], type: 'object')]
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
