<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { ref, reactive, onMounted } from 'vue';
import { router as inertiaRouter } from '@inertiajs/vue3';
import { ShieldCheck, ArrowLeft, Loader2 } from 'lucide-vue-next';
import { useLangStore } from '@/Stores/lang';
import api from '@/Services/api';

const props = defineProps<{
    email: string;
}>();

const loading = ref(false);
const error = ref('');
const resendLoading = ref(false);
const countdown = ref(60);
const canResend = ref(false);
const langStore = useLangStore();
const t = (key: string) => langStore.t(key);

// OTP Inputs
const otp = reactive(['', '', '', '', '', '']);
const inputRefs = ref<HTMLInputElement[]>([]);

const handleInput = (index: number, e: Event) => {
    const target = e.target as HTMLInputElement;
    const val = target.value;

    if (val.length > 1) {
        otp[index] = val.slice(-1);
    }

    if (val && index < 5) {
        inputRefs.value[index + 1]?.focus();
    }
};

const handleKeyDown = (index: number, e: KeyboardEvent) => {
    if (e.key === 'Backspace' && !otp[index] && index > 0) {
        inputRefs.value[index - 1]?.focus();
    }
};

const startCountdown = () => {
    canResend.value = false;
    countdown.value = 60;
    const timer = setInterval(() => {
        countdown.value--;
        if (countdown.value <= 0) {
            clearInterval(timer);
            canResend.value = true;
        }
    }, 1000);
};

const verifyOtp = async () => {
    const code = otp.join('');
    if (code.length < 6) return;

    loading.value = true;
    error.value = '';

    try {
        await api.post('/auth/email/verify-otp', {
            email: props.email,
            otp: code
        });

        inertiaRouter.visit('/dashboard');
    } catch (err: any) {
        error.value = err.response?.data?.message || t('auth.otp_invalid');
        // Reset OTP on error
        for (let i = 0; i < 6; i++) otp[i] = '';
        inputRefs.value[0]?.focus();
    } finally {
        loading.value = false;
    }
};

const resendOtp = async () => {
    if (!canResend.value) return;

    resendLoading.value = true;
    try {
        await api.post('/auth/email/resend-otp', { email: props.email });
        startCountdown();
    } catch (err: any) {
        error.value = t('auth.otp_resend_failed');
    } finally {
        resendLoading.value = false;
    }
};

onMounted(() => {
    startCountdown();
    inputRefs.value[0]?.focus();
});
</script>

<template>
    <AuthLayout :title="t('auth.verify_email_short_title')">
        <div class="w-full max-w-md mx-auto">
            <!-- Icon & Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 text-primary-600 rounded-2xl mb-4">
                    <ShieldCheck class="w-8 h-8" />
                </div>
                <h1 class="text-2xl font-bold text-slate-900">{{ t('auth.verify_email_title') }}</h1>
                <p class="text-slate-500 mt-2">
                    {{ t('auth.otp_sent_to') }} <br />
                    <span class="font-semibold text-slate-900">{{ email }}</span>
                </p>
            </div>

            <!-- OTP Form -->
            <form @submit.prevent="verifyOtp" class="space-y-6">
                <div class="flex justify-between gap-2">
                    <input
                        v-for="(digit, i) in otp"
                        :key="i"
                        :ref="el => inputRefs[i] = el as HTMLInputElement"
                        v-model="otp[i]"
                        type="text"
                        maxlength="1"
                        inputmode="numeric"
                        class="w-12 h-14 text-center text-2xl font-bold border-2 rounded-xl focus:border-primary-600 focus:ring-0 transition-colors"
                        :class="error ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-slate-50'"
                        @input="handleInput(i, $event)"
                        @keydown="handleKeyDown(i, $event)"
                    />
                </div>

                <div v-if="error" class="text-sm text-red-600 text-center font-medium">
                    {{ error }}
                </div>

                <button
                    type="submit"
                    :disabled="loading || otp.join('').length < 6"
                    class="w-full bg-primary-600 text-white py-4 rounded-xl font-bold hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2"
                >
                    <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
                    {{ t('auth.verify_now_button') }}
                </button>
            </form>

            <!-- Footer Actions -->
            <div class="mt-8 text-center space-y-4">
                <p class="text-sm text-slate-500">
                    {{ t('auth.did_not_receive_code') }}
                    <button 
                        @click="resendOtp"
                        :disabled="!canResend || resendLoading"
                        class="text-primary-600 font-bold hover:underline disabled:opacity-50"
                    >
                        {{ canResend ? t('auth.resend_short') : `${t('auth.resend_in')} ${countdown}s` }}
                    </button>
                </p>
                
                <button @click="inertiaRouter.visit('/register')" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-slate-600 transition-colors">
                    <ArrowLeft class="w-4 h-4" />
                    {{ t('auth.back_to_register') }}
                </button>
            </div>
        </div>
    </AuthLayout>
</template>
