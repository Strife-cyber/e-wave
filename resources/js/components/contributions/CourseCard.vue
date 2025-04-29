<script setup lang="ts">
import { computed, ref } from 'vue';
import { Course } from '@/types';

const lessonCount = computed(() => props.course.lessons?.length || 0);
const isHovering = ref(false);

const props = defineProps<{
    course: {
        type: Course,
        required: true
    }
}>();
</script>

<template>
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden transition-all duration-300 h-full flex flex-col"
        :class="{'transform scale-[1.02] shadow-xl': isHovering}"
        @mouseenter="isHovering = true"
        @mouseleave="isHovering = false"
    >
        <div :class="['h-1.5 w-full', course.bright ? 'bg-gradient-to-r from-teal-400 to-emerald-500' : 'bg-gradient-to-r from-slate-300 to-slate-400']"></div>
        <div class="p-5 flex-grow">
            <h3 class="text-lg font-bold mb-2 text-slate-900 dark:text-white">{{ course.title }}</h3>
            <p class="text-slate-600 dark:text-slate-300 text-sm mb-4" v-if="course.description">{{ course.description }}</p>
            <div class="flex justify-between items-center mt-auto">
          <span class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            {{ lessonCount }} lessons
          </span>
                <a
                    :href="route('courses.show', course.id)"
                    class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 text-sm flex items-center gap-1"
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

<style scoped>

</style>
