export interface User {
    id: number;
    name: string;
    email: string | null;
    roles: string[];
    permissions: string[];
}

export interface School {
    id: number;
    name: string;
    short_name: string | null;
    logo_url: string | null;
    level: 'primary' | 'secondary' | 'combined';
    school_type: 'day' | 'boarding' | 'mixed';
    currency: string;
    timezone: string;
}

export interface PageProps {
    auth: { user: User | null };
    flash: {
        success?: string;
        error?: string;
        warning?: string;
        info?: string;
    };
    school: School | null;
    base_url: string;
    [key: string]: unknown;
}
