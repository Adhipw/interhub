<script setup lang="ts">
import { computed } from 'vue';
import { 
    Inbox, 
    SearchX, 
    FileWarning, 
    ShieldAlert,
    LayoutList,
    Clock
} from 'lucide-vue-next';

interface Props {
    type?: 'empty' | 'search' | 'error' | 'unauthorized' | 'loading';
    title?: string;
    description?: string;
    actionLabel?: string;
    actionIcon?: any;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'empty'
});

const emit = defineEmits<{
    (e: 'action'): void;
}>();

const icon = computed(() => {
    switch (props.type) {
        case 'search': return SearchX;
        case 'error': return FileWarning;
        case 'unauthorized': return ShieldAlert;
        case 'loading': return Clock;
        default: return Inbox;
    }
});
</script>

<template>
    <div class="flex flex-col items-center justify-center py-20 px-4 text-center animate-fade-in">
        <div class="relative mb-8">
            <div class="absolute inset-0 bg-primary-100 dark:bg-primary-900/20 rounded-full blur-3xl opacity-50 scale-150"></div>
            <div class="relative w-24 h-24 bg-white dark:bg-neutral-800 rounded-[2rem] shadow-xl border border-neutral-100 dark:border-neutral-700 flex items-center justify-center transform -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                <component :is="icon" class="w-12 h-12 text-primary-600 dark:text-primary-400" />
            </div>
            <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-slate-900 dark:bg-neutral-700 rounded-full flex items-center justify-center border-4 border-white dark:border-neutral-900">
                <LayoutList class="w-4 h-4 text-white" />
            </div>
        </div>

        <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-3 tracking-tight">
            {{ title || 'Belum Ada Data' }}
        </h3>
        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-8 leading-relaxed">
            {{ description || 'Data yang Anda cari tidak ditemukan atau belum tersedia saat ini.' }}
        </p>

        <button 
            v-if="actionLabel"
            class="inline-flex items-center gap-2 bg-slate-900 dark:bg-neutral-800 text-white px-8 py-3 rounded-full font-bold hover:bg-slate-800 transition-all shadow-lg"
            @click="emit('action')"
        >
            <component :is="actionIcon" v-if="actionIcon" class="w-4 h-4" />
            {{ actionLabel }}
        </button>

        <slot name="actions"></slot>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
