<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    hoverable?: boolean;
    padding?: 'none' | 'sm' | 'md' | 'lg' | 'xl';
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    hoverable: false,
    padding: 'md',
    class: '',
});

const paddings = {
    none: "p-0",
    sm: "p-4",
    md: "p-8",
    lg: "p-10",
    xl: "p-12",
};

const classes = computed(() => {
    return [
        "bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 rounded-[2.5rem] transition-all duration-500",
        props.hoverable ? "hover:shadow-2xl hover:shadow-primary-600/5 hover:border-primary-200 dark:hover:border-primary-900/30" : "shadow-premium-sm",
        paddings[props.padding],
        props.class
    ].join(' ');
});
</script>

<template>
    <div :class="classes">
        <slot name="header" />
        <slot />
        <slot name="footer" />
    </div>
</template>
