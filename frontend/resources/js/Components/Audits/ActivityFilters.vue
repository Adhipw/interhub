<script setup lang="ts">
import { ref, watch } from 'vue';
import Button from '@/Components/Button.vue';
import { Search, Filter, RotateCcw } from 'lucide-vue-next';
import { useLangStore } from '@/Stores/lang';

const langStore = useLangStore();
const t = (key: string) => langStore.t(key);

const props = defineProps<{
    filters?: any;
}>();

const emit = defineEmits<{
    (e: 'filter', form: any): void;
    (e: 'reset'): void;
}>();

const form = ref({
    search: props.filters?.search || '',
    type: props.filters?.type || '',
    date: props.filters?.date || ''
});

const submit = () => {
    emit('filter', form.value);
};

const reset = () => {
    form.value = {
        search: '',
        type: '',
        date: ''
    };
    emit('reset');
};

watch(() => form.value.type, submit);
watch(() => form.value.date, submit);
</script>

<template>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 font-medium mb-3">{{ t('admin.audit.search_label') }}</label>
                <div class="relative">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <input 
                        v-model="form.search"
                        type="text"
                        :placeholder="t('admin.audit.search_placeholder')" 
                        class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 transition-all dark:text-white"
                        @keyup.enter="submit"
                    />
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 font-medium mb-3">{{ t('admin.audit.filter_type') }}</label>
                <select 
                    v-model="form.type"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 transition-all dark:text-white"
                >
                    <option value="">{{ t('admin.audit.all_actions') }}</option>
                    <option value="profile_updated">Profile Updated</option>
                    <option value="application_submitted">Application Submitted</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 font-medium mb-3">{{ t('common.date') }}</label>
                <input 
                    v-model="form.date"
                    type="date"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 transition-all dark:text-white"
                />
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 pt-6 border-t border-slate-50 dark:border-slate-800/50">
            <Button variant="ghost" size="sm" class="px-6" @click="reset">
                <RotateCcw class="w-4 h-4 mr-2" /> {{ t('common.reset') }}
            </Button>
            <Button variant="primary" size="sm" class="px-8 shadow-lg shadow-primary-500/20" @click="submit">
                <Filter class="w-4 h-4 mr-2" /> {{ t('common.filter') }}
            </Button>
        </div>
    </div>
</template>
