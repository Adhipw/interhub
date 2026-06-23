<script setup lang="ts">
import { reactive, ref } from 'vue';
import { Head } from '@/Components';
import { router as inertiaRouter } from '@inertiajs/vue3';
import api from '@/Services/api';

import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import { BuildingOfficeIcon } from '@heroicons/vue/24/outline';

const processing = ref(false);
const errors = reactive<Record<string, string[]>>({});

const form = reactive({
  name: '',
  website: '',
  location: '',
  description: '',
});

const submit = async () => {
  processing.value = true;
  Object.keys(errors).forEach(key => delete errors[key]);

  try {
    await api.post('/hr/companies', form);
    inertiaRouter.visit('/hr/dashboard');
  } catch (error: any) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    }
  } finally {
    processing.value = false;
  }
};
</script>

<template>
  <Head title="Daftarkan Perusahaan" />

  <DashboardLayout>
    <template #header>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Daftarkan Perusahaan</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400">Lengkapi data perusahaan Anda untuk mulai memposting lowongan magang.</p>
    </template>

    <div class="max-w-3xl mx-auto">
      <Card class="p-8 bg-white dark:bg-slate-900/50 border-none dark:border dark:border-white/5 shadow-2xl">
        <form class="space-y-6" @submit.prevent="submit">
          <div class="flex items-center justify-center p-8 bg-primary-50 dark:bg-primary-900/10 rounded-[2rem] mb-10">
            <div class="w-20 h-20 bg-white dark:bg-slate-800 rounded-[1.5rem] flex items-center justify-center shadow-xl">
              <BuildingOfficeIcon class="w-10 h-10 text-primary-600" />
            </div>
          </div>

          <Input 
            v-model="form.name"
            label="Nama Perusahaan"
            placeholder="Misal: PT Teknologi Maju Jaya"
            required
            :error="errors.name ? errors.name[0] : undefined"
          />

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Input 
              v-model="form.website"
              label="Website (Opsional)"
              placeholder="https://perusahaan.com"
              type="url"
              :error="errors.website ? errors.website[0] : undefined"
            />
            <Input 
              v-model="form.location"
              label="Lokasi Utama"
              placeholder="Jakarta, Indonesia"
              required
              :error="errors.location ? errors.location[0] : undefined"
            />
          </div>

          <div>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-400 uppercase tracking-wider ml-1 mb-1">Deskripsi Perusahaan</label>
            <textarea 
              v-model="form.description"
              rows="5"
              class="w-full rounded-xl border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-900 text-sm text-slate-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500 focus:ring-primary-500 focus:border-primary-500 transition-all px-4 py-3"
              placeholder="Ceritakan tentang visi, misi, dan budaya perusahaan Anda..."
              required
            ></textarea>
            <div v-if="errors.description" class="mt-1 text-xs text-red-600">{{ errors.description[0] }}</div>
          </div>

          <div class="pt-4">
            <Button type="submit" class="w-full py-3 text-lg rounded-xl" :loading="processing">
              Daftarkan Perusahaan
            </Button>
          </div>
        </form>
      </Card>
    </div>
  </DashboardLayout>
</template>
