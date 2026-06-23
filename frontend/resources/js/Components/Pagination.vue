<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface Link {
    url: string | null;
    label: string;
    active: boolean;
}

defineProps<{
    links: Link[];
}>();
</script>

<template>
    <nav v-if="links && links.length > 3" class="flex items-center justify-center space-x-1">
        <template v-for="(link, key) in links" :key="key">
            <div
v-if="link.url === null" 
                 class="px-4 py-2 text-sm font-bold text-slate-300 cursor-not-allowed"
                 v-html="link.label" />
            
            <Link
v-else 
                  :href="link.url" 
                  class="px-4 py-2 text-sm font-bold rounded-xl transition-all"
                  :class="{ 'bg-primary-600 text-white shadow-lg shadow-primary-200': link.active, 'text-slate-500 hover:bg-slate-100 hover:text-slate-900': !link.active }"
                  v-html="link.label" />
        </template>
    </nav>
</template>

