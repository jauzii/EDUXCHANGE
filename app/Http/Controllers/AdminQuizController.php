<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminQuizController extends Controller
{
    /**
     * Daftar semua modul (1 modul = 1 materi + beberapa soal pilihan
     * ganda) milik 1 paket kursus tertentu.
     */
    public function index(Course $course): Response
    {
        $quizzes = $course->quizzes()
            ->withCount('questions')
            ->with('materials')
            ->get()
            ->map(fn (Quiz $quiz) => [
                'id' => $quiz->id,
                'judul' => $quiz->judul,
                'urutan' => $quiz->urutan,
                'questions_count' => $quiz->questions_count,
                'punya_materi' => $quiz->materials->isNotEmpty(),
            ]);

        return Inertia::render('Admin/Quizzes/Index', [
            'course' => [
                'id' => $course->id,
                'nama_kursus' => $course->nama_kursus,
            ],
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Form tambah modul baru (materi + soal) untuk paket kursus ini.
     */
    public function create(Course $course): Response
    {
        return Inertia::render('Admin/Quizzes/Create', [
            'course' => [
                'id' => $course->id,
                'nama_kursus' => $course->nama_kursus,
            ],
            'nextUrutan' => (int) ($course->quizzes()->max('urutan') ?? 0) + 1,
        ]);
    }

    /**
     * Simpan modul baru (quiz + materi + soal) untuk paket kursus ini.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $this->validated($request);

        static::persistModule($course, $data);

        return redirect()->route('admin.courses.quizzes.index', $course)
            ->with('success', "Modul \"{$data['judul']}\" berhasil ditambahkan.");
    }

    /**
     * Form edit modul (materi + soal) yang sudah ada.
     */
    public function edit(Course $course, Quiz $quiz): Response
    {
        abort_if($quiz->course_id !== $course->id, 404);

        $quiz->load(['materials', 'questions']);
        $materi = $quiz->materials->first();

        return Inertia::render('Admin/Quizzes/Edit', [
            'course' => [
                'id' => $course->id,
                'nama_kursus' => $course->nama_kursus,
            ],
            'quiz' => [
                'id' => $quiz->id,
                'judul' => $quiz->judul,
                'urutan' => $quiz->urutan,
                'konten' => $materi?->konten ?? '',
                'soal' => $quiz->questions->values()->map(fn (Question $question) => [
                    'pertanyaan' => $question->pertanyaan,
                    'pilihan_a' => $question->pilihan_a,
                    'pilihan_b' => $question->pilihan_b,
                    'pilihan_c' => $question->pilihan_c,
                    'pilihan_d' => $question->pilihan_d,
                    'jawaban_benar' => $question->jawaban_benar,
                ]),
            ],
        ]);
    }

    /**
     * Perbarui modul (quiz + materi + soal). Soal lama diganti total
     * dengan soal baru dari form (paling sederhana & aman dibanding
     * sinkronisasi baris satu-satu).
     */
    public function update(Request $request, Course $course, Quiz $quiz): RedirectResponse
    {
        abort_if($quiz->course_id !== $course->id, 404);

        $data = $this->validated($request);

        DB::transaction(function () use ($course, $quiz, $data) {
            $quiz->update([
                'judul' => $data['judul'],
                'urutan' => $data['urutan'],
            ]);

            $materi = $quiz->materials()->first();

            if ($materi) {
                $materi->update([
                    'judul' => $data['judul'],
                    'konten' => $data['konten'],
                ]);
            } else {
                Material::create([
                    'course_id' => $course->id,
                    'quiz_id' => $quiz->id,
                    'judul' => $data['judul'],
                    'konten' => $data['konten'],
                ]);
            }

            $quiz->questions()->delete();

            foreach ($data['soal'] as $soal) {
                Question::create([
                    'course_id' => $course->id,
                    'quiz_id' => $quiz->id,
                    ...$soal,
                ]);
            }
        });

        return redirect()->route('admin.courses.quizzes.index', $course)
            ->with('success', "Modul \"{$data['judul']}\" berhasil diperbarui.");
    }

    /**
     * Hapus 1 modul beserta materi & soal di dalamnya. Siswa yang sudah
     * pernah mengerjakan modul ini (quiz_attempts) ikut terhapus otomatis
     * lewat cascade di database, sehingga progress sertifikat mereka
     * dihitung ulang berdasarkan modul yang tersisa.
     */
    public function destroy(Course $course, Quiz $quiz): RedirectResponse
    {
        abort_if($quiz->course_id !== $course->id, 404);

        DB::transaction(function () use ($quiz) {
            $quiz->materials()->delete();
            $quiz->questions()->delete();
            $quiz->delete();
        });

        return redirect()->route('admin.courses.quizzes.index', $course)
            ->with('success', 'Modul berhasil dihapus.');
    }

    /**
     * Validasi input form tambah/edit modul. 1 field "judul" dipakai
     * sekaligus sebagai judul kuis (Quiz::judul) dan judul materi
     * (Material::judul) supaya form tidak membingungkan admin dengan 2
     * kolom judul yang mirip.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
            'konten' => ['required', 'string'],
            'soal' => ['required', 'array', 'min:1'],
            'soal.*.pertanyaan' => ['required', 'string'],
            'soal.*.pilihan_a' => ['required', 'string', 'max:255'],
            'soal.*.pilihan_b' => ['required', 'string', 'max:255'],
            'soal.*.pilihan_c' => ['required', 'string', 'max:255'],
            'soal.*.pilihan_d' => ['required', 'string', 'max:255'],
            'soal.*.jawaban_benar' => ['required', 'in:a,b,c,d'],
        ]);
    }

    /**
     * Buat 1 modul (quiz + materi + soal) baru untuk paket kursus.
     * Dipakai oleh store() di sini, dan juga dipanggil langsung dari
     * AdminCourseController saat admin mengisi materi & soal contoh
     * langsung di form tambah paket kursus (supaya tidak perlu 2 kali
     * submit terpisah).
     */
    public static function persistModule(Course $course, array $data): Quiz
    {
        return DB::transaction(function () use ($course, $data) {
            $quiz = Quiz::create([
                'course_id' => $course->id,
                'judul' => $data['judul'],
                'urutan' => $data['urutan'],
            ]);

            Material::create([
                'course_id' => $course->id,
                'quiz_id' => $quiz->id,
                'judul' => $data['judul'],
                'konten' => $data['konten'],
            ]);

            foreach ($data['soal'] as $soal) {
                Question::create([
                    'course_id' => $course->id,
                    'quiz_id' => $quiz->id,
                    ...$soal,
                ]);
            }

            return $quiz;
        });
    }
}
