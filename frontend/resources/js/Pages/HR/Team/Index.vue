<script setup lang="ts">
import logger from '@/Lib/logger';
import { Head } from '@/Components';

import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import { 
  UsersIcon, 
  UserPlusIcon, 
  TrashIcon,
  Pencil
} from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import api from '@/Services/api';
import { useToastStore } from '@/Stores/toast';

interface UserProfile {
    id: number;
    name: string;
    email: string;
    profile_photo_url?: string;
}

interface TeamMember {
    id: number;
    company_id: number;
    user_id: number;
    role: string;
    is_active: boolean | number;
    user?: UserProfile;
}

interface CompanyRoleOption {
    value: string;
    label: string;
}

const members = ref<TeamMember[]>([]);
const roles = ref<CompanyRoleOption[]>([]);
const loading = ref(true);

const showAddModal = ref(false);
const showEditModal = ref(false);
const selectedMember = ref<TeamMember | null>(null);

const fetchMembers = async () => {
  loading.value = true;
  try {
    const response = await api.get('/hr/team');
    members.value = response.data?.data?.members || [];
    roles.value = response.data?.data?.roles || [];
  } catch (error) {
    logger.error('Failed to fetch members:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchMembers();
});

const addForm = ref({
  email: '',
  role: 'hr',
  processing: false,
  errors: {
    email: '',
    role: '',
  }
});

const editForm = ref({
  role: '',
  is_active: true,
  processing: false,
});

const openEditModal = (member: TeamMember) => {
  selectedMember.value = member;
  editForm.value.role = member.role;
  editForm.value.is_active = member.is_active === 1 || member.is_active === true;
  showEditModal.value = true;
};

const submitAdd = async () => {
  addForm.value.processing = true;
  addForm.value.errors = { email: '', role: '' };
  try {
    const response = await api.post('/hr/team', {
      email: addForm.value.email,
      role: addForm.value.role,
    });
    const toastStore = useToastStore();
    toastStore.success(response.data?.message || 'Anggota tim berhasil ditambahkan.');
    showAddModal.value = false;
    addForm.value.email = '';
    addForm.value.role = 'hr';
    fetchMembers();
  } catch (error) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const err = error as any;
    if (err.response?.status === 422) {
      const serverErrors = err.response.data?.errors || {};
      addForm.value.errors.email = serverErrors.email?.[0] || '';
      addForm.value.errors.role = serverErrors.role?.[0] || '';
    } else {
      const toastStore = useToastStore();
      toastStore.error(err.response?.data?.message || 'Gagal menambahkan anggota.');
    }
  } finally {
    addForm.value.processing = false;
  }
};

const submitEdit = async () => {
  if (!selectedMember.value) return;
  editForm.value.processing = true;
  try {
    const response = await api.put(`/hr/team/${selectedMember.value.id}`, {
      role: editForm.value.role,
      is_active: editForm.value.is_active,
    });
    const toastStore = useToastStore();
    toastStore.success(response.data?.message || 'Data anggota tim diperbarui.');
    showEditModal.value = false;
    fetchMembers();
  } catch (error) {
    logger.error('Failed to update member:', error);
  } finally {
    editForm.value.processing = false;
  }
};

const deleteMember = async (id: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus anggota ini dari perusahaan?')) {
    try {
      const response = await api.delete(`/hr/team/${id}`);
      const toastStore = useToastStore();
      toastStore.success(response.data?.message || 'Anggota tim berhasil dihapus.');
      fetchMembers();
    } catch (error) {
      const toastStore = useToastStore();
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const err = error as any;
      if (err.response?.data?.message) {
        toastStore.error(err.response.data.message);
      } else {
        toastStore.error('Gagal menghapus anggota tim.');
      }
    }
  }
};
</script>

<template>
  <Head title="Manajemen Tim HR" />

  <DashboardLayout>
    <template #header>
      <div class="flex items-center justify-between pb-6 border-b border-gray-100 dark:border-gray-800">
        <div>
          <h2 class="text-2xl font-display font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Tim</h2>
          <p class="text-sm text-slate-500 font-medium">Kelola anggota tim HR dan Mentor yang memiliki akses ke perusahaan ini.</p>
        </div>
        <Button @click="showAddModal = true">
          <UserPlusIcon class="w-4 h-4 mr-2" />
          Tambah Anggota
        </Button>
      </div>
    </template>

    <div class="mt-8">
      <!-- Members Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="member in members" :key="member.id" class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
          <!-- Active Status Indicator -->
          <div :class="member.is_active ? 'bg-green-500' : 'bg-slate-300'" class="absolute top-0 right-0 w-2 h-2 rounded-bl-xl"></div>
          
          <div class="flex items-start justify-between">
            <div class="h-14 w-14 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 font-bold text-xl uppercase">
              {{ member.user?.name?.charAt(0) || '' }}
            </div>
            <div class="flex items-center space-x-1">
              <button class="p-2 text-slate-400 hover:text-primary-600 transition-colors" @click="openEditModal(member)">
                <Pencil class="w-4 h-4" />
              </button>
              <button class="p-2 text-slate-400 hover:text-red-600 transition-colors" @click="deleteMember(member.id)">
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
          </div>

          <div class="mt-4">
            <h3 class="font-bold text-slate-900 dark:text-white truncate">{{ member.user?.name }}</h3>
            <p class="text-xs text-slate-500 truncate">{{ member.user?.email }}</p>
          </div>

          <div class="mt-6 flex items-center justify-between">
            <span class="px-2.5 py-1 bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-slate-100 dark:border-slate-700">
              {{ member.role }}
            </span>
            <div v-if="!member.is_active" class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold rounded-md">
              Nonaktif
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="members.length === 0" class="py-20 text-center bg-white dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-100 dark:border-slate-800">
        <UsersIcon class="w-12 h-12 text-slate-200 mx-auto mb-4" />
        <p class="text-slate-500 font-medium">Belum ada anggota tim yang ditambahkan.</p>
        <Button variant="secondary" class="mt-4" @click="showAddModal = true">Tambah Sekarang</Button>
      </div>
    </div>

    <!-- Add Member Modal -->
    <Modal :show="showAddModal" title="Tambah Anggota Tim" @close="showAddModal = false">
      <form class="p-8 space-y-6" @submit.prevent="submitAdd">
        <p class="text-sm text-slate-500 leading-relaxed">
          Masukkan email user yang sudah terdaftar di InterHub untuk diberikan akses ke perusahaan ini.
        </p>
        
        <Input 
          v-model="addForm.email"
          label="Email User"
          placeholder="user@example.com"
          :error="addForm.errors.email"
          required
        />

        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Role Anggota</label>
          <select v-model="addForm.role" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm focus:ring-primary-500 focus:border-primary-500 transition-all h-[42px]">
            <option v-for="role in roles" :key="role.value" :value="role.value">
              {{ role.label }}
            </option>
          </select>
          <div v-if="addForm.errors.role" class="mt-2 text-xs text-red-600">{{ addForm.errors.role }}</div>
        </div>

        <div class="flex justify-end space-x-3">
          <Button type="button" variant="secondary" @click="showAddModal = false">Batal</Button>
          <Button type="submit" :loading="addForm.processing">Tambahkan</Button>
        </div>
      </form>
    </Modal>

    <!-- Edit Member Modal -->
    <Modal :show="showEditModal" :title="`Edit Anggota: ${selectedMember?.user?.name || ''}`" @close="showEditModal = false">
      <form class="p-8 space-y-6" @submit.prevent="submitEdit">
        <div>
          <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Role Anggota</label>
          <select v-model="editForm.role" class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm focus:ring-primary-500 focus:border-primary-500 transition-all h-[42px]">
            <option v-for="role in roles" :key="role.value" :value="role.value">
              {{ role.label }}
            </option>
          </select>
        </div>

        <div class="flex items-center">
          <input 
            id="is_active" 
            v-model="editForm.is_active" 
            type="checkbox"
            class="rounded border-slate-300 text-primary-600 focus:ring-primary-500 h-5 w-5"
          />
          <label for="is_active" class="ml-3 text-sm font-bold text-slate-700 dark:text-slate-300">Status Aktif</label>
        </div>

        <div class="flex justify-end space-x-3">
          <Button type="button" variant="secondary" @click="showEditModal = false">Batal</Button>
          <Button type="submit" :loading="editForm.processing">Simpan Perubahan</Button>
        </div>
      </form>
    </Modal>
  </DashboardLayout>
</template>
