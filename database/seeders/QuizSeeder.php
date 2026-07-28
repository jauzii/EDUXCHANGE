<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder khusus untuk menambahkan 7 contoh kuis (paket soal) ke paket
 * kursus yang SUDAH ADA di database, tanpa mengubah/menghapus data lain
 * (kursus, siswa, transaksi, dll). Aman dijalankan di server production:
 *
 *      php artisan db:seed --class=QuizSeeder
 *
 * Cara kerja:
 * 1. Menyiapkan 7 bank soal contoh (5 soal per bank), masing-masing untuk
 *    1 kategori paket kursus yang umum dipakai di EDUXCHANGE.
 * 2. Mengambil paket kursus yang BELUM punya soal kuis sama sekali,
 *    diurutkan dari yang paling lama dibuat, maksimal 7 paket.
 * 3. Untuk tiap paket, dicocokkan bank soal berdasarkan kategori/nama
 *    paketnya. Kalau tidak ada yang cocok, dipakai bank soal umum
 *    ("Keterampilan Belajar Online") sebagai fallback.
 * 4. Idempotent: kalau seeder ini dijalankan ulang, paket yang sudah
 *    punya soal tidak akan ditambahi/didobel lagi.
 */
class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $bankSoal = $this->bankSoal();

        $courses = Course::query()
            ->whereDoesntHave('questions')
            ->oldest()
            ->take(7)
            ->get();

        if ($courses->isEmpty()) {
            $this->command?->info('Tidak ada paket kursus tanpa soal kuis. Tidak ada yang ditambahkan.');

            return;
        }

        foreach ($courses as $course) {
            $template = $this->cocokkanBankSoal($course, $bankSoal);

            $rows = collect($template['soal'])->map(fn (array $soal) => [
                'course_id' => $course->id,
                'pertanyaan' => $soal['pertanyaan'],
                'pilihan_a' => $soal['pilihan_a'],
                'pilihan_b' => $soal['pilihan_b'],
                'pilihan_c' => $soal['pilihan_c'],
                'pilihan_d' => $soal['pilihan_d'],
                'jawaban_benar' => $soal['jawaban_benar'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            Question::insert($rows);

            $this->command?->info("Kuis \"{$template['label']}\" ditambahkan ke paket: {$course->nama_kursus} (5 soal).");
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
     * 7 contoh bank soal (5 soal per bank = 35 soal total), masing-masing
     * mewakili 1 kategori paket kursus yang umum di EDUXCHANGE.
     */
    private function bankSoal(): array
    {
        return [
            [
                'label' => 'Dasar Algoritma & Pemrograman',
                'kata_kunci' => ['program', 'algoritma', 'data', 'coding', 'developer'],
                'soal' => [
                    ['pertanyaan' => 'Struktur data apa yang bekerja dengan prinsip "masuk terakhir, keluar pertama" (LIFO)?', 'pilihan_a' => 'Queue', 'pilihan_b' => 'Stack', 'pilihan_c' => 'Array', 'pilihan_d' => 'Linked List', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Algoritma pengurutan (sorting) yang membandingkan dan menukar elemen bersebelahan secara berulang disebut?', 'pilihan_a' => 'Bubble Sort', 'pilihan_b' => 'Binary Search', 'pilihan_c' => 'Hashing', 'pilihan_d' => 'Recursion', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Kompleksitas waktu pencarian pada array terurut menggunakan binary search adalah?', 'pilihan_a' => 'O(n)', 'pilihan_b' => 'O(n^2)', 'pilihan_c' => 'O(log n)', 'pilihan_d' => 'O(1)', 'jawaban_benar' => 'c'],
                    ['pertanyaan' => 'Variabel yang nilainya tidak dapat diubah setelah didefinisikan disebut?', 'pilihan_a' => 'Constant', 'pilihan_b' => 'Pointer', 'pilihan_c' => 'Array', 'pilihan_d' => 'Loop', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Proses memanggil fungsi yang memanggil dirinya sendiri disebut?', 'pilihan_a' => 'Iterasi', 'pilihan_b' => 'Rekursi', 'pilihan_c' => 'Deklarasi', 'pilihan_d' => 'Kompilasi', 'jawaban_benar' => 'b'],
                ],
            ],
            [
                'label' => 'Strategi Marketing & SEO',
                'kata_kunci' => ['marketing', 'seo', 'pemasaran', 'digital'],
                'soal' => [
                    ['pertanyaan' => 'Apa kepanjangan dari SEO?', 'pilihan_a' => 'Search Engine Optimization', 'pilihan_b' => 'Site Engagement Overview', 'pilihan_c' => 'Search Engagement Object', 'pilihan_d' => 'Sales Effort Online', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Proses riset untuk menemukan istilah pencarian yang relevan dengan audiens disebut?', 'pilihan_a' => 'Keyword research', 'pilihan_b' => 'Link building', 'pilihan_c' => 'A/B testing', 'pilihan_d' => 'Retargeting', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Optimasi SEO yang dilakukan di dalam website sendiri (judul, konten, struktur URL) disebut?', 'pilihan_a' => 'Off-page SEO', 'pilihan_b' => 'On-page SEO', 'pilihan_c' => 'Technical ads', 'pilihan_d' => 'Cold email', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Metrik yang mengukur persentase pengunjung yang langsung pergi setelah membuka 1 halaman disebut?', 'pilihan_a' => 'Bounce rate', 'pilihan_b' => 'Conversion rate', 'pilihan_c' => 'Click rate', 'pilihan_d' => 'Open rate', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Strategi pemasaran melalui kerja sama dengan tokoh berpengaruh di media sosial disebut?', 'pilihan_a' => 'Email marketing', 'pilihan_b' => 'Influencer marketing', 'pilihan_c' => 'Cold calling', 'pilihan_d' => 'Direct mail', 'jawaban_benar' => 'b'],
                ],
            ],
            [
                'label' => 'Dasar UI/UX & Desain',
                'kata_kunci' => ['desain', 'design', 'ui', 'ux', 'grafis'],
                'soal' => [
                    ['pertanyaan' => 'Sketsa kasar tata letak antarmuka tanpa detail visual seperti warna disebut?', 'pilihan_a' => 'Prototype', 'pilihan_b' => 'Wireframe', 'pilihan_c' => 'Mockup', 'pilihan_d' => 'Storyboard', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Prinsip desain yang menekankan konsistensi jarak antar elemen disebut?', 'pilihan_a' => 'Kontras', 'pilihan_b' => 'Spacing/whitespace', 'pilihan_c' => 'Gradasi', 'pilihan_d' => 'Skala', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'UX (User Experience) paling berfokus pada?', 'pilihan_a' => 'Warna dan tipografi', 'pilihan_b' => 'Pengalaman pengguna secara keseluruhan', 'pilihan_c' => 'Bahasa pemrograman', 'pilihan_d' => 'Ukuran file gambar', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Pendekatan desain yang menempatkan kebutuhan pengguna sebagai prioritas utama disebut?', 'pilihan_a' => 'Server-centered design', 'pilihan_b' => 'User-centered design', 'pilihan_c' => 'Data-first design', 'pilihan_d' => 'Code-first design', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Warna yang bertolak belakang dalam roda warna (color wheel) disebut?', 'pilihan_a' => 'Warna analog', 'pilihan_b' => 'Warna komplementer', 'pilihan_c' => 'Warna monokrom', 'pilihan_d' => 'Warna pastel', 'jawaban_benar' => 'b'],
                ],
            ],
            [
                'label' => 'Pendidikan Kewarganegaraan',
                'kata_kunci' => ['kewarganegaraan', 'pancasila', 'ppkn', 'negara'],
                'soal' => [
                    ['pertanyaan' => 'Pancasila sebagai dasar negara Indonesia disahkan pada tanggal?', 'pilihan_a' => '17 Agustus 1945', 'pilihan_b' => '18 Agustus 1945', 'pilihan_c' => '1 Juni 1945', 'pilihan_d' => '28 Oktober 1928', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Sila keberapa dalam Pancasila yang berbunyi "Persatuan Indonesia"?', 'pilihan_a' => 'Sila ke-2', 'pilihan_b' => 'Sila ke-3', 'pilihan_c' => 'Sila ke-4', 'pilihan_d' => 'Sila ke-5', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Undang-Undang Dasar yang menjadi konstitusi tertulis Indonesia adalah?', 'pilihan_a' => 'UUD 1945', 'pilihan_b' => 'UU ITE', 'pilihan_c' => 'UU Otonomi Daerah', 'pilihan_d' => 'UU Ketenagakerjaan', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Lembaga yang memiliki kewenangan menguji undang-undang terhadap UUD 1945 adalah?', 'pilihan_a' => 'Mahkamah Konstitusi', 'pilihan_b' => 'Kepolisian', 'pilihan_c' => 'DPRD', 'pilihan_d' => 'KPU', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Semboyan negara Indonesia yang berarti "berbeda-beda tetapi tetap satu" adalah?', 'pilihan_a' => 'Tut Wuri Handayani', 'pilihan_b' => 'Bhinneka Tunggal Ika', 'pilihan_c' => 'Garuda Pancasila', 'pilihan_d' => 'Ing Ngarso Sung Tuladha', 'jawaban_benar' => 'b'],
                ],
            ],
            [
                'label' => 'Dasar Kewirausahaan & Bisnis',
                'kata_kunci' => ['bisnis', 'wirausaha', 'usaha', 'entrepreneur'],
                'soal' => [
                    ['pertanyaan' => 'Dokumen yang merangkum rencana bisnis secara keseluruhan disebut?', 'pilihan_a' => 'Business plan', 'pilihan_b' => 'Invoice', 'pilihan_c' => 'Purchase order', 'pilihan_d' => 'Payslip', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Analisis kekuatan, kelemahan, peluang, dan ancaman suatu bisnis disebut?', 'pilihan_a' => 'SWOT', 'pilihan_b' => 'ROI', 'pilihan_c' => 'KPI', 'pilihan_d' => 'CRM', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Selisih antara pendapatan dan biaya dalam suatu usaha disebut?', 'pilihan_a' => 'Modal', 'pilihan_b' => 'Laba/rugi', 'pilihan_c' => 'Aset', 'pilihan_d' => 'Utang', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Kelompok orang yang menjadi sasaran penjualan produk disebut?', 'pilihan_a' => 'Supplier', 'pilihan_b' => 'Target pasar', 'pilihan_c' => 'Kompetitor', 'pilihan_d' => 'Distributor', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Modal yang berasal dari kepemilikan pribadi pemilik usaha disebut?', 'pilihan_a' => 'Modal asing', 'pilihan_b' => 'Modal sendiri', 'pilihan_c' => 'Modal pinjaman', 'pilihan_d' => 'Modal ventura', 'jawaban_benar' => 'b'],
                ],
            ],
            [
                'label' => 'Bahasa Inggris Dasar',
                'kata_kunci' => ['bahasa inggris', 'english', 'toefl', 'grammar'],
                'soal' => [
                    ['pertanyaan' => 'Bentuk lampau (past tense) dari kata kerja "go" adalah?', 'pilihan_a' => 'Goed', 'pilihan_b' => 'Went', 'pilihan_c' => 'Gone', 'pilihan_d' => 'Going', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Kalimat "She ___ a student" yang tepat menggunakan kata kerja bantu?', 'pilihan_a' => 'is', 'pilihan_b' => 'are', 'pilihan_c' => 'am', 'pilihan_d' => 'be', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Kata yang berfungsi menggantikan kata benda disebut?', 'pilihan_a' => 'Verb', 'pilihan_b' => 'Adjective', 'pilihan_c' => 'Pronoun', 'pilihan_d' => 'Adverb', 'jawaban_benar' => 'c'],
                    ['pertanyaan' => 'Antonim (lawan kata) dari "difficult" adalah?', 'pilihan_a' => 'Hard', 'pilihan_b' => 'Easy', 'pilihan_c' => 'Complex', 'pilihan_d' => 'Tough', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Struktur kalimat "Subject + will + verb1" digunakan untuk tenses?', 'pilihan_a' => 'Present tense', 'pilihan_b' => 'Past tense', 'pilihan_c' => 'Future tense', 'pilihan_d' => 'Perfect tense', 'jawaban_benar' => 'c'],
                ],
            ],
            [
                'label' => 'Keterampilan Belajar Online',
                'kata_kunci' => [],
                'soal' => [
                    ['pertanyaan' => 'Membuat jadwal belajar yang teratur setiap hari termasuk contoh dari?', 'pilihan_a' => 'Manajemen waktu', 'pilihan_b' => 'Manajemen keuangan', 'pilihan_c' => 'Manajemen risiko', 'pilihan_d' => 'Manajemen proyek', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Mencatat ulang materi dengan bahasa sendiri setelah belajar bertujuan untuk?', 'pilihan_a' => 'Menghabiskan waktu', 'pilihan_b' => 'Memperkuat pemahaman/ingatan', 'pilihan_c' => 'Menambah nilai tugas', 'pilihan_d' => 'Mengurangi fokus', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Istirahat singkat di sela sesi belajar (misalnya teknik Pomodoro) berguna untuk?', 'pilihan_a' => 'Menjaga fokus dan mencegah kelelahan', 'pilihan_b' => 'Memperlambat progres belajar', 'pilihan_c' => 'Menambah beban tugas', 'pilihan_d' => 'Mengurangi motivasi', 'jawaban_benar' => 'a'],
                    ['pertanyaan' => 'Menetapkan target belajar yang spesifik dan terukur termasuk prinsip?', 'pilihan_a' => 'SWOT', 'pilihan_b' => 'SMART goal', 'pilihan_c' => 'SEO', 'pilihan_d' => 'ATM', 'jawaban_benar' => 'b'],
                    ['pertanyaan' => 'Mengerjakan kuis di akhir materi berguna untuk?', 'pilihan_a' => 'Mengukur pemahaman terhadap materi', 'pilihan_b' => 'Mengganti materi lama', 'pilihan_c' => 'Menghapus riwayat belajar', 'pilihan_d' => 'Menambah durasi akses', 'jawaban_benar' => 'a'],
                ],
            ],
        ];
    }
}
