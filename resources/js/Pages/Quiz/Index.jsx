import { Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function QuizIndex({ enrollment, quizzes = [] }) {
    const course = enrollment.course;
    const progress = enrollment.total_kuis > 0
        ? Math.round((enrollment.kuis_selesai / enrollment.total_kuis) * 100)
        : 0;

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-5xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                        <p className="text-sm font-bold uppercase tracking-wide text-indigo-200">Kuis dan sertifikat</p>
                        <h1 className="mt-3 text-3xl font-extrabold tracking-tight">{course.nama_kursus}</h1>
                        <p className="mt-4 max-w-2xl text-slate-300">
                            Ada {enrollment.total_kuis} kuis checkpoint di paket ini. Selesaikan semuanya untuk membuka sertifikat kelulusan.
                        </p>
                        <div className="mt-6 flex items-center gap-4">
                            <div className="h-2.5 flex-1 rounded-full bg-white/20">
                                <div className="h-2.5 rounded-full bg-emerald-400" style={{ width: `${progress}%` }}></div>
                            </div>
                            <span className="whitespace-nowrap text-sm font-bold">
                                {enrollment.kuis_selesai} / {enrollment.total_kuis} selesai
                            </span>
                        </div>
                    </section>

                    {!enrollment.can_access_content && (
                        <div className="rounded-3xl border border-dashed border-amber-300 bg-amber-50 p-6 text-sm font-semibold text-amber-800">
                            Masa akses paket ini sudah habis, jadi kuis tidak bisa dikerjakan lagi. Daftar ulang paket untuk membuka kuis kembali.
                        </div>
                    )}

                    <section className="grid gap-4 sm:grid-cols-2">
                        {quizzes.map((quiz) => {
                            const terkunci = !enrollment.can_access_content || quiz.questions_count === 0;

                            return (
                                <div
                                    key={quiz.id}
                                    className="flex items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm"
                                >
                                    <div className="min-w-0">
                                        <p className="text-xs font-bold uppercase tracking-wide text-indigo-600">Kuis {quiz.urutan}</p>
                                        <h3 className="mt-1 truncate text-lg font-extrabold text-slate-950">{quiz.judul}</h3>
                                        <p className="mt-1 text-sm text-slate-500">
                                            {quiz.questions_count} soal
                                            {quiz.sudah_dikerjakan && ` · skor ${quiz.score}`}
                                        </p>
                                    </div>

                                    {quiz.questions_count === 0 ? (
                                        <span className="shrink-0 rounded-2xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-400">
                                            Belum ada soal
                                        </span>
                                    ) : !enrollment.can_access_content ? (
                                        <span className="shrink-0 rounded-2xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-400">
                                            Terkunci
                                        </span>
                                    ) : (
                                        <Link
                                            href={route('quiz.create', [enrollment.id, quiz.id])}
                                            className={`shrink-0 inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-bold text-white transition ${
                                                quiz.sudah_dikerjakan
                                                    ? 'bg-slate-700 hover:bg-slate-800'
                                                    : 'bg-indigo-600 hover:bg-indigo-700'
                                            }`}
                                        >
                                            {quiz.sudah_dikerjakan ? 'Kerjakan Ulang' : 'Kerjakan'}
                                        </Link>
                                    )}
                                </div>
                            );
                        })}

                        {quizzes.length === 0 && (
                            <div className="col-span-full rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                <h3 className="font-extrabold text-slate-950">Belum ada kuis</h3>
                                <p className="mt-2 text-sm text-slate-600">Kuis checkpoint untuk paket ini akan tampil di sini.</p>
                            </div>
                        )}
                    </section>

                    <section className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-700">Sertifikat</p>
                                <p className="mt-2 text-sm text-slate-600">
                                    {enrollment.bisa_unduh_sertifikat
                                        ? 'Semua kuis sudah selesai. Sertifikat kamu sudah bisa diunduh.'
                                        : `Selesaikan sisa ${Math.max(enrollment.total_kuis - enrollment.kuis_selesai, 0)} kuis lagi untuk membuka sertifikat.`}
                                </p>
                            </div>
                            {enrollment.bisa_unduh_sertifikat ? (
                                <a
                                    href={route('certificate.download', enrollment.id)}
                                    className="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700"
                                >
                                    Unduh Sertifikat
                                </a>
                            ) : (
                                <span className="inline-flex items-center justify-center rounded-2xl bg-slate-100 px-5 py-3 text-sm font-bold text-slate-500">
                                    Sertifikat Belum Tersedia
                                </span>
                            )}
                        </div>
                    </section>

                    <Link
                        href={route('enrollments.show', enrollment.id)}
                        className="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700"
                    >
                        Kembali ke Kelas
                    </Link>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
