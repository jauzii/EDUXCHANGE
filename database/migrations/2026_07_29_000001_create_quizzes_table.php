<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel "quizzes" merepresentasikan 1 paket kursus yang bisa punya
 * BEBERAPA kuis checkpoint (mis. 7 kuis), bukan cuma 1 kumpulan soal
 * besar seperti sebelumnya. Sertifikat baru terbuka setelah semua
 * checkpoint di sini selesai dikerjakan siswa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('judul');
            $table->unsignedTinyInteger('urutan')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
