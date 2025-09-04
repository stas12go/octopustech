<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('extension');
            $table->string('original_path');
            $table->string('processed_path')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->text('error_message')->nullable();
            $table->json('processing_options')->comment('Настройки обработки файла.');

            $table->timestamps();
            $table->timestamp('processed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
