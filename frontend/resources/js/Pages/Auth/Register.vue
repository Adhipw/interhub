<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useLangStore } from '@/Stores/lang';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import GoogleAuthButton from '@/Components/GoogleAuthButton.vue';
import AuthDivider from '@/Components/AuthDivider.vue';
import PasswordField from '@/Components/PasswordField.vue';
import PasswordStrengthMeter from '@/Components/PasswordStrengthMeter.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import AuthLink from '@/Components/AuthLink.vue';
import Input from '@/Components/Input.vue';
import FormError from '@/Components/FormError.vue';
import Captcha from '@/Components/Captcha.vue';
import { GraduationCap, Building2 } from 'lucide-vue-next';

const langStore = useLangStore();

const generalError = ref('');
const captchaRef = ref<InstanceType<typeof Captcha> | null>(null);

const form = useForm({
    name: '',
    email: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    captcha: '',
    role: 'user', // Default role
});

const t = (key: string) => langStore.t(key);

const submit = async () => {
    const hostname = window.location.hostname;
    const isLocal =
        hostname === 'localhost' ||
        hostname === '127.0.0.1' ||
        hostname === '::1' ||
        hostname.endsWith('.local');

    if (!form.captcha && !isLocal) {
        form.setError('captcha', t('auth.captcha_required'));
        return;
    }

    form.clearErrors();
    generalError.value = '';

    form.post('/register', {
        preserveScroll: true,
        onError: (pageErrors) => {
            generalError.value = String(pageErrors.general || '');

            if (!generalError.value && Object.keys(pageErrors).length === 0) {
                generalError.value = t('auth.register_failed');
            }

            captchaRef.value?.reset();
        },
    });
};
</script>

<template>
    <AuthLayout>
        <template #title>{{ t('auth.create_account') }}</template>
        <template #subtitle>{{ t('auth.register_subtitle') }}</template>

        <template v-if="$page.props.feature_flags?.social_login !== false">
            <GoogleAuthButton :processing="form.processing" />
            <AuthDivider />
        </template>

        <form class="space-y-6" @submit.prevent="submit">
            <div v-if="generalError" class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
                {{ generalError }}
            </div>
            <!-- Role Selector -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <button 
                    type="button"
                    :class="[
                        'flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all duration-300',
                        form.role === 'user' 
                            ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-500/10' 
                            : 'border-slate-100 dark:border-white/5 hover:border-indigo-200'
                    ]"
                    @click="form.role = 'user'"
                >
                    <GraduationCap :class="['w-8 h-8', form.role === 'user' ? 'text-indigo-600' : 'text-slate-400']" />
                    <span :class="['text-xs font-bold font-medium', form.role === 'user' ? 'text-indigo-600' : 'text-slate-500']">{{ t('auth.role_student_short') }}</span>
                </button>
                <button 
                    type="button"
                    :class="[
                        'flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all duration-300',
                        form.role === 'hr' 
                            ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-500/10' 
                            : 'border-slate-100 dark:border-white/5 hover:border-indigo-200'
                    ]"
                    @click="form.role = 'hr'"
                >
                    <Building2 :class="['w-8 h-8', form.role === 'hr' ? 'text-indigo-600' : 'text-slate-400']" />
                    <span :class="['text-xs font-bold font-medium', form.role === 'hr' ? 'text-indigo-600' : 'text-slate-500']">{{ t('auth.role_hr_short') }}</span>
                </button>
            </div>

            <Input
                id="name"
                v-model="form.name"
                type="text"
                :label="t('auth.full_name')"
                :error="form.errors.name"
                :placeholder="t('auth.full_name_placeholder')"
                required
            />

            <Input
                id="email"
                v-model="form.email"
                type="email"
                :label="t('auth.email')"
                :error="form.errors.email"
                :placeholder="t('auth.email_placeholder')"
                required
            />

            <Input
                id="phone"
                v-model="form.phone_number"
                type="tel"
                :label="t('auth.whatsapp_number')"
                :error="form.errors.phone_number"
                placeholder="08xxxxxxxxxx"
                required
            />

            <div class="space-y-1">
                <PasswordField
                    id="password"
                    v-model="form.password"
                    :label="t('auth.password')"
                    :error="form.errors.password"
                    :placeholder="t('auth.password_placeholder_register')"
                    required
                />
                <PasswordStrengthMeter :password="form.password" />
            </div>

            <PasswordField
                id="password_confirmation"
                v-model="form.password_confirmation"
                :label="t('auth.confirm_password')"
                :placeholder="t('auth.confirm_password_placeholder')"
                required
            />

            <Captcha ref="captchaRef" v-model="form.captcha" :error="form.errors.captcha" />

            <div class="pt-2">
                <LoadingButton :processing="form.processing">
                    {{ t('auth.register_button') }}
                </LoadingButton>
            </div>
        </form>

        <AuthLink 
            :label="t('auth.already_have_account')" 
            :link-text="t('auth.login_here')" 
            href="/login" 
        />
    </AuthLayout>
</template>
