<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Head } from '@/Components';

import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { route } from 'ziggy-js';
import { Building2, ChevronRight, Plus } from 'lucide-vue-next';

interface Props {
  companies: any[];
}

defineProps<Props>();

const selectCompany = (id: number) => {
  router.post(route('hr.companies.switch', id), {}, {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Pilih Perusahaan" />

  <DashboardLayout>
    <template #header>
      <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white tracking-tight">Pilih Perusahaan</h2>
        <p class="mt-2 text-slate-500 dark:text-gray-400">Pilih profil perusahaan yang ingin kamu kelola untuk masuk ke dashboard rekrutmen.</p>
      </div>
    </template>

    <div class="max-w-4xl mx-auto mt-8">
      <div class="grid grid-cols-1 gap-4">
        <!-- Editorial Company List -->
        <button 
          v-for="company in companies" 
          :key="company.id" 
          @click="selectCompany(company.id)"
          class="flex items-center justify-between p-6 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl hover:border-primary-500 hover:ring-1 hover:ring-primary-500 transition-all group text-left shadow-sm"
        >
          <div class="flex items-center">
            <div class="h-14 w-14 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 group-hover:text-primary-600 transition-colors">
              <Building2 class="w-7 h-7 stroke-1" />
            </div>
            <div class="ml-6">
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ company.name }}</h3>
              <p class="text-sm text-slate-500">{{ company.location }} • {{ company.website || 'No website' }}</p>
            </div>
          </div>
          <div class="flex items-center text-slate-300 group-hover:text-primary-600 transition-colors">
            <span class="text-xs font-bold mr-4 opacity-0 group-hover:opacity-100 transition-opacity">Kelola Dashboard</span>
            <ChevronRight class="w-5 h-5" />
          </div>
        </button>

        <!-- Create New Action -->
        <Link :href="route('hr.companies.create')" class="group">
          <div class="flex items-center justify-center p-8 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-2xl hover:border-primary-500 hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all mt-4">
            <div class="flex flex-col items-center">
              <div class="p-3 rounded-full bg-slate-50 dark:bg-slate-900 mb-4 group-hover:scale-110 transition-transform">
                <Plus class="w-6 h-6 text-slate-400 group-hover:text-primary-600" />
              </div>
              <p class="text-sm font-bold text-slate-600 dark:text-slate-400 group-hover:text-primary-600">Daftarkan Perusahaan Baru</p>
              <p class="text-xs text-slate-400 mt-1">Mulai rekrut talenta terbaik untuk tim Anda</p>
            </div>
          </div>
        </Link>
      </div>
    </div>
  </DashboardLayout>
</template>
