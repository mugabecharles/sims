import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Term { id: number; name: string; term_no: number; start_date: string; end_date: string; status: string; is_current: boolean; }
interface AcademicYear { id: number; year: string; is_current: boolean; terms: Term[]; }

export default function Terms({ academicYears }: { academicYears: AcademicYear[] }) {
    const { url } = useUrl();
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        academic_year_id: academicYears.find(y => y.is_current)?.id?.toString() ?? '',
        name: '',
        term_no: '1',
        start_date: '',
        end_date: '',
    });

    const statusColor = (s: string) =>
        s === 'active' ? 'bg-green-100 text-green-700' :
        s === 'completed' ? 'bg-gray-100 text-gray-500' :
        'bg-yellow-100 text-yellow-700';

    return (
        <AppLayout>
            <Head title="Terms" />
            <div className="max-w-4xl">
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">Terms</h1>
                    <button onClick={() => setShowForm(!showForm)}
                        className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-800">
                        + Add Term
                    </button>
                </div>

                {showForm && (
                    <div className="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <h2 className="font-semibold text-gray-700 mb-4">New Term</h2>
                        <form onSubmit={(e) => { e.preventDefault(); post(url('/school/terms'), { onSuccess: () => { reset(); setShowForm(false); } }); }}
                            className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                                <select value={data.academic_year_id} onChange={e => setData('academic_year_id', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                                    <option value="">Select year…</option>
                                    {academicYears.map(y => <option key={y.id} value={y.id}>{y.year}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Term Number</label>
                                <select value={data.term_no} onChange={e => setData('term_no', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="1">Term 1</option>
                                    <option value="2">Term 2</option>
                                    <option value="3">Term 3</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Term Name</label>
                                <input value={data.name} onChange={e => setData('name', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Term 1" required />
                            </div>
                            <div></div>
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
                            <div className="md:col-span-2 flex gap-3 justify-end">
                                <button type="button" onClick={() => setShowForm(false)} className="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
                                <button type="submit" disabled={processing} className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg disabled:opacity-50">
                                    {processing ? 'Saving…' : 'Add Term'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                {academicYears.map(year => (
                    <div key={year.id} className="mb-6">
                        <div className="flex items-center gap-2 mb-3">
                            <h2 className="font-semibold text-gray-700">Year {year.year}</h2>
                            {year.is_current && <span className="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Current</span>}
                        </div>
                        <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <table className="w-full text-sm">
                                <thead className="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        {['Term', 'Start', 'End', 'Status'].map(h => (
                                            <th key={h} className="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100">
                                    {year.terms.map(term => (
                                        <tr key={term.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 font-medium">
                                                {term.name}
                                                {term.is_current && <span className="ml-2 bg-green-100 text-green-600 text-xs px-1.5 rounded">Now</span>}
                                            </td>
                                            <td className="px-4 py-3 text-gray-600">{term.start_date}</td>
                                            <td className="px-4 py-3 text-gray-600">{term.end_date}</td>
                                            <td className="px-4 py-3">
                                                <span className={`text-xs px-2 py-0.5 rounded-full capitalize ${statusColor(term.status)}`}>{term.status}</span>
                                            </td>
                                        </tr>
                                    ))}
                                    {year.terms.length === 0 && (
                                        <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-400">No terms added yet.</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                ))}
            </div>
        </AppLayout>
    );
}
