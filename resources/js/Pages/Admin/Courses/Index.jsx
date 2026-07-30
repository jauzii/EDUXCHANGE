import { Link, router, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const formatRupiah = (value) => `Rp ${Number(value || 0).toLocaleString('id-ID')}`;

export default function AdminCoursesIndex({ courses = [], filters = {} }) {
    const { flash } = usePage().props;
    const { data, setData } = useForm({
        search: filters.search || '',
    });

    const applySearch = (event) => {
        event.preventDefault();
        router.get(route('admin.courses.index'), data, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const resetSearch = () => {
        router.get(route('admin.courses.index'));
    };

    const handleDelete = (course) => {
        const confirmed = window.confirm(
            `Hapus paket "${course.nama_kursus}"? Materi, soal, dan riwayat enrollment siswa untuk paket ini akan ikut terhapus dan tidak bisa dikembalikan.`
        );

        if (!confirmed) return;

        router.delete(route('admin.courses.destroy', course.id), { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                        <div className="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-200">Kelola paket</p>
                                <h1 className="mt-3 text-4xl font-extrabold tracking-tight">Paket kursus EDUXCHANGE.</h1>
                                <p className="mt-4 max-w-2xl text-slate-300">
                                    Tambah, ubah, atau hapus paket belajar. Perubahan di sini langsung tampil di halaman paket siswa.
                                </p>
                            </div>
                            <Link
                                href={route('admin.courses.create')}
                                className="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                + Tambah Paket Kursus
                            </Link>
                        </div>
                    </section>

                    {flash?.success && (
                        <div className="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">
                            {flash.success}
                        </div>
                    )}

                    <form onSubmit={applySearch} className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div className="grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                            <input
                                value={data.search}
                                onChange={(event) => setData('search', event.target.value)}
                                placeholder="Cari nama paket atau kategori"
                                className="rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <button type="submit" className="rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                                Cari
                            </button>
                            <button type="button" onClick={resetSearch} className="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Reset
                            </button>
                        </div>
                    </form>

                    <section className="rounded-3xl border border-slate-200 bg-white shadow-sm">
                        {courses.length > 0 ? (
                            <div className="divide-y divide-slate-200">
                                {courses.map((course) => (
                                    <div key={course.id} className="grid gap-4 p-5 lg:grid-cols-[1fr_auto_auto] lg:items-center">
                                        <div>
                                            <span className="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                                                {course.kategori || 'Paket Belajar'}
                                            </span>
                                            <p className="mt-3 text-lg font-extrabold text-slate-950">{course.nama_kursus}</p>
                                            <p className="text-sm font-medium text-slate-500">Tutor: {course.tutor}</p>
                                        </div>

                                        <div className="grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
                                            <div className="rounded-2xl bg-slate-50 px-4 py-3">
                                                <p className="text-lg font-extrabold text-slate-950">{formatRupiah(course.harga)}</p>
                                                <p className="text-xs font-bold text-slate-500">Harga</p>
                                            </div>
                                            <div className="rounded-2xl bg-slate-50 px-4 py-3">
                                                <p className="text-lg font-extrabold text-slate-950">{course.enrollments_count}</p>
                                                <p className="text-xs font-bold text-slate-500">Siswa</p>
                                            </div>
                                            <div className="rounded-2xl bg-slate-50 px-4 py-3">
                                                <p className="text-lg font-extrabold text-slate-950">{course.materials_count}</p>
                                                <p className="text-xs font-bold text-slate-500">Materi</p>
                                            </div>
                                            <div className="rounded-2xl bg-slate-50 px-4 py-3">
                                                <p className="text-lg font-extrabold text-slate-950">{course.questions_count}</p>
                                                <p className="text-xs font-bold text-slate-500">Soal</p>
                                            </div>
                                        </div>

                                        <div className="flex gap-3 lg:flex-col">
                                            <Link
                                                href={route('admin.courses.quizzes.index', course.id)}
                                                className="inline-flex flex-1 items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                            >
                                                Kelola Materi &amp; Kuis
                                            </Link>
                                            <Link
                                                href={route('admin.courses.edit', course.id)}
                                                className="inline-flex flex-1 items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() => handleDelete(course)}
                                                className="inline-flex flex-1 items-center justify-center rounded-2xl border border-red-200 px-5 py-3 text-sm font-bold text-red-600 transition hover:bg-red-50"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="p-12 text-center">
                                <h3 className="text-xl font-extrabold text-slate-950">Belum ada paket kursus</h3>
                                <p className="mt-2 text-sm text-slate-600">Tambahkan paket kursus pertama kamu.</p>
                                <Link
                                    href={route('admin.courses.create')}
                                    className="mt-6 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                >
                                    + Tambah Paket Kursus
                                </Link>
                            </div>
                        )}
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
