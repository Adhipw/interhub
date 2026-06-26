<script setup lang="ts">
import { computed } from 'vue';
import { router as inertiaRouter } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { 
    Building2, Globe, MapPin, Briefcase, 
    ArrowLeft, CheckCircle2, ShieldCheck, Mail, Star, MessageSquare
} from 'lucide-vue-next';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import { useForm } from '@inertiajs/vue3';
import { useAuthStore } from '@/Stores/auth';
import type { Company, Internship } from '@/Types/internship';

interface CompanyShowProps {
    company: Company;
    internships: { data: Internship[] } | Internship[];
}

const props = defineProps<CompanyShowProps>();

const company = computed(() => props.company);
const internships = computed(() => Array.isArray(props.internships) ? props.internships : props.internships.data || []);
const reviews = computed(() => (company.value as any).reviews || []);
const authStore = useAuthStore();

const reviewForm = useForm({
    rating: 5,
    review_text: '',
});

const submitReview = () => {
    reviewForm.post(`/companies/${company.value.slug}/reviews`, {
        preserveScroll: true,
        onSuccess: () => {
            reviewForm.reset('review_text');
            alert('Ulasan Anda berhasil dikirim!');
            // To update local reviews array, we could reload from server
            inertiaRouter.reload({ only: ['company'] });
        },
    });
};
</script>

<template>
    <PublicLayout>
        <div class="bg-neutral-50 dark:bg-neutral-950 min-h-screen pt-24 pb-24">
            <div class="container mx-auto px-4 sm:px-6 max-w-5xl">
                <!-- Header Card -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl p-8 md:p-12 border border-neutral-200/60 dark:border-neutral-800 shadow-sm mb-12 animate-reveal">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                        <div class="w-28 h-28 bg-white dark:bg-neutral-950 rounded-xl flex items-center justify-center border border-neutral-200/60 dark:border-neutral-800 shadow-sm shrink-0 overflow-hidden">
                            <img v-if="company.logo_url" loading="lazy" decoding="async" :src="company.logo_url" class="w-full h-full object-contain p-4" />
                            <Building2 v-else class="w-12 h-12 text-neutral-300" />
                        </div>
                        
                        <div class="flex-1 text-center md:text-left">
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-3">
                                <h1 class="text-3xl font-bold text-neutral-900 dark:text-white tracking-tight">{{ company.name }}</h1>
                                <div v-if="company.is_verified" class="flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md border border-emerald-200/60 text-xs font-semibold">
                                    <ShieldCheck class="w-3.5 h-3.5" />
                                    Terverifikasi
                                </div>
                            </div>
                            <div class="flex items-center justify-center md:justify-start gap-2 mb-5 text-sm">
                                <p class="font-semibold text-neutral-700 dark:text-neutral-300">{{ company.industry || 'Teknologi & Inovasi' }}</p>
                                <span class="text-neutral-300 dark:text-neutral-600">•</span>
                                <div class="flex items-center gap-1 text-amber-600 dark:text-amber-500 font-semibold">
                                    <Star class="w-4 h-4 fill-amber-500" />
                                    {{ (company as any).average_rating || 'Belum ada rating' }} 
                                    <span class="text-neutral-400 font-normal ml-1" v-if="(company as any).reviews_count">({{ (company as any).reviews_count }} Ulasan)</span>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap justify-center md:justify-start gap-6 text-neutral-500 text-sm">
                                <div class="flex items-center gap-1.5">
                                    <MapPin class="w-4 h-4 text-neutral-400" />
                                    {{ company.location || 'Indonesia' }}
                                </div>
                                <div v-if="company.website" class="flex items-center gap-2">
                                    <Globe class="w-5 h-5 text-neutral-400" />
                                    <a :href="company.website" target="_blank" class="hover:text-primary-600 transition-colors">{{ company.website }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- About -->
                    <div class="lg:col-span-2 space-y-10 animate-reveal delay-100 opacity-0">
                        <section>
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Tentang Perusahaan</h2>
                            <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed">
                                {{ company.description || 'Kami adalah perusahaan yang berkomitmen untuk memberdayakan talenta muda Indonesia melalui program magang yang terstruktur dan berkualitas. Di sini, Anda akan belajar langsung dari para ahli di industri.' }}
                            </p>
                        </section>

                        <section>
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-4">Lowongan Aktif</h2>
                            
                            <div v-if="internships.length > 0" class="space-y-6">
                                <Card 
                                    v-for="(internship, idx) in internships" 
                                    :key="internship.id"
                                    hoverable
                                    padding="md"
                                    class="cursor-pointer group animate-reveal opacity-0"
                                    :style="`animation-delay: ${150 + (Number(idx) * 50)}ms`"
                                    @click="inertiaRouter.visit('/internships/' + internship.slug)"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white group-hover:text-primary-600 transition-colors">{{ internship.title }}</h3>
                                            <p class="text-sm text-neutral-500 mt-1">{{ internship.type }} • {{ internship.location }}</p>
                                        </div>
                                        <div class="text-neutral-400 group-hover:text-primary-600 transition-colors">
                                            <ArrowLeft class="w-5 h-5 rotate-180" />
                                        </div>
                                    </div>
                                </Card>
                            </div>
                            <div v-else class="p-8 text-center bg-white dark:bg-neutral-900 rounded-2xl border border-dashed border-neutral-300 dark:border-neutral-700">
                                <p class="text-neutral-500 text-sm">Belum ada lowongan aktif saat ini.</p>
                            </div>
                        </section>

                        <!-- Review & Kultur -->
                        <section class="mt-10">
                            <h2 class="text-lg font-semibold text-neutral-900 dark:text-white mb-6">Kultur Magang & Ulasan</h2>

                            <!-- Review List -->
                            <div v-if="reviews.length > 0" class="space-y-4 mb-10">
                                <div v-for="review in reviews" :key="review.id" class="p-5 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200/60 dark:border-neutral-800 shadow-sm flex gap-4">
                                    <div class="w-10 h-10 bg-neutral-100 dark:bg-neutral-800 rounded-full flex items-center justify-center shrink-0 overflow-hidden border border-neutral-200 dark:border-neutral-700">
                                        <img v-if="review.user.avatar_url" :src="review.user.avatar_url" class="w-full h-full object-cover" />
                                        <span v-else class="font-medium text-neutral-500 text-sm">{{ review.user.name.charAt(0) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <h4 class="font-semibold text-sm text-neutral-900 dark:text-white">{{ review.user.name }}</h4>
                                            <div class="flex items-center gap-1 text-amber-500">
                                                <Star class="w-3.5 h-3.5 fill-amber-500" />
                                                <span class="text-xs font-semibold">{{ review.rating }}</span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ review.review_text }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="p-8 mb-10 text-center bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200/60 dark:border-neutral-800 shadow-sm">
                                <div class="w-12 h-12 bg-neutral-50 dark:bg-neutral-800 text-neutral-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <MessageSquare class="w-5 h-5" />
                                </div>
                                <p class="text-neutral-500 text-sm">Belum ada ulasan alumni magang. Jadilah yang pertama!</p>
                            </div>

                            <!-- Write Review Form -->
                            <div v-if="authStore.isAuthenticated" class="bg-white dark:bg-neutral-900 p-6 rounded-xl border border-neutral-200/60 dark:border-neutral-800 shadow-sm">
                                <h3 class="text-base font-semibold text-neutral-900 dark:text-white mb-4">Tulis Pengalaman Magang Anda</h3>
                                <form @submit.prevent="submitReview" class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-medium text-neutral-500 mb-2">Rating Perusahaan</label>
                                        <div class="flex items-center gap-1">
                                            <button 
                                                v-for="star in 5" :key="star" type="button"
                                                @click="reviewForm.rating = star"
                                                class="p-1 transition-transform hover:scale-110 focus:outline-none"
                                            >
                                                <Star 
                                                    class="w-6 h-6" 
                                                    :class="star <= reviewForm.rating ? 'fill-amber-500 text-amber-500' : 'text-neutral-200 dark:text-neutral-700'"
                                                />
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-neutral-500 mb-2">Ulasan Jujur</label>
                                        <textarea 
                                            v-model="reviewForm.review_text"
                                            rows="3" 
                                            class="w-full px-4 py-3 bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800 rounded-lg focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-sm transition-all resize-none"
                                            placeholder="Ceritakan pengalaman mentoring, kultur kerja..."
                                            required minlength="10"
                                        ></textarea>
                                        <p v-if="reviewForm.errors.review_text" class="text-rose-500 text-xs mt-1.5">{{ reviewForm.errors.review_text }}</p>
                                    </div>
                                    <div class="flex justify-end pt-2">
                                        <Button type="submit" variant="primary" :disabled="reviewForm.processing">
                                            Kirim Ulasan
                                        </Button>
                                    </div>
                                </form>
                            </div>
                            <div v-else class="p-5 bg-neutral-50 dark:bg-neutral-900 rounded-xl border border-neutral-200/60 dark:border-neutral-800 flex items-center justify-between">
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">Masuk untuk menulis ulasan perusahaan ini.</p>
                                <Button variant="secondary" @click="inertiaRouter.visit('/login')">Login</Button>
                            </div>
                        </section>
                    </div>

                    <!-- Sidebar Stats -->
                    <aside class="space-y-6 animate-reveal delay-200 opacity-0">
                        <div class="bg-white dark:bg-neutral-900 p-6 rounded-xl border border-neutral-200/60 dark:border-neutral-800 shadow-sm hover:shadow-md hover:border-primary-200/50 transition-all duration-300">
                            <h3 class="text-xs font-semibold font-medium text-neutral-500 dark:text-neutral-400 mb-6">Statistik Magang</h3>
                            <div class="space-y-6">
                                <div>
                                    <p class="text-3xl font-bold text-neutral-900 dark:text-white mb-1">{{ internships.length }}</p>
                                    <p class="text-xs text-neutral-500">Lowongan Aktif</p>
                                </div>
                                <div class="pt-6 border-t border-neutral-100 dark:border-neutral-800">
                                    <p class="text-3xl font-bold text-neutral-900 dark:text-white mb-1">45+</p>
                                    <p class="text-xs text-neutral-500">Alumni Magang</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-white dark:bg-neutral-900 border border-emerald-200/60 dark:border-emerald-900/30 rounded-xl flex items-start gap-3 shadow-sm">
                            <ShieldCheck class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" />
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 leading-relaxed">Perusahaan ini telah diverifikasi oleh tim kepatuhan InternHub.</p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
