<?php

namespace App\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\Info(version: '1.0.0', description: 'API для асинхронной обработки пакета изображений', title: 'API пакетного обработчика изображений')]
#[OAT\Server(url: 'http://localhost:80/api', description: 'Локальный сервер разработки')]
#[OAT\Tag(name: 'Batches', description: 'Операции с пакетами обработки')]
#[OAT\Tag(name: 'Files', description: 'Операции с файлами')]
class OpenApiInfo
{
}
