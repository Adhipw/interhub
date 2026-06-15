<script setup lang="ts">
import { computed } from 'vue';
import Badge from './Badge.vue';
import { useLangStore } from '@/Stores/lang';

interface Props {
    status: string;
    size?: 'xs' | 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
});

const langStore = useLangStore();
const t = (key: string) => langStore.t(key);

const statusMap = computed(() => ({
    pending: { variant: 'warning', label: t('status.pending') },
    reviewing: { variant: 'info', label: t('status.reviewing') },
    interviewing: { variant: 'primary', label: t('status.interviewing') },
    offered: { variant: 'accent', label: t('status.offered') },
    accepted: { variant: 'success', label: t('status.accepted') },
    rejected: { variant: 'danger', label: t('status.rejected') },
    withdrawn: { variant: 'secondary', label: t('status.withdrawn') },
}));

const currentStatus = computed(() => statusMap.value[props.status as keyof typeof statusMap.value] || { variant: 'secondary', label: props.status });
</script>

<template>
    <Badge :variant="(currentStatus.variant as any)" :size="size">
        {{ currentStatus.label }}
    </Badge>
</template>
