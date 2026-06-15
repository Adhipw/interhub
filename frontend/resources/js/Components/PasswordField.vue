<script setup lang="ts">
import { ref } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';
import Input from './Input.vue';

interface Props {
    modelValue: string;
    label?: string;
    placeholder?: string;
    error?: string;
    required?: boolean;
}

defineProps<Props>();
const emit = defineEmits(['update:modelValue']);

const showPassword = ref(false);
</script>

<template>
    <Input
        :type="showPassword ? 'text' : 'password'"
        :modelValue="modelValue"
        @update:modelValue="emit('update:modelValue', $event)"
        :label="label"
        :placeholder="placeholder"
        :error="error"
        :required="required"
    >
        <template #suffix>
            <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 p-1 text-neutral-400 hover:text-primary-600 transition-colors focus:outline-none"
            >
                <Eye v-if="!showPassword" class="w-5 h-5" />
                <EyeOff v-else class="w-5 h-5" />
            </button>
        </template>
    </Input>
</template>
