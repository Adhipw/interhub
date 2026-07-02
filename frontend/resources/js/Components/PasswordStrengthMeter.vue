<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    password?: string;
}>();

const strength = computed(() => {
    const pwd = props.password || '';
    if (!pwd) return 0;
    
    let score = 0;
    if (pwd.length >= 8) score++;
    if (/[A-Z]/.test(pwd)) score++;
    if (/[a-z]/.test(pwd)) score++;
    if (/[0-9]/.test(pwd)) score++;
    if (/[^A-Za-z0-9]/.test(pwd)) score++;
    
    return score;
});

const strengthText = computed(() => {
    switch (strength.value) {
        case 0: return '';
        case 1: return 'Sangat Lemah';
        case 2: return 'Lemah';
        case 3: return 'Cukup';
        case 4: return 'Kuat';
        case 5: return 'Sangat Kuat';
        default: return '';
    }
});

const strengthColor = computed(() => {
    switch (strength.value) {
        case 1: return 'bg-red-500';
        case 2: return 'bg-orange-500';
        case 3: return 'bg-yellow-500';
        case 4: return 'bg-blue-500';
        case 5: return 'bg-green-500';
        default: return 'bg-slate-200';
    }
});
</script>

<template>
    <div class="mt-3">
        <div class="flex gap-1 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div 
                v-for="i in 5" 
                :key="i"
                class="flex-1 transition-colors duration-500"
                :class="[i <= strength ? strengthColor : 'bg-transparent']"
            ></div>
        </div>
        <div v-if="password" class="mt-2 flex justify-between items-center text-xs font-medium">
            <span class="text-slate-400">Keamanan Password</span>
            <span :class="strength > 0 ? strengthColor.replace('bg-', 'text-') : 'text-slate-400'">
                {{ strengthText }}
            </span>
        </div>
    </div>
</template>
