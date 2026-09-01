import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Subject { id: number; code: string | null; name: string; level: string; subject_type: string; department: string | null; active: boolean; }

export default function Subjects({ subjects }: { subjects: Subject[] }) {
    const { url } = useUrl();
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        code: '', name: '', level: 'both', subject_type: 'compulsory', department: '',
    });

    return (
        <AppLayout>
            <Head title="Subjects" />
            <div className="max-w-4xl">
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">Subject Catalogue</h1>
                    <button onClick={() => setShowForm(!showForm)}
                        className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-800">+ Add Subject</button>
                </div>

                {showForm && (
                    <div className="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <form onSubmit={(e) => { e.preventDefault(); post(url('/school/subjects'), { onSuccess: () => { reset(); setShowForm(false); } }); }}
                            className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Code</label>
                                <input value={data.code} onChange={e => setData('code', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="ENG" />
                            </div>
                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">Subject Name *</label>
                                <input value={data.name} onChange={e => setData('name', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Level</label>
                                <select value={data.level} onChange={e => setData('level', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="both">Both (Primary & Secondary)</option>
                                    <option value="primary">Primary Only</option>
                                    <option value="secondary">Secondary Only</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select value={data.subject_type} onChange={e => setData('subject_type', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="compulsory">Compulsory</option>
                                    <option value="optional">Optional</option>
                                    <option value="elective">Elective</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <input value={data.department} onChange={e => setData('department', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Sciences, Arts…" />
                            </div>
                            <div className="md:col-span-3 flex gap-3 justify-end">
                                <button type="button" onClick={() => setShowForm(false)} className="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
                                <button type="submit" disabled={processing} className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg disabled:opacity-50">
                                    {processing ? 'Saving…' : 'Add Subject'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50 border-b border-gray-200">
                            <tr>
                                {['Code', 'Subject Name', 'Level', 'Type', 'Department', 'Status'].map(h => (
                                    <th key={h} className="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {subjects.map(s => (
                                <tr key={s.id} className="hover:bg-gray-50">
                                    <td className="px-4 py-3 font-mono text-xs text-gray-500">{s.code ?? '—'}</td>
                                    <td className="px-4 py-3 font-medium text-gray-800">{s.name}</td>
                                    <td className="px-4 py-3 capitalize text-gray-600">{s.level}</td>
                                    <td className="px-4 py-3 capitalize text-gray-600">{s.subject_type}</td>
                                    <td className="px-4 py-3 text-gray-600">{s.department ?? '—'}</td>
                                    <td className="px-4 py-3">
                                        <span className={`text-xs px-2 py-0.5 rounded-full ${s.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>
                                            {s.active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
