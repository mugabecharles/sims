import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { useUrl } from '@/utils/route';

interface DashboardStats {
    total_learners?: number;
    total_outstanding?: number;
    payments_today?: number;
}

interface Props extends PageProps {
    stats: DashboardStats;
}

function StatCard({ label, value, color }: { label: string; value: string | number; color: string }) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <p className="text-sm text-gray-500 mb-1">{label}</p>
            <p className={`text-2xl font-bold ${color}`}>{value}</p>
        </div>
    );
}

export default function Dashboard() {
    const { stats, school } = usePage<Props>().props;
    const { url } = useUrl();

    const formatCurrency = (n?: number) =>
        n != null ? `UGX ${n.toLocaleString()}` : '—';

    const quickActions = [
        { label: 'Admit Learner',     path: '/admissions/create' },
        { label: 'Record Attendance', path: '/attendance' },
        { label: 'Enter Marks',       path: '/academics' },
        { label: 'Record Payment',    path: '/finance/payments/create' },
        { label: 'Send SMS',          path: '/communication' },
        { label: 'View Reports',      path: '/reports' },
    ];

    return (
        <AppLayout>
            <Head title="Dashboard" />

            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-800">
                    {school ? school.name : 'Dashboard'}
                </h1>
                <p className="text-sm text-gray-500 mt-1">Welcome back. Here's what's happening today.</p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
                <StatCard label="Enrolled Learners"  value={stats.total_learners ?? 0}                  color="text-blue-700" />
                <StatCard label="Outstanding Fees"   value={formatCurrency(stats.total_outstanding)}   color="text-red-600" />
                <StatCard label="Payments Today"     value={formatCurrency(stats.payments_today)}      color="text-green-600" />
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <h2 className="text-base font-semibold text-gray-700 mb-3">Quick Actions</h2>
                    <div className="grid grid-cols-2 gap-3">
                        {quickActions.map((a) => (
                            <a
                                key={a.path}
                                href={url(a.path)}
                                className="text-sm text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg px-3 py-2 transition-colors text-center"
                            >
                                {a.label}
                            </a>
                        ))}
                    </div>
                </div>

                <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                    <h2 className="text-base font-semibold text-gray-700 mb-3">System Info</h2>
                    <dl className="space-y-2 text-sm">
                        <div className="flex justify-between">
                            <dt className="text-gray-500">School Level</dt>
                            <dd className="font-medium capitalize">{school?.level ?? '—'}</dd>
                        </div>
                        <div className="flex justify-between">
                            <dt className="text-gray-500">School Type</dt>
                            <dd className="font-medium capitalize">{school?.school_type ?? '—'}</dd>
                        </div>
                        <div className="flex justify-between">
                            <dt className="text-gray-500">Currency</dt>
                            <dd className="font-medium">{school?.currency ?? 'UGX'}</dd>
                        </div>
                        <div className="flex justify-between">
                            <dt className="text-gray-500">Timezone</dt>
                            <dd className="font-medium">{school?.timezone ?? 'Africa/Kampala'}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </AppLayout>
    );
}