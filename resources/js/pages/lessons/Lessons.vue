<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Lesson } from '@/types';
import { BookOpenIcon, ClockIcon, GamepadIcon, HelpCircleIcon, PlayCircleIcon, SparklesIcon } from 'lucide-vue-next';

const props = defineProps<{
    lessons: Lesson[];
}>();

const getTypeIcon = (type) => {
    switch (type) {
        case 'video':
            return PlayCircleIcon;
        case 'quiz':
            return HelpCircleIcon;
        case 'game':
            return GamepadIcon;
        default:
            return BookOpenIcon;
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(date);
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Courses', href: '/user/courses' },
    { title: props.lessons[0].course_id, href: `/user/courses/${props.lessons[0].course_id}` },
    { title: 'Lessons', href: `/user/courses/${props.lessons[0].course}/lessons` },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-50 px-4 py-8 dark:bg-gray-900 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <h1 class="mb-8 text-3xl font-bold text-purple-800 dark:text-purple-400">Course Lessons</h1>

                <!-- Lessons Grid -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <a
                        v-for="lesson in lessons"
                        :key="lesson.id"
                        :href="`/lessons/${lesson.id}`"
                        :class="[
                            'transform overflow-hidden rounded-lg shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl',
                            lesson.bright ? 'ring-2 ring-purple-500 dark:ring-purple-400' : '',
                        ]"
                    >
                        <div class="flex h-full flex-col bg-white dark:bg-gray-800">
                            <!-- Card Header with Type Badge -->
                            <div class="flex items-center justify-between border-b border-gray-200 p-5 dark:border-gray-700">
                                <div class="flex items-center space-x-2">
                                    <div
                                        :class="[
                                            'rounded-full p-2',
                                            lesson.type === 'video'
                                                ? 'bg-purple-100 dark:bg-purple-900'
                                                : lesson.type === 'quiz'
                                                  ? 'bg-indigo-100 dark:bg-indigo-900'
                                                  : 'bg-pink-100 dark:bg-pink-900',
                                        ]"
                                    >
                                        <component
                                            :is="getTypeIcon(lesson.type)"
                                            :class="[
                                                'h-5 w-5',
                                                lesson.type === 'video'
                                                    ? 'text-purple-600 dark:text-purple-400'
                                                    : lesson.type === 'quiz'
                                                      ? 'text-indigo-600 dark:text-indigo-400'
                                                      : 'text-pink-600 dark:text-pink-400',
                                            ]"
                                        />
                                    </div>
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-medium uppercase tracking-wider"
                                        :class="[
                                            lesson.type === 'video'
                                                ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
                                                : lesson.type === 'quiz'
                                                  ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300'
                                                  : 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                                        ]"
                                    >
                                        {{ lesson.type }}
                                    </span>
                                </div>
                                <div v-if="lesson.bright" class="flex items-center">
                                    <SparklesIcon class="h-5 w-5 text-yellow-500" />
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="flex-grow p-5">
                                <h2 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">{{ lesson.title }}</h2>
                                <p class="mb-4 line-clamp-3 text-gray-600 dark:text-gray-300">{{ lesson.content }}</p>

                                <div class="mt-auto">
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <ClockIcon class="mr-1 h-4 w-4" />
                                        <span>Lesson {{ lesson.order_no }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="bg-gray-50 px-5 py-3 text-xs text-gray-500 dark:bg-gray-700/50 dark:text-gray-400">
                                <div class="flex justify-between">
                                    <span>Created: {{ formatDate(lesson.created_at) }}</span>
                                    <span>Updated: {{ formatDate(lesson.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Empty State -->
                <div v-if="!lessons.length" class="py-12 text-center">
                    <BookOpenIcon class="mx-auto mb-4 h-12 w-12 text-purple-300 dark:text-purple-700" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">No lessons available</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Check back later for new content.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
