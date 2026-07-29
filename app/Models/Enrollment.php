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

    /**
     * Riwayat checkpoint kuis yang sudah diselesaikan siswa untuk
     * enrollment ini (1 baris = 1 kuis yang sudah dikerjakan).
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
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
     * Total kuis checkpoint yang tersedia di paket kursus ini (mis. 2).
     */
    public function getTotalKuisAttribute(): int
    {
        return $this->course->quizzes()->count();
    }

    /**
     * Sudah berapa kuis checkpoint yang selesai dikerjakan siswa untuk
     * enrollment ini.
     */
    public function getKuisSelesaiAttribute(): int
    {
        return $this->quizAttempts()->count();
    }

    /**
     * Apakah siswa sudah mengerjakan kuis kursus ini minimal sekali
     * (dipakai untuk tampilan "Nilai Kuis" berjalan, sebelum semua
     * checkpoint selesai).
     */
    public function getSudahMengerjakanKuisAttribute(): bool
    {
        return ! is_null($this->score);
    }

    /**
     * Syarat utama sertifikat: SEMUA kuis checkpoint paket ini (mis.
     * semuanya) sudah selesai dikerjakan siswa. Kalau paketnya belum
     * punya kuis sama sekali, ini selalu false (belum ada yang bisa
     * "diselesaikan").
     */
    public function getSudahMenyelesaikanSemuaKuisAttribute(): bool
    {
        return $this->total_kuis > 0 && $this->kuis_selesai >= $this->total_kuis;
    }

    /**
     * Syarat sertifikat: siswa sudah menyelesaikan SEMUA kuis checkpoint
     * paket ini. Tidak perlu menunggu masa akses 30 hari berakhir, supaya
     * sertifikat langsung tercetak begitu kuis terakhir selesai dikerjakan.
     */
    public function getBisaUnduhSertifikatAttribute(): bool
    {
        return $this->sudah_menyelesaikan_semua_kuis;
    }

    /**
     * Progress belajar (0-100) untuk 1 paket kursus yang sedang diikuti.
     * Disusun dari 3 komponen supaya SINKRON di semua halaman (dashboard
     * student maupun daftar "Kelas Saya"), bukan cuma berdasarkan waktu:
     *  - 50% dari waktu akses yang sudah berjalan (maks 50)
     *  - maks 30% mengikuti proporsi kuis checkpoint yang sudah selesai
     *    (mis. 1 dari 2 kuis = 15%)
     *  - 20% begitu SEMUA kuis selesai & sertifikat bisa diunduh
     * Kalau kursus belum punya kuis sama sekali, komponen kuis &
     * sertifikat otomatis 0 (tidak akan pernah tercapai), jadi progress
     * tidak lagi "jalan sendiri" tanpa aktivitas nyata dari siswa.
     */
    public function getProgressPercentAttribute(): int
    {
        $totalDays = $this->started_at->diffInDays($this->ends_at) ?: 30;
        $elapsedDays = min($totalDays, $this->started_at->diffInDays(now()));

        $timeProgress = ($elapsedDays / $totalDays) * 50;
        $quizProgress = $this->total_kuis > 0 ? ($this->kuis_selesai / $this->total_kuis) * 30 : 0;
        $certificateProgress = $this->bisa_unduh_sertifikat ? 20 : 0;

        return (int) round(min(100, $timeProgress + $quizProgress + $certificateProgress));
    }

    /**
     * Hitung ulang nilai rata-rata enrollment ini dari semua checkpoint
     * kuis yang sudah pernah diselesaikan, lalu simpan ke kolom score.
     * Dipanggil setiap kali 1 kuis checkpoint baru saja disubmit.
     */
    public function recalculateScore(): int
    {
        $rataRata = (int) round($this->quizAttempts()->avg('score') ?? 0);

        $this->update(['score' => $rataRata]);

        return $rataRata;
    }
}
