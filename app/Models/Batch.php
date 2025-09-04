<?php

namespace App\Models;

use App\Enums\BatchStatusEnum;
use App\Enums\FileStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * Модель соответствует сущности пакета (батча) документов.
 *
 * @property int $id
 * @property BatchStatusEnum $status
 * @property-read  float $progress
 * @property Collection<File> $files
 * @property User $user
 * @property Carbon $created_at
 * @property ?Carbon $processed_at
 */
class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'error_message',
        'user_id',
        'processed_at',
    ];

    /**
     * Пользователь, отправивший пакет изображений.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Текущий прогресс обработки пакета.
     */
    public function getProgressAttribute(): float
    {
        if ($this->files->count() === 0) {
            return 0;
        }

        return round(($this->getFilesCountByStatus(FileStatusEnum::COMPLETED) + $this->getFilesCountByStatus(FileStatusEnum::FAILED)) / $this->files->count() * 100, 2);
    }

    /**
     * Получение кол-ва файлов, находящихся в определённом статусе.
     */
    public function getFilesCountByStatus(FileStatusEnum $status): int
    {
        return Cache::remember("BATCH_{$this->id}_{$status->name}_FILES_COUNT", 60, fn() => $this->files()->where('status', $status->value)->count());
    }

    /**
     * Коллекция файлов, отправленных в пакете.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Обновление статуса пакета на основе статусов файлов, входящих в пакет.
     */
    public function updateStatus(): void
    {
        $pending = $this->getFilesCountByStatus(FileStatusEnum::PENDING);
        $processing = $this->getFilesCountByStatus(FileStatusEnum::PROCESSING);
        $completed = $this->getFilesCountByStatus(FileStatusEnum::COMPLETED);
        $failed = $this->getFilesCountByStatus(FileStatusEnum::FAILED);
        $total = $this->files->count();

        if ($pending > 0 || $processing > 0) {
            $this->status = BatchStatusEnum::PROCESSING;
        } elseif ($failed === $total) {
            $this->status = BatchStatusEnum::FAILED;
        } elseif ($completed === $total) {
            $this->status = BatchStatusEnum::COMPLETED;
            $this->processed_at = now();
        } elseif ($completed > 0 || $failed > 0) {
            $this->status = BatchStatusEnum::PARTIAL;
            $this->processed_at = now();
        }

        $this->save();
    }

    protected function casts(): array
    {
        return [
            'status'       => BatchStatusEnum::class,
            'processed_at' => 'datetime',
        ];
    }
}
