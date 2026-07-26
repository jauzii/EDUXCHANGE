<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom tutor_nama supaya admin bisa mengisi nama tutor
     * secara manual (teks bebas) saat membuat/mengedit paket kursus,
     * tanpa harus memilih dari data tutor yang sudah terdaftar.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('tutor_nama')->nullable()->after('tutor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('tutor_nama');
        });
    }
};
