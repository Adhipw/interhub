<script setup lang="ts">
import { useLangStore } from '@/Stores/lang';

interface Props {
    processing?: boolean;
}

defineProps<Props>();

const langStore = useLangStore();
const t = (key: string) => langStore.t(key);

const handleGoogleLogin = () => {
    window.location.href = '/auth/google/redirect';
};
</script>

<template>
    <button 
        @click="handleGoogleLogin"
        type="button"
        :disabled="processing"
        class="w-full flex items-center justify-center gap-3 px-6 py-3.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all duration-300 active:scale-[0.98] shadow-sm disabled:opacity-50"
    >
        <img loading="lazy" decoding="async" src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5" alt="Google" />
        {{ t('auth.google_login') }}
    </button>
</template>
