<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Nullable supaya soal lama (kalau ada, yang belum dikelompokkan
            // ke kuis manapun) tetap aman dan tidak error saat migrate.
            $table->foreignId('quiz_id')
                  ->nullable()
                  ->after('course_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quiz_id');
        });
    }
};
