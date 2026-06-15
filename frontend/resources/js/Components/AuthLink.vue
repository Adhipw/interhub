<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
defineProps<{
    href: string;
    label?: string;
    linkText: string;
}>();
</script>

<template>
    <p class="text-center text-sm text-slate-500 mt-8">
        {{ label }}
        <Link 
            :href="href" 
            class="font-bold text-blue-600 hover:text-blue-700 transition-colors"
        >
            {{ linkText }}
        </Link>
    </p>
</template>

