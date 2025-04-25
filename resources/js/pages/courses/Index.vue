<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Define the Course type (assumed from your types file)
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Course } from '@/types';

// Props: courses are passed from Laravel via Inertia
const props = defineProps<{
    courses: {
        data: Course[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

// Search state
const searchTerm = ref(usePage().props.search || '');

// Handle search form submission
const handleSearch = (e: Event) => {
    e.preventDefault();
    router.get(route('courses.index'), { search: searchTerm.value, page: 1 }, { preserveState: true, preserveScroll: true });
};

// Navigate to a specific page
const navigateToPage = (page: number) => {
    router.get(route('courses.index'), { page, search: searchTerm.value }, { preserveState: true, preserveScroll: true });
};

// Computed property to determine visible pages for pagination
const visiblePages = computed(() => {
    const pages = [];
    for (let page = 1; page <= props.courses.last_page; page++) {
        if (page === 1 || page === props.courses.last_page || (page >= props.courses.current_page - 1 && page <= props.courses.current_page + 1)) {
            pages.push(page);
        }
    }
    return pages;
});

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Courses', href: '/courses' }];
</script>

<template>
    <Head title="Courses" />
    <div class="relative min-h-screen bg-gray-50 dark:bg-black">
        <AppLayout :breadcrumbs="breadcrumbs">
            <div class="container mx-auto px-4 py-8">
                <!-- Header and Search -->
                <div class="mb-8 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                    <h1 class="text-3xl font-bold text-purple-700 dark:text-purple-300">Courses</h1>
                    <form @submit="handleSearch" class="w-full md:w-auto">
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                            <input
                                type="search"
                                placeholder="Search courses..."
                                class="w-full rounded-md border-purple-200 bg-white px-3 py-2 pl-10 focus:border-purple-500 dark:border-purple-800 dark:bg-gray-800 dark:focus:border-purple-400 md:w-[300px]"
                                v-model="searchTerm"
                            />
                            <button
                                type="submit"
                                class="absolute right-0 top-0 h-full px-3 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300"
                            >
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Courses Grid -->
                <div v-if="courses.data.length === 0" class="py-12 text-center">
                    <h2 class="text-xl font-medium text-gray-600 dark:text-gray-300">No courses found</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Try adjusting your search criteria</p>
                </div>
                <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="course in courses.data"
                        :key="course.id"
                        class="overflow-hidden rounded-lg border transition-all duration-200 hover:shadow-lg"
                        :class="
                            course.bright
                                ? 'border-purple-300 bg-purple-50 dark:border-purple-700 dark:bg-purple-900/20'
                                : 'border-gray-200 dark:border-gray-700'
                        "
                    >
                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <h2 class="text-xl text-purple-800 dark:text-purple-300">{{ course.title }}</h2>
                                <span
                                    v-if="course.bright"
                                    class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                                >
                                    Featured
                                </span>
                            </div>
                            <p class="mt-1 line-clamp-2 text-gray-600 dark:text-gray-300">{{ course.description }}</p>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ course.lessons?.length || 0 }} {{ course.lessons?.length === 1 ? 'lesson' : 'lessons' }}
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span
                                    v-for="lesson in (course.lessons || []).slice(0, 3)"
                                    :key="lesson.id"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="{
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': lesson.type === 'video',
                                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300': lesson.type === 'quiz',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300': lesson.type === 'game',
                                    }"
                                >
                                    {{ lesson.type }}
                                </span>
                                <span
                                    v-if="(course.lessons?.length || 0) > 3"
                                    class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-800 dark:text-gray-300"
                                >
                                    +{{ (course.lessons?.length || 0) - 3 }} more
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between p-4">
                            <a
                                class="rounded-md border border-purple-200 px-4 py-2 text-purple-600 hover:bg-purple-50 hover:text-purple-700 dark:border-purple-800 dark:text-purple-400 dark:hover:bg-purple-900/30 dark:hover:text-purple-300"
                                :href="`/courses/${course.id}`"
                            >
                                View Details
                            </a>
                            <a
                                :href="`/courses/${course.id}`"
                                class="rounded-md bg-purple-600 px-4 py-2 text-white hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600"
                            >
                                Enroll Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    <div class="flex items-center gap-2">
                        <button
                            :disabled="courses.current_page <= 1"
                            @click="navigateToPage(courses.current_page - 1)"
                            class="rounded-md px-3 py-1"
                            :class="
                                courses.current_page <= 1
                                    ? 'cursor-not-allowed opacity-50'
                                    : 'text-purple-600 hover:bg-purple-50 dark:text-purple-400 dark:hover:bg-purple-900/20'
                            "
                        >
                            « Previous
                        </button>

                        <template v-for="(page, index) in visiblePages" :key="page">
                            <button v-if="index > 0 && page !== visiblePages[index - 1] + 1" class="px-3 py-1 text-gray-500" disabled>...</button>
                            <button
                                @click="navigateToPage(page)"
                                class="rounded-md px-3 py-1"
                                :class="
                                    page === courses.current_page
                                        ? 'bg-purple-600 text-white dark:bg-purple-700'
                                        : 'text-purple-600 hover:bg-purple-50 dark:text-purple-400 dark:hover:bg-purple-900/20'
                                "
                            >
                                {{ page }}
                            </button>
                        </template>

                        <button
                            :disabled="courses.current_page >= courses.last_page"
                            @click="navigateToPage(courses.current_page + 1)"
                            class="rounded-md px-3 py-1"
                            :class="
                                courses.current_page >= courses.last_page
                                    ? 'cursor-not-allowed opacity-50'
                                    : 'text-purple-600 hover:bg-purple-50 dark:text-purple-400 dark:hover:bg-purple-900/20'
                            "
                        >
                            Next »
                        </button>
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<style scoped>
.container {
    max-width: 1200px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
