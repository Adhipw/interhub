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
        "bg-white dark:bg-neutral-900 border border-neutral-200/60 dark:border-neutral-800 rounded-xl transition-all duration-300",
        props.hoverable ? "hover:shadow-md hover:border-neutral-300 dark:hover:border-neutral-700" : "shadow-sm",
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
