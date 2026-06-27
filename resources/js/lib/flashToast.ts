import { router, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
    let shown = new Set<string>();

    router.on('success', () => {
        const flash = usePage().props.flash as { toast?: FlashToast } | undefined;
        const data = flash?.toast;

        if (!data?.message) {
            return;
        }

        const key = data.type + ':' + data.message;
        if (shown.has(key)) {
            return;
        }

        shown.add(key);
        setTimeout(() => shown.delete(key), 3000);

        toast[data.type](data.message);
    });
}
