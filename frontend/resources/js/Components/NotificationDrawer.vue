<script setup lang="ts">
import { onMounted } from 'vue';
import { useNotifications } from '@/Composables/useNotifications';
import { Bell, X, Check, Clock } from 'lucide-vue-next';
import { formatDistanceToNow } from 'date-fns';

const { notifications, unreadCount, fetchNotifications, markAsRead, setupListeners } = useNotifications();

onMounted(() => {
    fetchNotifications();
    setupListeners();
});

const props = defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-[110] overflow-hidden">
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-sm" @click="emit('close')"></div>
        <div class="absolute inset-y-0 right-0 max-w-sm w-full bg-white shadow-2xl flex flex-col transform transition-transform duration-300">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-slate-900">Notifications</h2>
                    <span v-if="unreadCount > 0" class="px-2 py-0.5 bg-blue-100 text-blue-600 text-xs font-bold rounded-full">
                        {{ unreadCount }} New
                    </span>
                </div>
                <button @click="emit('close')" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
                    <X class="w-6 h-6" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div v-if="notifications.length === 0" class="h-full flex flex-col items-center justify-center p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <Bell class="w-8 h-8 text-slate-300" />
                    </div>
                    <p class="text-slate-500">No notifications yet.</p>
                </div>

                <div v-else class="divide-y divide-slate-50">
                    <div 
                        v-for="notification in notifications" 
                        :key="notification.id"
                        class="p-6 hover:bg-slate-50 transition-colors relative group"
                        :class="{ 'bg-blue-50/30': !notification.read_at }"
                    >
                        <div class="flex gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-900 font-medium mb-1">
                                    {{ notification.data.message }}
                                </p>
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <Clock class="w-3 h-3" />
                                    {{ formatDistanceToNow(new Date(notification.created_at)) }} ago
                                </div>
                            </div>
                            <button 
                                v-if="!notification.read_at"
                                @click="markAsRead(notification.id)"
                                class="p-1.5 text-slate-300 hover:text-blue-600 transition-colors rounded-full hover:bg-white border border-transparent hover:border-blue-100"
                                title="Mark as read"
                            >
                                <Check class="w-4 h-4" />
                            </button>
                        </div>
                        <div v-if="!notification.read_at" class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                <button class="w-full py-3 text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors">
                    View All Notifications
                </button>
            </div>
        </div>
    </div>
</template>
