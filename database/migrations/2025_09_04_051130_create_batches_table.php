<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('status')->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->timestamp('processed_at')->nullable();
            // смысла в индексах нет
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
