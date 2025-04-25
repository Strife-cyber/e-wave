<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Course } from '@/types';
import { router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    courses: Course[];
    progress: any;
}>();

function goToCourse(courseId) {
    router.visit(`/courses/${courseId}`);
}

// Animation states
const isLoaded = ref(false);
const activeCard = ref<number | null>(null);

// Search and filter state
const searchQuery = ref('');
const activeFilter = ref('all');
const filters = [
    { id: 'all', label: 'All Courses' },
    { id: 'completed', label: 'Completed' },
    { id: 'failed', label: 'Failed' },
    { id: 'not-started', label: 'Not Started' },
];

// Calculate progress for each course
const coursesWithProgress = computed(() => {
    return props.courses.map((course) => {
        const courseProgress = props.progress.find((p) => p.lesson_id === course.id);
        return {
            ...course,
            progress: courseProgress || null,
            status: courseProgress?.status || 'not-started',
            score: courseProgress?.score || 0,
        };
    });
});

// Filtered courses based on active filter and search query
const filteredCourses = computed(() => {
    let filtered = coursesWithProgress.value;

    // Filter by status
    if (activeFilter.value !== 'all') {
        filtered = filtered.filter((course) => course.status === activeFilter.value);
    }

    // Filter by search query
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim();
        filtered = filtered.filter(
            (course) => course.title.toLowerCase().includes(query) || (course.description && course.description.toLowerCase().includes(query)),
        );
    }

    return filtered;
});

// Status color mapping with Tailwind classes
const getStatusColor = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-500 dark:bg-emerald-400';
        case 'failed':
            return 'bg-rose-500 dark:bg-rose-400';
        default:
            return 'bg-gray-400 dark:bg-gray-500';
    }
};

const getStatusTextColor = (status) => {
    switch (status) {
        case 'completed':
            return 'text-emerald-600 dark:text-emerald-400';
        case 'failed':
            return 'text-rose-600 dark:text-rose-400';
        default:
            return 'text-gray-500 dark:text-gray-400';
    }
};

// Handle card click
const toggleCard = (id) => {
    activeCard.value = activeCard.value === id ? null : id;
};

// Set active filter
const setFilter = (filterId) => {
    activeFilter.value = filterId;
};

// Clear search
const clearSearch = () => {
    searchQuery.value = '';
};

// Animate cards on mount
onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Courses', href: '/user/courses' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-50 p-6 dark:bg-gray-900">
            <!-- Header with title -->
            <div class="mb-6">
                <div class="relative inline-block">
                    <h1 class="text-3xl font-bold text-purple-700 dark:text-purple-400">My Courses</h1>
                </div>
            </div>

            <!-- Search and filters section -->
            <div class="mb-8 space-y-4">
                <!-- Search bar -->
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search courses by title or description..."
                        class="w-full rounded-lg border border-gray-200 bg-white py-3 pl-10 pr-10 text-gray-800 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:focus:ring-purple-400"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Filter tabs -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="filter in filters"
                        :key="filter.id"
                        @click="setFilter(filter.id)"
                        :class="[
                            'rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200',
                            activeFilter === filter.id
                                ? 'bg-purple-600 text-white shadow-lg shadow-purple-200 dark:shadow-purple-900/30'
                                : 'bg-white text-gray-700 hover:bg-purple-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700',
                        ]"
                    >
                        {{ filter.label }}
                        <span
                            v-if="filter.id === 'all' || coursesWithProgress.filter((c) => c.status === filter.id).length > 0"
                            class="ml-1.5 rounded-full bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                        >
                            {{ filter.id === 'all' ? coursesWithProgress.length : coursesWithProgress.filter((c) => c.status === filter.id).length }}
                        </span>
                    </button>
                </div>

                <!-- Search results info -->
                <div v-if="searchQuery.trim()" class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>Found {{ filteredCourses.length }} {{ filteredCourses.length === 1 ? 'result' : 'results' }} for "{{ searchQuery }}"</span>
                    <button @click="clearSearch" class="ml-2 text-purple-600 hover:underline focus:outline-none dark:text-purple-400">
                        Clear search
                    </button>
                </div>
            </div>

            <!-- Course grid -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(course, index) in filteredCourses"
                    :key="course.id"
                    :class="[
                        'overflow-hidden rounded-xl bg-white shadow-lg transition-all duration-300 hover:shadow-xl dark:bg-gray-800',
                        'flex flex-col border border-gray-100 dark:border-gray-700',
                        'transform transition-all duration-300',
                        isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0',
                        activeCard === course.id ? 'scale-[1.02] ring-2 ring-purple-400 dark:ring-purple-500' : '',
                    ]"
                    :style="`transition-delay: ${index * 75}ms`"
                    @click="toggleCard(course.id)"
                >
                    <!-- Card header -->
                    <div class="flex items-center border-b border-gray-100 p-5 dark:border-gray-700">
                        <div :class="['mr-3 h-3 w-3 rounded-full', getStatusColor(course.status)]"></div>
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ course.title }}</h2>
                    </div>

                    <!-- Card content -->
                    <div class="flex-grow p-5">
                        <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">{{ course.description }}</p>

                        <!-- Progress section -->
                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Progress</span>
                                <span :class="['text-xs font-semibold', getStatusTextColor(course.status)]">
                                    {{
                                        course.status === 'not-started'
                                            ? 'Not Started'
                                            : course.status.charAt(0).toUpperCase() + course.status.slice(1)
                                    }}
                                </span>
                            </div>

                            <!-- Progress bar -->
                            <div class="mb-2 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                <div
                                    :class="[
                                        'h-full rounded-full transition-all duration-1000 ease-out',
                                        course.status === 'completed'
                                            ? 'bg-emerald-500 dark:bg-emerald-400'
                                            : course.status === 'failed'
                                              ? 'bg-rose-500 dark:bg-rose-400'
                                              : 'bg-gray-400 dark:bg-gray-500',
                                    ]"
                                    :style="{ width: `${course.score}%` }"
                                ></div>
                            </div>

                            <!-- Score display -->
                            <div v-if="course.score > 0" class="flex items-center justify-end">
                                <span class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ course.score }}</span>
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">points</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card actions -->
                    <div class="border-t border-gray-100 p-5 dark:border-gray-700">
                        <button
                            @click="goToCourse(course.id)"
                            class="w-full transform rounded-lg bg-purple-600 px-4 py-2 font-medium text-white transition-all duration-200 hover:-translate-y-0.5 hover:bg-purple-700 active:translate-y-0"
                        >
                            {{ course.status === 'not-started' ? 'Start Course' : 'Continue Course' }}
                        </button>
                    </div>
                </div>

                <!-- Empty state when no courses match filter -->
                <div v-if="filteredCourses.length === 0" class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-purple-500 dark:text-purple-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">No courses found</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">
                        {{ searchQuery ? 'Try a different search term or clear filters' : 'Try changing your filter selection' }}
                    </p>
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="rounded-lg bg-purple-100 px-4 py-2 text-purple-700 transition-colors hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50"
                        >
                            Clear search
                        </button>
                        <button
                            v-if="activeFilter !== 'all'"
                            @click="setFilter('all')"
                            class="rounded-lg bg-purple-100 px-4 py-2 text-purple-700 transition-colors hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:hover:bg-purple-900/50"
                        >
                            Show all courses
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
