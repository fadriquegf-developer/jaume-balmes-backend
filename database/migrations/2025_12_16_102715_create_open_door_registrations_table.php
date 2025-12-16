<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_door_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('open_door_session_id')->constrained()->onDelete('cascade');

            // Datos del alumno
            $table->string('student_name');
            $table->string('student_surname');
            $table->date('student_birthdate')->nullable();
            $table->string('current_school')->nullable();
            $table->string('current_grade')->nullable();

            // Datos del tutor/familia
            $table->string('tutor_name');
            $table->string('tutor_surname');
            $table->string('tutor_email');
            $table->string('tutor_phone');
            $table->enum('tutor_relationship', ['father', 'mother', 'tutor', 'other'])->default('tutor');

            // Intereses
            $table->json('interested_grades')->nullable(); // Ciclos de interés
            $table->text('comments')->nullable();
            $table->string('how_did_you_know')->nullable(); // Cómo nos conoció

            // Control
            $table->enum('status', ['pending', 'confirmed', 'attended', 'no_show', 'cancelled'])->default('pending');
            $table->string('confirmation_token')->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('attended_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_door_registrations');
    }
};
