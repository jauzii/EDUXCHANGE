<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Tutor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminCourseController extends Controller
{
    /**
     * Daftar seluruh paket kursus untuk dikelola admin.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $courses = Course::with('tutor.user')
            ->withCount(['enrollments', 'materials', 'questions'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama_kursus', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'nama_kursus' => $course->nama_kursus,
                'kategori' => $course->kategori,
                'harga' => $course->harga,
                'tutor' => $course->tutor_display_name,
                'enrollments_count' => $course->enrollments_count,
                'materials_count' => $course->materials_count,
                'questions_count' => $course->questions_count,
            ]);

        return Inertia::render('Admin/Courses/Index', [
            'courses' => $courses,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Form tambah paket kursus baru.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Courses/Create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Simpan paket kursus baru. Kalau admin juga mengisi bagian materi
     * & soal contoh di form yang sama, 1 modul (quiz + materi + soal)
     * langsung dibuat sekalian supaya paket ini bisa langsung dites
     * (dikerjakan siswa, sampai sertifikat) tanpa perlu langkah terpisah.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $moduleData = $this->validatedModule($request);

        // Kolom tutor_id di database masih mewajibkan relasi teknis ke
        // tabel tutors (foreign key), tapi nama yang ditampilkan ke user
        // sekarang selalu berasal dari input manual tutor_nama.
        $data['tutor_id'] = $this->fallbackTutorId();

        $course = Course::create($data);

        $moduleDibuat = false;

        if (! empty($moduleData['judul'])) {
            AdminQuizController::persistModule($course, [
                ...$moduleData,
                'urutan' => 1,
            ]);
            $moduleDibuat = true;
        }

        return redirect()->route('admin.courses.index')->with(
            'success',
            $moduleDibuat
                ? 'Paket kursus berhasil ditambahkan, lengkap dengan materi & soal contohnya.'
                : 'Paket kursus berhasil ditambahkan. Kamu bisa menambahkan materi & soal kapan saja lewat tombol "Kelola Materi & Kuis".'
        );
    }

    /**
     * Form edit paket kursus.
     */
    public function edit(Course $course): Response
    {
        return Inertia::render('Admin/Courses/Edit', [
            'course' => [
                'id' => $course->id,
                'tutor_nama' => $course->tutor_display_name,
                'nama_kursus' => $course->nama_kursus,
                'kategori' => $course->kategori,
                'harga' => $course->harga,
                'deskripsi' => $course->deskripsi,
            ],
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Update paket kursus.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $data = $this->validated($request);

        $course->update($data);

        return redirect()->route('admin.courses.index')->with('success', 'Paket kursus berhasil diperbarui.');
    }

    /**
     * Hapus paket kursus. Materi, soal, enrollment, dan transaksi terkait
     * ikut terhapus otomatis (cascade) sesuai relasi di database.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Paket kursus berhasil dihapus.');
    }

    /**
     * Validasi input form tambah/edit kursus.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'tutor_nama' => ['required', 'string', 'max:255'],
            'nama_kursus' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);
    }

    /**
     * Validasi bagian materi & soal contoh yang OPSIONAL di form tambah
     * paket kursus. Kalau admin mengisi "judul" modul, maka konten materi
     * dan minimal 1 soal lengkap wajib diisi. Kalau "judul" dikosongkan
     * (admin memilih menambahkan materi/soal nanti), seluruh bagian ini
     * dilewati tanpa error.
     */
    private function validatedModule(Request $request): array
    {
        return $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'konten' => ['required_with:judul', 'nullable', 'string'],
            'soal' => ['required_with:judul', 'nullable', 'array', 'min:1'],
            'soal.*.pertanyaan' => ['required_with:judul', 'string'],
            'soal.*.pilihan_a' => ['required_with:judul', 'string', 'max:255'],
            'soal.*.pilihan_b' => ['required_with:judul', 'string', 'max:255'],
            'soal.*.pilihan_c' => ['required_with:judul', 'string', 'max:255'],
            'soal.*.pilihan_d' => ['required_with:judul', 'string', 'max:255'],
            'soal.*.jawaban_benar' => ['required_with:judul', 'in:a,b,c,d'],
        ], [], [
            'judul' => 'judul modul',
            'konten' => 'konten materi',
        ]);
    }

    /**
     * Ambil id tutor teknis untuk mengisi kolom tutor_id (foreign key)
     * yang masih wajib diisi di database. Nama tutor yang sebenarnya
     * ditampilkan ke user selalu diambil dari tutor_nama, bukan dari sini.
     *
     * Sebelumnya method ini abort(500) kalau tabel tutors masih kosong
     * (misalnya di server hosting yang baru di-migrate tapi belum/skip
     * di-seed), sehingga tombol "Simpan Paket" gagal total. Sekarang,
     * kalau belum ada data tutor sama sekali, buat otomatis satu baris
     * tutor "placeholder" yang ditautkan ke akun admin yang sedang
     * login (akun ini pasti sudah ada karena route ini di balik
     * middleware role:admin), supaya syarat foreign key tetap terpenuhi
     * tanpa mengganggu proses simpan paket kursus.
     */
    private function fallbackTutorId(): int
    {
        $tutorId = Tutor::query()->value('id');

        if ($tutorId !== null) {
            return $tutorId;
        }

        return Tutor::create([
            'user_id' => auth()->id(),
            'keahlian' => 'Umum',
            'harga' => 0,
            'deskripsi' => 'Data teknis otomatis (fallback) — tidak ditampilkan ke user. Nama tutor yang tampil selalu berasal dari kolom tutor_nama di masing-masing paket kursus.',
        ])->id;
    }

    /**
     * Kategori yang sudah ada, dipakai sebagai saran cepat di form
     * supaya penamaan kategori tetap konsisten dengan halaman student.
     */
    private function categoryOptions()
    {
        return Course::query()
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');
    }
}