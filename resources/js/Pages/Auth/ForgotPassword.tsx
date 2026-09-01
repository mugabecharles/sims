import React from 'react';
import { useForm, Head } from '@inertiajs/react';
import { useUrl } from '@/utils/route';

export default function ForgotPassword({ status }: { status?: string }) {
    const { url } = useUrl();
    const { data, setData, post, processing, errors } = useForm({ email: '' });

    return (
        <>
            <Head title="Forgot Password" />
            <div className="min-h-screen flex items-center justify-center bg-blue-950">
                <div className="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
                    <h2 className="text-xl font-semibold text-gray-800 mb-2">Reset your password</h2>
                    <p className="text-sm text-gray-500 mb-6">
                        Enter your email address and we'll send you a reset link.
                    </p>

                    {status && (
                        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded text-sm mb-4">
                            {status}
                        </div>
                    )}

                    <form onSubmit={(e) => { e.preventDefault(); post(url('/forgot-password')); }} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                            <input
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="your@email.com"
                                required
                                autoFocus
                            />
                            {errors.email && <p className="text-red-600 text-xs mt-1">{errors.email}</p>}
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-blue-700 hover:bg-blue-800 disabled:opacity-50 text-white font-medium py-2.5 rounded-lg text-sm"
                        >
                            {processing ? 'Sending…' : 'Send Reset Link'}
                        </button>
                        <a href={url('/login')} className="block text-center text-sm text-blue-600 hover:underline mt-2">
                            ← Back to login
                        </a>
                    </form>
                </div>
            </div>
        </>
    );
}
