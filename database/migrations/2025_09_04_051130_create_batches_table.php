<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('total_files');
            $table->tinyInteger('processed_files')->default(0);
            $table->tinyInteger('failed_files')->default(0);
            $table->text('error_message')->nullable();
            $table->json('processing_options')->comment('Настройки обработки файлов.');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->timestamp('processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
