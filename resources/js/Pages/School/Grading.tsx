import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface GradeRule { min: number; max: number; grade: string; points: number | null; remark: string; }
interface Scheme { id: number; name: string; level: string; is_default: boolean; rules: GradeRule[]; }
interface AcademicYear { id: number; year: string; }

export default function Grading({ schemes, academicYears }: { schemes: Scheme[]; academicYears: AcademicYear[] }) {
    const { url } = useUrl();
    const [showForm, setShowForm] = useState(false);
    const [expanded, setExpanded] = useState<number | null>(null);

    const defaultRules: GradeRule[] = [
        { min: 75, max: 100, grade: 'D1', points: 1, remark: 'Distinction 1' },
        { min: 70, max: 74,  grade: 'D2', points: 2, remark: 'Distinction 2' },
        { min: 65, max: 69,  grade: 'C3', points: 3, remark: 'Credit 3' },
        { min: 60, max: 64,  grade: 'C4', points: 4, remark: 'Credit 4' },
        { min: 55, max: 59,  grade: 'C5', points: 5, remark: 'Credit 5' },
        { min: 50, max: 54,  grade: 'C6', points: 6, remark: 'Credit 6' },
        { min: 45, max: 49,  grade: 'P7', points: 7, remark: 'Pass 7' },
        { min: 40, max: 44,  grade: 'P8', points: 8, remark: 'Pass 8' },
        { min: 0,  max: 39,  grade: 'F9', points: 9, remark: 'Fail 9' },
    ];

    const { data, setData, post, processing, reset } = useForm({
        name: '',
        academic_year_id: academicYears[0]?.id?.toString() ?? '',
        level: 'secondary',
        rules: defaultRules,
        is_default: false,
    });

    const updateRule = (i: number, field: keyof GradeRule, value: string | number) => {
        const updated = [...data.rules];
        (updated[i] as any)[field] = value;
        setData('rules', updated);
    };

    return (
        <AppLayout>
            <Head title="Grading Schemes" />
            <div className="max-w-4xl">
                <div className="flex items-center justify-between mb-6">
                    <h1 className="text-2xl font-bold text-gray-800">Grading Schemes</h1>
                    <button onClick={() => setShowForm(!showForm)}
                        className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-800">+ New Scheme</button>
                </div>

                {showForm && (
                    <div className="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <h2 className="font-semibold text-gray-700 mb-4">New Grading Scheme</h2>
                        <form onSubmit={(e) => { e.preventDefault(); post(url('/school/grading'), { onSuccess: () => { reset(); setShowForm(false); } }); }}>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                    <input value={data.name} onChange={e => setData('name', e.target.value)}
                                        className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Level</label>
                                    <select value={data.level} onChange={e => setData('level', e.target.value)}
                                        className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <option value="both">Both</option>
                                        <option value="primary">Primary</option>
                                        <option value="secondary">Secondary</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                                    <select value={data.academic_year_id} onChange={e => setData('academic_year_id', e.target.value)}
                                        className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        {academicYears.map(y => <option key={y.id} value={y.id}>{y.year}</option>)}
                                    </select>
                                </div>
                            </div>
                            <div className="overflow-x-auto mb-4">
                                <table className="w-full text-sm">
                                    <thead><tr className="bg-gray-50">
                                        {['Min %', 'Max %', 'Grade', 'Points', 'Remark'].map(h => (
                                            <th key={h} className="text-left px-2 py-2 text-xs text-gray-500 font-semibold">{h}</th>
                                        ))}
                                    </tr></thead>
                                    <tbody>
                                        {data.rules.map((r, i) => (
                                            <tr key={i} className="border-t border-gray-100">
                                                <td className="px-2 py-1"><input type="number" value={r.min} onChange={e => updateRule(i, 'min', +e.target.value)} className="w-16 border border-gray-300 rounded px-2 py-1 text-sm" /></td>
                                                <td className="px-2 py-1"><input type="number" value={r.max} onChange={e => updateRule(i, 'max', +e.target.value)} className="w-16 border border-gray-300 rounded px-2 py-1 text-sm" /></td>
                                                <td className="px-2 py-1"><input value={r.grade} onChange={e => updateRule(i, 'grade', e.target.value)} className="w-16 border border-gray-300 rounded px-2 py-1 text-sm" /></td>
                                                <td className="px-2 py-1"><input type="number" value={r.points ?? ''} onChange={e => updateRule(i, 'points', +e.target.value)} className="w-16 border border-gray-300 rounded px-2 py-1 text-sm" /></td>
                                                <td className="px-2 py-1"><input value={r.remark} onChange={e => updateRule(i, 'remark', e.target.value)} className="w-32 border border-gray-300 rounded px-2 py-1 text-sm" /></td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <div className="flex justify-end gap-3">
                                <button type="button" onClick={() => setShowForm(false)} className="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
                                <button type="submit" disabled={processing} className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg disabled:opacity-50">
                                    {processing ? 'Saving…' : 'Save Scheme'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                <div className="space-y-3">
                    {schemes.map(scheme => (
                        <div key={scheme.id} className="bg-white rounded-xl border border-gray-200">
                            <div className="flex items-center justify-between px-5 py-4 cursor-pointer" onClick={() => setExpanded(expanded === scheme.id ? null : scheme.id)}>
                                <div className="flex items-center gap-3">
                                    <h3 className="font-semibold text-gray-800">{scheme.name}</h3>
                                    <span className="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded capitalize">{scheme.level}</span>
                                    {scheme.is_default && <span className="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded">Default</span>}
                                </div>
                                <span className="text-gray-400">{expanded === scheme.id ? '▲' : '▼'}</span>
                            </div>
                            {expanded === scheme.id && (
                                <div className="px-5 pb-4 overflow-x-auto">
                                    <table className="w-full text-sm">
                                        <thead><tr className="bg-gray-50">
                                            {['Grade', 'Min %', 'Max %', 'Points', 'Remark'].map(h => (
                                                <th key={h} className="text-left px-3 py-2 text-xs text-gray-500 font-semibold">{h}</th>
                                            ))}
                                        </tr></thead>
                                        <tbody className="divide-y divide-gray-100">
                                            {scheme.rules.map((r, i) => (
                                                <tr key={i}>
                                                    <td className="px-3 py-2 font-bold text-gray-800">{r.grade}</td>
                                                    <td className="px-3 py-2 text-gray-600">{r.min}%</td>
                                                    <td className="px-3 py-2 text-gray-600">{r.max}%</td>
                                                    <td className="px-3 py-2 text-gray-600">{r.points ?? '—'}</td>
                                                    <td className="px-3 py-2 text-gray-600">{r.remark}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}
