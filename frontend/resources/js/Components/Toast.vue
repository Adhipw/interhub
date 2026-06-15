<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { CheckCircle2, AlertCircle, X } from 'lucide-vue-next';

const props = defineProps<{
    message: string | null;
    type?: 'success' | 'error';
}>();

const visible = ref(false);

watch(() => props.message, (newVal) => {
    if (newVal) {
        visible.value = true;
        setTimeout(() => {
            visible.value = false;
        }, 5000);
    }
}, { immediate: true });
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible && message" class="fixed bottom-10 right-10 z-[100] max-w-sm w-full bg-white shadow-2xl rounded-2xl border border-slate-100 overflow-hidden">
            <div class="p-4 flex items-center gap-4">
                <div :class="[
                    'w-10 h-10 rounded-full flex items-center justify-center shrink-0',
                    type === 'error' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'
                ]">
                    <CheckCircle2 v-if="type !== 'error'" class="w-6 h-6" />
                    <AlertCircle v-else class="w-6 h-6" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900">{{ message }}</p>
                </div>
                <button @click="visible = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <X class="w-5 h-5" />
                </button>
            </div>
            <!-- Progress Bar -->
            <div class="h-1 bg-slate-100 w-full overflow-hidden">
                <div 
                    class="h-full transition-all duration-[5000ms] linear"
                    :class="type === 'error' ? 'bg-red-500' : 'bg-green-500'"
                    :style="{ width: visible ? '100%' : '0%' }"
                ></div>
            </div>
        </div>
    </Transition>
</template>
