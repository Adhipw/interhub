<script setup lang="ts">
import { ref, reactive } from 'vue';
import api from '@/Services/api';
import { useLangStore } from '@/Stores/lang';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import FormError from '@/Components/FormError.vue';
import AuthLink from '@/Components/AuthLink.vue';

const langStore = useLangStore();
const processing = ref(false);
const status = ref('');
const errors = reactive({
    email: '',
});

const form = reactive({
    email: '',
});

const t = (key: string) => langStore.t(key);

const submit = async () => {
    processing.value = true;
    errors.email = '';
    status.value = '';

    try {
        const response = await api.post('/auth/password/email', form);
        status.value = response.data.message || t('auth.otp_sent_status');
    } catch (error: any) {
        if (error.response?.data?.errors) {
            errors.email = error.response.data.errors.email?.[0] || '';
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <AuthLayout :title="t('auth.forgot_password_title')">
        <div v-if="status" class="mb-6 p-4 bg-green-50 text-green-600 text-sm rounded-xl border border-green-100 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">{{ t('auth.registered_email_label') }}</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-50"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-50': errors.email }"
                    :placeholder="t('auth.email_placeholder')"
                    required
                    autofocus
                />
                <FormError :message="errors.email" />
            </div>

            <LoadingButton :processing="processing">
                {{ t('auth.send_otp_button') }}
            </LoadingButton>
        </form>

        <AuthLink 
            :label="t('auth.remember_password_label')" 
            :linkText="t('auth.back_to_login')" 
            href="/login" 
        />
    </AuthLayout>
</template>
