import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Term { id: number; name: string; term_no: number; start_date: string; end_date: string; status: string; is_current: boolean; }
interface AcademicYear { id: number; year: string; start_date: string; end_date: string; status: string; is_current: boolean; terms: Term[]; }

export default function AcademicYears({ academicYears }: { academicYears: AcademicYear[] }) {
    const { url } = useUrl();
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({ year: '', start_date: '', end_date: '' });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(url('/school/academic-years'), { onSuccess: () => { reset(); setShowForm(false); } });
    };

    return (
        <AppLayout>
            <Head title="Academic Years" />
            <div className="max-w-4xl">
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">Academic Years</h1>
                    <button onClick={() => setShowForm(!showForm)}
                        className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-800">
                        + New Academic Year
                    </button>
                </div>

                {showForm && (
                    <div className="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <h2 className="text-base font-semibold mb-4">New Academic Year</h2>
                        <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                <input type="text" value={data.year} onChange={e => setData('year', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="2027" required />
                                {errors.year && <p className="text-red-600 text-xs mt-1">{errors.year}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" value={data.start_date} onChange={e => setData('start_date', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input type="date" value={data.end_date} onChange={e => setData('end_date', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required />
                            </div>
                            <div className="md:col-span-3 flex gap-3 justify-end">
                                <button type="button" onClick={() => setShowForm(false)} className="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
                                <button type="submit" disabled={processing} className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg disabled:opacity-50">
                                    {processing ? 'Creating…' : 'Create Year'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                <div className="space-y-4">
                    {academicYears.map(year => (
                        <div key={year.id} className="bg-white rounded-xl border border-gray-200 p-5">
                            <div className="flex items-center justify-between mb-3">
                                <div className="flex items-center gap-3">
                                    <h3 className="font-semibold text-gray-800">Academic Year {year.year}</h3>
                                    {year.is_current && <span className="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Current</span>}
                                    <span className={`text-xs px-2 py-0.5 rounded-full capitalize ${year.status === 'active' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'}`}>
                                        {year.status}
                                    </span>
                                </div>
                                {!year.is_current && (
                                    <button onClick={() => router.post(url(`/school/academic-years/${year.id}/set-current`))}
                                        className="text-xs text-blue-600 hover:underline">Set as Current</button>
                                )}
                            </div>
                            <p className="text-sm text-gray-500 mb-3">{year.start_date} → {year.end_date}</p>
                            <div className="grid grid-cols-3 gap-2">
                                {year.terms?.map(term => (
                                    <div key={term.id} className="bg-gray-50 rounded-lg px-3 py-2 text-sm">
                                        <div className="flex items-center gap-2 font-medium">
                                            {term.name}
                                            {term.is_current && <span className="bg-green-100 text-green-600 text-xs px-1.5 rounded">Now</span>}
                                        </div>
                                        <div className="text-xs text-gray-500 mt-0.5">{term.start_date} → {term.end_date}</div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}
                    {academicYears.length === 0 && (
                        <div className="text-center py-12 text-gray-400">No academic years configured yet.</div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
