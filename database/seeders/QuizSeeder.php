<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Material;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

/**
 * Seeder yang menambahkan CONTOH 2 KUIS (modul) ke setiap paket kursus
 * yang SUDAH ADA di database, tanpa mengubah/menghapus data lain (kursus,
 * siswa, transaksi, dll). Aman dijalankan di server production:
 *
 *      php artisan migrate
 *      php artisan db:seed --class=QuizSeeder
 *
 * Struktur per paket kursus:
 * - 2 "Quiz" (modul), masing-masing berisi:
 *   - 1 materi belajar (bacaan singkat)
 *   - 1 set kuis soal berisi 10 soal pilihan ganda (a/b/c/d)
 *
 * Soal di sini CONTOH/placeholder saja (sesuai permintaan), berlaku sama
 * untuk semua paket kursus. Begitu siswa menyelesaikan KEDUA modul ini,
 * sertifikat otomatis bisa diunduh.
 *
 * Idempotent: paket yang sudah punya kuis tidak akan ditambahi lagi kalau
 * seeder ini dijalankan ulang.
 */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $modul = $this->templateModul();

        $courses = Course::query()
            ->whereDoesntHave('quizzes')
            ->oldest()
            ->get();

        if ($courses->isEmpty()) {
            $this->command?->info('Semua paket kursus sudah punya kuis. Tidak ada yang ditambahkan.');

            return;
        }

        foreach ($courses as $course) {
            foreach ($modul as $urutan => $data) {
                $quiz = Quiz::create([
                    'course_id' => $course->id,
                    'judul' => $data['judul_kuis'],
                    'urutan' => $urutan + 1,
                ]);

                Material::create([
                    'course_id' => $course->id,
                    'quiz_id' => $quiz->id,
                    'judul' => $data['materi']['judul'],
                    'konten' => str_replace('{{nama_kursus}}', $course->nama_kursus, $data['materi']['konten']),
                ]);

                foreach ($data['soal'] as $soal) {
                    Question::create([
                        'course_id' => $course->id,
                        'quiz_id' => $quiz->id,
                        'pertanyaan' => $soal['pertanyaan'],
                        'pilihan_a' => $soal['pilihan_a'],
                        'pilihan_b' => $soal['pilihan_b'],
                        'pilihan_c' => $soal['pilihan_c'],
                        'pilihan_d' => $soal['pilihan_d'],
                        'jawaban_benar' => $soal['jawaban_benar'],
                    ]);
                }
            }

            $this->command?->info("2 kuis (masing-masing 1 materi + 10 soal) ditambahkan ke paket: {$course->nama_kursus}.");
        }
    }

    /**
     * 2 modul contoh (materi + 10 soal pilihan ganda per modul).
     * Kontennya generik/placeholder, sesuai permintaan ("hanya untuk
     * contoh"), berlaku sama untuk semua paket kursus.
     */
    private function templateModul(): array
    {
        return [
            [
                'judul_kuis' => 'Kuis 1: Pengenalan Modul',
                'materi' => [
                    'judul' => 'Pengantar Kursus',
                    'konten' => "Selamat datang di kursus \"{{nama_kursus}}\"!\n\nSebelum mengerjakan kuis, luangkan waktu untuk membaca ringkasan materi ini terlebih dahulu. Materi pada tiap modul dirancang sebagai bacaan singkat untuk membantu kamu memahami konsep dasar sebelum diuji lewat soal pilihan ganda.\n\nSetelah selesai membaca, lanjutkan ke bagian \"Kuis soal\" di bawah untuk mengerjakan 10 soal pilihan ganda modul ini.",
                ],
                'soal' => [
                    ['pertanyaan' => 'Ibu kota negara Indonesia adalah?', 'pilihan_a' => 'Jakarta', 'pilihan_b' => 'Bandung', 'pilihan_c' => 'Surabaya', 'pilihan_d' => 'Medan', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Berapa hasil dari 12 x 8?', 'pilihan_a' => '88', 'pilihan_b' => '96', 'pilihan_c' => '108', 'pilihan_d' => '90', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Planet yang dikenal sebagai "Planet Merah" adalah?', 'pilihan_a' => 'Venus', 'pilihan_b' => 'Mars', 'pilihan_c' => 'Jupiter', 'pilihan_d' => 'Saturnus', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Bahasa resmi negara Indonesia adalah?', 'pilihan_a' => 'Bahasa Inggris', 'pilihan_b' => 'Bahasa Indonesia', 'pilihan_c' => 'Bahasa Melayu', 'pilihan_d' => 'Bahasa Belanda', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => '1 jam sama dengan berapa menit?', 'pilihan_a' => '30 menit', 'pilihan_b' => '45 menit', 'pilihan_c' => '60 menit', 'pilihan_d' => '90 menit', 'jawaban_benar' => 'c'],
                    ['pertanyaan' => 'Air membeku pada suhu berapa derajat Celsius?', 'pilihan_a' => '0 derajat', 'pilihan_b' => '10 derajat', 'pilihan_c' => '50 derajat', 'pilihan_d' => '100 derajat', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Lambang kimia untuk air adalah?', 'pilihan_a' => 'O2', 'pilihan_b' => 'CO2', 'pilihan_c' => 'H2O', 'pilihan_d' => 'NaCl', 'jawaban_benar' => 'c'],
                    ['pertanyaan' => 'Benua terbesar di dunia adalah?', 'pilihan_a' => 'Afrika', 'pilihan_b' => 'Asia', 'pilihan_c' => 'Eropa', 'pilihan_d' => 'Australia', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Dalam satu minggu terdapat berapa hari?', 'pilihan_a' => '5 hari', 'pilihan_b' => '6 hari', 'pilihan_c' => '7 hari', 'pilihan_d' => '8 hari', 'jawaban_benar' => 'c'],
                    ['pertanyaan' => 'Warna yang dihasilkan dari campuran warna biru dan kuning adalah?', 'pilihan_a' => 'Hijau', 'pilihan_b' => 'Ungu', 'pilihan_c' => 'Oranye', 'pilihan_d' => 'Merah', 'jawaban_benar' => 'a'],
                ],
            ],
            [
                'judul_kuis' => 'Kuis 2: Pendalaman & Evaluasi',
                'materi' => [
                    'judul' => 'Pendalaman & Evaluasi',
                    'konten' => "Kamu sudah menyelesaikan modul pertama, lanjutkan ke modul kedua!\n\nModul ini membahas bagaimana proses belajar di \"{{nama_kursus}}\" dievaluasi lewat kuis, dan bagaimana sertifikat kelulusan bisa didapatkan. Pahami dulu alurnya lewat bacaan singkat ini sebelum mengerjakan 10 soal pilihan ganda modul kedua.\n\nSetelah modul ke-2 ini selesai (kuis terjawab semua), sertifikat kelulusan kamu untuk paket ini akan langsung bisa diunduh.",
                ],
                'soal' => [
                    ['pertanyaan' => 'Membaca materi sebelum mengerjakan kuis bertujuan untuk?', 'pilihan_a' => 'Membuang waktu', 'pilihan_b' => 'Memahami konsep sebelum diuji', 'pilihan_c' => 'Menambah nilai secara otomatis', 'pilihan_d' => 'Mengganti jawaban benar', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Satu modul pembelajaran pada kursus ini biasanya terdiri dari?', 'pilihan_a' => 'Materi dan kuis', 'pilihan_b' => 'Hanya sertifikat', 'pilihan_c' => 'Hanya riwayat transaksi', 'pilihan_d' => 'Hanya halaman profil', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Setiap soal pilihan ganda pada kuis ini terdiri dari berapa opsi jawaban?', 'pilihan_a' => '2 opsi', 'pilihan_b' => '3 opsi', 'pilihan_c' => '4 opsi', 'pilihan_d' => '5 opsi', 'jawaban_benar' => 'c'],
                    ['pertanyaan' => 'Nilai (skor) sebuah kuis dihitung berdasarkan?', 'pilihan_a' => 'Jumlah soal yang terjawab benar', 'pilihan_b' => 'Lama waktu pengerjaan', 'pilihan_c' => 'Jumlah huruf pada jawaban', 'pilihan_d' => 'Warna tampilan halaman', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Sertifikat kelulusan pada paket kursus ini terbuka setelah?', 'pilihan_a' => 'Mendaftar akun', 'pilihan_b' => 'Menyelesaikan semua kuis checkpoint', 'pilihan_c' => 'Melihat halaman paket kursus', 'pilihan_d' => 'Login untuk pertama kali', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Tujuan utama diadakannya kuis di akhir setiap modul adalah?', 'pilihan_a' => 'Mengukur pemahaman peserta terhadap materi', 'pilihan_b' => 'Mempercantik tampilan halaman', 'pilihan_c' => 'Mengganti nama peserta', 'pilihan_d' => 'Menghapus data lama', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Sebelum menekan tombol submit kuis, sebaiknya peserta?', 'pilihan_a' => 'Membiarkan beberapa soal kosong', 'pilihan_b' => 'Memastikan semua soal sudah dijawab', 'pilihan_c' => 'Menutup halaman begitu saja', 'pilihan_d' => 'Mematikan koneksi internet', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Setelah kuis pertama (modul 1) selesai dikerjakan, langkah berikutnya adalah?', 'pilihan_a' => 'Mengulang kuis pertama terus-menerus', 'pilihan_b' => 'Melanjutkan ke kuis/modul berikutnya', 'pilihan_c' => 'Menghapus akun', 'pilihan_d' => 'Keluar dari aplikasi secara permanen', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Bagian "Materi" pada tiap modul berfungsi sebagai?', 'pilihan_a' => 'Bahan bacaan sebelum mengerjakan kuis', 'pilihan_b' => 'Pengganti sertifikat', 'pilihan_c' => 'Riwayat transaksi pembayaran', 'pilihan_d' => 'Data pribadi pengguna', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Soal-soal pada 2 kuis contoh ini sifatnya?', 'pilihan_a' => 'Data resmi dari tutor yang tidak boleh diubah', 'pilihan_b' => 'Placeholder/contoh yang bisa diganti admin kapan saja', 'pilihan_c' => 'Rahasia dan tidak boleh dilihat siapa pun', 'pilihan_d' => 'Otomatis terhapus setelah 30 hari', 'jawaban_benar' => 'b'],
                ],
            ],
        ];
    }
}
