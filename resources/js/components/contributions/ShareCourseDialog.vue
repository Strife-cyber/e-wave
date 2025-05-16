<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
    open: boolean;
    course: any;
}>();

defineEmits<{
    (e: 'close'): void;
    (e: 'share', email: string, permission: string): void;
}>();

const shareEmail = ref('');
const sharePermission = ref('view');

const handleShare = () => {
    if (!shareEmail.value.trim()) return;
    emit('share', shareEmail.value, sharePermission.value);
    shareEmail.value = '';
    sharePermission.value = 'view';
};
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div
            class="animate-fadeIn mx-4 w-full max-w-md rounded-xl border border-purple-100 bg-white shadow-xl dark:border-purple-900/30 dark:bg-slate-800"
        >
            <div class="border-b border-purple-100 p-5 dark:border-purple-900/30">
                <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300">Share Course</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Share "{{ course?.title }}" with others</p>
            </div>

            <div class="space-y-4 p-5">
                <div>
                    <label for="share-email" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Email Address</label>
                    <input
                        id="share-email"
                        v-model="shareEmail"
                        type="email"
                        placeholder="Enter email address"
                        class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                    />
                </div>

                <div>
                    <label for="share-permission" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Permission</label>
                    <select
                        id="share-permission"
                        v-model="sharePermission"
                        class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                    >
                        <option value="view">View only</option>
                        <option value="edit">Can edit</option>
                        <option value="admin">Admin access</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 border-t border-purple-100 p-5 dark:border-purple-900/30">
                <button
                    @click="$emit('close')"
                    class="rounded-lg border border-purple-200 px-4 py-2 text-slate-700 transition-colors duration-300 hover:bg-slate-50 dark:border-purple-800 dark:text-slate-300 dark:hover:bg-slate-700"
                >
                    Cancel
                </button>
                <button
                    @click="handleShare"
                    class="rounded-lg bg-purple-600 px-4 py-2 text-white shadow-md transition-all duration-300 hover:bg-purple-700 hover:shadow-lg"
                    :disabled="!shareEmail.trim()"
                >
                    Share
                </button>
            </div>
        </div>
    </div>
</template>
