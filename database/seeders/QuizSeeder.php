<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder yang menambahkan CONTOH 7 KUIS CHECKPOINT ke setiap paket kursus
 * yang SUDAH ADA di database (tidak menghapus/mengubah data lain: kursus,
 * siswa, transaksi, dll). Aman dijalankan di server production:
 *
 *      php artisan migrate
 *      php artisan db:seed --class=QuizSeeder
 *
 * Cara kerja:
 * 1. Menyiapkan 7 bank soal contoh (per kategori paket kursus yang umum
 *    dipakai di EDUXCHANGE), masing-masing berisi 7 pertanyaan.
 * 2. Untuk SETIAP paket kursus yang belum punya kuis sama sekali,
 *    dibuatkan 7 baris "Quiz" (Kuis 1 s.d. Kuis 7), masing-masing diisi
 *    1 soal dari bank yang paling cocok dengan kategori/nama paketnya
 *    (fallback ke bank umum kalau tidak ada yang cocok).
 * 3. Begitu siswa menyelesaikan ke-7 kuis ini (lewat halaman "Kelas
 *    Saya" -> "Kerjakan Kuis"), sertifikat otomatis bisa diunduh.
 * 4. Idempotent: paket yang sudah punya kuis tidak akan ditambahi lagi
 *    kalau seeder ini dijalankan ulang.
 */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $bankSoal = $this->bankSoal();

        $courses = Course::query()
            ->whereDoesntHave('quizzes')
            ->oldest()
            ->get();

        if ($courses->isEmpty()) {
            $this->command?->info('Semua paket kursus sudah punya kuis. Tidak ada yang ditambahkan.');

            return;
        }

        foreach ($courses as $course) {
            $template = $this->cocokkanBankSoal($course, $bankSoal);

            foreach ($template['soal'] as $urutan => $soal) {
                $quiz = Quiz::create([
                    'course_id' => $course->id,
                    'judul' => $soal['judul_kuis'],
                    'urutan' => $urutan + 1,
                ]);

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

            $this->command?->info("7 kuis \"{$template['label']}\" ditambahkan ke paket: {$course->nama_kursus}.");
        }
    }

    /**
     * Cari bank soal yang paling cocok untuk 1 paket kursus, berdasarkan
     * kata kunci di kategori atau nama kursusnya. Fallback ke bank umum
     * kalau tidak ada kecocokan.
     */
    private function cocokkanBankSoal(Course $course, array $bankSoal): array
    {
        $teks = Str::lower($course->kategori.' '.$course->nama_kursus);

        foreach ($bankSoal as $template) {
            foreach ($template['kata_kunci'] as $kataKunci) {
                if (Str::contains($teks, $kataKunci)) {
                    return $template;
                }
            }
        }

        return collect($bankSoal)->firstWhere('label', 'Keterampilan Belajar Online');
    }

    /**
     * 7 contoh bank soal (7 pertanyaan per bank = 1 pertanyaan per kuis
     * checkpoint), masing-masing mewakili 1 kategori paket kursus yang
     * umum di EDUXCHANGE.
     */
    private function bankSoal(): array
    {
        return [
            [
                'label' => 'Dasar Algoritma & Pemrograman',
                'kata_kunci' => ['program', 'algoritma', 'data', 'coding', 'developer'],
                'soal' => [
                    ['judul_kuis' => 'Kuis 1: Struktur Data', 'pertanyaan' => 'Struktur data apa yang bekerja dengan prinsip "masuk terakhir, keluar pertama" (LIFO)?', 'pilihan_a' => 'Queue', 'pilihan_b' => 'Stack', 'pilihan_c' => 'Array', 'pilihan_d' => 'Linked List', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 2: Algoritma Sorting', 'pertanyaan' => 'Algoritma pengurutan (sorting) yang membandingkan dan menukar elemen bersebelahan secara berulang disebut?', 'pilihan_a' => 'Bubble Sort', 'pilihan_b' => 'Binary Search', 'pilihan_c' => 'Hashing', 'pilihan_d' => 'Recursion', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 3: Kompleksitas Algoritma', 'pertanyaan' => 'Kompleksitas waktu pencarian pada array terurut menggunakan binary search adalah?', 'pilihan_a' => 'O(n)', 'pilihan_b' => 'O(n^2)', 'pilihan_c' => 'O(log n)', 'pilihan_d' => 'O(1)', 'jawaban_benar' => 'c'],
                    ['judul_kuis' => 'Kuis 4: Variabel & Konstanta', 'pertanyaan' => 'Variabel yang nilainya tidak dapat diubah setelah didefinisikan disebut?', 'pilihan_a' => 'Constant', 'pilihan_b' => 'Pointer', 'pilihan_c' => 'Array', 'pilihan_d' => 'Loop', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 5: Rekursi', 'pertanyaan' => 'Proses memanggil fungsi yang memanggil dirinya sendiri disebut?', 'pilihan_a' => 'Iterasi', 'pilihan_b' => 'Rekursi', 'pilihan_c' => 'Deklarasi', 'pilihan_d' => 'Kompilasi', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 6: Perulangan', 'pertanyaan' => 'Perintah untuk mengulang blok kode selama kondisi tertentu terpenuhi disebut?', 'pilihan_a' => 'Loop', 'pilihan_b' => 'Class', 'pilihan_c' => 'Interface', 'pilihan_d' => 'Enum', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 7: Dasar Web', 'pertanyaan' => 'Bahasa markup yang dipakai untuk menyusun struktur halaman web adalah?', 'pilihan_a' => 'HTML', 'pilihan_b' => 'CSS', 'pilihan_c' => 'SQL', 'pilihan_d' => 'JSON', 'jawaban_benar' => 'a'],
                ],
            ],
            [
                'label' => 'Strategi Marketing & SEO',
                'kata_kunci' => ['marketing', 'seo', 'pemasaran', 'digital'],
                'soal' => [
                    ['judul_kuis' => 'Kuis 1: Dasar SEO', 'pertanyaan' => 'Apa kepanjangan dari SEO?', 'pilihan_a' => 'Search Engine Optimization', 'pilihan_b' => 'Site Engagement Overview', 'pilihan_c' => 'Search Engagement Object', 'pilihan_d' => 'Sales Effort Online', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 2: Riset Kata Kunci', 'pertanyaan' => 'Proses riset untuk menemukan istilah pencarian yang relevan dengan audiens disebut?', 'pilihan_a' => 'Keyword research', 'pilihan_b' => 'Link building', 'pilihan_c' => 'A/B testing', 'pilihan_d' => 'Retargeting', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 3: On-page vs Off-page', 'pertanyaan' => 'Optimasi SEO yang dilakukan di dalam website sendiri (judul, konten, struktur URL) disebut?', 'pilihan_a' => 'Off-page SEO', 'pilihan_b' => 'On-page SEO', 'pilihan_c' => 'Technical ads', 'pilihan_d' => 'Cold email', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 4: Metrik Website', 'pertanyaan' => 'Metrik yang mengukur persentase pengunjung yang langsung pergi setelah membuka 1 halaman disebut?', 'pilihan_a' => 'Bounce rate', 'pilihan_b' => 'Conversion rate', 'pilihan_c' => 'Click rate', 'pilihan_d' => 'Open rate', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 5: Influencer Marketing', 'pertanyaan' => 'Strategi pemasaran melalui kerja sama dengan tokoh berpengaruh di media sosial disebut?', 'pilihan_a' => 'Email marketing', 'pilihan_b' => 'Influencer marketing', 'pilihan_c' => 'Cold calling', 'pilihan_d' => 'Direct mail', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 6: Iklan Berbayar', 'pertanyaan' => 'Konten berbayar yang ditampilkan di bagian atas hasil pencarian disebut?', 'pilihan_a' => 'Iklan PPC/SEM', 'pilihan_b' => 'Organic post', 'pilihan_c' => 'Backlink', 'pilihan_d' => 'Newsletter', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 7: Email Marketing', 'pertanyaan' => 'Email yang dikirim rutin ke pelanggan berisi promo atau info terbaru disebut?', 'pilihan_a' => 'Cold calling', 'pilihan_b' => 'Email marketing', 'pilihan_c' => 'Billboard', 'pilihan_d' => 'Radio ads', 'jawaban_benar' => 'b'],
                ],
            ],
            [
                'label' => 'Dasar UI/UX & Desain',
                'kata_kunci' => ['desain', 'design', 'ui', 'ux', 'grafis'],
                'soal' => [
                    ['judul_kuis' => 'Kuis 1: Wireframe', 'pertanyaan' => 'Sketsa kasar tata letak antarmuka tanpa detail visual seperti warna disebut?', 'pilihan_a' => 'Prototype', 'pilihan_b' => 'Wireframe', 'pilihan_c' => 'Mockup', 'pilihan_d' => 'Storyboard', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 2: Prinsip Desain', 'pertanyaan' => 'Prinsip desain yang menekankan konsistensi jarak antar elemen disebut?', 'pilihan_a' => 'Kontras', 'pilihan_b' => 'Spacing/whitespace', 'pilihan_c' => 'Gradasi', 'pilihan_d' => 'Skala', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 3: UX vs UI', 'pertanyaan' => 'UX (User Experience) paling berfokus pada?', 'pilihan_a' => 'Warna dan tipografi', 'pilihan_b' => 'Pengalaman pengguna secara keseluruhan', 'pilihan_c' => 'Bahasa pemrograman', 'pilihan_d' => 'Ukuran file gambar', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 4: Pendekatan Desain', 'pertanyaan' => 'Pendekatan desain yang menempatkan kebutuhan pengguna sebagai prioritas utama disebut?', 'pilihan_a' => 'Server-centered design', 'pilihan_b' => 'User-centered design', 'pilihan_c' => 'Data-first design', 'pilihan_d' => 'Code-first design', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 5: Teori Warna', 'pertanyaan' => 'Warna yang bertolak belakang dalam roda warna (color wheel) disebut?', 'pilihan_a' => 'Warna analog', 'pilihan_b' => 'Warna komplementer', 'pilihan_c' => 'Warna monokrom', 'pilihan_d' => 'Warna pastel', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 6: Tipografi', 'pertanyaan' => 'Jarak antar huruf dalam tipografi disebut?', 'pilihan_a' => 'Kerning', 'pilihan_b' => 'Padding', 'pilihan_c' => 'Margin', 'pilihan_d' => 'Grid', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 7: Grid System', 'pertanyaan' => 'Grid system dipakai dalam desain untuk?', 'pilihan_a' => 'Menyusun tata letak yang konsisten', 'pilihan_b' => 'Menyimpan file', 'pilihan_c' => 'Mengompres gambar', 'pilihan_d' => 'Mengubah warna', 'jawaban_benar' => 'a'],
                ],
            ],
            [
                'label' => 'Pendidikan Kewarganegaraan',
                'kata_kunci' => ['kewarganegaraan', 'pancasila', 'ppkn', 'negara'],
                'soal' => [
                    ['judul_kuis' => 'Kuis 1: Sejarah Pancasila', 'pertanyaan' => 'Pancasila sebagai dasar negara Indonesia disahkan pada tanggal?', 'pilihan_a' => '17 Agustus 1945', 'pilihan_b' => '18 Agustus 1945', 'pilihan_c' => '1 Juni 1945', 'pilihan_d' => '28 Oktober 1928', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 2: Sila Pancasila', 'pertanyaan' => 'Sila keberapa dalam Pancasila yang berbunyi "Persatuan Indonesia"?', 'pilihan_a' => 'Sila ke-2', 'pilihan_b' => 'Sila ke-3', 'pilihan_c' => 'Sila ke-4', 'pilihan_d' => 'Sila ke-5', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 3: Konstitusi', 'pertanyaan' => 'Undang-Undang Dasar yang menjadi konstitusi tertulis Indonesia adalah?', 'pilihan_a' => 'UUD 1945', 'pilihan_b' => 'UU ITE', 'pilihan_c' => 'UU Otonomi Daerah', 'pilihan_d' => 'UU Ketenagakerjaan', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 4: Lembaga Negara', 'pertanyaan' => 'Lembaga yang memiliki kewenangan menguji undang-undang terhadap UUD 1945 adalah?', 'pilihan_a' => 'Mahkamah Konstitusi', 'pilihan_b' => 'Kepolisian', 'pilihan_c' => 'DPRD', 'pilihan_d' => 'KPU', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 5: Semboyan Negara', 'pertanyaan' => 'Semboyan negara Indonesia yang berarti "berbeda-beda tetapi tetap satu" adalah?', 'pilihan_a' => 'Tut Wuri Handayani', 'pilihan_b' => 'Bhinneka Tunggal Ika', 'pilihan_c' => 'Garuda Pancasila', 'pilihan_d' => 'Ing Ngarso Sung Tuladha', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 6: Hari Lahir Pancasila', 'pertanyaan' => 'Hari Lahir Pancasila diperingati setiap tanggal?', 'pilihan_a' => '1 Juni', 'pilihan_b' => '17 Agustus', 'pilihan_c' => '28 Oktober', 'pilihan_d' => '10 November', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 7: Penyelenggara Pemilu', 'pertanyaan' => 'Lembaga yang bertugas menyelenggarakan pemilu di Indonesia adalah?', 'pilihan_a' => 'KPU', 'pilihan_b' => 'MK', 'pilihan_c' => 'BPK', 'pilihan_d' => 'DPD', 'jawaban_benar' => 'a'],
                ],
            ],
            [
                'label' => 'Dasar Kewirausahaan & Bisnis',
                'kata_kunci' => ['bisnis', 'wirausaha', 'usaha', 'entrepreneur'],
                'soal' => [
                    ['judul_kuis' => 'Kuis 1: Rencana Bisnis', 'pertanyaan' => 'Dokumen yang merangkum rencana bisnis secara keseluruhan disebut?', 'pilihan_a' => 'Business plan', 'pilihan_b' => 'Invoice', 'pilihan_c' => 'Purchase order', 'pilihan_d' => 'Payslip', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 2: Analisis SWOT', 'pertanyaan' => 'Analisis kekuatan, kelemahan, peluang, dan ancaman suatu bisnis disebut?', 'pilihan_a' => 'SWOT', 'pilihan_b' => 'ROI', 'pilihan_c' => 'KPI', 'pilihan_d' => 'CRM', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 3: Laba Rugi', 'pertanyaan' => 'Selisih antara pendapatan dan biaya dalam suatu usaha disebut?', 'pilihan_a' => 'Modal', 'pilihan_b' => 'Laba/rugi', 'pilihan_c' => 'Aset', 'pilihan_d' => 'Utang', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 4: Target Pasar', 'pertanyaan' => 'Kelompok orang yang menjadi sasaran penjualan produk disebut?', 'pilihan_a' => 'Supplier', 'pilihan_b' => 'Target pasar', 'pilihan_c' => 'Kompetitor', 'pilihan_d' => 'Distributor', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 5: Sumber Modal', 'pertanyaan' => 'Modal yang berasal dari kepemilikan pribadi pemilik usaha disebut?', 'pilihan_a' => 'Modal asing', 'pilihan_b' => 'Modal sendiri', 'pilihan_c' => 'Modal pinjaman', 'pilihan_d' => 'Modal ventura', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 6: Penjualan Langsung', 'pertanyaan' => 'Kegiatan menjajakan produk secara langsung ke calon pembeli disebut?', 'pilihan_a' => 'Direct selling', 'pilihan_b' => 'Outsourcing', 'pilihan_c' => 'Auditing', 'pilihan_d' => 'Budgeting', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 7: Bukti Transaksi', 'pertanyaan' => 'Dokumen bukti transaksi penjualan kepada pelanggan disebut?', 'pilihan_a' => 'Invoice/nota', 'pilihan_b' => 'CV', 'pilihan_c' => 'Proposal', 'pilihan_d' => 'Kontrak kerja', 'jawaban_benar' => 'a'],
                ],
            ],
            [
                'label' => 'Bahasa Inggris Dasar',
                'kata_kunci' => ['bahasa inggris', 'english', 'toefl', 'grammar'],
                'soal' => [
                    ['judul_kuis' => 'Kuis 1: Past Tense', 'pertanyaan' => 'Bentuk lampau (past tense) dari kata kerja "go" adalah?', 'pilihan_a' => 'Goed', 'pilihan_b' => 'Went', 'pilihan_c' => 'Gone', 'pilihan_d' => 'Going', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 2: To Be', 'pertanyaan' => 'Kalimat "She ___ a student" yang tepat menggunakan kata kerja bantu?', 'pilihan_a' => 'is', 'pilihan_b' => 'are', 'pilihan_c' => 'am', 'pilihan_d' => 'be', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 3: Jenis Kata', 'pertanyaan' => 'Kata yang berfungsi menggantikan kata benda disebut?', 'pilihan_a' => 'Verb', 'pilihan_b' => 'Adjective', 'pilihan_c' => 'Pronoun', 'pilihan_d' => 'Adverb', 'jawaban_benar' => 'c'],
                    ['judul_kuis' => 'Kuis 4: Antonim', 'pertanyaan' => 'Antonim (lawan kata) dari "difficult" adalah?', 'pilihan_a' => 'Hard', 'pilihan_b' => 'Easy', 'pilihan_c' => 'Complex', 'pilihan_d' => 'Tough', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 5: Future Tense', 'pertanyaan' => 'Struktur kalimat "Subject + will + verb1" digunakan untuk tenses?', 'pilihan_a' => 'Present tense', 'pilihan_b' => 'Past tense', 'pilihan_c' => 'Future tense', 'pilihan_d' => 'Perfect tense', 'jawaban_benar' => 'c'],
                    ['judul_kuis' => 'Kuis 6: Adjective', 'pertanyaan' => 'Kata "beautiful" termasuk jenis kata?', 'pilihan_a' => 'Verb', 'pilihan_b' => 'Adjective', 'pilihan_c' => 'Noun', 'pilihan_d' => 'Adverb', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 7: Kalimat Tanya', 'pertanyaan' => 'Bentuk kalimat tanya yang benar untuk "You are a student" adalah?', 'pilihan_a' => 'You are a student?', 'pilihan_b' => 'Are you a student?', 'pilihan_c' => 'Is you a student?', 'pilihan_d' => 'Do you a student?', 'jawaban_benar' => 'b'],
                ],
            ],
            [
                'label' => 'Keterampilan Belajar Online',
                'kata_kunci' => [],
                'soal' => [
                    ['judul_kuis' => 'Kuis 1: Manajemen Waktu', 'pertanyaan' => 'Membuat jadwal belajar yang teratur setiap hari termasuk contoh dari?', 'pilihan_a' => 'Manajemen waktu', 'pilihan_b' => 'Manajemen keuangan', 'pilihan_c' => 'Manajemen risiko', 'pilihan_d' => 'Manajemen proyek', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 2: Mencatat Ulang', 'pertanyaan' => 'Mencatat ulang materi dengan bahasa sendiri setelah belajar bertujuan untuk?', 'pilihan_a' => 'Menghabiskan waktu', 'pilihan_b' => 'Memperkuat pemahaman/ingatan', 'pilihan_c' => 'Menambah nilai tugas', 'pilihan_d' => 'Mengurangi fokus', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 3: Istirahat Belajar', 'pertanyaan' => 'Istirahat singkat di sela sesi belajar (misalnya teknik Pomodoro) berguna untuk?', 'pilihan_a' => 'Menjaga fokus dan mencegah kelelahan', 'pilihan_b' => 'Memperlambat progres belajar', 'pilihan_c' => 'Menambah beban tugas', 'pilihan_d' => 'Mengurangi motivasi', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 4: Target Belajar', 'pertanyaan' => 'Menetapkan target belajar yang spesifik dan terukur termasuk prinsip?', 'pilihan_a' => 'SWOT', 'pilihan_b' => 'SMART goal', 'pilihan_c' => 'SEO', 'pilihan_d' => 'ATM', 'jawaban_benar' => 'b'],
                    ['judul_kuis' => 'Kuis 5: Manfaat Kuis', 'pertanyaan' => 'Mengerjakan kuis di akhir materi berguna untuk?', 'pilihan_a' => 'Mengukur pemahaman terhadap materi', 'pilihan_b' => 'Mengganti materi lama', 'pilihan_c' => 'Menghapus riwayat belajar', 'pilihan_d' => 'Menambah durasi akses', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 6: Konsolidasi Ingatan', 'pertanyaan' => 'Membaca ulang catatan sebelum tidur membantu proses apa pada otak?', 'pilihan_a' => 'Konsolidasi memori/ingatan', 'pilihan_b' => 'Menghapus ingatan', 'pilihan_c' => 'Menambah kantuk saja', 'pilihan_d' => 'Mengurangi fokus', 'jawaban_benar' => 'a'],
                    ['judul_kuis' => 'Kuis 7: Syarat Sertifikat', 'pertanyaan' => 'Menyelesaikan semua kuis checkpoint sampai tuntas adalah syarat untuk?', 'pilihan_a' => 'Menghapus akun', 'pilihan_b' => 'Membuka sertifikat kelulusan', 'pilihan_c' => 'Mengurangi harga paket', 'pilihan_d' => 'Memperpanjang masa akses otomatis', 'jawaban_benar' => 'b'],
                ],
            ],
        ];
    }
}
