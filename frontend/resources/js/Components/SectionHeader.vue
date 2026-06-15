<script setup lang="ts">
interface Props {
    title: string;
    subtitle?: string;
    align?: 'left' | 'center';
}

withDefaults(defineProps<Props>(), {
    align: 'left',
});
</script>

<template>
    <div :class="[align === 'center' ? 'text-center max-w-3xl mx-auto' : 'max-w-2xl']" class="mb-16">
        <h2 class="text-3xl md:text-5xl font-extrabold text-neutral-900 dark:text-white mb-6 tracking-tight" v-html="title"></h2>
        <p v-if="subtitle" class="text-lg text-neutral-500 dark:text-neutral-400 font-medium leading-relaxed">
            {{ subtitle }}
        </p>
    </div>
</template>
