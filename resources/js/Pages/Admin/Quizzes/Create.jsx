import { Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const soalKosong = () => ({
    pertanyaan: '',
    pilihan_a: '',
    pilihan_b: '',
    pilihan_c: '',
    pilihan_d: '',
    jawaban_benar: 'a',
});

export default function AdminQuizzesCreate({ course, nextUrutan = 1 }) {
    const { data, setData, post, processing, errors } = useForm({
        judul: '',
        urutan: nextUrutan,
        konten: '',
        soal: [soalKosong()],
    });

    const submit = (event) => {
        event.preventDefault();
        post(route('admin.courses.quizzes.store', course.id));
    };

    const updateSoal = (index, field, value) => {
        const soal = data.soal.map((item, i) => (i === index ? { ...item, [field]: value } : item));
        setData('soal', soal);
    };

    const tambahSoal = () => setData('soal', [...data.soal, soalKosong()]);

    const hapusSoal = (index) => setData('soal', data.soal.filter((_, i) => i !== index));

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-3xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                        <Link href={route('admin.courses.quizzes.index', course.id)} className="text-sm font-bold text-indigo-200 hover:text-white">
                            Kembali ke daftar modul
                        </Link>
                        <p className="mt-4 text-sm font-bold uppercase tracking-wide text-indigo-200">{course.nama_kursus}</p>
                        <h1 className="mt-2 text-3xl font-extrabold tracking-tight">Tambah Modul</h1>
                        <p className="mt-3 text-slate-300">1 modul = 1 materi bacaan + beberapa soal pilihan ganda.</p>
                    </section>

                    <form onSubmit={submit} className="space-y-6">
                        <div className="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                            <div className="grid gap-5 sm:grid-cols-[1fr_auto]">
                                <div>
                                    <label className="block text-sm font-bold text-slate-700">Judul modul</label>
                                    <input
                                        type="text"
                                        value={data.judul}
                                        onChange={(event) => setData('judul', event.target.value)}
                                        placeholder="Contoh: Modul 1 - Pengenalan"
                                        className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    {errors.judul && <p className="mt-2 text-sm text-red-600">{errors.judul}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-slate-700">Urutan</label>
                                    <input
                                        type="number"
                                        min="1"
                                        value={data.urutan}
                                        onChange={(event) => setData('urutan', event.target.value)}
                                        className="mt-2 block w-24 rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    {errors.urutan && <p className="mt-2 text-sm text-red-600">{errors.urutan}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-sm font-bold text-slate-700">Konten materi</label>
                                <textarea
                                    rows={6}
                                    value={data.konten}
                                    onChange={(event) => setData('konten', event.target.value)}
                                    placeholder="Tulis ringkasan bacaan yang harus dibaca siswa sebelum mengerjakan soal."
                                    className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                {errors.konten && <p className="mt-2 text-sm text-red-600">{errors.konten}</p>}
                            </div>
                        </div>

                        <div className="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                            <div className="flex items-center justify-between">
                                <p className="text-sm font-bold text-slate-700">Soal pilihan ganda</p>
                                <button
                                    type="button"
                                    onClick={tambahSoal}
                                    className="rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-indigo-700"
                                >
                                    + Tambah Soal
                                </button>
                            </div>
                            {errors.soal && <p className="text-sm text-red-600">{errors.soal}</p>}

                            {data.soal.map((soal, index) => (
                                <div key={index} className="space-y-3 rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                                    <div className="flex items-center justify-between">
                                        <p className="text-xs font-bold uppercase tracking-wide text-indigo-600">Soal {index + 1}</p>
                                        {data.soal.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => hapusSoal(index)}
                                                className="text-xs font-bold text-red-600 hover:text-red-700"
                                            >
                                                Hapus
                                            </button>
                                        )}
                                    </div>

                                    <textarea
                                        rows={2}
                                        value={soal.pertanyaan}
                                        onChange={(event) => updateSoal(index, 'pertanyaan', event.target.value)}
                                        placeholder="Tulis pertanyaan"
                                        className="block w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    {errors[`soal.${index}.pertanyaan`] && (
                                        <p className="text-xs text-red-600">{errors[`soal.${index}.pertanyaan`]}</p>
                                    )}

                                    <div className="grid gap-3 sm:grid-cols-2">
                                        {['a', 'b', 'c', 'd'].map((opt) => (
                                            <div key={opt} className="flex items-center gap-2">
                                                <input
                                                    type="radio"
                                                    name={`jawaban_benar_${index}`}
                                                    checked={soal.jawaban_benar === opt}
                                                    onChange={() => updateSoal(index, 'jawaban_benar', opt)}
                                                    className="text-indigo-600 focus:ring-indigo-500"
                                                    title="Tandai sebagai jawaban benar"
                                                />
                                                <input
                                                    type="text"
                                                    value={soal[`pilihan_${opt}`]}
                                                    onChange={(event) => updateSoal(index, `pilihan_${opt}`, event.target.value)}
                                                    placeholder={`Pilihan ${opt.toUpperCase()}`}
                                                    className="block w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                />
                                            </div>
                                        ))}
                                    </div>
                                    <p className="text-xs font-medium text-slate-500">Pilih radio di depan opsi untuk menandai jawaban yang benar.</p>
                                </div>
                            ))}
                        </div>

                        <div className="flex gap-3 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex flex-1 items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:opacity-60"
                            >
                                Simpan Modul
                            </button>
                            <Link
                                href={route('admin.courses.quizzes.index', course.id)}
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
