<?php

namespace App\Models;

use App\Enums\BatchFileStatusEnum;
use App\Enums\BatchStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property BatchStatusEnum $status
 * @property float $progress
 * @property Collection<BatchFile> $files
 * @property User $user
 * @property Carbon $created_at
 * @property Carbon $processed_at
 */
class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'total_files',
        'processed_files',
        'failed_files',
        'error_message',
        'processing_options',
        'user_id',
        'processed_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressAttribute(): float
    {
        if ($this->files->count() === 0) {
            return 0;
        }

        return round(($this->getFilesCountByStatus(BatchFileStatusEnum::COMPLETED) + $this->getFilesCountByStatus(BatchFileStatusEnum::FAILED)) / $this->files->count() * 100, 2);
    }

    public function getFilesCountByStatus(BatchFileStatusEnum $status): int
    {
        return $this->files()->where('status', $status)->count();
    }

    public function files(): HasMany
    {
        return $this->hasMany(BatchFile::class);
    }

    protected function casts(): array

    {
        return [
            'status'             => BatchStatusEnum::class,
            'processing_options' => 'array',
            'processed_at'       => 'datetime',
        ];
    }
}
