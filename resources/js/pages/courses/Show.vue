<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'; // Ensure types are correctly imported
import type { BreadcrumbItem, Course } from '@/types';
import { computed } from 'vue';

// Define props with proper typing
const props = defineProps<{
    course: Course | null; // Allow null to handle loading/error states
}>();

// Sort lessons by order_no
const sortedLessons = computed(() => {
    if (!props.course?.lessons) return [];
    return [...props.course.lessons].sort((a, b) => a.order_no - b.order_no);
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Courses', href: '/courses' },
    { title: props.course?.id, href: `/courses/${props.course?.id}` },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-50 transition-colors duration-300 dark:bg-gray-900">
            <!-- Loading/Error State -->
            <div v-if="!course" class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 lg:px-8">
                <div class="animate-pulse space-y-4">
                    <div class="mx-auto h-8 w-3/4 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="mx-auto h-4 w-1/2 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-64 rounded-xl bg-gray-200 dark:bg-gray-700"></div>
                </div>
            </div>

            <!-- Main Content -->
            <template v-else>
                <!-- Hero Section -->
                <div class="relative overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-purple-400 to-purple-600 opacity-20 dark:from-purple-900 dark:to-black dark:opacity-60"
                    ></div>
                    <div class="relative z-10 mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                        <div class="animate-fade-in-down max-w-3xl">
                            <span
                                class="mb-4 inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-sm font-medium text-purple-800 backdrop-blur-sm dark:bg-purple-900/30 dark:text-purple-200"
                            >
                                Course #{{ course.id }}
                            </span>
                            <h1 class="mb-6 text-4xl font-bold leading-tight text-gray-900 dark:text-white md:text-6xl">
                                {{ course.title }}
                            </h1>
                            <p class="flex items-center text-lg text-gray-700 dark:text-gray-300">
                                <span
                                    class="mr-2 inline-block flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-purple-600 dark:text-purple-300"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        />
                                    </svg>
                                </span>
                                Created by: User #{{ course.created_by }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Course Content -->
                <div class="mx-auto -mt-10 max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <!-- Main Content -->
                        <div class="animate-fade-in space-y-8 lg:col-span-2">
                            <!-- Description -->
                            <div
                                class="rounded-xl border border-gray-100 bg-white p-8 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
                            >
                                <h2 class="mb-6 flex items-center text-2xl font-bold text-gray-900 dark:text-white">
                                    <span
                                        class="mr-3 inline-block flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-purple-600 dark:text-purple-300"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </span>
                                    About this Course
                                </h2>
                                <div class="prose dark:prose-invert max-w-none">
                                    <p class="mb-4 text-lg text-gray-700 dark:text-gray-300">{{ course.description }}</p>
                                    <div class="text-gray-700 dark:text-gray-300" v-html="course.text"></div>
                                </div>
                                <div class="mt-6 flex items-center text-sm text-purple-600 dark:text-purple-400">
                                    <span
                                        class="mr-2 inline-block flex h-5 w-5 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3 w-3 text-purple-600 dark:text-purple-300"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                                            />
                                        </svg>
                                    </span>
                                    <span class="font-medium">Brightness level:</span> {{ course.bright }}
                                </div>
                            </div>

                            <!-- Lessons -->
                            <div
                                v-if="course.lessons.length > 0"
                                class="rounded-xl border border-gray-100 bg-white p-8 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
                            >
                                <h2 class="mb-6 flex items-center text-2xl font-bold text-gray-900 dark:text-white">
                                    <span
                                        class="mr-3 inline-block flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-purple-600 dark:text-purple-300"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                            />
                                        </svg>
                                    </span>
                                    Lessons
                                </h2>
                                <ul class="space-y-6">
                                    <li
                                        v-for="lesson in sortedLessons"
                                        :key="lesson.id"
                                        class="group rounded-xl border border-gray-100 p-5 transition-all duration-300 hover:-translate-y-1 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
                                    >
                                        <div class="flex items-start">
                                            <div
                                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 text-lg font-bold text-purple-800 shadow-sm transition-transform duration-300 group-hover:scale-110 dark:bg-purple-900/30 dark:text-purple-200"
                                            >
                                                {{ lesson.order_no }}
                                            </div>
                                            <div class="ml-5 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h3
                                                        class="text-xl font-semibold text-gray-900 transition-colors duration-300 group-hover:text-purple-700 dark:text-white dark:group-hover:text-purple-300"
                                                    >
                                                        {{ lesson.title }}
                                                    </h3>
                                                    <span
                                                        class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-200"
                                                    >
                                                        {{ lesson.type }}
                                                    </span>
                                                </div>
                                                <p class="mt-2 line-clamp-2 text-gray-600 dark:text-gray-300">
                                                    {{ lesson.content || 'No content available.' }}
                                                </p>
                                                <div class="mt-3 flex items-center text-xs text-purple-600 dark:text-purple-400">
                                                    <span
                                                        class="mr-2 inline-block flex h-4 w-4 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-2 w-2 text-purple-600 dark:text-purple-300"
                                                            fill="none"
                                                            viewBox="0 0 24 24"
                                                            stroke="currentColor"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                                                            />
                                                        </svg>
                                                    </span>
                                                    <span class="font-medium">Brightness:</span> {{ lesson.bright }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div
                                v-else
                                class="rounded-xl border border-gray-100 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
                            >
                                <p class="text-gray-700 dark:text-gray-300">No lessons available for this course.</p>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="animate-fade-in-up space-y-6">
                            <!-- Course Stats -->
                            <div
                                class="rounded-xl border border-gray-100 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
                            >
                                <h2 class="mb-4 flex items-center text-xl font-bold text-gray-900 dark:text-white">
                                    <span
                                        class="mr-2 inline-block flex h-6 w-6 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3 w-3 text-purple-600 dark:text-purple-300"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                            />
                                        </svg>
                                    </span>
                                    Course Stats
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50">
                                        <span class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mr-2 h-4 w-4 text-purple-500 dark:text-purple-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                                />
                                            </svg>
                                            Lessons
                                        </span>
                                        <span class="text-lg font-medium text-purple-600 dark:text-purple-400">{{ course.lessons.length }}</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50">
                                        <span class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mr-2 h-4 w-4 text-purple-500 dark:text-purple-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                                />
                                            </svg>
                                            Groups
                                        </span>
                                        <span class="text-lg font-medium text-purple-600 dark:text-purple-400">{{ course.groups?.length || 0 }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Groups -->
                            <div
                                v-if="course.groups?.length > 0"
                                class="rounded-xl border border-gray-100 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
                            >
                                <h2 class="mb-4 flex items-center text-xl font-bold text-gray-900 dark:text-white">
                                    <span
                                        class="mr-2 inline-block flex h-6 w-6 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3 w-3 text-purple-600 dark:text-purple-300"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            />
                                        </svg>
                                    </span>
                                    Groups
                                </h2>
                                <ul class="space-y-3">
                                    <li
                                        v-for="group in course.groups"
                                        :key="group.id"
                                        class="group rounded-lg border border-gray-100 p-4 transition-all duration-300 hover:-translate-y-1 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
                                    >
                                        <h3
                                            class="text-md flex items-center font-medium text-gray-900 transition-colors duration-300 group-hover:text-purple-700 dark:text-white dark:group-hover:text-purple-300"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mr-2 h-4 w-4 text-purple-500 dark:text-purple-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                                />
                                            </svg>
                                            {{ group.name }}
                                        </h3>
                                        <div class="mt-2 flex items-center text-xs text-purple-600 dark:text-purple-400">
                                            <span
                                                class="mr-2 inline-block flex h-4 w-4 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/50"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-2 w-2 text-purple-600 dark:text-purple-300"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                                                    />
                                                </svg>
                                            </span>
                                            <span class="font-medium">Brightness:</span> {{ group.bright }}
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <!-- Join Course Button -->
                            <div
                                class="rounded-xl border border-gray-100 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800"
                            >
                                <button
                                    class="flex w-full items-center justify-center rounded-lg bg-purple-600 px-4 py-3 font-medium text-white transition-colors duration-300 hover:bg-purple-700"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="mr-2 h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Enroll in Course
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.8s ease-out;
}

.animate-fade-in {
    animation: fadeIn 1s ease-out 0.3s both;
}

.animate-fade-in-up {
    animation: fadeInUp 1s ease-out 0.5s both;
}

/* Base Transitions */
.transition-all {
    transition: all 0.3s ease;
}

.hover\:shadow-xl:hover {
    box-shadow:
        0 10px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.hover\:-translate-y-1:hover {
    transform: translateY(-0.25rem);
}

.hover\:scale-110:hover {
    transform: scale(1.1);
}

/* Prose Styling */
.prose {
    color: #374151; /* gray-700 */
}

.dark .prose {
    color: #e5e7eb; /* gray-200 */
}

.prose p {
    margin-bottom: 1rem;
}

/* Line Clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Loading Animation */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}
</style>
