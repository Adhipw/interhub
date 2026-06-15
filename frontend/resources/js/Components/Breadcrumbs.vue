<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Home } from 'lucide-vue-next';

defineProps<{
    items: {
        label: string;
        href?: string;
    }[];
}>();
</script>

<template>
    <nav class="flex mb-8 overflow-x-auto no-scrollbar" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 whitespace-nowrap">
            <li class="inline-flex items-center">
                <Link 
                    href="/dashboard" 
                    class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-primary-600 transition-colors"
                >
                    <Home class="w-4 h-4 mr-2" />
                    Beranda
                </Link>
            </li>
            <li v-for="(item, index) in items" :key="index">
                <div class="flex items-center">
                    <ChevronRight class="w-4 h-4 text-slate-300 mx-1" />
                    <Link 
                        v-if="item.href" 
                        :href="item.href" 
                        class="ml-1 text-sm font-bold text-slate-400 hover:text-primary-600 transition-colors md:ml-2"
                    >
                        {{ item.label }}
                    </Link>
                    <span 
                        v-else 
                        class="ml-1 text-sm font-bold text-slate-900 md:ml-2"
                    >
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

