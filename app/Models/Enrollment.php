<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'transaction_id',
        'started_at',
        'ends_at',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function quizAnswers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    /**
     * Sisa hari belajar (0 kalau sudah lewat batas waktu).
     */
    public function getSisaHariAttribute(): int
    {
        if (now()->greaterThanOrEqualTo($this->ends_at)) {
            return 0;
        }

        return now()->diffInDays($this->ends_at);
    }

    /**
     * Apakah periode 30 hari kursus ini sudah berakhir.
     */
    public function getSudahSelesaiAttribute(): bool
    {
        return now()->greaterThanOrEqualTo($this->ends_at);
    }

    /**
     * Apakah siswa sudah mengerjakan kuis kursus ini minimal sekali.
     */
    public function getSudahMengerjakanKuisAttribute(): bool
    {
        return ! is_null($this->score);
    }

    /**
     * Syarat sertifikat: siswa sudah menyelesaikan (menjawab) semua soal
     * kuis kursus ini. Tidak perlu menunggu masa akses 30 hari berakhir,
     * supaya sertifikat langsung tercetak begitu kuis selesai dikerjakan.
     */
    public function getBisaUnduhSertifikatAttribute(): bool
    {
        return $this->sudah_mengerjakan_kuis;
    }

    /**
     * Progress belajar (0-100) untuk 1 paket kursus yang sedang diikuti.
     * Disusun dari 3 komponen supaya SINKRON di semua halaman (dashboard
     * student maupun daftar "Kelas Saya"), bukan cuma berdasarkan waktu:
     *  - 50% dari waktu akses yang sudah berjalan (maks 50)
     *  - 30% begitu kuis kursus ini sudah dikerjakan
     *  - 20% begitu sertifikat sudah bisa diunduh
     * Kalau kursus belum punya soal kuis sama sekali, komponen kuis &
     * sertifikat otomatis 0 (tidak akan pernah tercapai), jadi progress
     * tidak lagi "jalan sendiri" tanpa aktivitas nyata dari siswa.
     */
    public function getProgressPercentAttribute(): int
    {
        $totalDays = $this->started_at->diffInDays($this->ends_at) ?: 30;
        $elapsedDays = min($totalDays, $this->started_at->diffInDays(now()));

        $timeProgress = ($elapsedDays / $totalDays) * 50;
        $quizProgress = $this->sudah_mengerjakan_kuis ? 30 : 0;
        $certificateProgress = $this->bisa_unduh_sertifikat ? 20 : 0;

        return (int) round(min(100, $timeProgress + $quizProgress + $certificateProgress));
    }

    /**
     * Hitung ulang nilai berdasarkan jawaban kuis yang tersimpan, lalu simpan ke kolom score.
     */
    public function hitungNilai(): int
    {
        $total = $this->quizAnswers()->count();

        if ($total === 0) {
            $this->update(['score' => 0]);

            return 0;
        }

        $benar = $this->quizAnswers()->where('is_benar', true)->count();
        $nilai = (int) round(($benar / $total) * 100);

        $this->update(['score' => $nilai]);

        return $nilai;
    }
}
