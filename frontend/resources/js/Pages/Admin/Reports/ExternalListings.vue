<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Head } from '@/Components';

import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import { route } from 'ziggy-js';
import { 
  Puzzle, 
  CheckCircle, 
  XCircle, 
  ExternalLink,
  Search,
  Building2,
  AlertCircle
} from 'lucide-vue-next';

interface Props {
  listings: {
    data: any[];
    links: any[];
  };
}

defineProps<Props>();

const approve = (id: number) => {
    useForm({}).post(route('admin.integrations.review.approve', id), {
        preserveScroll: true,
    });
};

const reject = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menolak dan menghapus lowongan ini?')) {
        useForm({}).post(route('admin.integrations.review.reject', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
  <Head title="Review Lowongan Eksternal" />

  <DashboardLayout>
    <div class="space-y-10">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
          <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Review Lowongan Eksternal 🧩</h1>
          <p class="text-slate-500 dark:text-slate-400">Verifikasi lowongan yang diimpor dari partner eksternal sebelum dipublikasikan.</p>
        </div>
        
        <div class="flex items-center gap-3">
          <div class="relative group">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-primary-600 transition-colors" />
            <input 
              type="text" 
              placeholder="Cari lowongan..." 
              class="pl-11 pr-6 py-3 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all w-64"
            />
          </div>
        </div>
      </div>

      <!-- Info Alert -->
      <div class="bg-primary-50 dark:bg-primary-900/10 border border-primary-100 dark:border-primary-900/30 p-6 rounded-2xl flex items-start gap-4">
        <AlertCircle class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" />
        <p class="text-sm text-primary-700 dark:text-primary-400 leading-relaxed">
          Semua lowongan dari MagangHub, CSV, atau Webhook masuk ke dalam status <strong>Pending Review</strong>. Anda wajib memeriksa validitas deskripsi dan kredibilitas perusahaan sebelum menekan tombol Approve.
        </p>
      </div>

      <!-- Listings Table -->
      <Card class="overflow-hidden border-none shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lowongan & Perusahaan</th>
                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sumber</th>
                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tipe & Lokasi</th>
                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
              <tr v-for="listing in listings.data" :key="listing.id" class="hover:bg-slate-50/30 dark:hover:bg-slate-900/20 transition-colors group">
                <td class="px-8 py-6">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-primary-600 shrink-0">
                      <Building2 class="w-5 h-5" />
                    </div>
                    <div>
                      <p class="text-sm font-bold text-slate-900 dark:text-white">{{ listing.title }}</p>
                      <p class="text-xs text-slate-500 font-medium">{{ listing.company.name }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6">
                  <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg text-[10px] font-bold text-slate-500 uppercase tracking-widest border border-slate-200 dark:border-slate-700">
                    {{ listing.external_source }}
                  </span>
                </td>
                <td class="px-8 py-6">
                  <div class="flex flex-col gap-1">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ listing.type }}</p>
                    <p class="text-[10px] text-slate-500">{{ listing.location }}</p>
                  </div>
                </td>
                <td class="px-8 py-6 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a :href="listing.external_url" target="_blank" class="p-2 text-slate-400 hover:text-primary-600 transition-colors">
                      <ExternalLink class="w-4 h-4" />
                    </a>
                    <button 
                      class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 rounded-xl text-xs font-bold hover:bg-green-600 hover:text-white transition-all"
                      @click="approve(listing.id)"
                    >
                      <CheckCircle class="w-3.5 h-3.5" />
                      Approve
                    </button>
                    <button 
                      class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-600 hover:text-white transition-all"
                      @click="reject(listing.id)"
                    >
                      <XCircle class="w-3.5 h-3.5" />
                      Reject
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="listings.data.length === 0">
                <td colspan="4" class="px-8 py-20 text-center">
                   <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                     <Puzzle class="w-8 h-8" />
                   </div>
                   <p class="font-bold text-slate-900 dark:text-white">Tidak ada antrean review</p>
                   <p class="text-sm text-slate-500">Semua lowongan eksternal telah diproses.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  </DashboardLayout>
</template>
