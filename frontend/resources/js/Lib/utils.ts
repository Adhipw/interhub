/**
 * Utility functions for formatting dates and other data.
 */

export const formatDate = (date: string | Date, options: Intl.DateTimeFormatOptions = { month: 'long', year: 'numeric' }) => {
    if (!date) return '-';
    const d = typeof date === 'string' ? new Date(date) : date;
    return d.toLocaleDateString('id-ID', options);
};

export const formatFullDate = (date: string | Date) => {
    return formatDate(date, { day: 'numeric', month: 'long', year: 'numeric' });
};

export const formatShortDate = (date: string | Date) => {
    return formatDate(date, { day: 'numeric', month: 'short' });
};

/**
 * Returns a human-readable relative time string (e.g., "2 jam yang lalu", "3 hari yang lalu").
 * Note: Simple implementation for Indonesian.
 */
export const formatRelativeTime = (date: string | Date) => {
    if (!date) return '-';
    const d = typeof date === 'string' ? new Date(date) : date;
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - d.getTime()) / 1000);

    if (diffInSeconds < 60) return 'Baru saja';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit yang lalu`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam yang lalu`;
    if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} hari yang lalu`;
    if (diffInSeconds < 31536000) return `${Math.floor(diffInSeconds / 2592000)} bulan yang lalu`;
    return `${Math.floor(diffInSeconds / 31536000)} tahun yang lalu`;
};
