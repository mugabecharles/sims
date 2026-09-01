import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Stream { id: number; name: string; capacity: number; }
interface ClassData { id: number; name: string; display_name: string | null; level: string; section: string; sort_order: number; active: boolean; streams: Stream[]; }

export default function Classes({ classes }: { classes: ClassData[] }) {
    const { url } = useUrl();
    const [showForm, setShowForm] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '', display_name: '', level: 'secondary', section: 'o_level', sort_order: '0',
    });

    const levelBadge = (l: string) => l === 'primary' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';

    return (
        <AppLayout>
            <Head title="Classes" />
            <div className="max-w-4xl">
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">Classes & Streams</h1>
                    <button onClick={() => setShowForm(!showForm)}
                        className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-800">
                        + Add Class
                    </button>
                </div>

                {showForm && (
                    <div className="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <h2 className="font-semibold text-gray-700 mb-4">New Class</h2>
                        <form onSubmit={(e) => { e.preventDefault(); post(url('/school/classes'), { onSuccess: () => { reset(); setShowForm(false); } }); }}
                            className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Class Name *</label>
                                <input value={data.name} onChange={e => setData('name', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="P1, S1, etc." required />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                                <input value={data.display_name} onChange={e => setData('display_name', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Primary One" />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Level</label>
                                <select value={data.level} onChange={e => setData('level', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Section</label>
                                <select value={data.section} onChange={e => setData('section', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="primary">Primary</option>
                                    <option value="o_level">O-Level</option>
                                    <option value="a_level">A-Level</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                                <input type="number" value={data.sort_order} onChange={e => setData('sort_order', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />
                            </div>
                            <div className="flex items-end gap-3">
                                <button type="button" onClick={() => setShowForm(false)} className="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
                                <button type="submit" disabled={processing} className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg disabled:opacity-50">
                                    {processing ? 'Creating…' : 'Create Class'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50 border-b border-gray-200">
                            <tr>
                                {['Class', 'Display Name', 'Level', 'Section', 'Streams', 'Status'].map(h => (
                                    <th key={h} className="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">{h}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {classes.map(cls => (
                                <tr key={cls.id} className="hover:bg-gray-50">
                                    <td className="px-4 py-3 font-semibold text-gray-800">{cls.name}</td>
                                    <td className="px-4 py-3 text-gray-600">{cls.display_name ?? '—'}</td>
                                    <td className="px-4 py-3">
                                        <span className={`text-xs px-2 py-0.5 rounded-full capitalize ${levelBadge(cls.level)}`}>{cls.level}</span>
                                    </td>
                                    <td className="px-4 py-3 text-gray-600 capitalize">{cls.section.replace('_', '-')}</td>
                                    <td className="px-4 py-3">
                                        <div className="flex flex-wrap gap-1">
                                            {cls.streams.map(s => (
                                                <span key={s.id} className="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">{s.name}</span>
                                            ))}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <span className={`text-xs px-2 py-0.5 rounded-full ${cls.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>
                                            {cls.active ? 'Active' : 'Inactive'}
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
