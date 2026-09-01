/**
 * URL helper for Inertia apps running in a subdirectory.
 *
 * When APP_URL = "http://localhost/sims/public", Laravel routes like
 * /login resolve to /sims/public/login. Inertia's router uses the
 * path as-is, so we must prefix all route paths with the subdirectory.
 *
 * The base path (/sims/public) is derived from the shared `base_url` prop
 * injected by HandleInertiaRequests middleware.
 */

import { usePage } from '@inertiajs/react';
import { PageProps } from '@/types';

/**
 * Hook: returns a url() function scoped to the app's base path.
 *
 * Usage in components:
 *   const { url } = useUrl();
 *   post(url('/login'));
 */
export function useUrl() {
    const { base_url } = usePage<PageProps & { base_url?: string }>().props;

    const url = (path: string): string => {
        if (!base_url) return path;
        // Extract just the pathname from base_url
        let base = base_url;
        try {
            base = new URL(base_url).pathname;
        } catch {
            // already a path
        }
        base = base.replace(/\/$/, '');
        const normalPath = path.startsWith('/') ? path : `/${path}`;
        return `${base}${normalPath}`;
    };

    return { url, base_url: base_url ?? '' };
}

/**
 * Standalone function: extract base path from the Inertia page DOM
 * (for use outside React components, e.g. in app.tsx).
 */
export function getBasePathFromDom(): string {
    if (typeof window === 'undefined') return '';
    try {
        const script = document.querySelector('script[data-page]');
        if (!script) return '';
        const page = JSON.parse(script.textContent || '{}');
        const baseUrl: string = page?.props?.base_url || '';
        if (!baseUrl) return '';
        try {
            return new URL(baseUrl).pathname.replace(/\/$/, '');
        } catch {
            return baseUrl.replace(/\/$/, '');
        }
    } catch {
        return '';
    }
}
