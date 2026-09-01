import React from 'react';
import { useForm, Head } from '@inertiajs/react';
import { useUrl } from '@/utils/route';

export default function ResetPassword({ token, email }: { token: string; email: string }) {
    const { url } = useUrl();
    const { data, setData, post, processing, errors } = useForm({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    return (
        <>
            <Head title="Reset Password" />
            <div className="min-h-screen flex items-center justify-center bg-blue-950">
                <div className="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
                    <h2 className="text-xl font-semibold text-gray-800 mb-6">Create new password</h2>
                    <form onSubmit={(e) => { e.preventDefault(); post(url('/reset-password')); }} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" value={data.email} onChange={(e) => setData('email', e.target.value)}
                                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required />
                            {errors.email && <p className="text-red-600 text-xs mt-1">{errors.email}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" value={data.password} onChange={(e) => setData('password', e.target.value)}
                                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required />
                            {errors.password && <p className="text-red-600 text-xs mt-1">{errors.password}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)}
                                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required />
                        </div>
                        <button type="submit" disabled={processing}
                            className="w-full bg-blue-700 hover:bg-blue-800 disabled:opacity-50 text-white font-medium py-2.5 rounded-lg text-sm">
                            {processing ? 'Resetting…' : 'Reset Password'}
                        </button>
                    </form>
                </div>
            </div>
        </>
    );
}
