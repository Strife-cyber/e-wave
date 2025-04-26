<template>
    <div class="mt-8 flex justify-between">
        <button
            v-if="hasPreviousLesson"
            @click="$emit('previous')"
            class="flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
        >
            <ArrowLeftIcon class="mr-2 h-4 w-4" />
            Previous Lesson
        </button>
        <div v-else></div>

        <button
            v-if="hasNextLesson"
            @click="$emit('next')"
            class="flex items-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800"
        >
            Next Lesson
            <ArrowRightIcon class="ml-2 h-4 w-4" />
        </button>
        <button
            v-else
            @click="$emit('complete')"
            class="flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800"
        >
            Complete Course
            <CheckIcon class="ml-2 h-4 w-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { ArrowLeftIcon, ArrowRightIcon, CheckIcon } from 'lucide-vue-next';

defineProps({
    hasPreviousLesson: {
        type: Boolean,
        default: false,
    },
    hasNextLesson: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['previous', 'next', 'complete']);
</script>
