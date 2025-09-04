<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batch_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('original_path');
            $table->string('processed_path')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('error_message')->nullable();
            $table->json('processing_options')->comment('Настройки обработки файла.');

            $table->timestamps();
            $table->timestamp('processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_files');
    }
};
