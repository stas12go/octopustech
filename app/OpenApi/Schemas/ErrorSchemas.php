<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'ValidationError', description: 'Ошибка валидации данных', properties: [
    new OAT\Property(property: 'error', type: 'string', example: 'Ошибка валидации'),
    new OAT\Property(property: 'messages', type: 'object', example: [
        'processing_options.0.operation.string' => ['Тип операции   должен быть строкой'],
        'files.1.mimes'                         => ['Некорректный mime-тип файла'],
    ], additionalProperties: new OAT\AdditionalProperties(type: 'array', items: new OAT\Items(type: 'string'))),
], type: 'object')]
#[OAT\Schema(schema: 'NotFoundError', description: 'Ресурс не найден', properties: [
    new OAT\Property(property: 'error', type: 'string', example: 'Not found'),
    new OAT\Property(property: 'messages', type: 'object', example: 'The requested resource was not found', additionalProperties: new OAT\AdditionalProperties(type: 'array',
        items: new OAT\Items(type: 'string'))),
], type: 'object')]
class ErrorSchemas
{
}
