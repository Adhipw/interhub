import axios, { AxiosResponse } from 'axios';
import { useToastStore } from '@/Stores/toast';

const api = axios.create({
    baseURL: '/api/v1',
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Interceptor to handle responses and errors
api.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error) => {
        const toastStore = useToastStore();

        if (error.response) {
            const status = error.response.status;
            const message = error.response.data?.message || 'Terjadi kesalahan pada server.';

            if (status === 401) {
                // Avoid infinite redirect if already on login
                if (window.location.pathname !== '/login') {
                    window.location.href = '/login';
                }
            } else if (status === 422) {
                // Validation errors are usually handled by forms, but we can show a general toast
                toastStore.error('Mohon periksa kembali input Anda.');
            } else if (status >= 500) {
                toastStore.error('Server sedang bermasalah. Mohon coba lagi nanti.');
            } else {
                toastStore.error(message);
            }
        } else {
            toastStore.error('Koneksi internet terputus atau server tidak merespon.');
        }

        return Promise.reject(error);
    }
);

export default api;
