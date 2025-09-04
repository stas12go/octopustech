<?php

namespace App\Models;

use App\Enums\FileStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property \App\Models\Batch $batch
 * @property FileStatusEnum $status
 * @property string $original_name
 * @property string $extension
 * @property string $original_path
 * @property string $processed_path
 * @property string $error_message
 * @property array $processing_options
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $processed_at
 */
class File extends Model
{
    use HasFactory;

    public const UPLOADED_PATH = 'uploads/';
    public const PROCESSED_PATH = 'processed/';

    protected $fillable = [
        'batch_id',
        'original_name',
        'extension',
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
        $this->status = FileStatusEnum::PROCESSING;

        $this->save();
    }

    public function markAsCompleted(string $processedPath): void
    {
        $this->status = FileStatusEnum::COMPLETED;
        $this->processed_path = $processedPath;
        $this->processed_at = now();

        $this->save();
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->status = FileStatusEnum::FAILED;
        $this->error_message = $errorMessage;
        $this->processed_at = now();

        $this->save();
    }

    protected function casts(): array
    {
        return [
            'status'             => FileStatusEnum::class,
            'processing_options' => 'array',
            'processed_at'       => 'datetime',
        ];
    }
}
