<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\Question;
use App\Models\Transaction;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Akun untuk login & testing cepat.
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'student',
        ]);

        User::factory()->create([
            'name' => 'Admin EDUXCHANGE',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // 5 user yang menjadi tutor, masing-masing punya profil Tutor.
        $tutors = Tutor::factory(5)->for(User::factory()->state(['role' => 'admin']))->create();
        $tutors[0]->user->update(['name' => 'Andi Pratama']);
        $tutors[1]->user->update(['name' => 'Sari Wijaya']);

        // ----- 2 kursus contoh dengan materi & soal asli (bukan lorem ipsum) -----

        $kursusLaravel = Course::create([
            'tutor_id' => $tutors[0]->id,
            'nama_kursus' => 'Belajar Laravel dari Nol',
            'kategori' => 'Programming',
            'harga' => 150000,
            'deskripsi' => 'Kursus dasar untuk memahami fondasi framework Laravel: arsitektur MVC, routing, controller, dan Eloquent ORM.',
        ]);

        Material::insert([
            [
                'course_id' => $kursusLaravel->id,
                'judul' => 'Pengenalan Laravel dan Konsep MVC',
                'konten' => 'Laravel adalah framework PHP yang dibangun di atas pola desain Model-View-Controller (MVC). Dalam pola ini, Model bertugas mengurus data dan logika bisnis, View menampilkan tampilan ke pengguna, dan Controller menjadi penghubung yang mengatur aliran data antara Model dan View. Pemisahan tanggung jawab ini membuat kode lebih rapi, mudah diuji, dan mudah dikembangkan dalam tim.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'course_id' => $kursusLaravel->id,
                'judul' => 'Routing dan Controller',
                'konten' => "Routing di Laravel didefinisikan di dalam file routes/web.php atau routes/api.php, dan berfungsi memetakan sebuah URL ke sebuah aksi tertentu, baik berupa closure maupun method di dalam Controller. Controller sendiri adalah kelas PHP yang mengelompokkan logika penanganan beberapa request terkait, sehingga routing tidak perlu menumpuk banyak logika langsung di file routes.",
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'course_id' => $kursusLaravel->id,
                'judul' => 'Eloquent ORM dan Relasi Antar Tabel',
                'konten' => 'Eloquent adalah ORM (Object Relational Mapping) bawaan Laravel yang memungkinkan kita berinteraksi dengan tabel database menggunakan objek PHP, tanpa harus menulis query SQL secara manual. Eloquent juga mendukung berbagai jenis relasi seperti belongsTo, hasMany, dan belongsToMany, sehingga data yang saling berhubungan antar tabel dapat diambil dengan cara yang ekspresif dan mudah dibaca.',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        Question::insert([
            ['course_id' => $kursusLaravel->id, 'pertanyaan' => 'Apa kepanjangan dari MVC dalam konteks arsitektur aplikasi web?', 'pilihan_a' => 'Model View Controller', 'pilihan_b' => 'Main View Class', 'pilihan_c' => 'Model Variable Controller', 'pilihan_d' => 'Module View Component', 'jawaban_benar' => 'a', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusLaravel->id, 'pertanyaan' => 'Di Laravel, file mana yang umum digunakan untuk mendefinisikan rute halaman web?', 'pilihan_a' => 'routes/api.php', 'pilihan_b' => 'routes/web.php', 'pilihan_c' => 'app/Http/Kernel.php', 'pilihan_d' => 'config/app.php', 'jawaban_benar' => 'b', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusLaravel->id, 'pertanyaan' => 'Apa nama ORM bawaan Laravel untuk berinteraksi dengan database?', 'pilihan_a' => 'Doctrine', 'pilihan_b' => 'Eloquent', 'pilihan_c' => 'Hibernate', 'pilihan_d' => 'Sequelize', 'jawaban_benar' => 'b', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusLaravel->id, 'pertanyaan' => "Relasi Eloquent manakah yang tepat untuk menyatakan 'satu kursus memiliki banyak materi'?", 'pilihan_a' => 'belongsTo', 'pilihan_b' => 'hasMany', 'pilihan_c' => 'belongsToMany', 'pilihan_d' => 'hasOne', 'jawaban_benar' => 'b', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusLaravel->id, 'pertanyaan' => 'Perintah artisan apa yang digunakan untuk membuat migration baru?', 'pilihan_a' => 'php artisan make:model', 'pilihan_b' => 'php artisan make:migration', 'pilihan_c' => 'php artisan migrate', 'pilihan_d' => 'php artisan make:controller', 'jawaban_benar' => 'b', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $kursusUiUx = Course::create([
            'tutor_id' => $tutors[1]->id,
            'nama_kursus' => 'Dasar-Dasar UI/UX Design',
            'kategori' => 'Design',
            'harga' => 120000,
            'deskripsi' => 'Kursus pengantar untuk memahami perbedaan UI dan UX, prinsip user-centered design, serta proses wireframing dan prototyping.',
        ]);

        Material::insert([
            [
                'course_id' => $kursusUiUx->id,
                'judul' => 'Apa itu UI dan UX?',
                'konten' => 'UI (User Interface) berfokus pada tampilan visual sebuah produk digital, seperti warna, tipografi, dan tata letak elemen. UX (User Experience) berfokus pada keseluruhan pengalaman pengguna saat berinteraksi dengan produk tersebut, termasuk seberapa mudah, efisien, dan menyenangkan produk itu digunakan. Keduanya saling melengkapi: tampilan yang indah tanpa pengalaman yang baik bisa membuat pengguna frustrasi, begitu juga sebaliknya.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'course_id' => $kursusUiUx->id,
                'judul' => 'Prinsip Dasar User-Centered Design',
                'konten' => 'User-centered design adalah pendekatan perancangan yang menempatkan kebutuhan, perilaku, dan keterbatasan pengguna sebagai pertimbangan utama di setiap tahap proses desain. Pendekatan ini biasanya melibatkan riset pengguna, pembuatan persona, dan pengujian berulang (iterative testing) untuk memastikan produk akhir benar-benar menyelesaikan masalah penggunanya, bukan sekadar terlihat menarik.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'course_id' => $kursusUiUx->id,
                'judul' => 'Wireframing dan Prototyping',
                'konten' => 'Wireframe adalah sketsa kasar dari sebuah tampilan yang menunjukkan struktur dan tata letak elemen tanpa detail visual seperti warna atau gambar, biasanya dibuat di awal proses desain. Prototype adalah versi yang lebih interaktif dan mendekati produk akhir, digunakan untuk mensimulasikan alur penggunaan sebelum produk benar-benar dikembangkan oleh tim engineering.',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        Question::insert([
            ['course_id' => $kursusUiUx->id, 'pertanyaan' => 'Apa fokus utama dari UI (User Interface)?', 'pilihan_a' => 'Tampilan visual produk', 'pilihan_b' => 'Kecepatan server', 'pilihan_c' => 'Struktur database', 'pilihan_d' => 'Keamanan aplikasi', 'jawaban_benar' => 'a', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusUiUx->id, 'pertanyaan' => 'Apa fokus utama dari UX (User Experience)?', 'pilihan_a' => 'Warna dan tipografi', 'pilihan_b' => 'Pengalaman pengguna secara keseluruhan', 'pilihan_c' => 'Bahasa pemrograman yang dipakai', 'pilihan_d' => 'Ukuran file gambar', 'jawaban_benar' => 'b', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusUiUx->id, 'pertanyaan' => 'Pendekatan desain yang menempatkan kebutuhan pengguna sebagai pertimbangan utama disebut?', 'pilihan_a' => 'Server-centered design', 'pilihan_b' => 'Data-driven design', 'pilihan_c' => 'User-centered design', 'pilihan_d' => 'Code-first design', 'jawaban_benar' => 'c', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusUiUx->id, 'pertanyaan' => 'Sketsa kasar tata letak tanpa detail visual seperti warna disebut?', 'pilihan_a' => 'Prototype', 'pilihan_b' => 'Wireframe', 'pilihan_c' => 'Mockup', 'pilihan_d' => 'Storyboard', 'jawaban_benar' => 'b', 'created_at' => now(), 'updated_at' => now()],
            ['course_id' => $kursusUiUx->id, 'pertanyaan' => 'Apa tujuan utama dari pengujian berulang (iterative testing) dalam proses desain?', 'pilihan_a' => 'Mempercantik logo', 'pilihan_b' => 'Memastikan produk menyelesaikan masalah pengguna', 'pilihan_c' => 'Mengurangi ukuran file', 'pilihan_d' => 'Menambah jumlah halaman', 'jawaban_benar' => 'b', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ----- Kursus tambahan (filler) untuk variasi statistik dashboard -----

        $kursusFiller = collect(range(1, 6))->map(function (int $i) use ($tutors) {
            $course = Course::factory()->create([
                'tutor_id' => $tutors->random()->id,
            ]);

            Material::factory(fake()->numberBetween(2, 4))->create([
                'course_id' => $course->id,
            ]);

            Question::factory(5)->create([
                'course_id' => $course->id,
            ]);

            return $course;
        });

        $semuaKursus = $kursusFiller->push($kursusLaravel, $kursusUiUx);

        // ----- 15 user sebagai siswa, masing-masing "membeli" 1-2 kursus -----

        $students = User::factory(15)->create([
            'role' => 'student',
        ]);

        $students->each(function (User $student) use ($semuaKursus) {
            $semuaKursus->random(fake()->numberBetween(1, 2))->each(function (Course $course) use ($student) {
                $status = fake()->randomElement(['pending', 'success', 'success', 'failed']);

                $transaction = Transaction::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => $status,
                ]);

                if ($status !== 'success') {
                    return;
                }

                $mulai = now()->subDays(fake()->numberBetween(0, 40));

                $enrollment = Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'transaction_id' => $transaction->id,
                    'started_at' => $mulai,
                    'ends_at' => $mulai->copy()->addDays(30),
                ]);

                $questions = $course->questions;

                // 60% kemungkinan siswa ini sudah mengerjakan kuis kursusnya.
                if ($questions->isNotEmpty() && fake()->boolean(60)) {
                    foreach ($questions as $question) {
                        $jawabanBenar = fake()->boolean(70);
                        $pilihan = $jawabanBenar
                            ? $question->jawaban_benar
                            : fake()->randomElement(array_diff(['a', 'b', 'c', 'd'], [$question->jawaban_benar]));

                        $enrollment->quizAnswers()->create([
                            'question_id' => $question->id,
                            'jawaban_dipilih' => $pilihan,
                            'is_benar' => $jawabanBenar,
                        ]);
                    }

                    $enrollment->recalculateScore();
                }
            });
        });
    }
}
