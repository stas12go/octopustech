<?php

namespace App\Models;

use App\Enums\FileStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property Batch $batch
 * @property FileStatusEnum $status
 * @property string $original_name
 * @property string $extension
 * @property string $original_path
 * @property-read  string $full_original_path
 * @property-read  string $original_url
 * @property-read  int $original_file_size
 * @property ?string $processed_path
 * @property-read ?string $full_processed_path
 * @property-read ?string $processed_url
 * @property-read ?int $processed_file_size
 * @property ?string $error_message
 * @property array $processing_options
 * @property Carbon $created_at
 * @property ?Carbon $processed_at
 */
class File extends Model
{
    use HasFactory;

    /** @var string Директория хранения загруженных файлов. */
    public const UPLOADED_DIR = 'uploads/';
    /** @var string Директория хранения обработанных файлов. */
    public const PROCESSED_DIR = 'processed/';

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

    /**
     * Пакет, которому принадлежит текущий файл.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Отметить файл как находящийся в процессе обработки.
     */
    public function markAsProcessing(): void
    {
        $this->status = FileStatusEnum::PROCESSING;

        $this->save();
    }

    /**
     * Отметить файл как успешно обработанный.
     */
    public function markAsCompleted(string $processedPath): void
    {
        $this->status = FileStatusEnum::COMPLETED;
        $this->processed_path = $processedPath;
        $this->processed_at = now();

        $this->save();
    }

    /**
     * Отметить файл как неудачно обработанный.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->status = FileStatusEnum::FAILED;
        $this->error_message = $errorMessage;
        $this->processed_at = now();

        $this->save();
    }

    /**
     * Относительный путь до оригинального файла.
     */
    public function getFullOriginalPathAttribute(): string
    {
        return self::UPLOADED_DIR . $this->original_path . '.' . $this->extension;
    }

    /**
     * Относительный путь до обработанного файла.
     */
    public function getFullProcessedPathAttribute(): ?string
    {
        return $this->processed_path === null
            ? null
            : self::PROCESSED_DIR . $this->processed_path . '.' . $this->extension;
    }

    /**
     * Ссылка на оригинальный файл.
     */
    public function getOriginalUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->full_original_path);
    }

    /**
     * Ссылка на обработанный файл.
     */
    public function getProcessedUrlAttribute(): ?string
    {
        return $this->full_processed_path === null
            ? null
            : Storage::disk('public')->url($this->full_processed_path);
    }

    /**
     * Размер оригинального файла.
     */
    public function getOriginalFileSizeAttribute(): ?int
    {
        if (!Storage::disk('public')->exists($this->full_original_path)) {
            return null;
        }

        return Storage::disk('public')->size($this->full_original_path);
    }

    /**
     * Размер оригинального файла.
     */
    public function getProcessedFileSizeAttribute(): ?int
    {
        if (!$this->full_processed_path || !Storage::disk('public')->exists($this->full_processed_path)) {
            return null;
        }

        return Storage::disk('public')->size($this->full_processed_path);
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
