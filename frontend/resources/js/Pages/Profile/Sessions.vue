<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { Head } from '@/Components';

import { route } from 'ziggy-js';
import { computed } from 'vue';

defineProps<{
    sessions: any[];
}>();

const page = usePage();
const form = useForm({});

// Get current session ID safely
const currentSessionId = computed(() => (page.props.auth as any)?.session_id);

const logoutOthers = () => {
    form.post(route('sessions.logout-others'), {
        preserveScroll: true,
    });
};

const formatDate = (timestamp: number) => {
    return new Date(timestamp * 1000).toLocaleString('id-ID');
};
</script>

<template>
    <Head title="Manajemen Sesi" />

    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Manajemen Sesi</h1>
                    <p class="text-slate-500">Lihat dan kelola perangkat yang sedang login ke akun Anda.</p>
                </div>
                <Link :href="route('dashboard')" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke Dashboard</Link>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Perangkat Aktif</h2>
                    <button 
                        @click="logoutOthers"
                        :disabled="form.processing"
                        class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 px-3 py-2 rounded-lg transition-colors disabled:opacity-50"
                    >
                        Logout dari Perangkat Lain
                    </button>
                </div>

                <div class="divide-y divide-slate-100">
                    <div v-for="session in sessions" :key="session.id" class="p-6 flex items-start gap-4">
                        <div class="p-3 bg-slate-100 rounded-xl text-slate-400">
                            <svg v-if="session.user_agent.includes('Mobile')" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900">{{ session.ip_address }}</span>
                                <span v-if="session.id === currentSessionId" class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase tracking-tighter">Perangkat Ini</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 line-clamp-1">{{ session.user_agent }}</div>
                            <div class="text-[10px] text-slate-400 mt-2">Aktivitas Terakhir: {{ formatDate(session.last_activity) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="form.wasSuccessful" class="mt-4 p-4 bg-green-50 border border-green-100 rounded-xl text-green-700 text-sm font-medium text-center">
                Semua perangkat lain telah berhasil dikeluarkan.
            </div>
        </div>
    </div>
</template>
