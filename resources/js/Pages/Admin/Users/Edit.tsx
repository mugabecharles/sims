import React from 'react';
import { Head, useForm, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Role { id: number; name: string; display_name: string; }
interface UserData { id: number; name: string; username: string | null; email: string | null; phone: string | null; status: string; roles: Role[]; }

export default function EditUser({ user, roles }: { user: UserData; roles: Role[] }) {
    const { url } = useUrl();
    const { data, setData, put, processing, errors } = useForm({
        name: user.name,
        username: user.username ?? '',
        email: user.email ?? '',
        phone: user.phone ?? '',
        status: user.status,
    });

    const [selectedRoles, setSelectedRoles] = React.useState<number[]>(user.roles.map(r => r.id));

    const syncRoles = () => {
        router.post(url(`/admin/users/${user.id}/roles`), { roles: selectedRoles }, {
            onSuccess: () => {},
        });
    };

    return (
        <AppLayout>
            <Head title={`Edit ${user.name}`} />
            <div className="max-w-2xl">
                <div className="flex items-center gap-3 mb-6">
                    <Link href={url('/admin/users')} className="text-sm text-gray-500 hover:text-gray-700">← Users</Link>
                    <span className="text-gray-300">/</span>
                    <h1 className="text-xl font-bold text-gray-800">Edit User</h1>
                </div>

                <form onSubmit={(e) => { e.preventDefault(); put(url(`/admin/users/${user.id}`)); }} className="space-y-5">
                    <div className="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                        <h2 className="font-semibold text-gray-700">Account Details</h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input value={data.name} onChange={e => setData('name', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required />
                                {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input value={data.username} onChange={e => setData('username', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                {errors.username && <p className="text-red-500 text-xs mt-1">{errors.username}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" value={data.email} onChange={e => setData('email', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="tel" value={data.phone} onChange={e => setData('phone', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                                {errors.phone && <p className="text-red-500 text-xs mt-1">{errors.phone}</p>}
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select value={data.status} onChange={e => setData('status', e.target.value)}
                                className="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                                <option value="locked">Locked</option>
                            </select>
                        </div>
                    </div>

                    <div className="flex justify-end gap-3">
                        <Link href={url('/admin/users')} className="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</Link>
                        <button type="submit" disabled={processing}
                            className="bg-blue-700 text-white text-sm px-6 py-2 rounded-lg hover:bg-blue-800 disabled:opacity-50">
                            {processing ? 'Saving…' : 'Save Changes'}
                        </button>
                    </div>
                </form>

                {/* Roles section */}
                <div className="bg-white rounded-xl border border-gray-200 p-6 mt-5">
                    <h2 className="font-semibold text-gray-700 mb-3">Roles</h2>
                    <div className="grid grid-cols-2 gap-2 mb-4">
                        {roles.map(role => (
                            <label key={role.id} className="flex items-center gap-2 text-sm cursor-pointer p-2 rounded hover:bg-gray-50">
                                <input type="checkbox"
                                    checked={selectedRoles.includes(role.id)}
                                    onChange={e => setSelectedRoles(prev =>
                                        e.target.checked ? [...prev, role.id] : prev.filter(id => id !== role.id)
                                    )}
                                    className="rounded text-blue-600" />
                                {role.display_name}
                            </label>
                        ))}
                    </div>
                    <button onClick={syncRoles} className="bg-gray-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-gray-800">
                        Update Roles
                    </button>
                </div>
            </div>
        </AppLayout>
    );
}
