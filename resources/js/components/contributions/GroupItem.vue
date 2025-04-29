<script setup lang="ts">
import { ref } from 'vue';

const isHovering = ref(false);
defineProps<{
    group: {
        type: object;
        required: true;
    };
}>();
</script>

<template>
    <div
        class="flex cursor-pointer items-center gap-3 rounded-lg p-3 transition-all duration-300"
        :class="[
            isHovering ? 'bg-amber-50 shadow-sm dark:bg-amber-900/20' : 'bg-white hover:bg-amber-50/50 dark:bg-slate-800 dark:hover:bg-amber-900/10',
        ]"
        @mouseenter="isHovering = true"
        @mouseleave="isHovering = false"
    >
        <div
            class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 font-medium text-amber-600 dark:bg-amber-900/30 dark:text-amber-400"
        >
            {{ group.name.charAt(0) }}
        </div>
        <div>
            <h3 class="font-medium text-slate-900 dark:text-white">{{ group.name }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Course ID: {{ group.course_id }}</p>
        </div>
    </div>
</template>

<style scoped></style>
