import { defineStore } from 'pinia';
import { router as inertiaRouter } from '@inertiajs/vue3';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as any,
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
        roles: (state) => state.user?.all_roles || [],
        isHR: (state) => state.user?.role === 'hr' || state.user?.all_roles?.includes('hr') || false,
        isMentor: (state) => state.user?.role === 'mentor' || state.user?.all_roles?.includes('mentor') || false,
        isAdmin: (state) => state.user?.role === 'admin' || state.user?.all_roles?.includes('admin') || false,
        isSuperAdmin: (state) => state.user?.role === 'super_admin' || state.user?.all_roles?.includes('super_admin') || false,
    },

    actions: {
        syncFromInertiaUser(user: any) {
            this.user = user || null;
            localStorage.removeItem('auth_token');
            sessionStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            sessionStorage.removeItem('user');
        },

        async logout() {
            return new Promise<void>((resolve) => {
                this.user = null;
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user');

                inertiaRouter.post('/logout', {}, {
                    preserveScroll: false,
                    onFinish: () => resolve(),
                });
            });
        },
    }
});
