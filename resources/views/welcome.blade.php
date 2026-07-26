<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUXCHANGE</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
    <link rel="icon" href="/favicon-16x16.png" type="image/png" sizes="16x16">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="/" class="flex items-center gap-3">
                <img src="/images/eduxchange-logo.png" alt="Logo EDUXCHANGE" class="h-10 w-10 rounded-2xl object-cover shadow-sm shadow-indigo-200">
                <div>
                    <div class="text-lg font-extrabold tracking-tight text-slate-950">EDUXCHANGE</div>
                    <div class="text-xs font-medium text-slate-500">Bimbel online luas</div>
                </div>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-semibold text-slate-600 md:flex">
                <a href="#kategori" class="hover:text-indigo-700">Kategori</a>
                <a href="#paket" class="hover:text-indigo-700">Paket</a>
                <a href="#benefit" class="hover:text-indigo-700">Benefit</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('login.select') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                    Login
                </a>
                <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700">
                    Register
                </a>
            </div>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden bg-white">
            <div class="mx-auto grid min-h-[calc(100vh-4rem)] max-w-7xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
                <div>
                    <p class="mb-5 inline-flex rounded-full border border-indigo-100 bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700">
                        Platform bimbel online untuk belajar lebih luas
                    </p>
                    <h1 class="max-w-4xl text-5xl font-extrabold leading-[1.05] tracking-tight text-slate-950 sm:text-6xl lg:text-7xl">
                        Belajar sekolah, skill, bahasa, dan karier dalam satu dashboard.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        EDUXCHANGE membantu student memilih paket belajar 30 hari, mengikuti materi, mengerjakan kuis, dan memantau progres tanpa batas kategori.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-4 text-base font-bold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">
                            Mulai Belajar
                        </a>
                        <a href="#paket" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-4 text-base font-bold text-slate-800 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            Lihat Paket
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="rounded-[2rem] border border-slate-200 bg-slate-950 p-5 shadow-2xl shadow-slate-300">
                        <div class="rounded-3xl bg-white p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-slate-500">Progress Belajar</p>
                                    <h2 class="mt-1 text-2xl font-extrabold text-slate-950">Dashboard Student</h2>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Paket Aktif</span>
                            </div>

                            <div class="mt-6 rounded-2xl bg-indigo-600 p-5 text-white">
                                <p class="text-sm font-semibold text-indigo-100">Sisa akses paket</p>
                                <div class="mt-2 flex items-end justify-between">
                                    <div class="text-5xl font-extrabold">24</div>
                                    <div class="pb-2 text-sm font-semibold text-indigo-100">dari 30 hari</div>
                                </div>
                                <div class="mt-5 h-2 rounded-full bg-white/20">
                                    <div class="h-2 w-4/5 rounded-full bg-white"></div>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-sm font-bold text-slate-500">Materi selesai</p>
                                    <p class="mt-2 text-3xl font-extrabold text-slate-950">70%</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 p-4">
                                    <p class="text-sm font-bold text-slate-500">Kuis</p>
                                    <p class="mt-2 text-3xl font-extrabold text-slate-950">86</p>
                                </div>
                            </div>

                            <div class="mt-5 space-y-3">
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span class="text-sm font-bold text-slate-700">Academic Booster</span>
                                    <span class="text-sm font-bold text-indigo-700">Lanjut</span>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span class="text-sm font-bold text-slate-700">Digital Skill Starter</span>
                                    <span class="text-sm font-bold text-slate-400">Paket</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="kategori" class="py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-wide text-indigo-700">Kategori belajar</p>
                    <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-950">Bimbel yang cakupannya lebih luas.</h2>
                    <p class="mt-4 text-slate-600">Student bisa belajar sesuai kebutuhan: sekolah, skill praktis, bahasa, sampai persiapan karier.</p>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    @foreach ([
                        ['Akademik', 'Materi sekolah dan persiapan ujian.'],
                        ['Skill Digital', 'Programming, UI/UX, data, dan tools industri.'],
                        ['Bahasa', 'Bahasa asing untuk studi dan kerja.'],
                        ['Karier', 'Portofolio, interview, dan soft skill.'],
                        ['Ujian', 'Latihan terarah dan evaluasi progres.'],
                    ] as $category)
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <x-category-icon :kategori="$category[0]" class="mb-4 h-10 w-10" />
                            <h3 class="font-extrabold text-slate-950">{{ $category[0] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $category[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="paket" class="bg-white py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-indigo-700">Paket populer</p>
                        <h2 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-950">Pilih paket belajar 30 hari.</h2>
                        <p class="mt-4 max-w-2xl text-slate-600">Paket diambil dari kursus terbaru yang tersedia di EDUXCHANGE.</p>
                    </div>
                    <a href="{{ route('login.select') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-100">
                        Login untuk berlangganan
                    </a>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($popularCourses as $course)
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200">
                            <div class="flex items-start justify-between gap-4">
                                <x-category-icon :kategori="$course->kategori" class="h-11 w-11" />
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">30 hari</span>
                            </div>
                            <div class="mt-4">
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $course->kategori ?? 'Paket Belajar' }}</span>
                                <h3 class="mt-4 text-xl font-extrabold text-slate-950">{{ $course->nama_kursus }}</h3>
                            </div>
                            <p class="mt-4 text-sm leading-6 text-slate-600">
                                {{ \Illuminate\Support\Str::limit($course->deskripsi ?? 'Pelajari skill baru bersama tutor EDUXCHANGE dengan materi terstruktur.', 120) }}
                            </p>
                            <div class="mt-6 border-t border-slate-100 pt-5">
                                <p class="text-sm font-semibold text-slate-500">Tutor</p>
                                <p class="mt-1 font-bold text-slate-900">{{ $course->tutor->user->name ?? 'Tutor EDUXCHANGE' }}</p>
                            </div>
                            <div class="mt-5 flex items-end justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Harga paket</p>
                                    <p class="mt-1 text-2xl font-extrabold text-slate-950">Rp {{ number_format($course->harga, 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ route('login.select') }}" class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                                    Pilih
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                            <h3 class="text-xl font-extrabold text-slate-950">Belum ada paket belajar</h3>
                            <p class="mt-2 text-slate-600">Paket akan tampil setelah tutor menambahkan kursus.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="benefit" class="py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-[2rem] bg-slate-950 p-8 text-white lg:p-12">
                    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-wide text-indigo-200">Kenapa EDUXCHANGE</p>
                            <h2 class="mt-3 text-4xl font-extrabold tracking-tight">Belajar terarah, progres terlihat, akses jelas.</h2>
                            <p class="mt-4 text-slate-300">Student tahu paket aktifnya, admin nanti bisa memantau progres, dan sistem siap dikembangkan untuk role serta subscription penuh.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ([
                                ['Progress pribadi', 'Dashboard menampilkan progres belajar dan checkpoint.'],
                                ['Paket 30 hari', 'Bahasa produk sudah siap untuk model langganan.'],
                                ['Banyak bidang', 'Tidak berhenti di pelajaran sekolah saja.'],
                                ['Siap admin', 'Struktur UI disiapkan untuk monitoring student.'],
                            ] as $benefit)
                                <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                                    <h3 class="font-extrabold">{{ $benefit[0] }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-300">{{ $benefit[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white py-8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-center gap-2 px-4 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
            <p class="font-semibold text-slate-700">EDUXCHANGE</p>
            <p>© Copyright 2026, Eduxchange. All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>

