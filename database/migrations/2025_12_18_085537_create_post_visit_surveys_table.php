<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_visit_surveys', function (Blueprint $table) {
            $table->id();

            // Relació amb la inscripció
            $table->foreignId('open_door_registration_id')
                ->constrained()
                ->onDelete('cascade');

            // Token únic per accedir a l'enquesta
            $table->uuid('survey_token')->unique();

            // Estat de l'enquesta
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');

            // Respostes de l'enquesta
            $table->unsignedTinyInteger('overall_rating')->nullable(); // 1-5
            $table->unsignedTinyInteger('information_rating')->nullable(); // 1-5
            $table->unsignedTinyInteger('attention_rating')->nullable(); // 1-5
            $table->unsignedTinyInteger('facilities_rating')->nullable(); // 1-5
            $table->boolean('doubts_resolved')->nullable(); // Sí/No
            $table->text('liked_most')->nullable(); // Text lliure
            $table->text('improvements')->nullable(); // Text lliure
            $table->enum('enrollment_interest', ['very_high', 'high', 'medium', 'low', 'none'])->nullable();
            $table->text('additional_comments')->nullable();

            // Control
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_visit_surveys');
    }
};
