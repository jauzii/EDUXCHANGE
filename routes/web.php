<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $popularCourses = Course::with('tutor.user')->latest()->take(3)->get();

    return view('welcome', compact('popularCourses'));
});

Route::get('/test-inertia', function () {
    return Inertia::render('Test');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:student'])
    ->name('dashboard');

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/{student}', [AdminStudentController::class, 'show'])->name('admin.students.show');

    Route::get('/admin/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/admin/courses/create', [AdminCourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/admin/courses', [AdminCourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/admin/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('admin.courses.edit');
    Route::put('/admin/courses/{course}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/admin/courses/{course}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');
});

Route::middleware('auth')->group(function () {
    Route::middleware('role:student')->group(function () {
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}/checkout', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/courses/{course}/checkout', [PaymentController::class, 'process'])->name('payment.process');
        Route::get('/pembayaran/{transaction}/selesai', [PaymentController::class, 'success'])->name('payment.success');

        Route::get('/kelas-saya', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/kelas-saya/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');

        Route::get('/kelas-saya/{enrollment}/kuis', [QuizController::class, 'index'])->name('quiz.index');
        Route::get('/kelas-saya/{enrollment}/kuis/{quiz}', [QuizController::class, 'create'])->name('quiz.create');
        Route::post('/kelas-saya/{enrollment}/kuis/{quiz}', [QuizController::class, 'store'])->name('quiz.store');

        Route::get('/kelas-saya/{enrollment}/sertifikat', [CertificateController::class, 'download'])->name('certificate.download');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
