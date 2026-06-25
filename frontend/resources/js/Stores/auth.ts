import { defineStore } from 'pinia';
import { router as inertiaRouter } from '@inertiajs/vue3';

const clearStoredAuth = () => {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    localStorage.removeItem('remembered_email');
    sessionStorage.removeItem('auth_token');
    sessionStorage.removeItem('user');
};

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
        hasPermission: (state) => (permission: string) => {
            return state.user?.all_permissions?.includes(permission) || false;
        },
    },

    actions: {
        syncFromInertiaUser(user: any) {
            this.user = user || null;
            clearStoredAuth();
        },

        async logout() {
            if (this.loading) return;

            this.loading = true;

            return new Promise<void>((resolve) => {
                inertiaRouter.post('/logout', {}, {
                    replace: true,
                    preserveScroll: false,
                    preserveState: false,
                    onSuccess: () => {
                        this.user = null;
                        clearStoredAuth();
                        window.location.assign('/');
                    },
                    onError: () => {
                        this.user = null;
                        clearStoredAuth();
                        window.location.assign('/login');
                    },
                    onFinish: () => {
                        this.loading = false;
                        resolve();
                    },
                });
            });
        },
    }
});
