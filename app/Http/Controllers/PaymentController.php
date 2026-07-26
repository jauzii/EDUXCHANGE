<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    /**
     * Daftar metode pembayaran yang bisa dipilih user. Karena belum
     * terhubung ke payment gateway sungguhan, pembayaran disimulasikan.
     */
    public const METODE_PEMBAYARAN = [
        'transfer_bank' => 'Transfer Bank',
        'qris' => 'QRIS',
        'e_wallet' => 'E-Wallet',
    ];

    /**
     * Tampilkan halaman pembayaran untuk sebuah paket/kursus.
     */
    public function show(Course $course): Response|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        $sudahTerdaftar = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('ends_at', '>', now())
            ->exists();

        if ($sudahTerdaftar) {
            return redirect()
                ->route('courses.index')
                ->with('info', 'Kamu sudah terdaftar di kursus ini. Lanjutkan belajar lewat menu "Kelas Saya".');
        }

        $course->load('tutor.user');

        return Inertia::render('Payment/Show', [
            'course' => [
                'id' => $course->id,
                'nama_kursus' => $course->nama_kursus,
                'kategori' => $course->kategori,
                'harga' => $course->harga,
                'deskripsi' => $course->deskripsi,
                'tutor' => [
                    'name' => $course->tutor_display_name,
                ],
            ],
            'paymentMethods' => collect(self::METODE_PEMBAYARAN)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->values(),
        ]);
    }

    /**
     * Proses pembayaran: buat transaksi + enrollment sekaligus (simulasi,
     * karena belum ada payment gateway sungguhan).
     */
    public function process(Request $request, Course $course)
    {
        $user = Auth::user();

        $sudahTerdaftar = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('ends_at', '>', now())
            ->exists();

        if ($sudahTerdaftar) {
            return redirect()
                ->route('courses.index')
                ->with('info', 'Kamu sudah terdaftar di kursus ini. Lanjutkan belajar lewat menu "Kelas Saya".');
        }

        $validated = $request->validate([
            'metode_pembayaran' => ['required', 'string', 'in:'.implode(',', array_keys(self::METODE_PEMBAYARAN))],
        ]);

        $transaction = DB::transaction(function () use ($user, $course, $validated) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'success',
                'metode_pembayaran' => $validated['metode_pembayaran'],
            ]);

            Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'transaction_id' => $transaction->id,
                'started_at' => now(),
                'ends_at' => now()->addDays(30),
            ]);

            return $transaction;
        });

        return redirect()->route('payment.success', $transaction);
    }

    /**
     * Halaman konfirmasi/bukti pembayaran setelah proses berhasil.
     */
    public function success(Transaction $transaction): Response
    {
        abort_unless($transaction->user_id === Auth::id(), 403);

        $transaction->load(['course.tutor.user', 'enrollment']);

        return Inertia::render('Payment/Success', [
            'transaction' => [
                'id' => $transaction->id,
                'status' => $transaction->status,
                'metode_pembayaran' => self::METODE_PEMBAYARAN[$transaction->metode_pembayaran] ?? $transaction->metode_pembayaran,
                'created_at' => $transaction->created_at->translatedFormat('d F Y, H:i'),
                'course' => [
                    'nama_kursus' => $transaction->course->nama_kursus,
                    'harga' => $transaction->course->harga,
                    'tutor' => [
                        'name' => $transaction->course->tutor_display_name,
                    ],
                ],
                'enrollment_id' => $transaction->enrollment?->id,
                'ends_at' => $transaction->enrollment?->ends_at?->translatedFormat('d F Y'),
            ],
        ]);
    }
}
