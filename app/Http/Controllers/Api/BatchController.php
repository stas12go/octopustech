<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Resources\DetailedBatchResource;
use App\Models\Batch;
use App\Services\BatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OAT;

class BatchController extends Controller
{
    #[OAT\Post(path: '/batches', description: 'Загрузка множества изображений (пакета) для их асинхронной обработки', summary: 'Создание нового пакета', requestBody: new OAT\RequestBody(required: true,
        content: new OAT\MediaType(mediaType: 'multipart/form-data', schema: new OAT\Schema(required: ['files[]'], properties: [
            new OAT\Property(property: 'files[]',
                description: 'Массив файлов изображений (1-20 файлов, каждый до 15MB, форматы: jpg, jpeg, png, bmp, webp)',
                type: 'array',
                items: new OAT\Items(type: 'string', format: 'binary')),
            new OAT\Property(property: 'processing_options',
                description: 'JSON строка с массивом опций обработки для каждого файла',
                type: 'string',
                example: '[{"operation": "resize", "width": 800}, {"operation": "crop", "width": 400}]'),
        ]))), tags: ['Batches'], responses: [
        new OAT\Response(response: 201, description: 'Пакет файлов сохранён', content: new OAT\JsonContent(properties: [
            new OAT\Property(property: 'message', type: 'string', example: 'Пакет файлов сохранён'),
            new OAT\Property(property: 'batch_id', type: 'integer', example: 1),
            new OAT\Property(property: 'total_files', type: 'integer', example: 5),
            new OAT\Property(property: 'status', type: 'string', example: 'PENDING'),
        ])),
        new OAT\Response(response: 422, description: 'Ошибка валидации', content: new OAT\JsonContent(ref: '#/components/schemas/ValidationError')),
        new OAT\Response(response: 500, description: 'Ошибка сохранения пакета файлов', content: new OAT\JsonContent(properties: [
            new OAT\Property(property: 'error', type: 'string', example: 'Ошибка сохранения пакета файлов'),
            new OAT\Property(property: 'message', type: 'string', example: 'Пожалуйста, повторите позже'),
        ])),
    ])]
    public function store(StoreBatchRequest $request, BatchService $batchService): JsonResponse
    {
        try {
            $batch = $batchService->process($request);

            // Думаю, нет смысла создавать ресурсы на подобного рода ответы, т.к. они достаточно уникальны
            return response()->json([
                'message'     => 'Пакет файлов сохранён',
                'batch_id'    => $batch->id,
                'total_files' => $batch->files()->count(),
                'status'      => $batch->status->name,
            ], 201);
        } catch (\Exception $e) {
            logger()->error('Ошибка сохранения пакета файлов', [
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            // Думаю, нет смысла создавать ресурсы на подобного рода ответы, т.к. они достаточно уникальны
            return response()->json([
                'error'   => 'Ошибка сохранения пакета файлов',
                'message' => 'Пожалуйста, повторите позже',
            ], 500);
        }
    }

    #[OAT\Get(path: '/batches/{id}', description: 'Получение текущего статуса и детальной информации о пакете обработки', summary: 'Получение статуса пакета', tags: ['Batches'], parameters: [
        new OAT\Parameter(name: 'id', description: 'ID пакета обработки', in: 'path', required: true, schema: new OAT\Schema(type: 'integer', example: 1)),
    ], responses: [
        new OAT\Response(response: 200, description: 'Детальная информация о пакете', content: new OAT\JsonContent(ref: '#/components/schemas/DetailedBatchResource')),
        new OAT\Response(response: 404, description: 'Ресурс не найден', content: new OAT\JsonContent(ref: '#/components/schemas/NotFoundError')),
    ])]
    public function show(Batch $batch): JsonResource
    {
        $batch->loadMissing(['files', 'user']);

        return DetailedBatchResource::make($batch);
    }
}
