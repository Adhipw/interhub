<script setup lang="ts">
import { useLangStore } from '@/Stores/lang';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import PasswordField from '@/Components/PasswordField.vue';
import PasswordStrengthMeter from '@/Components/PasswordStrengthMeter.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import FormError from '@/Components/FormError.vue';

const langStore = useLangStore();

const props = defineProps<{
    email: string;
}>();

const t = (key: string) => langStore.t(key);

const form = useForm({
    email: props.email,
    otp: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout :title="t('auth.reset_password_title')">
        <div class="mb-8">
            <p class="text-sm text-slate-500 leading-relaxed text-center">
                {{ t('auth.reset_password_desc') }}
            </p>
        </div>

        <form class="space-y-5" @submit.prevent="submit">
            <input v-model="form.email" type="hidden" />

            <div>
                <label for="otp" class="block text-sm font-semibold text-slate-700 mb-2 text-center">{{ t('auth.otp_code_label') }}</label>
                <input
                    id="otp"
                    v-model="form.otp"
                    type="text"
                    class="w-full text-center tracking-[0.5em] text-xl font-bold rounded-xl border border-slate-200 bg-white px-4 py-3 transition-all focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-50"
                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-50': form.errors.otp }"
                    placeholder="000000"
                    maxlength="6"
                    required
                />
                <FormError :message="form.errors.otp" class="text-center mt-2" />
            </div>

            <div class="space-y-1">
                <PasswordField
                    id="password"
                    v-model="form.password"
                    :label="t('auth.new_password_label')"
                    :error="form.errors.password"
                    :placeholder="t('auth.new_password_placeholder')"
                    required
                />
                <PasswordStrengthMeter :password="form.password" />
            </div>

            <PasswordField
                id="password_confirmation"
                v-model="form.password_confirmation"
                :label="t('auth.confirm_new_password_label')"
                :placeholder="t('auth.confirm_new_password_placeholder')"
                required
            />

            <div class="pt-2">
                <LoadingButton :processing="form.processing">
                    {{ t('auth.save_new_password') }}
                </LoadingButton>
            </div>
        </form>
    </AuthLayout>
</template>
