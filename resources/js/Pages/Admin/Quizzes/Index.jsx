import { Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function AdminQuizzesIndex({ course, quizzes = [] }) {
    const handleDelete = (quiz) => {
        const confirmed = window.confirm(
            `Hapus modul "${quiz.judul}"? Materi dan semua soal di dalamnya ikut terhapus dan tidak bisa dikembalikan.`
        );

        if (!confirmed) return;

        router.delete(route('admin.courses.quizzes.destroy', [course.id, quiz.id]), { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-5xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                        <Link href={route('admin.courses.index')} className="text-sm font-bold text-indigo-200 hover:text-white">
                            Kembali ke daftar paket
                        </Link>
                        <div className="mt-4 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-200">Kelola Materi &amp; Kuis</p>
                                <h1 className="mt-3 text-3xl font-extrabold tracking-tight">{course.nama_kursus}</h1>
                                <p className="mt-3 max-w-2xl text-slate-300">
                                    Setiap modul berisi 1 materi bacaan + beberapa soal pilihan ganda. Siswa harus menyelesaikan
                                    semua modul di sini sebelum sertifikat bisa diunduh.
                                </p>
                            </div>
                            <div className="flex flex-col gap-3 sm:flex-row">
                                <a
                                    href={route('admin.courses.certificate-preview', course.id)}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/20"
                                >
                                    Lihat Contoh Sertifikat
                                </a>
                                <Link
                                    href={route('admin.courses.quizzes.create', course.id)}
                                    className="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                >
                                    + Tambah Modul
                                </Link>
                            </div>
                        </div>
                    </section>

                    <section className="rounded-3xl border border-slate-200 bg-white shadow-sm">
                        {quizzes.length > 0 ? (
                            <div className="divide-y divide-slate-200">
                                {quizzes.map((quiz) => (
                                    <div key={quiz.id} className="grid gap-4 p-5 lg:grid-cols-[auto_1fr_auto] lg:items-center">
                                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-extrabold text-indigo-700">
                                            {quiz.urutan}
                                        </div>
                                        <div>
                                            <p className="text-lg font-extrabold text-slate-950">{quiz.judul}</p>
                                            <p className="text-sm font-medium text-slate-500">
                                                {quiz.punya_materi ? 'Materi tersedia' : 'Belum ada materi'} · {quiz.questions_count} soal
                                            </p>
                                        </div>
                                        <div className="flex gap-3">
                                            <Link
                                                href={route('admin.courses.quizzes.edit', [course.id, quiz.id])}
                                                className="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() => handleDelete(quiz)}
                                                className="inline-flex items-center justify-center rounded-2xl border border-red-200 px-5 py-3 text-sm font-bold text-red-600 transition hover:bg-red-50"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="p-12 text-center">
                                <h3 className="text-xl font-extrabold text-slate-950">Belum ada modul</h3>
                                <p className="mt-2 text-sm text-slate-600">Tambahkan modul pertama (materi + soal) untuk paket ini.</p>
                                <Link
                                    href={route('admin.courses.quizzes.create', course.id)}
                                    className="mt-6 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                >
                                    + Tambah Modul
                                </Link>
                            </div>
                        )}
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
