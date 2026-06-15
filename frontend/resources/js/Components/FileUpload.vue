<script setup lang="ts">
import { ref, watch } from 'vue';
import { Upload, X, FileText, CheckCircle2, AlertCircle } from 'lucide-vue-next';
import { useLangStore } from '@/Stores/lang';

const langStore = useLangStore();
const t = (key: string) => langStore.t(key);

const props = defineProps<{
    label: string;
    accept?: string;
    maxSize?: number; // in MB
    currentFile?: string | null;
    modelValue?: File | null;
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', file: File | null): void;
    (e: 'clear'): void;
}>();

const isDragging = ref(false);
const selectedFile = ref<File | null>(props.modelValue || null);
const error = ref<string | null>(null);

watch(() => props.modelValue, (newVal) => {
    selectedFile.value = newVal || null;
});

const handleFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        validateAndSelect(input.files[0]);
    }
};

const handleDrop = (e: DragEvent) => {
    isDragging.value = false;
    if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
        validateAndSelect(e.dataTransfer.files[0]);
    }
};

const validateAndSelect = (file: File) => {
    error.value = null;
    
    // Generic validation based on 'accept' prop
    if (props.accept) {
        const acceptedTypes = props.accept.split(',').map(type => type.trim());
        const isAccepted = acceptedTypes.some(type => {
            if (type.startsWith('.')) {
                return file.name.toLowerCase().endsWith(type.toLowerCase());
            }
            return file.type === type;
        });

        if (!isAccepted) {
            error.value = `Tipe file tidak didukung. Harap gunakan: ${props.accept}`;
            return;
        }
    }

    if (props.maxSize && file.size > props.maxSize * 1024 * 1024) {
        error.value = `Ukuran file terlalu besar. Maksimal ${props.maxSize}MB.`;
        return;
    }

    selectedFile.value = file;
    emit('update:modelValue', file);
};

const clearFile = () => {
    selectedFile.value = null;
    error.value = null;
    emit('update:modelValue', null);
    emit('clear');
};
</script>

<template>
    <div class="space-y-4">
        <label v-if="label" class="text-sm font-bold text-slate-700 flex items-center gap-2">
            {{ label }}
            <span v-if="currentFile" class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full uppercase tracking-wider">Sudah Tersedia</span>
        </label>

        <div 
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
                'relative border-2 border-dashed rounded-2xl p-8 transition-all flex flex-col items-center justify-center gap-4 cursor-pointer',
                isDragging ? 'border-primary-500 bg-primary-50/50 dark:bg-primary-900/10' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'
            ]"
            @click="($refs.fileInput as HTMLInputElement).click()"
        >
            <input 
                type="file" 
                ref="fileInput" 
                class="hidden" 
                :accept="accept"
                @change="handleFileChange"
            />

            <div v-if="!selectedFile" class="text-center">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <Upload :class="['w-8 h-8', isDragging ? 'text-primary-500' : 'text-slate-400']" />
                </div>
                <p class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ isDragging ? 'Lepas file di sini' : 'Klik atau tarik file ke sini' }}
                </p>
                <p class="text-xs text-slate-500">
                    {{ accept ? `Format: ${accept}` : 'Semua format' }} 
                    <span v-if="maxSize">(Maks {{ maxSize }}MB)</span>
                </p>
                
                <div v-if="currentFile" class="mt-4 p-2 bg-white rounded-lg border border-slate-100 shadow-sm flex items-center gap-2 text-xs text-slate-500">
                    <CheckCircle2 class="w-4 h-4 text-green-500" />
                    File saat ini tersimpan aman
                </div>
            </div>

            <div v-else class="w-full flex items-center gap-4 bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center shrink-0">
                    <FileText class="w-6 h-6 text-primary-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ selectedFile.name }}</p>
                    <p class="text-[10px] text-slate-500 font-mono uppercase">{{ (selectedFile.size / 1024).toFixed(1) }} KB</p>
                </div>
                <button 
                    @click.stop="clearFile"
                    class="p-2 hover:bg-slate-200 dark:hover:bg-slate-800 rounded-lg transition-colors text-slate-400 hover:text-red-500"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>
        </div>

        <div v-if="error" class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/10 text-red-600 rounded-xl text-xs font-bold border border-red-100 dark:border-red-900/20">
            <AlertCircle class="w-4 h-4" />
            {{ error }}
        </div>
    </div>
</template>
