import React, { useState, PropsWithChildren } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { PageProps } from '@/types';
import { useUrl } from '@/utils/route';

const NAV_ITEMS = [
    { label: 'Dashboard',     path: '/',                    icon: '🏠', permission: null },
    { label: 'Admissions',    path: '/admissions',          icon: '📋', permission: 'admissions.view' },
    { label: 'Learners',      path: '/learners',            icon: '👩‍🎓', permission: 'learners.view' },
    { label: 'Staff',         path: '/staff',               icon: '👨‍🏫', permission: 'staff.view' },
    { label: 'Academics',     path: '/academics',           icon: '📚', permission: 'assessments.view' },
    { label: 'Attendance',    path: '/attendance',          icon: '✅', permission: 'attendance.view' },
    { label: 'Examinations',  path: '/examinations',        icon: '📝', permission: 'uneb.view' },
    { label: 'Finance',       path: '/finance',             icon: '💰', permission: 'finance.view' },
    { label: 'Communication', path: '/communication',       icon: '📱', permission: 'sms.view' },
    { label: 'Boarding',      path: '/boarding',            icon: '🛏️', permission: 'boarding.view' },
    { label: 'Library',       path: '/library',             icon: '📖', permission: 'library.view' },
    { label: 'Inventory',     path: '/inventory',           icon: '📦', permission: 'inventory.view' },
    { label: 'Transport',     path: '/transport',           icon: '🚌', permission: 'transport.view' },
    { label: 'Reports',       path: '/reports',             icon: '📊', permission: 'reports.view' },
    { label: 'School Setup',  path: '/school/profile',      icon: '⚙️',  permission: 'school.view' },
    { label: 'Admin',         path: '/admin/users',         icon: '🔧', permission: 'users.view' },
];

export default function AppLayout({ children }: PropsWithChildren) {
    const { auth, flash, school } = usePage<PageProps>().props;
    const { url } = useUrl();
    const [sidebarOpen, setSidebarOpen] = useState(true);
    const permissions = auth.user?.permissions ?? [];

    const visibleNav = NAV_ITEMS.filter(
        (item) => !item.permission || permissions.includes(item.permission)
    );

    const handleLogout = () => {
        router.post(url('/logout'));
    };

    return (
        <div className="flex h-screen bg-gray-50 overflow-hidden">
            {/* Sidebar */}
            <aside
                className={`${sidebarOpen ? 'w-64' : 'w-16'} bg-blue-900 text-white flex flex-col transition-all duration-200 flex-shrink-0`}
            >
                {/* Logo */}
                <div className="flex items-center gap-3 px-4 py-4 border-b border-blue-800">
                    {school?.logo_url ? (
                        <img src={school.logo_url} alt="logo" className="w-8 h-8 rounded object-cover" />
                    ) : (
                        <div className="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-sm font-bold">
                            {(school?.short_name ?? 'S')[0]}
                        </div>
                    )}
                    {sidebarOpen && (
                        <span className="font-semibold text-sm truncate">
                            {school?.name ?? 'SIMS'}
                        </span>
                    )}
                </div>

                {/* Navigation */}
                <nav className="flex-1 overflow-y-auto py-3">
                    {visibleNav.map((item) => (
                        <Link
                            key={item.path}
                            href={url(item.path)}
                            className="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-blue-800 transition-colors"
                        >
                            <span className="text-base">{item.icon}</span>
                            {sidebarOpen && <span>{item.label}</span>}
                        </Link>
                    ))}
                </nav>

                {/* Collapse toggle */}
                <button
                    onClick={() => setSidebarOpen(!sidebarOpen)}
                    className="px-4 py-3 border-t border-blue-800 text-xs text-blue-300 hover:text-white text-left"
                >
                    {sidebarOpen ? '◀ Collapse' : '▶'}
                </button>
            </aside>

            {/* Main area */}
            <div className="flex-1 flex flex-col overflow-hidden">
                {/* Top bar */}
                <header className="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
                    <div className="text-sm text-gray-500">School Information Management System</div>
                    <div className="flex items-center gap-4">
                        <span className="text-sm text-gray-700">{auth.user?.name}</span>
                        <button
                            onClick={handleLogout}
                            className="text-sm text-red-600 hover:text-red-800"
                        >
                            Logout
                        </button>
                    </div>
                </header>

                {/* Flash messages */}
                {(flash.success || flash.error || flash.warning || flash.info) && (
                    <div className="px-6 pt-4 space-y-2">
                        {flash.success && (
                            <div className="bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded text-sm">
                                ✓ {flash.success}
                            </div>
                        )}
                        {flash.error && (
                            <div className="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded text-sm">
                                ✗ {flash.error}
                            </div>
                        )}
                        {flash.warning && (
                            <div className="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-2 rounded text-sm">
                                ⚠ {flash.warning}
                            </div>
                        )}
                        {flash.info && (
                            <div className="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-2 rounded text-sm">
                                ℹ {flash.info}
                            </div>
                        )}
                    </div>
                )}

                {/* Page content */}
                <main className="flex-1 overflow-y-auto p-6">
                    {children}
                </main>
            </div>
        </div>
    );
}
