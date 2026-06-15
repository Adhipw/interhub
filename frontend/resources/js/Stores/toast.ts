import { defineStore } from 'pinia';
import { ref } from 'vue';

export interface ToastAction {
    label: string;
    handler: () => void | Promise<void>;
    variant?: 'primary' | 'secondary' | 'danger';
}

export interface Toast {
    id: number;
    message: string;
    type: 'success' | 'error' | 'info' | 'warning';
    duration?: number | null;
    actions?: ToastAction[];
}

export const useToastStore = defineStore('toast', () => {
    const toasts = ref<Toast[]>([]);
    const timers = new Map<number, ReturnType<typeof window.setTimeout>>();
    let counter = 0;

    const add = (
        message: string,
        type: Toast['type'] = 'info',
        duration: number | null = 3000,
        actions: ToastAction[] = [],
    ) => {
        const id = counter++;
        toasts.value.push({ id, message, type, duration, actions });

        if (duration !== null && duration > 0) {
            const timer = window.setTimeout(() => {
                remove(id);
            }, duration);

            timers.set(id, timer);
        }

        return id;
    };

    const remove = (id: number) => {
        const timer = timers.get(id);

        if (timer) {
            window.clearTimeout(timer);
            timers.delete(id);
        }

        toasts.value = toasts.value.filter(t => t.id !== id);
    };

    return {
        toasts,
        add,
        remove,
        success: (msg: string) => add(msg, 'success'),
        error: (msg: string) => add(msg, 'error'),
        info: (msg: string) => add(msg, 'info'),
        warning: (msg: string) => add(msg, 'warning'),
    };
});
