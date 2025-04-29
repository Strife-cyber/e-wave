<script setup lang="ts">
import { Course } from '@/types';
import { computed, ref } from 'vue';

const lessonCount = computed(() => props.course.lessons?.length || 0);
const isHovering = ref(false);

const props = defineProps<{
    course: {
        type: Course;
        required: true;
    };
}>();
</script>

<template>
    <div
        class="flex h-full flex-col overflow-hidden rounded-xl bg-white shadow-lg transition-all duration-300 dark:bg-slate-800"
        :class="{ 'scale-[1.02] transform shadow-xl': isHovering }"
        @mouseenter="isHovering = true"
        @mouseleave="isHovering = false"
    >
        <div
            :class="[
                'h-1.5 w-full',
                course.bright ? 'bg-gradient-to-r from-teal-400 to-emerald-500' : 'bg-gradient-to-r from-slate-300 to-slate-400',
            ]"
        ></div>
        <div class="flex-grow p-5">
            <h3 class="mb-2 text-lg font-bold text-slate-900 dark:text-white">{{ course.title }}</h3>
            <p class="mb-4 text-sm text-slate-600 dark:text-slate-300" v-if="course.description">{{ course.description }}</p>
            <div class="mt-auto flex items-center justify-between">
                <span class="flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                    {{ lessonCount }} lessons
                </span>
                <a
                    :href="route('courses.show', course.id)"
                    class="flex items-center gap-1 rounded-lg bg-teal-600 px-3 py-1.5 text-sm text-white shadow-sm transition-all duration-300 hover:bg-teal-700 hover:shadow-md"
                >
                    View Course
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
