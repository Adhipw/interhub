<script setup lang="ts">
import { useLangStore } from '@/Stores/lang';

const langStore = useLangStore();
const t = (key: string) => langStore.t(key);

defineProps<{
    label?: string;
}>();
</script>

<template>
    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-slate-100"></div>
        </div>
        <div class="relative flex justify-center text-xs font-bold font-medium">
            <span class="bg-white px-4 text-slate-400">{{ label || t('common.or') }}</span>
        </div>
    </div>
</template>
