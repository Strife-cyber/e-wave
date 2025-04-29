<script setup lang="ts">
import { computed } from 'vue';

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const isActive = computed(() => !props.event.left_at);

const props = defineProps<{
    event: {
        type: object;
        required: true;
    };
}>();
</script>

<template>
    <div
        class="flex items-center gap-3 rounded-lg bg-white p-3 transition-colors duration-300 hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700/50"
    >
        <div :class="['h-3 w-3 rounded-full', isActive ? 'bg-green-500' : 'bg-slate-400']"></div>
        <div>
            <p class="text-sm font-medium text-slate-900 dark:text-white">Collaboration #{{ event.collaboration_id }}</p>
            <div class="flex gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span>Joined: {{ formatDate(event.joined_at) }}</span>
                <span v-if="event.left_at">Left: {{ formatDate(event.left_at) }}</span>
                <span v-else class="text-green-600 dark:text-green-400">Active</span>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
