<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'FileResource', description: 'Базовая информация о файле в пакете обработки', properties: [
    new OAT\Property(property: 'id', type: 'integer', example: 1),
    new OAT\Property(property: 'status', type: 'string', enum: ['pending', 'processing', 'completed', 'failed'], example: 'completed'),
    new OAT\Property(property: 'created_at', type: 'string', example: '15/01/24 10:30:00'),
    new OAT\Property(property: 'processed_at', type: 'string', example: '15/01/24 10:32:15', nullable: true),
], type: 'object')]
/** @mixin \App\Models\File */
class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status->name,
            'created_at'   => $this->created_at->format('d/m/y H:i:s'),
            'processed_at' => $this->processed_at?->format('d/m/y H:i:s'),
        ];
    }
}
