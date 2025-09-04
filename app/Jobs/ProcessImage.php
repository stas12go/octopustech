<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessImage implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;

    public int $maxExceptions = 2;

    public int $timeout = 60;

    public function __construct(public int $fileId)
    {
    }

    public function handle(): void
    {
        /** @var File $file */
        $file = File::query()->findOrFail($this->fileId);

        try {
            $file->markAsProcessing();

            $processedPath = $this->process($file);

            $file->markAsCompleted($processedPath);
            $file->batch->updateStatus();
        } catch (\Exception $e) {
            $file->markAsFailed($e->getMessage());
            $file->batch->updateStatus();

            throw $e;
        }
    }

    private function process(File $file): string
    {
        $success = fake()->boolean(67);

        if (!$success) {
            throw new \Exception('Что-то пошло не так :(');
        }

        $processedPath = "{$file->original_path}_{$file->processing_options['operation']}";

        sleep(10);
        Storage::disk('public')->copy($file->full_original_path, File::PROCESSED_DIR . $processedPath . '.' . $file->extension);

        return $processedPath;
    }

    public function fail(\Throwable $exception = null)
    {
        // TODO is method correct?
        logger()->error('Ошибка при обработке файла', [
            'file_id' => $this->fileId,
            'message' => $exception->getMessage(),
        ]);
    }
}
