import { Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function AdminCoursesEdit({ course, categories = [] }) {
    const { data, setData, put, processing, errors } = useForm({
        tutor_nama: course.tutor_nama ?? '',
        nama_kursus: course.nama_kursus ?? '',
        kategori: course.kategori ?? '',
        harga: course.harga ?? '',
        deskripsi: course.deskripsi ?? '',
    });

    const submit = (event) => {
        event.preventDefault();
        put(route('admin.courses.update', course.id));
    };

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-3xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                        <Link href={route('admin.courses.index')} className="text-sm font-bold text-indigo-200 hover:text-white">
                            Kembali ke daftar paket
                        </Link>
                        <h1 className="mt-4 text-3xl font-extrabold tracking-tight">Edit Paket Kursus</h1>
                        <p className="mt-3 text-slate-300">Perubahan langsung berlaku di halaman paket belajar siswa.</p>
                    </section>

                    <form onSubmit={submit} className="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Tutor pengajar</label>
                            <input
                                type="text"
                                value={data.tutor_nama}
                                onChange={(event) => setData('tutor_nama', event.target.value)}
                                placeholder="Contoh: Budi Santoso"
                                className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {errors.tutor_nama && <p className="mt-2 text-sm text-red-600">{errors.tutor_nama}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-slate-700">Nama paket kursus</label>
                            <input
                                type="text"
                                value={data.nama_kursus}
                                onChange={(event) => setData('nama_kursus', event.target.value)}
                                className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {errors.nama_kursus && <p className="mt-2 text-sm text-red-600">{errors.nama_kursus}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-slate-700">Kategori</label>
                            <input
                                type="text"
                                list="kategori-options"
                                value={data.kategori}
                                onChange={(event) => setData('kategori', event.target.value)}
                                className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <datalist id="kategori-options">
                                {categories.map((category) => (
                                    <option key={category} value={category} />
                                ))}
                            </datalist>
                            {errors.kategori && <p className="mt-2 text-sm text-red-600">{errors.kategori}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-slate-700">Harga (Rp)</label>
                            <input
                                type="number"
                                min="0"
                                value={data.harga}
                                onChange={(event) => setData('harga', event.target.value)}
                                className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {errors.harga && <p className="mt-2 text-sm text-red-600">{errors.harga}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-slate-700">Deskripsi</label>
                            <textarea
                                rows={4}
                                value={data.deskripsi}
                                onChange={(event) => setData('deskripsi', event.target.value)}
                                className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {errors.deskripsi && <p className="mt-2 text-sm text-red-600">{errors.deskripsi}</p>}
                        </div>

                        <div className="flex gap-3 pt-2">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex flex-1 items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:opacity-60"
                            >
                                Simpan Perubahan
                            </button>
                            <Link
                                href={route('admin.courses.index')}
                                className="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
