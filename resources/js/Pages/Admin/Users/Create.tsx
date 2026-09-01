import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Role { id: number; name: string; display_name: string; }

export default function CreateUser({ roles }: { roles: Role[] }) {
    const { url } = useUrl();
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        username: '',
        email: '',
        phone: '',
        password: '',
        password_confirmation: '',
        status: 'active',
        roles: [] as number[],
    });

    const toggleRole = (id: number) => {
        setData('roles', data.roles.includes(id)
            ? data.roles.filter(r => r !== id)
            : [...data.roles, id]);
    };

    return (
        <AppLayout>
            <Head title="Create User" />
            <div className="max-w-2xl">
                <div className="flex items-center gap-3 mb-6">
                    <Link href="/admin/users" className="text-sm text-gray-500 hover:text-gray-700">← Users</Link>
                    <span className="text-gray-300">/</span>
                    <h1 className="text-xl font-bold text-gray-800">New User</h1>
                </div>

                <form onSubmit={(e) => { e.preventDefault(); post(url('/admin/users')); }} className="space-y-5">
                    <div className="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                        <h2 className="font-semibold text-gray-700 mb-2">Account Details</h2>

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
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                                <input type="password" value={data.password} onChange={e => setData('password', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required />
                                {errors.password && <p className="text-red-500 text-xs mt-1">{errors.password}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                                <input type="password" value={data.password_confirmation} onChange={e => setData('password_confirmation', e.target.value)}
                                    className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select value={data.status} onChange={e => setData('status', e.target.value)}
                                className="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div className="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 className="font-semibold text-gray-700 mb-3">Assign Roles</h2>
                        <div className="grid grid-cols-2 gap-2">
                            {roles.map(role => (
                                <label key={role.id} className="flex items-center gap-2 text-sm cursor-pointer p-2 rounded hover:bg-gray-50">
                                    <input type="checkbox" checked={data.roles.includes(role.id)} onChange={() => toggleRole(role.id)}
                                        className="rounded text-blue-600" />
                                    {role.display_name}
                                </label>
                            ))}
                        </div>
                    </div>

                    <div className="flex justify-end gap-3">
                        <Link href={url('/admin/users')} className="text-sm text-gray-600 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</Link>
                        <button type="submit" disabled={processing}
                            className="bg-blue-700 text-white text-sm px-6 py-2 rounded-lg hover:bg-blue-800 disabled:opacity-50">
                            {processing ? 'Creating…' : 'Create User'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
