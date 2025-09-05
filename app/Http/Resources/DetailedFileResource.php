<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'DetailedFileResource', description: 'Детальная информация о файле обработки', properties: [
    new OAT\Property(property: 'id', type: 'integer', example: 1),
    new OAT\Property(property: 'original_name', type: 'string', example: 'image.jpg'),
    new OAT\Property(property: 'status', type: 'string', enum: ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED'], example: 'COMPLETED'),
    new OAT\Property(property: 'processing_options', properties: [
        new OAT\Property(property: 'operation', type: 'string', enum: ['resize', 'crop', 'normalize']),
        new OAT\Property(property: 'width', type: 'integer', minimum: 1),
        new OAT\Property(property: 'height', type: 'integer', minimum: 1),
        new OAT\Property(property: 'quality', type: 'integer', maximum: 100, minimum: 1),
        new OAT\Property(property: 'crop', type: 'boolean'),
    ], type: 'object', example: ['operation' => 'resize', 'width' => 800, 'quality' => 90], nullable: true),
    new OAT\Property(property: 'error_message', type: 'string', nullable: true),
    new OAT\Property(property: 'original_url', type: 'string', format: 'uri', example: 'http://localhost/storage/uploads/abc123.jpg'),
    new OAT\Property(property: 'original_file_size', type: 'integer', example: 1024567, nullable: true),
    new OAT\Property(property: 'processed_url', type: 'string', format: 'uri', example: 'http://localhost/storage/processed/abc123_processed.jpg', nullable: true),
    new OAT\Property(property: 'processed_file_size', type: 'integer', example: 987654, nullable: true),
    new OAT\Property(property: 'batch', type: 'array', items: new OAT\Items(properties: [
        new OAT\Property(property: 'id', type: 'integer'),
        new OAT\Property(property: 'status', type: 'string', enum: ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED', 'PARTIAL']),
    ], type: 'object'), example: [
        'id'     => 1,
        'status' => 'PENDING',
    ]),
    new OAT\Property(property: 'created_at', type: 'string', example: '15/01/24 10:30:00'),
    new OAT\Property(property: 'processed_at', type: 'string', example: '15/01/24 10:32:15', nullable: true),
], type: 'object')]
/** @mixin \App\Models\File */
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
