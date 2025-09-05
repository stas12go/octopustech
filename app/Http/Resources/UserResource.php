<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'UserResource', description: 'Базовая информация о пользователе', properties: [
    new OAT\Property(property: 'id', type: 'integer', example: 1),
    new OAT\Property(property: 'name', type: 'string', example: 'Test User'),
], type: 'object')]
/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
