<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\Question;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userEnrollments = $user->enrollments()
            ->with(['course.materials'])
            ->get();

        $quizDoneCount = $userEnrollments->whereNotNull('score')->count();
        $certificateAvailableCount = $userEnrollments->filter->bisa_unduh_sertifikat->count();
        $materialCount = $userEnrollments->sum(fn (Enrollment $enrollment) => $enrollment->course->materials->count());

        // Pakai accessor progress_percent yang sama dengan yang dipakai di
        // halaman "Kelas Saya", supaya nilai progress SELALU sinkron di
        // semua tempat dan ikut naik begitu kuis dikerjakan (bukan cuma
        // berjalan karena waktu saja).
        $progressPercent = $userEnrollments->count() > 0
            ? (int) round($userEnrollments->avg(fn (Enrollment $enrollment) => $enrollment->progress_percent))
            : 0;

        $stats = [
            'kursus'    => Course::count(),
            // Total tutor = jumlah nama tutor UNIK yang benar-benar dipakai
            // di paket kursus (kolom tutor_nama), bukan jumlah baris di
            // tabel tutors (yang cuma data teknis/fallback). Dengan begini,
            // angka ini otomatis ikut bertambah setiap admin mengisi nama
            // tutor baru saat menambah/mengedit paket kursus.
            'tutor'     => Course::query()
                ->get()
                ->pluck('tutor_display_name')
                ->filter()
                ->unique()
                ->count(),
            'siswa'     => User::count(),
            'transaksi' => Transaction::count(),
            'materi' => Material::count(),
            'soal' => Question::count(),
            'sertifikat' => Certificate::count(),
        ];

        $popularCourses = Course::withCount('transactions')
            ->with('tutor.user')
            ->orderByDesc('transactions_count')
            ->orderByDesc('created_at')
            ->take(4)
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'nama_kursus' => $course->nama_kursus,
                'kategori' => $course->kategori,
                'harga' => $course->harga,
                'transactions_count' => $course->transactions_count,
                // Selalu pakai nama tutor yang diisi admin di tutor_nama,
                // sama seperti di halaman Paket Belajar & Kelas Saya.
                'tutor_display_name' => $course->tutor_display_name,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'popularCourses' => $popularCourses,
            'learningSummary' => [
                'active_enrollments' => $userEnrollments->where('sudah_selesai', false)->count(),
                'total_enrollments' => $userEnrollments->count(),
                'progress_percent' => $progressPercent,
                'materials_count' => $materialCount,
                'quiz_done_count' => $quizDoneCount,
                'certificates_available_count' => $certificateAvailableCount,
            ],
        ]);
    }
}
