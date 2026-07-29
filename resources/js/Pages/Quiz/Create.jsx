import { Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const optionLabels = ['a', 'b', 'c', 'd'];

export default function QuizCreate({ enrollment, quiz, totalKuis = 0, questions = [], jawabanSebelumnya = {} }) {
    const initialAnswers = questions.reduce((answers, question) => ({
        ...answers,
        [question.id]: jawabanSebelumnya[question.id] || '',
    }), {});

    const { data, setData, post, processing, errors } = useForm({
        jawaban: initialAnswers,
    });

    const answeredCount = Object.values(data.jawaban).filter(Boolean).length;
    const progress = questions.length > 0 ? Math.round((answeredCount / questions.length) * 100) : 0;

    const handleSubmit = (event) => {
        event.preventDefault();
        post(route('quiz.store', [enrollment.id, quiz.id]));
    };

    const chooseAnswer = (questionId, option) => {
        setData('jawaban', {
            ...data.jawaban,
            [questionId]: option,
        });
    };

    return (
        <AuthenticatedLayout>
            <div className="py-8">
                <div className="mx-auto max-w-4xl space-y-8 px-4 sm:px-6 lg:px-8">
                    <section className="rounded-[2rem] bg-slate-950 p-8 text-white shadow-xl shadow-slate-200">
                        <div className="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-wide text-indigo-200">
                                    {enrollment.course.nama_kursus} · Kuis {quiz.urutan} dari {totalKuis}
                                </p>
                                <h1 className="mt-3 text-4xl font-extrabold tracking-tight">{quiz.judul}</h1>
                                <p className="mt-4 max-w-2xl text-slate-300">
                                    Jawab semua soal pilihan ganda. Nilai kuis ini dihitung otomatis setelah submit.
                                </p>
                            </div>
                            <div className="rounded-3xl bg-white/10 p-5 ring-1 ring-white/10">
                                <p className="text-sm font-bold text-indigo-100">Terjawab</p>
                                <p className="mt-2 text-4xl font-extrabold">{answeredCount}/{questions.length}</p>
                                <div className="mt-5 h-2.5 rounded-full bg-white/20">
                                    <div className="h-2.5 rounded-full bg-white" style={{ width: `${progress}%` }}></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        {questions.map((question, index) => (
                            <section key={question.id} className="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                                <div className="flex items-start gap-4">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-sm font-extrabold text-indigo-700">
                                        {index + 1}
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="text-lg font-extrabold leading-7 text-slate-950">{question.pertanyaan}</p>

                                        <div className="mt-5 space-y-3">
                                            {optionLabels.map((option) => {
                                                const selected = data.jawaban[question.id] === option;

                                                return (
                                                    <label
                                                        key={option}
                                                        className={`flex cursor-pointer items-start gap-3 rounded-2xl border p-4 transition ${
                                                            selected
                                                                ? 'border-indigo-300 bg-indigo-50'
                                                                : 'border-slate-200 hover:border-indigo-200 hover:bg-slate-50'
                                                        }`}
                                                    >
                                                        <input
                                                            type="radio"
                                                            name={`jawaban[${question.id}]`}
                                                            value={option}
                                                            checked={selected}
                                                            onChange={() => chooseAnswer(question.id, option)}
                                                            className="mt-1 text-indigo-600 focus:ring-indigo-600"
                                                            required
                                                        />
                                                        <span className="text-sm leading-6 text-slate-700">
                                                            <span className="font-extrabold text-slate-950">{option.toUpperCase()}.</span> {question.options[option]}
                                                        </span>
                                                    </label>
                                                );
                                            })}
                                        </div>

                                        {errors[`jawaban.${question.id}`] && (
                                            <p className="mt-3 text-sm font-semibold text-rose-600">{errors[`jawaban.${question.id}`]}</p>
                                        )}
                                    </div>
                                </div>
                            </section>
                        ))}

                        {errors.jawaban && (
                            <div className="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                                {errors.jawaban}
                            </div>
                        )}

                        <div className="flex flex-col-reverse justify-between gap-3 sm:flex-row sm:items-center">
                            <Link
                                href={route('quiz.index', enrollment.id)}
                                className="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700"
                            >
                                Kembali ke Daftar Kuis
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-indigo-300"
                            >
                                {processing ? 'Menyimpan...' : 'Selesai & Lihat Nilai'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
