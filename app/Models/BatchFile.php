<?php

namespace App\Models;

use App\Enums\BatchFileStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property BatchFileStatusEnum $status
 * @property string $processed_path
 * @property string $error_message
 * @property Carbon $processed_at
 */
class BatchFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'original_name',
        'original_path',
        'processed_path',
        'status',
        'error_message',
        'processing_options',
        'processed_at',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function markAsProcessing(): void
    {
        $this->status = BatchFileStatusEnum::PROCESSING;

        $this->save();
    }

    public function markAsCompleted(string $processedPath): void
    {
        $this->status = BatchFileStatusEnum::COMPLETED;
        $this->processed_path = $processedPath;
        $this->processed_at = now();

        $this->save();
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->status = BatchFileStatusEnum::FAILED;
        $this->error_message = $errorMessage;
        $this->processed_at = now();

        $this->save();
    }

    protected function casts(): array
    {
        return [
            'status'             => BatchFileStatusEnum::class,
            'processing_options' => 'array',
            'processed_at'       => 'datetime',
        ];
    }
}
