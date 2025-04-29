<script setup lang="ts">
import { ref } from 'vue';

const isHovering = ref(false);
defineProps<{
    group: {
        type: object,
        required: true
    }
}>();
</script>

<template>
    <div
        class="p-3 rounded-lg transition-all duration-300 flex items-center gap-3 cursor-pointer"
        :class="[
        isHovering
          ? 'bg-amber-50 dark:bg-amber-900/20 shadow-sm'
          : 'bg-white dark:bg-slate-800 hover:bg-amber-50/50 dark:hover:bg-amber-900/10'
      ]"
        @mouseenter="isHovering = true"
        @mouseleave="isHovering = false"
    >
        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 font-medium">
            {{ group.name.charAt(0) }}
        </div>
        <div>
            <h3 class="font-medium text-slate-900 dark:text-white">{{ group.name }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Course ID: {{ group.course_id }}</p>
        </div>
    </div>
</template>

<style scoped>

</style>
