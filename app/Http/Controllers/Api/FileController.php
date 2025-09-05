<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailedFileResource;
use App\Models\Batch;
use App\Models\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OAT;

class FileController extends Controller
{
    #[OAT\Get(path: '/batches/{batch}/files/{file}', description: 'Получение детальной информации о конкретном файле в пакете обработки', summary: 'Получение детальной информации о файле', tags: ['Files'], parameters: [
        new OAT\Parameter(name: 'batch', description: 'ID пакета обработки', in: 'path', required: true, schema: new OAT\Schema(type: 'integer', example: 1)),
        new OAT\Parameter(name: 'file', description: 'ID файла в пакете', in: 'path', required: true, schema: new OAT\Schema(type: 'integer', example: 1)),
    ], responses: [
        new OAT\Response(response: 200, description: 'Детальная информация о файле', content: new OAT\JsonContent(ref: '#/components/schemas/DetailedFileResource')),
        new OAT\Response(response: 404, description: 'Ресурс не найден', content: new OAT\JsonContent(ref: '#/components/schemas/NotFoundError')),
    ])]
    public function show(Batch $batch, File $file): JsonResource
    {
        $file->loadMissing('batch');

        if ($file->batch->id !== $batch->id) {
            throw new ModelNotFoundException();
        }

        return DetailedFileResource::make($file);
    }
}
