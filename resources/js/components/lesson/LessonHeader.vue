<template>
    <header class="sticky top-0 z-20 bg-white shadow-md dark:bg-gray-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ lesson.title }}</h1>
                        <div class="mt-1 flex items-center">
                            <component
                                :is="getTypeIcon(lesson.type)"
                                class="mr-1 h-4 w-4"
                                :class="[
                                    lesson.type === 'video'
                                        ? 'text-purple-600 dark:text-purple-400'
                                        : lesson.type === 'quiz'
                                          ? 'text-indigo-600 dark:text-indigo-400'
                                          : 'text-pink-600 dark:text-pink-400',
                                ]"
                            />
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {{ lesson.type.charAt(0).toUpperCase() + lesson.type.slice(1) }} Lesson
                            </span>
                            <span v-if="lesson.bright" class="ml-2">
                                <SparklesIcon class="h-4 w-4 text-yellow-500" />
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div class="flex items-center">
                    <div class="mr-2 h-2 w-32 rounded-full bg-gray-200 dark:bg-gray-700">
                        <div
                            class="h-2 rounded-full bg-purple-600 transition-all duration-300 ease-out dark:bg-purple-500"
                            :style="{ width: `${completionPercentage}%` }"
                        ></div>
                    </div>
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ Math.round(completionPercentage) }}%</span>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
import { GamepadIcon, HelpCircleIcon, PlayCircleIcon, SparklesIcon } from 'lucide-vue-next';

defineProps({
    lesson: {
        type: Object,
        required: true,
    },
    completionPercentage: {
        type: Number,
        default: 0,
    },
});

defineEmits(['back']);

const getTypeIcon = (type) => {
    switch (type) {
        case 'video':
            return PlayCircleIcon;
        case 'quiz':
            return HelpCircleIcon;
        case 'game':
            return GamepadIcon;
        default:
            return PlayCircleIcon;
    }
};
</script>
