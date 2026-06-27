export type UseDateFormatReturn = {
    formatDate: (date: string | null | undefined) => string;
};

export function formatDate(date: string | null | undefined): string {
    if (!date) {
        return 'N/A';
    }

    const d = new Date(date);

    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(d);
}

export function useDateFormat(): UseDateFormatReturn {
    return { formatDate };
}
