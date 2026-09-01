import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface Permission { id: number; code: string; display_name: string; module: string; }
interface Role { id: number; name: string; display_name: string; description: string | null; is_system: boolean; permissions: Permission[]; }

export default function RolesIndex({ roles, permissions }: { roles: Role[]; permissions: Permission[] }) {
    const { url } = useUrl();
    const [selectedRole, setSelectedRole] = useState<Role | null>(null);
    const [selectedPerms, setSelectedPerms] = useState<number[]>([]);
    const { data, setData, post, processing, errors, reset } = useForm({ name: '', display_name: '', description: '' });

    const modules = [...new Set(permissions.map(p => p.module))].sort();

    const openPermissions = (role: Role) => {
        setSelectedRole(role);
        setSelectedPerms(role.permissions.map(p => p.id));
    };

    const syncPermissions = () => {
        if (!selectedRole) return;
        router.post(url(`/admin/roles/${selectedRole.id}/permissions`), { permissions: selectedPerms }, {
            onSuccess: () => setSelectedRole(null),
        });
    };

    return (
        <AppLayout>
            <Head title="Roles & Permissions" />
            <h1 className="text-2xl font-bold text-gray-800 mb-6">Roles & Permissions</h1>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Roles list */}
                <div className="lg:col-span-1">
                    <div className="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                        {roles.map(role => (
                            <div key={role.id} className="p-4">
                                <div className="flex items-start justify-between">
                                    <div>
                                        <p className="font-medium text-gray-800 text-sm">{role.display_name}</p>
                                        <p className="text-xs text-gray-400 mt-0.5">{role.description}</p>
                                        <p className="text-xs text-gray-400 mt-1">{role.permissions.length} permissions</p>
                                    </div>
                                    <button onClick={() => openPermissions(role)}
                                        className="text-xs text-blue-600 hover:underline">Edit Permissions</button>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Permission editor */}
                {selectedRole && (
                    <div className="lg:col-span-2">
                        <div className="bg-white rounded-xl border border-gray-200 p-5">
                            <div className="flex items-center justify-between mb-4">
                                <h2 className="font-semibold text-gray-800">Permissions — {selectedRole.display_name}</h2>
                                <button onClick={() => setSelectedRole(null)} className="text-gray-400 hover:text-gray-600 text-lg">×</button>
                            </div>
                            <div className="space-y-4 max-h-96 overflow-y-auto">
                                {modules.map(mod => (
                                    <div key={mod}>
                                        <p className="text-xs font-semibold text-gray-500 uppercase mb-2">{mod}</p>
                                        <div className="grid grid-cols-2 gap-2">
                                            {permissions.filter(p => p.module === mod).map(perm => (
                                                <label key={perm.id} className="flex items-center gap-2 text-sm cursor-pointer">
                                                    <input type="checkbox"
                                                        checked={selectedPerms.includes(perm.id)}
                                                        onChange={e => setSelectedPerms(prev =>
                                                            e.target.checked ? [...prev, perm.id] : prev.filter(id => id !== perm.id)
                                                        )}
                                                        className="rounded text-blue-600"
                                                    />
                                                    {perm.display_name}
                                                </label>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                            <div className="mt-4 flex justify-end">
                                <button onClick={syncPermissions} className="bg-blue-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-800">
                                    Save Permissions
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
