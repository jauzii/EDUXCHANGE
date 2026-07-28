import { Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Dashboard({ stats, popularCourses, learningSummary }) {
    const { auth } = usePage().props;

    const statCards = [
        ['Total Kursus', stats.kursus, 'bg-indigo-50 text-indigo-700'],
        ['Total Tutor', stats.tutor, 'bg-sky-50 text-sky-700'],
        ['Total Siswa', stats.siswa, 'bg-emerald-50 text-emerald-700'],
        ['Total Transaksi', stats.transaksi, 'bg-amber-50 text-amber-700'],
    ];

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                    <section className="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                        <div className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                            <p className="text-sm font-bold uppercase tracking-wide text-indigo-200">Dashboard student</p>
                            <h1 className="mt-3 text-4xl font-extrabold tracking-tight">
                                Halo, {auth.user.name}. Lanjutkan progres belajarmu.
                            </h1>
                            <p className="mt-4 max-w-2xl text-slate-300">
                                Pantau paket aktif, progress belajar, dan rekomendasi paket yang bisa kamu ambil selama 30 hari akses.
                            </p>
                            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                                <Link href={route('courses.index')} className="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-indigo-50">
                                    Jelajahi Paket
                                </Link>
                                <Link href={route('enrollments.index')} className="inline-flex items-center justify-center rounded-2xl border border-white/20 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                                    Buka Kelas Saya
                                </Link>
                            </div>
                        </div>

                        <div className="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-sm font-bold text-slate-500">Status paket</p>
                                    <h2 className="mt-1 text-2xl font-extrabold text-slate-950">
                                        {learningSummary.active_enrollments > 0 ? `${learningSummary.active_enrollments} paket aktif` : 'Belum ada paket aktif'}
                                    </h2>
                                </div>
                                <span className="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">{learningSummary.total_enrollments} total</span>
                            </div>

                            <div className="mt-6 rounded-3xl bg-indigo-50 p-5">
                                <div className="flex items-end justify-between">
                                    <div>
                                        <p className="text-sm font-bold text-indigo-700">Progress umum</p>
                                        <p className="mt-2 text-5xl font-extrabold text-slate-950">{learningSummary.progress_percent}%</p>
                                    </div>
                                    <p className="pb-2 text-sm font-semibold text-slate-500">Waktu, kuis, sertifikat</p>
                                </div>
                                <div className="mt-5 h-3 rounded-full bg-white">
                                    <div className="h-3 rounded-full bg-indigo-600" style={{ width: `${learningSummary.progress_percent}%` }}></div>
                                </div>
                            </div>

                            <div className="mt-5 grid grid-cols-3 gap-3 text-center">
                                <div className="rounded-2xl border border-slate-200 p-3">
                                    <div className="text-lg font-extrabold text-slate-950">{learningSummary.materials_count}</div>
                                    <div className="text-xs font-semibold text-slate-500">Materi</div>
                                </div>
                                <div className="rounded-2xl border border-slate-200 p-3">
                                    <div className="text-lg font-extrabold text-slate-950">{learningSummary.quiz_done_count}</div>
                                    <div className="text-xs font-semibold text-slate-500">Kuis</div>
                                </div>
                                <div className="rounded-2xl border border-slate-200 p-3">
                                    <div className="text-lg font-extrabold text-slate-950">{learningSummary.certificates_available_count}</div>
                                    <div className="text-xs font-semibold text-slate-500">Sertifikat</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        {statCards.map(([label, value, color]) => (
                            <div key={label} className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div className="flex items-center justify-between">
                                    <p className="text-sm font-bold text-slate-500">{label}</p>
                                    <span className={`h-10 w-10 rounded-2xl ${color}`}></span>
                                </div>
                                <p className="mt-5 text-4xl font-extrabold tracking-tight text-slate-950">{value}</p>
                                <p className="mt-2 text-sm font-medium text-slate-500">Data real dari sistem EDUXCHANGE.</p>
                            </div>
                        ))}
                    </section>

                    <section className="grid gap-6">
                        <div className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                                <div>
                                    <p className="text-sm font-bold uppercase tracking-wide text-indigo-700">Paket populer</p>
                                    <h2 className="mt-2 text-2xl font-extrabold text-slate-950">Rekomendasi belajar</h2>
                                </div>
                                <Link href={route('courses.index')} className="text-sm font-bold text-indigo-700 hover:text-indigo-900">
                                    Lihat semua
                                </Link>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                {popularCourses.length > 0 ? (
                                    popularCourses.map((course) => (
                                        <article key={course.id} className="rounded-3xl border border-slate-200 p-5 transition hover:border-indigo-200 hover:bg-indigo-50/40">
                                            <div className="flex items-start justify-between gap-3">
                                                <span className="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                                    {course.kategori ?? 'Paket Belajar'}
                                                </span>
                                                <span className="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">30 hari</span>
                                            </div>
                                            <h3 className="mt-4 text-lg font-extrabold text-slate-950">{course.nama_kursus}</h3>
                                            <p className="mt-2 text-sm font-medium text-slate-500">
                                                Tutor: {course.tutor_display_name}
                                            </p>
                                            <div className="mt-5 flex items-end justify-between">
                                                <p className="text-xl font-extrabold text-slate-950">
                                                    Rp {Number(course.harga).toLocaleString('id-ID')}
                                                </p>
                                                <p className="text-xs font-bold text-slate-500">{course.transactions_count} transaksi</p>
                                            </div>
                                        </article>
                                    ))
                                ) : (
                                    <div className="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center md:col-span-2">
                                        <h3 className="font-extrabold text-slate-950">Belum ada paket populer</h3>
                                        <p className="mt-2 text-sm text-slate-600">Data transaksi belum tersedia.</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
