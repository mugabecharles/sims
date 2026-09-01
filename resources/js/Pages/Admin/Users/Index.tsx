import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Role { id: number; name: string; display_name: string; }
interface User { id: number; name: string; email: string | null; username: string | null; status: string; roles: Role[]; deleted_at: string | null; }

export default function UsersIndex({ users }: { users: { data: User[]; current_page: number; last_page: number } }) {
    const { url } = useUrl();

    const statusColor = (s: string) =>
        s === 'active' ? 'bg-green-100 text-green-700' :
        s === 'inactive' ? 'bg-gray-100 text-gray-500' :
        'bg-red-100 text-red-600';

    return (
        <AppLayout>
            <Head title="Users" />
            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-bold text-gray-800">System Users</h1>
                <Link href={url('/admin/users/create')} className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-800">
                    + New User
                </Link>
            </div>

            <div className="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table className="w-full text-sm">
                    <thead className="bg-gray-50 border-b border-gray-200">
                        <tr>
                            {['Name', 'Email', 'Username', 'Roles', 'Status', 'Actions'].map(h => (
                                <th key={h} className="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">{h}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {users.data.map(user => (
                            <tr key={user.id} className="hover:bg-gray-50">
                                <td className="px-4 py-3 font-medium text-gray-800">
                                    {user.name}
                                    {user.deleted_at && <span className="ml-2 text-xs text-gray-400">(archived)</span>}
                                </td>
                                <td className="px-4 py-3 text-gray-600">{user.email ?? '—'}</td>
                                <td className="px-4 py-3 text-gray-600">{user.username ?? '—'}</td>
                                <td className="px-4 py-3">
                                    <div className="flex flex-wrap gap-1">
                                        {user.roles.map(r => (
                                            <span key={r.id} className="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded">{r.display_name}</span>
                                        ))}
                                    </div>
                                </td>
                                <td className="px-4 py-3">
                                    <span className={`text-xs px-2 py-0.5 rounded-full capitalize ${statusColor(user.status)}`}>{user.status}</span>
                                </td>
                                <td className="px-4 py-3">
                                    <Link href={url(`/admin/users/${user.id}/edit`)} className="text-blue-600 hover:underline text-xs mr-3">Edit</Link>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {users.data.length === 0 && (
                    <div className="text-center py-12 text-gray-400">No users found.</div>
                )}
            </div>
        </AppLayout>
    );
}
