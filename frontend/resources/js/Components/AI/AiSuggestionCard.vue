<script setup lang="ts">
import { Sparkles, ArrowRight } from 'lucide-vue-next';

defineProps<{
    title: string;
    description: string;
    loading?: boolean;
}>();

defineEmits(['click']);
</script>

<template>
    <div 
        class="p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-200 hover:shadow-lg hover:shadow-blue-500/5 transition-all cursor-pointer group relative overflow-hidden"
        @click="$emit('click')"
    >
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 group-hover:bg-blue-600 transition-colors">
                <Sparkles class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors" />
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-slate-900 mb-1 flex items-center justify-between">
                    {{ title }}
                    <ArrowRight class="w-4 h-4 text-slate-300 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" />
                </h4>
                <p class="text-sm text-slate-500 leading-relaxed">{{ description }}</p>
            </div>
        </div>
        
        <!-- Subtle AI Badge -->
        
    </div>
</template>
