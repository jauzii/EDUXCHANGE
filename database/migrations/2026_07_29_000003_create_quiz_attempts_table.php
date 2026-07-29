<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu baris di "quiz_attempts" = 1 checkpoint kuis yang sudah selesai
 * dikerjakan oleh 1 siswa (enrollment) untuk 1 kuis tertentu. Dipakai
 * untuk menghitung "sudah berapa dari 7 kuis yang selesai" dan sebagai
 * syarat sertifikat (semua checkpoint harus punya baris di sini).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('enrollment_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('quiz_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->unsignedTinyInteger('score')->default(0);

            $table->timestamps();

            $table->unique(['enrollment_id', 'quiz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
