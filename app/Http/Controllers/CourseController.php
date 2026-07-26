<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * Tampilkan daftar seluruh kursus beserta nama tutornya, dan tandai
     * kursus mana saja yang sudah diikuti oleh user yang login.
     */
    public function index(): Response
    {
        $courses = Course::with('tutor.user')
            ->latest()
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'nama_kursus' => $course->nama_kursus,
                'kategori' => $course->kategori,
                'harga' => $course->harga,
                'deskripsi' => $course->deskripsi,
                'tutor' => [
                    'name' => $course->tutor_display_name,
                ],
            ]);

        $enrolledCourseIds = Auth::user()
            ->enrollments()
            ->where('ends_at', '>', now())
            ->pluck('course_id')
            ->toArray();

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }
}
