<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AdminCertificateController extends Controller
{
    /**
     * Tampilkan CONTOH sertifikat (data dummy, tidak disimpan ke
     * database) untuk 1 paket kursus, supaya admin bisa mengecek
     * tampilan/desain sertifikat kapan saja tanpa harus mendaftar,
     * membayar, dan menyelesaikan semua kuis sebagai siswa sungguhan.
     */
    public function preview(Course $course)
    {
        $enrollment = (object) [
            'user' => (object) ['name' => 'Nama Siswa (Contoh)'],
            'course' => $course,
            'started_at' => now()->subDays(30),
            'ends_at' => now(),
            'score' => 95,
        ];

        $certificate = (object) [
            'kode_sertifikat' => 'EDX-CONTOH-PREVIEW',
            'diterbitkan_pada' => now(),
        ];

        $pdf = Pdf::loadView('certificates.pdf', [
            'enrollment' => $enrollment,
            'certificate' => $certificate,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Contoh-Sertifikat-'.Str::slug($course->nama_kursus).'.pdf');
    }
}
