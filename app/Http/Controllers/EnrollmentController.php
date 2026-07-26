<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    /**
     * Daftar semua kursus yang sedang/sudah diikuti user yang login ("Kelas Saya").
     */
    public function index(): Response
    {
        $enrollments = Auth::user()
            ->enrollments()
            ->with(['course.tutor.user'])
            ->latest()
            ->get()
            ->map(fn (Enrollment $enrollment) => [
                'id' => $enrollment->id,
                'started_at' => $enrollment->started_at?->toDateString(),
                'ends_at' => $enrollment->ends_at?->toDateString(),
                'ends_at_label' => $enrollment->ends_at?->translatedFormat('d M Y'),
                'sisa_hari' => $enrollment->sisa_hari,
                'sudah_selesai' => $enrollment->sudah_selesai,
                'sudah_mengerjakan_kuis' => $enrollment->sudah_mengerjakan_kuis,
                'bisa_unduh_sertifikat' => $enrollment->bisa_unduh_sertifikat,
                'can_access_content' => ! $enrollment->sudah_selesai,
                'score' => $enrollment->score,
                'progress' => $enrollment->sudah_selesai
                    ? 100
                    : max(12, min(100, (int) round(100 - (($enrollment->sisa_hari / 30) * 100)))),
                'course' => [
                    'id' => $enrollment->course->id,
                    'nama_kursus' => $enrollment->course->nama_kursus,
                    'kategori' => $enrollment->course->kategori,
                    'tutor' => [
                        'name' => $enrollment->course->tutor_display_name,
                    ],
                ],
            ]);

        return Inertia::render('Enrollments/Index', [
            'enrollments' => $enrollments,
        ]);
    }

    /**
     * Halaman detail kursus yang sedang berjalan: materi, info kursus,
     * sisa waktu, nilai, dan akses kuis/sertifikat.
     */
    public function show(Enrollment $enrollment): Response
    {
        abort_if($enrollment->user_id !== Auth::id(), 403);

        $enrollment->load([
            'course.tutor.user',
            'course.materials',
            'course.questions',
        ]);

        $totalDays = $enrollment->started_at->diffInDays($enrollment->ends_at) ?: 30;
        $elapsedDays = min($totalDays, $enrollment->started_at->diffInDays(now()));
        $timeProgress = (int) round(($elapsedDays / $totalDays) * 100);

        return Inertia::render('Enrollments/Show', [
            'enrollment' => [
                'id' => $enrollment->id,
                'started_at' => $enrollment->started_at?->toDateString(),
                'ends_at' => $enrollment->ends_at?->toDateString(),
                'sisa_hari' => $enrollment->sisa_hari,
                'sudah_selesai' => $enrollment->sudah_selesai,
                'sudah_mengerjakan_kuis' => $enrollment->sudah_mengerjakan_kuis,
                'bisa_unduh_sertifikat' => $enrollment->bisa_unduh_sertifikat,
                'score' => $enrollment->score,
                'time_progress' => $timeProgress,
                'course' => [
                    'id' => $enrollment->course->id,
                    'nama_kursus' => $enrollment->course->nama_kursus,
                    'kategori' => $enrollment->course->kategori,
                    'deskripsi' => $enrollment->course->deskripsi,
                    'tutor' => [
                        'name' => $enrollment->course->tutor_display_name,
                    ],
                    'materials' => $enrollment->course->materials
                        ->values()
                        ->map(fn ($material) => [
                            'id' => $material->id,
                            'judul' => $material->judul,
                            'konten' => $material->konten,
                        ]),
                    'questions_count' => $enrollment->course->questions->count(),
                ],
            ],
        ]);
    }
}
