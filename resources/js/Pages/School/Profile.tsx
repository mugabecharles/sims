import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { useUrl } from '@/utils/route';

interface SchoolData {
    id: number;
    name: string;
    short_name: string | null;
    emis_no: string | null;
    level: string;
    school_type: string;
    ownership: string;
    district: string | null;
    subcounty: string | null;
    address: string | null;
    phone: string | null;
    email: string | null;
    website: string | null;
    motto: string | null;
    head_teacher_name: string | null;
    proprietor_name: string | null;
    admission_no_prefix: string;
    invoice_no_prefix: string;
    receipt_no_prefix: string;
    sms_sender_id: string | null;
}

export default function Profile({ school }: { school: SchoolData }) {
    const { url } = useUrl();
    const { data, setData, put, processing, errors } = useForm<Partial<SchoolData>>({ ...school });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(url('/school/profile'));
    };

    const field = (name: keyof SchoolData, label: string, type = 'text', required = false) => (
        <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">{label}{required && ' *'}</label>
            <input
                type={type}
                value={(data[name] as string) ?? ''}
                onChange={(e) => setData(name, e.target.value as any)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            {errors[name] && <p className="text-red-600 text-xs mt-1">{errors[name]}</p>}
        </div>
    );

    const selectField = (name: keyof SchoolData, label: string, options: {value: string; label: string}[]) => (
        <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
            <select
                value={(data[name] as string) ?? ''}
                onChange={(e) => setData(name, e.target.value as any)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                {options.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
            </select>
        </div>
    );

    return (
        <AppLayout>
            <Head title="School Profile" />
            <div className="max-w-4xl">
                <h1 className="text-2xl font-bold text-gray-800 mb-6">School Profile</h1>

                <form onSubmit={handleSubmit} className="space-y-6">
                    {/* Basic Info */}
                    <div className="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 className="text-base font-semibold text-gray-700 mb-4">Basic Information</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {field('name', 'School Name', 'text', true)}
                            {field('short_name', 'Short Name')}
                            {field('emis_no', 'EMIS Number')}
                            {field('head_teacher_name', 'Head Teacher Name')}
                            {field('proprietor_name', 'Proprietor Name')}
                            {field('motto', 'School Motto')}
                        </div>
                    </div>

                    {/* Classification */}
                    <div className="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 className="text-base font-semibold text-gray-700 mb-4">Classification</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {selectField('level', 'School Level', [
                                { value: 'primary', label: 'Primary' },
                                { value: 'secondary', label: 'Secondary' },
                                { value: 'combined', label: 'Combined (Primary & Secondary)' },
                            ])}
                            {selectField('school_type', 'School Type', [
                                { value: 'day', label: 'Day School' },
                                { value: 'boarding', label: 'Boarding School' },
                                { value: 'mixed', label: 'Day & Boarding' },
                            ])}
                            {selectField('ownership', 'Ownership', [
                                { value: 'government', label: 'Government' },
                                { value: 'private', label: 'Private' },
                                { value: 'community', label: 'Community' },
                                { value: 'religious', label: 'Religious / Faith-Based' },
                            ])}
                        </div>
                    </div>

                    {/* Location & Contact */}
                    <div className="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 className="text-base font-semibold text-gray-700 mb-4">Location & Contact</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {field('district', 'District')}
                            {field('subcounty', 'Subcounty')}
                            {field('phone', 'Phone Number', 'tel')}
                            {field('email', 'Email', 'email')}
                            {field('website', 'Website', 'url')}
                        </div>
                        <div className="mt-4">
                            <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea
                                value={(data.address as string) ?? ''}
                                onChange={(e) => setData('address', e.target.value)}
                                rows={3}
                                className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    {/* Numbering */}
                    <div className="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 className="text-base font-semibold text-gray-700 mb-4">Number Prefixes</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {field('admission_no_prefix', 'Admission No. Prefix')}
                            {field('invoice_no_prefix', 'Invoice No. Prefix')}
                            {field('receipt_no_prefix', 'Receipt No. Prefix')}
                        </div>
                    </div>

                    {/* SMS */}
                    <div className="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 className="text-base font-semibold text-gray-700 mb-4">SMS Configuration</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {field('sms_sender_id', 'SMS Sender ID')}
                        </div>
                    </div>

                    <div className="flex justify-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="bg-blue-700 hover:bg-blue-800 disabled:opacity-50 text-white font-medium px-6 py-2.5 rounded-lg text-sm"
                        >
                            {processing ? 'Saving…' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
