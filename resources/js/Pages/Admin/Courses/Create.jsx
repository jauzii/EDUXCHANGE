import { useState } from 'react';
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

export default function AdminCoursesCreate({ categories = [] }) {
    const [tambahMateri, setTambahMateri] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        tutor_nama: '',
        nama_kursus: '',
        kategori: '',
        harga: '',
        deskripsi: '',
        judul: '',
        konten: '',
        soal: [soalKosong()],
    });

    const submit = (event) => {
        event.preventDefault();
        post(route('admin.courses.store'), {
            transform: (formData) => (tambahMateri ? formData : { ...formData, judul: '', konten: '', soal: [] }),
        });
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
                        <Link href={route('admin.courses.index')} className="text-sm font-bold text-indigo-200 hover:text-white">
                            Kembali ke daftar paket
                        </Link>
                        <h1 className="mt-4 text-3xl font-extrabold tracking-tight">Tambah Paket Kursus</h1>
                        <p className="mt-3 text-slate-300">Paket baru akan langsung tampil di halaman paket belajar siswa.</p>
                    </section>

                    <form onSubmit={submit} className="space-y-8">
                        <div className="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
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
                                    placeholder="Contoh: Belajar Laravel dari Nol"
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
                                    placeholder="Contoh: Programming"
                                    className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <datalist id="kategori-options">
                                    {categories.map((category) => (
                                        <option key={category} value={category} />
                                    ))}
                                </datalist>
                                <p className="mt-2 text-xs font-medium text-slate-500">Pakai kategori yang sudah ada supaya konsisten dengan filter di halaman siswa.</p>
                                {errors.kategori && <p className="mt-2 text-sm text-red-600">{errors.kategori}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-bold text-slate-700">Harga (Rp)</label>
                                <input
                                    type="number"
                                    min="0"
                                    value={data.harga}
                                    onChange={(event) => setData('harga', event.target.value)}
                                    placeholder="150000"
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
                                    placeholder="Ceritakan singkat isi paket belajar ini."
                                    className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                {errors.deskripsi && <p className="mt-2 text-sm text-red-600">{errors.deskripsi}</p>}
                            </div>
                        </div>

                        <div className="rounded-3xl border border-indigo-100 bg-indigo-50/50 p-6 shadow-sm sm:p-8">
                            <label className="flex cursor-pointer items-start gap-3">
                                <input
                                    type="checkbox"
                                    checked={tambahMateri}
                                    onChange={(event) => setTambahMateri(event.target.checked)}
                                    className="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span>
                                    <span className="block text-sm font-bold text-slate-950">Sekalian tambahkan 1 modul materi & soal contoh</span>
                                    <span className="mt-1 block text-xs font-medium text-slate-600">
                                        Berguna untuk membuat contoh cepat yang bisa langsung dites siswa (materi → soal pilihan ganda → sertifikat).
                                        Bisa dilewati dan ditambahkan/diedit kapan saja nanti lewat tombol &quot;Kelola Materi &amp; Kuis&quot;.
                                    </span>
                                </span>
                            </label>

                            {tambahMateri && (
                                <div className="mt-6 space-y-6">
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
                                        <label className="block text-sm font-bold text-slate-700">Konten materi</label>
                                        <textarea
                                            rows={5}
                                            value={data.konten}
                                            onChange={(event) => setData('konten', event.target.value)}
                                            placeholder="Tulis ringkasan bacaan yang harus dibaca siswa sebelum mengerjakan soal."
                                            className="mt-2 block w-full rounded-2xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        {errors.konten && <p className="mt-2 text-sm text-red-600">{errors.konten}</p>}
                                    </div>

                                    <div className="space-y-4">
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

                                        {data.soal.map((soal, index) => (
                                            <div key={index} className="space-y-3 rounded-2xl border border-slate-200 bg-white p-4">
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

                                    <div className="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800">
                                        Sertifikat tidak diisi manual di sini — sertifikat PDF dibuat otomatis oleh sistem begitu siswa
                                        menyelesaikan semua modul kuis paket ini. Setelah paket tersimpan, kamu bisa cek contoh
                                        tampilan sertifikatnya lewat tombol &quot;Lihat Contoh Sertifikat&quot; di halaman Kelola Materi &amp; Kuis.
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="flex gap-3 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex flex-1 items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:opacity-60"
                            >
                                Simpan Paket
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
