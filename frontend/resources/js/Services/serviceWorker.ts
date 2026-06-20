import logger from '@/Lib/logger';
import type { useToastStore } from '@/Stores/toast';

type ToastStore = ReturnType<typeof useToastStore>;

const SW_SCRIPT = '/sw.js?v=interhub-sw-cleanup-v1';
const serviceWorkerEnabled = false;

const unregisterExistingServiceWorkers = async () => {
    const registrations = await navigator.serviceWorker.getRegistrations();
    await Promise.all(registrations.map((item) => item.unregister()));

    if ('caches' in window) {
        const cacheNames = await caches.keys();
        await Promise.all(cacheNames.map((cacheName) => caches.delete(cacheName)));
    }
};

export function setupServiceWorker(_toastStore: ToastStore) {
    if (!import.meta.env.PROD || typeof window === 'undefined' || !('serviceWorker' in navigator)) {
        return;
    }

    if (!serviceWorkerEnabled) {
        unregisterExistingServiceWorkers().catch((error) => {
            logger.error('Failed to unregister legacy service workers:', error);
        });

        return;
    }

    navigator.serviceWorker.register(SW_SCRIPT, { updateViaCache: 'none' }).catch((error) => {
        logger.error('PWA Service Worker Registration Failed:', error);
    });
}
