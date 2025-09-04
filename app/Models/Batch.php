<?php

namespace App\Models;

use App\Enums\BatchStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $total_files
 * @property int $processed_files
 * @property int $failed_files
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
        'completed_at',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(BatchFile::class);
    }

    public function getProgressAttribute(): float
    {
        if ($this->total_files === 0) {
            return 0;
        }

        return round(($this->processed_files + $this->failed_files) / $this->total_files * 100, 2);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected function casts(): array

    {
        return [
            'status'             => BatchStatusEnum::class,
            'processing_options' => 'array',
            'completed_at'       => 'datetime',
        ];
    }
}
