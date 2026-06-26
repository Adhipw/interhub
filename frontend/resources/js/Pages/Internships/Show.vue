<script setup lang="ts">
import { ref, computed } from 'vue';
import { router as inertiaRouter } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { 
    MapPin, Calendar, Clock, DollarSign, Building2, 
    ArrowLeft, ArrowRight, Share2, ShieldCheck, CheckCircle2, 
    Briefcase, Globe, Info, Mail
} from 'lucide-vue-next';
import { useLangStore } from '@/Stores/lang';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import { useAuthStore } from '@/Stores/auth';
import type { Internship } from '@/Types/internship';
import DOMPurify from 'dompurify';

interface InternshipShowProps {
    internship: Internship;
    relatedInternships?: Internship[];
    hasApplied?: boolean;
    matchScore?: number | null;
    missingSkills?: string[];
}

const props = defineProps<InternshipShowProps>();

const langStore = useLangStore();
const authStore = useAuthStore();
const t = (key: string) => langStore.t(key);

const internship = computed(() => props.internship);

const updateSeo = () => {
    if (!props.internship) return;

    const companyName = props.internship.company.name ?? 'InternHub';
    const pageTitle = `${props.internship.title} di ${companyName} - InternHub`;
    document.title = pageTitle;

    const metaDesc = document.querySelector('meta[name="description"]');
    if (metaDesc) {
        metaDesc.setAttribute('content', `Daftar lowongan magang ${props.internship.title} di ${companyName} melalui InternHub. Lokasi: ${props.internship.location}.`);
    }
};

import Modal from '@/Components/Modal.vue';

const showApplyModal = ref(false);
const coverLetter = ref('');
const applying = ref(false);
const applyError = ref('');

const handleApply = () => {
    if (!authStore.isAuthenticated) {
        inertiaRouter.visit(`/login?redirect=${encodeURIComponent(window.location.pathname)}`);
        return;
    }
    if (!internship.value) return;
    
    coverLetter.value = '';
    applyError.value = '';
    showApplyModal.value = true;
};

const submitApplication = async () => {
    if (!internship.value) return;
    applying.value = true;
    applyError.value = '';

    inertiaRouter.post(`/internships/${internship.value.slug}/apply`, {
        cover_letter: coverLetter.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showApplyModal.value = false;
            inertiaRouter.visit('/my-applications');
        },
        onError: (errors) => {
            applyError.value = String(errors.application || errors.cover_letter || 'Terjadi kesalahan saat mengirim lamaran. Silakan coba lagi.');
        },
        onFinish: () => {
            applying.value = false;
        },
    });
};

const goBack = () => {
    if (typeof window !== 'undefined' && window.history.length > 1) {
        window.history.back();
    } else {
        inertiaRouter.visit('/internships');
    }
};

const cleanHtml = (html?: string | null) => {
    if (!html) return '';
    // Hapus tag komentar Google Translate/Kalibrr
    const noComments = html.replace(/<!--TgQPHd\|?\[\]-->/g, '');
    return DOMPurify.sanitize(noComments);
};

updateSeo();
</script>

<template>
    <PublicLayout>
        <div class="bg-neutral-50 dark:bg-neutral-950 min-h-screen pt-24 pb-24">
            <div class="container mx-auto px-4 sm:px-6 max-w-5xl">
                <!-- Back Button -->
                <button class="flex items-center gap-2 text-neutral-500 hover:text-primary-600 transition-colors mb-8 group font-medium text-sm" @click="goBack">
                    <ArrowLeft class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
                    {{ t('common.back') }}
                </button>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-10 animate-reveal">
                        <!-- Header Card -->
                        <div class="bg-white dark:bg-neutral-900 rounded-2xl p-8 md:p-12 border border-neutral-200/60 dark:border-neutral-800 shadow-sm relative overflow-hidden">
                            <div class="relative z-10">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-10">
                                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                                        <div class="w-20 h-20 bg-white dark:bg-neutral-950 rounded-xl flex items-center justify-center border border-neutral-200/60 dark:border-neutral-800 shadow-sm overflow-hidden shrink-0">
                                            <img v-if="internship.company?.logo_url" loading="lazy" decoding="async" :src="internship.company.logo_url" class="w-full h-full object-contain p-3" />
                                            <Building2 v-else class="w-10 h-10 text-neutral-300" />
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-3 mb-3">
                                                <Badge variant="primary" size="sm">{{ internship.type }}</Badge>
                                                
                                                <div v-if="matchScore !== undefined && matchScore !== null" class="flex items-center gap-2 px-2.5 py-1 rounded-md border text-xs font-semibold" :class="matchScore >= 80 ? 'bg-emerald-50 border-emerald-200/60 text-emerald-700' : 'bg-amber-50 border-amber-200/60 text-amber-700'">
                                                    <span>Match {{ matchScore }}%</span>
                                                </div>
                                            </div>

                                            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white tracking-tight leading-tight mb-2">{{ internship.title }}</h1>
                                            <div class="text-sm font-medium text-neutral-600 dark:text-neutral-400 flex items-center gap-1.5">
                                                <Building2 class="w-4 h-4" />
                                                {{ internship.company?.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <button class="w-10 h-10 border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-lg flex items-center justify-center text-neutral-500 hover:text-primary-600 transition-all shadow-sm">
                                        <Share2 class="w-4 h-4" />
                                    </button>
                                </div>
                                
                                <div v-if="matchScore !== undefined && matchScore !== null && matchScore < 80 && missingSkills && missingSkills.length" class="mb-8 p-4 bg-amber-50/50 border border-amber-100 dark:bg-amber-900/10 dark:border-amber-900/30 rounded-xl text-sm text-amber-800 dark:text-amber-400 flex items-start gap-3">
                                    <Info class="w-5 h-5 shrink-0 mt-0.5" />
                                    <p>💡 Tingkatkan peluang Anda dengan mempelajari skill berikut: <span class="font-semibold">{{ missingSkills.slice(0, 3).join(', ') }}{{ missingSkills.length > 3 ? '...' : '' }}</span></p>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-8 border-t border-neutral-100 dark:border-neutral-800">
                                    <div>
                                        <p class="text-[11px] font-semibold text-neutral-500 font-medium mb-1.5">{{ t('job.stipend') }}</p>
                                        <p class="text-sm font-semibold text-neutral-900 dark:text-white">{{ internship.stipend || t('job.stipend_default') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-semibold text-neutral-500 font-medium mb-1.5">{{ t('filters.location_label') }}</p>
                                        <p class="text-sm font-semibold text-neutral-900 dark:text-white">{{ internship.location }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-semibold text-neutral-500 font-medium mb-1.5">{{ t('job.deadline_label') }}</p>
                                        <p class="text-sm font-semibold text-rose-600">{{ internship.deadline_at_human || t('job.deadline_urgent') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-semibold text-neutral-500 font-medium mb-1.5">{{ t('job.published_at') }}</p>
                                        <p class="text-sm font-semibold text-neutral-900 dark:text-white">{{ internship.created_at_human }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description & Content -->
                        <div class="space-y-10 px-2 md:px-4 animate-reveal delay-100 opacity-0">
                            <section>
                                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">
                                    {{ t('hr.internships.about_role') }}
                               </h2>
                                <div class="prose prose-neutral dark:prose-invert max-w-none text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed" v-html="cleanHtml(internship.description)">
                                </div>
                            </section>

                            <section v-if="internship.requirements && internship.requirements.length">
                                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">
                                    {{ t('hr.internships.requirements') }}
                                </h2>
                                <div v-if="typeof internship.requirements === 'string'" class="prose prose-neutral dark:prose-invert max-w-none text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed" v-html="cleanHtml(internship.requirements)"></div>
                                <ul v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <li v-for="(req, index) in internship.requirements" :key="index" class="flex items-start gap-3 p-4 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200/60 dark:border-neutral-800 shadow-sm">
                                        <CheckCircle2 class="w-5 h-5 text-emerald-500 shrink-0" />
                                        <span class="text-neutral-700 dark:text-neutral-300 text-sm leading-snug" v-html="cleanHtml(req)"></span>
                                    </li>
                                </ul>
                            </section>

                            <section v-if="internship.benefits && internship.benefits.length">
                                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">
                                    {{ t('job.benefit') }}
                                </h2>
                                <div v-if="typeof internship.benefits === 'string'" class="prose prose-neutral dark:prose-invert max-w-none text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed" v-html="cleanHtml(internship.benefits)"></div>
                                <ul v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <li v-for="(benefit, index) in internship.benefits" :key="index" class="flex items-start gap-3 p-4 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200/60 dark:border-neutral-800 shadow-sm">
                                        <CheckCircle2 class="w-5 h-5 text-primary-500 shrink-0" />
                                        <span class="text-neutral-700 dark:text-neutral-300 text-sm leading-snug" v-html="cleanHtml(benefit)"></span>
                                    </li>
                                </ul>
                            </section>
                        </div>
                    </div>

                    <!-- Sidebar: Action & Info -->
                    <aside class="space-y-6 lg:sticky lg:top-24 animate-reveal delay-200 opacity-0">
                        <!-- Action Card -->
                        <div class="bg-white dark:bg-neutral-900 p-8 rounded-2xl shadow-sm hover:shadow-md hover:border-primary-200/50 transition-all duration-300 border border-neutral-200/60 dark:border-neutral-800 relative overflow-hidden group">
                            <div class="relative z-10">
                                <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-2">{{ t('job.apply_card_title') }}</h3>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-6 leading-relaxed">{{ t('job.apply_card_desc') }}</p>
                                <Button 
                                    size="lg" 
                                    variant="primary" 
                                    class="w-full" 
                                    @click="handleApply"
                                >
                                    {{ t('job.apply_now') }}
                                </Button>
                            </div>
                        </div>

                        <!-- Company Card -->
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-neutral-200/60 dark:border-neutral-800 shadow-sm space-y-6">
                            <h3 class="text-[11px] font-semibold font-medium text-neutral-500">{{ t('company.about') }}</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white dark:bg-neutral-950 rounded-lg flex items-center justify-center border border-neutral-200/60 dark:border-neutral-800 shadow-sm shrink-0 overflow-hidden">
                                    <img v-if="internship.company?.logo_url" loading="lazy" decoding="async" :src="internship.company.logo_url" class="w-full h-full object-contain p-2" />
                                    <Building2 v-else class="w-6 h-6 text-neutral-300" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-neutral-900 dark:text-white line-clamp-1">{{ internship.company?.name }}</h4>
                                    <p class="text-xs text-neutral-500">{{ internship.company?.industry || 'Industri Teknologi' }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed line-clamp-3">
                                {{ internship.company?.description || 'Perusahaan terverifikasi di platform InternHub yang berfokus pada inovasi dan pengembangan talenta muda.' }}
                            </p>
                            <Button variant="outline" class="w-full" @click="inertiaRouter.visit('/companies/' + internship.company?.slug)">
                                {{ t('company.view_profile') }}
                            </Button>
                            <a 
                                :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(internship.location || internship.company?.name || '')}`" 
                                target="_blank"
                                class="w-full flex items-center justify-center gap-2 py-2 px-4 rounded-lg font-medium text-sm text-neutral-600 border border-neutral-200 hover:bg-neutral-50 transition-colors"
                            >
                                <MapPin class="w-4 h-4" /> Buka di Maps
                            </a>
                        </div>

                        <!-- Trust Badge -->
                        <div class="flex items-start gap-3 p-4 bg-white dark:bg-neutral-900 border border-emerald-200/60 dark:border-emerald-900/30 rounded-xl shadow-sm">
                            <ShieldCheck class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
                            <div>
                                <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 mb-0.5">{{ t('company.verified_badge_title') }}</p>
                                <p class="text-[11px] text-neutral-500">{{ t('company.verified_badge_desc') }}</p>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>

        <!-- Apply Modal -->
        <Modal 
            :show="showApplyModal" 
            title="Kirim Lamaran Magang" 
            max-width="lg"
            @close="showApplyModal = false"
        >
            <div v-if="internship" class="space-y-6">
                <div class="p-4 bg-neutral-50 dark:bg-neutral-800/50 rounded-xl border border-neutral-200/60 dark:border-neutral-700 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white dark:bg-neutral-900 rounded-lg flex items-center justify-center border border-neutral-200/60 dark:border-neutral-800 shadow-sm shrink-0 overflow-hidden">
                        <img v-if="internship.company?.logo_url" loading="lazy" decoding="async" :src="internship.company.logo_url" class="w-full h-full object-contain p-2" />
                        <Building2 v-else class="w-6 h-6 text-neutral-300" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-neutral-900 dark:text-white leading-tight">{{ internship.title }}</h4>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ internship.company?.name }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-neutral-600 dark:text-neutral-400">
                        Surat Lamaran / Pesan Pengantar (Opsional)
                    </label>
                    <textarea 
                        v-model="coverLetter"
                        rows="4"
                        class="w-full px-4 py-3 bg-white dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-xl focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 transition-all text-sm leading-relaxed dark:text-white resize-none"
                        placeholder="Tuliskan mengapa Anda tertarik dengan posisi ini..."
                    ></textarea>
                </div>

                <!-- Info Alert -->
                <div class="flex items-start gap-3 p-4 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-xl text-blue-700 dark:text-blue-400">
                    <Info class="w-4 h-4 mt-0.5 shrink-0" />
                    <div class="text-sm leading-relaxed">
                        <p class="font-semibold mb-0.5">Informasi Profil</p>
                        CV dan Portfolio dari profil Anda akan otomatis disertakan.
                    </div>
                </div>

                <!-- Error Alert -->
                <div v-if="applyError" class="flex items-start gap-3 p-4 bg-rose-50/50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30 rounded-xl text-rose-600 dark:text-rose-400">
                    <Info class="w-4 h-4 mt-0.5 shrink-0" />
                    <p class="text-sm font-medium">{{ applyError }}</p>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                    <Button 
                        variant="outline" 
                        :disabled="applying"
                        @click="showApplyModal = false"
                    >
                        Batal
                    </Button>
                    <Button 
                        variant="primary" 
                        :disabled="applying"
                        class="shadow-lg shadow-primary-500/25"
                        @click="submitApplication"
                    >
                        <span v-if="applying">Mengirim...</span>
                        <span v-else>Kirim Lamaran</span>
                    </Button>
                </div>
            </div>
        </Modal>
    </PublicLayout>
</template>
