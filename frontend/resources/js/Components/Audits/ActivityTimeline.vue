<script setup lang="ts">
import { computed } from 'vue';
import Badge from '@/Components/Badge.vue';
import { 
    PlusCircle, PencilLine, Trash2, Eye, 
    LogIn, Activity as ActivityIcon, Clock, MapPin, User
} from 'lucide-vue-next';

interface Activity {
    id: number;
    action: string;
    description: string;
    created_at_human: string;
    ip_address?: string;
    region?: string;
    user?: {
        name: string;
    };
}

const props = defineProps<{
    activities: Activity[];
}>();

const getIcon = (action: string) => {
    const a = action.toLowerCase();
    if (a.includes('create') || a.includes('submit')) return PlusCircle;
    if (a.includes('update') || a.includes('edit')) return PencilLine;
    if (a.includes('delete') || a.includes('withdraw')) return Trash2;
    if (a.includes('view')) return Eye;
    if (a.includes('login')) return LogIn;
    return ActivityIcon;
};
</script>

<template>
    <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 dark:before:via-slate-700 before:to-transparent">
        
        <div v-for="activity in activities" :key="activity.id" class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
            <!-- Icon -->
            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                <component :is="getIcon(activity.action)" class="w-5 h-5" />
            </div>
            
            <!-- Content -->
            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm transition-all hover:shadow-md">
                <div class="flex items-center justify-between space-x-2 mb-2">
                    <div class="font-bold text-slate-800 dark:text-slate-100 uppercase text-[10px] tracking-widest">
                        {{ activity.action.replace('_', ' ') }}
                    </div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                        <Clock class="w-3 h-3 text-primary-500" />
                        {{ activity.created_at_human }}
                    </div>
                </div>
                <div class="text-slate-600 dark:text-slate-400 text-sm mb-4 leading-relaxed">
                    {{ activity.description }}
                </div>
                <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-50 dark:border-slate-800/50">
                    <div class="flex items-center gap-2">
                        <div v-if="activity.user" class="flex items-center gap-1.5 px-2 py-1 bg-slate-50 dark:bg-slate-800 rounded-lg">
                            <User class="w-3 h-3 text-slate-400" />
                            <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400">{{ activity.user.name }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-mono text-slate-300 dark:text-slate-600">
                            {{ activity.ip_address }}
                        </span>
                        <div v-if="activity.region" class="flex items-center gap-1 text-slate-400">
                            <MapPin class="w-2.5 h-2.5" />
                            <span class="text-[9px] font-bold uppercase tracking-tighter">{{ activity.region }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* Add any custom timeline styles here if needed */
</style>
