import { Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function EnrollmentsIndex({ enrollments = [] }) {
    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-white p-8 shadow-sm ring-1 ring-slate-200">
                        <div className="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-700">Kelas Saya</p>
                                <h1 className="mt-3 text-4xl font-extrabold tracking-tight text-slate-950">Paket yang sedang kamu ikuti.</h1>
                                <p className="mt-4 max-w-2xl text-slate-600">
                                    Pantau sisa masa akses 30 hari, progress belajar, nilai kuis, dan sertifikat dari semua paket aktif kamu.
                                </p>
                            </div>
                            <Link
                                href={route('courses.index')}
                                className="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                Tambah Paket
                            </Link>
                        </div>
                    </section>

                    <section className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {enrollments.length > 0 ? (
                            enrollments.map((enrollment) => (
                                <article
                                    key={enrollment.id}
                                    className="flex flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200"
                                >
                                    <div className="flex items-start justify-between gap-4">
                                        <span className={`rounded-full px-3 py-1 text-xs font-bold ${enrollment.sudah_selesai ? 'bg-slate-100 text-slate-600' : 'bg-emerald-50 text-emerald-700'}`}>
                                            {enrollment.sudah_selesai ? 'Selesai' : 'Aktif'}
                                        </span>
                                        <span className="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">30 hari</span>
                                    </div>

                                    <div className="mt-5 flex-1">
                                        <h2 className="text-2xl font-extrabold tracking-tight text-slate-950">{enrollment.course.nama_kursus}</h2>
                                        <p className="mt-2 text-sm font-medium text-slate-500">Tutor: {enrollment.course.tutor?.name || 'Tutor EDUXCHANGE'}</p>
                                    </div>

                                    <div className="mt-6 rounded-3xl bg-slate-50 p-5">
                                        <div className="flex items-end justify-between gap-4">
                                            <div>
                                                <p className="text-sm font-bold text-slate-500">Sisa akses</p>
                                                <p className="mt-1 text-3xl font-extrabold text-slate-950">
                                                    {enrollment.sudah_selesai ? 'Expired' : `${enrollment.sisa_hari} hari`}
                                                </p>
                                            </div>
                                            <p className="text-right text-xs font-bold text-slate-500">
                                                {enrollment.sudah_selesai ? `Berakhir ${enrollment.ends_at_label}` : `Aktif sampai ${enrollment.ends_at_label}`}
                                            </p>
                                        </div>
                                        <div className="mt-4 h-2.5 rounded-full bg-white">
                                            <div className="h-2.5 rounded-full bg-indigo-600" style={{ width: `${enrollment.progress}%` }}></div>
                                        </div>
                                        <div className="mt-2 flex justify-between text-xs font-bold text-slate-500">
                                            <span>Progress belajar (waktu, kuis, sertifikat)</span>
                                            <span>{enrollment.progress}%</span>
                                        </div>
                                    </div>

                                    <div className="mt-5 grid grid-cols-2 gap-3">
                                        <div className="rounded-2xl border border-slate-200 p-4">
                                            <p className="text-xs font-bold uppercase tracking-wide text-slate-400">Nilai kuis</p>
                                            <p className="mt-2 text-lg font-extrabold text-slate-950">
                                                {enrollment.sudah_mengerjakan_kuis ? enrollment.score : 'Belum'}
                                            </p>
                                        </div>
                                        <div className="rounded-2xl border border-slate-200 p-4">
                                            <p className="text-xs font-bold uppercase tracking-wide text-slate-400">Sertifikat</p>
                                            <p className="mt-2 text-lg font-extrabold text-slate-950">
                                                {enrollment.bisa_unduh_sertifikat ? 'Tersedia' : 'Belum'}
                                            </p>
                                        </div>
                                    </div>

                                    <Link
                                        href={route('enrollments.show', enrollment.id)}
                                        className="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        Buka Kelas
                                    </Link>
                                </article>
                            ))
                        ) : (
                            <div className="col-span-full rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center">
                                <h3 className="text-2xl font-extrabold text-slate-950">Kamu belum punya paket aktif</h3>
                                <p className="mt-3 text-slate-600">Pilih paket belajar 30 hari untuk mulai mengakses materi dan progress dashboard.</p>
                                <Link
                                    href={route('courses.index')}
                                    className="mt-6 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                >
                                    Jelajahi Paket
                                </Link>
                            </div>
                        )}
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
