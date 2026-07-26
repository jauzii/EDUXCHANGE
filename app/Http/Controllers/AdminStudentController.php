<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminStudentController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'kategori']);

        $students = User::query()
            ->where('role', 'student')
            ->withCount([
                'enrollments',
                'enrollments as active_enrollments_count' => fn ($query) => $query->where('ends_at', '>', now()),
            ])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, string $status) {
                $operator = $status === 'active' ? '>' : '<=';

                $query->whereHas('enrollments', fn ($query) => $query->where('ends_at', $operator, now()));
            })
            ->when($filters['kategori'] ?? null, function ($query, string $kategori) {
                $query->whereHas('enrollments.course', fn ($query) => $query->where('kategori', $kategori));
            })
            ->orderBy('name')
            ->get()
            ->map(fn (User $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'enrollments_count' => $student->enrollments_count,
                'active_enrollments_count' => $student->active_enrollments_count,
            ]);

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'status' => $filters['status'] ?? '',
                'kategori' => $filters['kategori'] ?? '',
            ],
            'categories' => Course::query()
                ->select('kategori')
                ->distinct()
                ->orderBy('kategori')
                ->pluck('kategori'),
        ]);
    }

    public function show(User $student): Response
    {
        abort_if($student->role !== 'student', 404);

        $student->load([
            'enrollments.course.tutor.user',
            'enrollments.quizAnswers',
        ]);

        $enrollments = $student->enrollments
            ->sortByDesc('created_at')
            ->values()
            ->map(fn (Enrollment $enrollment) => [
                'id' => $enrollment->id,
                'course' => [
                    'nama_kursus' => $enrollment->course->nama_kursus,
                    'kategori' => $enrollment->course->kategori,
                    'tutor' => $enrollment->course->tutor_display_name,
                ],
                'status' => $enrollment->sudah_selesai ? 'expired' : 'active',
                'started_at' => $enrollment->started_at->translatedFormat('d M Y'),
                'ends_at' => $enrollment->ends_at->translatedFormat('d M Y'),
                'sisa_hari' => $enrollment->sisa_hari,
                'score' => $enrollment->score,
                'answered_count' => $enrollment->quizAnswers->count(),
                'certificate_available' => $enrollment->bisa_unduh_sertifikat,
            ]);

        return Inertia::render('Admin/Students/Show', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ],
            'summary' => [
                'total_enrollments' => $enrollments->count(),
                'active_enrollments' => $enrollments->where('status', 'active')->count(),
                'average_score' => (int) round((float) $student->enrollments->whereNotNull('score')->avg('score')),
                'certificates_available' => $enrollments->where('certificate_available', true)->count(),
            ],
            'enrollments' => $enrollments,
        ]);
    }
}
