# API для пакетной обработки изображений

REST API сервис для асинхронной обработки пакетов изображений на Laravel.

## Возможности

- **Прием пакетов** из нескольких файлов изображений (до 20 файлов размером до 15 МБ каждый)
- **Асинхронная обработка** через очереди Redis
- **Отслеживание статуса** обработки в реальном времени (методы `show()`)
- **Параллельная обработка** нескольких файлов (запуск нескольких воркеров)
- **Детальная информация** по каждому файлу в пакете
- **REST API** с полной документацией OpenAPI/Swagger

## Требования

- Docker и Docker Compose
- PHP 8.2+
- Composer

## Быстрый старт

### 1. Клонирование и настройка

```bash
git clone <repository-url>
cd octopustech

# Копируем .env файл
cp .env.example .env

# Генерируем ключ приложения
./vendor/bin/sail artisan key:generate
```

### 2. Запуск контейнеров

```bash
./vendor/bin/sail up -d
```

### 3. Настройка приложения

```bash
# Устанавливаем зависимости
composer install

# Запускаем миграции
./vendor/bin/sail artisan migrate

# Генерируем симлинк для storage
./vendor/bin/sail artisan storage:link

# Генерируем документацию API
./vendor/bin/sail artisan l5-swagger:generate
```

### 4. Запуск workers

```bash
# хорайзон настроен на 2 процесса с двумя
./vendor/bin/sail artisan horizon
```

## Документация API

Документация доступна по адресу: http://localhost/api/documentation

Swagger UI предоставляет:
- Описание всех endpoints
- Примеры запросов и ответов
- Возможность тестирования API прямо из браузера
- Схемы данных и модели ошибок

## Конфигурация

### Environment variables (.env)

```env
APP_URL=http://localhost:80
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_CLIENT=phpredis
```

### Rate Limiting

- **Создание батчей**: 10 запросов в минуту
- **Просмотр статусов**: 30 запросов в минуту

## Мониторинг

### horizon dashboard
http://localhost/horizon
