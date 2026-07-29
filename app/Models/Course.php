<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'tutor_nama',
        'nama_kursus',
        'kategori',
        'harga',
        'deskripsi',
    ];

    /**
     * Tutor pemilik/pengajar kursus ini (relasi lama, tetap dipakai
     * sebagai data teknis di database).
     */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    /**
     * Nama tutor yang ditampilkan ke user. Mengutamakan nama yang diisi
     * manual oleh admin (tutor_nama), lalu fallback ke relasi tutor lama
     * untuk data kursus yang dibuat sebelum field ini ada.
     */
    public function getTutorDisplayNameAttribute(): string
    {
        return $this->tutor_nama ?: ($this->tutor?->user?->name ?? 'Tutor EDUXCHANGE');
    }

    /**
     * Materi-materi pembelajaran di dalam kursus ini.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Transaksi pembelian untuk kursus ini.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Pendaftaran/progress belajar siswa untuk kursus ini.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Soal-soal kuis untuk kursus ini (relasi lama, dipertahankan supaya
     * data lama tetap terbaca).
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Kuis-kuis checkpoint untuk kursus ini (mis. 7 kuis per paket).
     * Siswa harus menyelesaikan SEMUA kuis di sini sebelum sertifikat
     * bisa diunduh.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('urutan');
    }
}
