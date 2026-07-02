<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { ChevronDown } from 'lucide-vue-next';
import { useLangStore } from '@/Stores/lang';

const langStore = useLangStore();
const currentLocale = computed(() => langStore.locale);

const languages = [
    { code: 'id', name: 'Bahasa Indonesia', flag: '🇮🇩' },
    { code: 'en', name: 'English', flag: '🇺🇸' },
];

const currentLanguage = computed(() =>
    languages.find(lang => lang.code === currentLocale.value) || languages[0]
);

const switchLanguage = async (code: string) => {
    await langStore.setLocale(code as 'id' | 'en');
};

const props = withDefaults(defineProps<{
    direction?: 'up' | 'down';
}>(), {
    direction: 'down',
});

onMounted(() => {
    if (Object.keys(langStore.translations).length === 0) {
        langStore.fetchTranslations();
    }
});
</script>

<template>
    <div class="px-2 py-1">
        <div class="group relative">
            <button class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-slate-50 dark:bg-neutral-800 hover:bg-slate-100 dark:hover:bg-neutral-700 rounded-xl transition-colors border border-slate-100 dark:border-neutral-700 group-hover:border-primary-400">
                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="text-lg leading-none">{{ currentLanguage.flag }}</span>
                    <span class="text-sm font-bold text-slate-700 dark:text-neutral-200 lg:hidden xl:block">{{ currentLanguage.name }}</span>
                    <span class="text-sm font-bold text-slate-700 dark:text-neutral-200 hidden lg:block xl:hidden uppercase">{{ currentLanguage.code }}</span>
                </div>
                <ChevronDown class="w-4 h-4 text-slate-400 group-hover:text-primary-600 transition-colors" />
            </button>

            <div
                class="absolute left-0 w-full bg-white dark:bg-neutral-900 rounded-xl shadow-2xl border border-slate-100 dark:border-neutral-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-colors duration-300 z-[100] overflow-hidden"
                :class="direction === 'up' ? 'bottom-full mb-2' : 'top-full mt-2'"
            >
                <div class="p-2 space-y-1">
                    <button
                        v-for="lang in languages"
                        :key="lang.code"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left"
                        :class="currentLocale === lang.code ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'hover:bg-slate-50 dark:hover:bg-neutral-800 text-slate-600 dark:text-neutral-400'"
                        @click="switchLanguage(lang.code)"
                    >
                        <span class="text-xl leading-none">{{ lang.flag }}</span>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold">{{ lang.name }}</span>
                            <span class="text-xs opacity-50 font-medium">{{ lang.code }}</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
