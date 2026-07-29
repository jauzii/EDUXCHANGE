<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    /**
     * Daftar semua kuis checkpoint (mis. 7 kuis) untuk 1 paket kursus yang
     * sedang diikuti, lengkap dengan status per kuis (sudah/belum
     * dikerjakan beserta skornya). Sertifikat baru terbuka kalau SEMUA
     * kuis di sini sudah selesai.
     */
    public function index(Enrollment $enrollment): Response
    {
        abort_if($enrollment->user_id !== Auth::id(), 403);

        $enrollment->load('quizAttempts');

        $quizzes = $enrollment->course->quizzes()
            ->withCount('questions')
            ->get()
            ->map(function (Quiz $quiz) use ($enrollment) {
                $attempt = $enrollment->quizAttempts->firstWhere('quiz_id', $quiz->id);

                return [
                    'id' => $quiz->id,
                    'judul' => $quiz->judul,
                    'urutan' => $quiz->urutan,
                    'questions_count' => $quiz->questions_count,
                    'sudah_dikerjakan' => (bool) $attempt,
                    'score' => $attempt?->score,
                ];
            })
            ->values();

        return Inertia::render('Quiz/Index', [
            'enrollment' => [
                'id' => $enrollment->id,
                'can_access_content' => ! $enrollment->sudah_selesai,
                'bisa_unduh_sertifikat' => $enrollment->bisa_unduh_sertifikat,
                'kuis_selesai' => $enrollment->kuis_selesai,
                'total_kuis' => $enrollment->total_kuis,
                'course' => [
                    'nama_kursus' => $enrollment->course->nama_kursus,
                    'kategori' => $enrollment->course->kategori,
                ],
            ],
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Tampilkan form 1 kuis checkpoint tertentu (bukan semua soal
     * sekursus sekaligus seperti sebelumnya).
     */
    public function create(Enrollment $enrollment, Quiz $quiz): Response|RedirectResponse
    {
        abort_if($enrollment->user_id !== Auth::id(), 403);
        abort_if($quiz->course_id !== $enrollment->course_id, 404);

        if ($enrollment->sudah_selesai) {
            return redirect()
                ->route('enrollments.show', $enrollment)
                ->with('error', 'Masa akses paket ini sudah habis. Daftar ulang paket untuk mengerjakan kuis lagi.');
        }

        $quiz->load('questions');

        if ($quiz->questions->isEmpty()) {
            return redirect()
                ->route('quiz.index', $enrollment)
                ->with('info', 'Kuis ini belum memiliki soal.');
        }

        // Jawaban yang sudah pernah diisi sebelumnya (kalau ini pengulangan).
        $jawabanSebelumnya = $enrollment->quizAnswers()
            ->whereIn('question_id', $quiz->questions->pluck('id'))
            ->pluck('jawaban_dipilih', 'question_id');

        return Inertia::render('Quiz/Create', [
            'enrollment' => [
                'id' => $enrollment->id,
                'course' => [
                    'nama_kursus' => $enrollment->course->nama_kursus,
                    'kategori' => $enrollment->course->kategori,
                ],
            ],
            'quiz' => [
                'id' => $quiz->id,
                'judul' => $quiz->judul,
                'urutan' => $quiz->urutan,
            ],
            'totalKuis' => $enrollment->total_kuis,
            'questions' => $quiz->questions
                ->values()
                ->map(fn ($question) => [
                    'id' => $question->id,
                    'pertanyaan' => $question->pertanyaan,
                    'options' => [
                        'a' => $question->pilihan_a,
                        'b' => $question->pilihan_b,
                        'c' => $question->pilihan_c,
                        'd' => $question->pilihan_d,
                    ],
                ]),
            'jawabanSebelumnya' => $jawabanSebelumnya,
        ]);
    }

    /**
     * Simpan jawaban 1 kuis checkpoint, hitung skor kuis ini, catat
     * sebagai selesai (quiz_attempts), lalu perbarui rata-rata nilai
     * enrollment. Sertifikat otomatis terbuka begitu checkpoint terakhir
     * (ke-7) tersimpan di sini.
     */
    public function store(Request $request, Enrollment $enrollment, Quiz $quiz): RedirectResponse
    {
        abort_if($enrollment->user_id !== Auth::id(), 403);
        abort_if($quiz->course_id !== $enrollment->course_id, 404);

        if ($enrollment->sudah_selesai) {
            return redirect()
                ->route('enrollments.show', $enrollment)
                ->with('error', 'Masa akses paket ini sudah habis. Daftar ulang paket untuk mengirim jawaban kuis.');
        }

        $quiz->load('questions');
        $questions = $quiz->questions;

        $request->validate([
            'jawaban' => ['required', 'array'],
            'jawaban.*' => ['required', 'in:a,b,c,d'],
        ]);

        $jumlahBenar = 0;

        foreach ($questions as $question) {
            $dipilih = $request->input("jawaban.{$question->id}");

            if (! $dipilih) {
                continue;
            }

            $isBenar = $dipilih === $question->jawaban_benar;
            $jumlahBenar += $isBenar ? 1 : 0;

            $enrollment->quizAnswers()->updateOrCreate(
                ['question_id' => $question->id],
                [
                    'jawaban_dipilih' => $dipilih,
                    'is_benar' => $isBenar,
                ]
            );
        }

        $skorKuisIni = $questions->count() > 0
            ? (int) round(($jumlahBenar / $questions->count()) * 100)
            : 0;

        $enrollment->quizAttempts()->updateOrCreate(
            ['quiz_id' => $quiz->id],
            ['score' => $skorKuisIni]
        );

        $enrollment->recalculateScore();

        $selesaiSemua = $enrollment->fresh()->sudah_menyelesaikan_semua_kuis;

        return redirect()
            ->route('quiz.index', $enrollment)
            ->with('success', $selesaiSemua
                ? "Kuis \"{$quiz->judul}\" selesai dengan skor {$skorKuisIni}. Semua kuis sudah selesai — sertifikat sudah bisa diunduh!"
                : "Kuis \"{$quiz->judul}\" selesai dengan skor {$skorKuisIni}. Lanjutkan ke kuis berikutnya.");
    }
}
