import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function EnrollmentShow({ enrollment }) {
    const course = enrollment.course;
    const materials = course.materials || [];

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                        <div className="grid gap-8 lg:grid-cols-[1fr_0.7fr] lg:items-end">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-200">Detail kelas</p>
                                <h1 className="mt-3 text-4xl font-extrabold tracking-tight">{course.nama_kursus}</h1>
                                <p className="mt-4 max-w-2xl text-slate-300">
                                    {course.deskripsi || 'Lanjutkan materi, kerjakan kuis, dan pantau masa akses paket belajar kamu.'}
                                </p>
                            </div>
                            <div className="rounded-3xl bg-white/10 p-5 ring-1 ring-white/10">
                                <p className="text-sm font-bold text-indigo-100">Sisa waktu belajar</p>
                                <p className="mt-2 text-4xl font-extrabold">
                                    {enrollment.sudah_selesai ? 'Expired' : `${enrollment.sisa_hari} hari`}
                                </p>
                                <div className="mt-5 h-2.5 rounded-full bg-white/20">
                                    <div className="h-2.5 rounded-full bg-white" style={{ width: `${enrollment.time_progress}%` }}></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section className="grid gap-4 md:grid-cols-4">
                        {[
                            ['Tutor Pengajar', course.tutor?.name || 'Tutor EDUXCHANGE'],
                            ['Kategori', course.kategori || 'Paket Belajar'],
                            ['Kuis Selesai', `${enrollment.kuis_selesai} / ${enrollment.total_kuis}`],
                            ['Sertifikat', enrollment.bisa_unduh_sertifikat ? 'Tersedia' : 'Belum tersedia'],
                        ].map(([label, value]) => (
                            <div key={label} className="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                                <p className="text-sm font-bold text-slate-500">{label}</p>
                                <p className="mt-2 text-xl font-extrabold text-slate-950">{value}</p>
                            </div>
                        ))}
                    </section>

                    <section className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-700">Kuis dan sertifikat</p>
                                <h2 className="mt-2 text-2xl font-extrabold text-slate-950">Checkpoint penyelesaian kelas</h2>
                                <p className="mt-2 text-sm text-slate-600">
                                    {enrollment.can_access_content
                                        ? `Ada ${enrollment.total_kuis} kuis checkpoint di paket ini. Selesaikan semuanya untuk membuka sertifikat.`
                                        : 'Masa akses paket sudah habis. Daftar ulang untuk membuka materi dan kuis lagi.'}
                                </p>
                            </div>
                            <div className="flex flex-col gap-3 sm:flex-row">
                                {enrollment.can_access_content && enrollment.total_kuis > 0 && (
                                    <a
                                        href={route('quiz.index', enrollment.id)}
                                        className="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        {enrollment.kuis_selesai > 0 ? 'Lanjutkan Kuis' : 'Kerjakan Kuis'}
                                    </a>
                                )}

                                {!enrollment.can_access_content && (
                                    <a
                                        href={route('courses.index')}
                                        className="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        Daftar Ulang Paket
                                    </a>
                                )}

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
                        </div>
                    </section>

                    <section className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div className="mb-6 flex items-center justify-between gap-4">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-700">Materi pembelajaran</p>
                                <h2 className="mt-2 text-2xl font-extrabold text-slate-950">Daftar materi</h2>
                            </div>
                            <span className="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{materials.length} materi</span>
                        </div>

                        <div className="space-y-3">
                            {!enrollment.can_access_content ? (
                                <div className="rounded-3xl border border-dashed border-amber-300 bg-amber-50 p-8 text-center">
                                    <h3 className="font-extrabold text-slate-950">Akses materi sudah berakhir</h3>
                                    <p className="mt-2 text-sm text-slate-600">Paket belajar ini sudah melewati masa akses 30 hari.</p>
                                    <a
                                        href={route('courses.index')}
                                        className="mt-5 inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                                    >
                                        Perpanjang Akses
                                    </a>
                                </div>
                            ) : materials.length > 0 ? (
                                materials.map((material, index) => (
                                    <details key={material.id} className="group rounded-2xl border border-slate-200 p-5 open:bg-slate-50">
                                        <summary className="cursor-pointer list-none font-extrabold text-slate-950">
                                            {index + 1}. {material.judul}
                                        </summary>
                                        <div className="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                                            {material.konten}
                                        </div>
                                    </details>
                                ))
                            ) : (
                                <div className="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                                    <h3 className="font-extrabold text-slate-950">Belum ada materi</h3>
                                    <p className="mt-2 text-sm text-slate-600">Materi untuk paket ini akan tampil di sini.</p>
                                </div>
                            )}
                        </div>
                    </section>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
