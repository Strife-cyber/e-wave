<script setup lang="ts">
import AddCourseDialog from '@/components/contributions/AddCourseDialog.vue';
import CourseCard from '@/components/contributions/CourseCard.vue';
import EventFilters from '@/components/contributions/EventFilters.vue';
import EventItem from '@/components/contributions/EventItem.vue';
import GroupItem from '@/components/contributions/GroupItem.vue';
import LessonOrderingDialog from '@/components/contributions/LessonOrderingDialog.vue';
import LessonEditor from '@/components/contributions/LessonWizard.vue';
import Pagination from '@/components/contributions/Pagination.vue';
import ShareCourseDialog from '@/components/contributions/ShareCourseDialog.vue';
import { useFilters } from '@/composables/use-filters';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { BookOpen, Calendar, Filter, GripVertical, PlusCircle, Search, Share2, Users } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

// Filter setup
const courseFiltersSetup = useFilters('courses', {
    search: '',
    bright: '',
    sort: 'newest',
});

const eventFiltersSetup = useFilters('events', {
    status: '',
    collaborationId: '',
    dateFrom: '',
    dateTo: '',
});

// Get data from Inertia props
const page = usePage();
const groups = computed(() => page.props.groups || []);
const courses = computed(() => page.props.courses?.data || []);
const totalCourses = computed(() => page.props.courses?.total || 0);
const coursesPagination = computed(() => ({
    currentPage: page.props.courses?.current_page || 1,
    lastPage: page.props.courses?.last_page || 1,
}));

const events = computed(() => page.props.events?.data || []);
const totalEvents = computed(() => page.props.events?.total || 0);
const eventsPagination = computed(() => ({
    currentPage: page.props.events?.current_page || 1,
    lastPage: page.props.events?.last_page || 1,
}));

// Dialog states
const addCourseDialogOpen = ref(false);
const LessonEditorOpen = ref(false);
const shareCourseDialogOpen = ref(false);
const selectedCourseForSharing = ref(null);
const showLessonOrderingDialog = ref(false);
const selectedCourseForLessons = ref(null);
const courseLessons = ref([]);

const changePage = (type, page) => {
    const params = { [`${type}_page`]: page };
    const filters = type === 'courses' ? courseFiltersSetup.filters.value : eventFiltersSetup.filters.value;
    Object.keys(filters).forEach((key) => {
        if (filters[key]) {
            params[`${type}_${key}`] = filters[key];
        }
    });

    router.get(router.page.url, params, {
        preserveState: true,
        preserveScroll: true,
        only: [type, 'auth'],
    });
};

const openAddCourseDialog = () => {
    addCourseDialogOpen.value = true;
};

const closeAddCourseDialog = () => {
    addCourseDialogOpen.value = false;
};

const addCourse = (course) => {
    const form = useForm({
        ...course,
    });
    form.post('/courses', {
        onSuccess: () => closeAddCourseDialog(),
    });
};

const openLessonEditor = () => {
    LessonEditorOpen.value = true;
};

const closeLessonEditor = () => {
    LessonEditorOpen.value = false;
};

const addLesson = (lesson) => {
    // In a real app, submit to backend
    closeLessonEditor();
};

const openShareCourseDialog = (course) => {
    selectedCourseForSharing.value = course;
    shareCourseDialogOpen.value = true;
};

const closeShareCourseDialog = () => {
    shareCourseDialogOpen.value = false;
    selectedCourseForSharing.value = null;
};

const shareCourse = (email, permission) => {
    // In a real app, submit to backend
    closeShareCourseDialog();
};

const openLessonOrderingDialog = (course) => {
    selectedCourseForLessons.value = course;
    courseLessons.value = [...(course.lessons[course.id] || [])].sort((a, b) => a.order - b.order);
    showLessonOrderingDialog.value = true;
};

const closeLessonOrderingDialog = () => {
    showLessonOrderingDialog.value = false;
    selectedCourseForLessons.value = null;
    courseLessons.value = [];
};

const saveLessonOrder = (lessons) => {
    // In a real app, submit to backend
    closeLessonOrderingDialog();
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Contributions', href: '/contributions' }];

onMounted(() => {
    //courseFiltersSetup.initializeFilters();
    //eventFiltersSetup.initializeFilters();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Contributions" />
        <div class="min-h-screen text-slate-800 transition-colors duration-300 dark:text-slate-200">
            <!-- Main content -->
            <main class="container mx-auto p-8">
                <div class="mb-8 flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-purple-800 dark:text-purple-300">Contributions</h1>

                    <div class="flex gap-3">
                        <button
                            @click="openAddCourseDialog"
                            class="flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-white shadow-md transition-all duration-300 hover:bg-purple-700 hover:shadow-lg"
                        >
                            <PlusCircle class="h-4 w-4" />
                            Add Course
                        </button>

                        <button
                            @click="openLessonEditor"
                            class="flex items-center gap-2 rounded-lg border border-purple-200 px-4 py-2 text-purple-700 transition-all duration-300 hover:bg-purple-50 dark:border-purple-800 dark:text-purple-300 dark:hover:bg-purple-900/20"
                        >
                            <PlusCircle class="h-4 w-4" />
                            Add Lesson
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Courses Section -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Courses Header -->
                        <div class="animate-fadeIn flex flex-col justify-between gap-4 sm:flex-row sm:items-center" style="animation-delay: 0.1s">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                    <BookOpen class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">My Courses</h2>
                            </div>

                            <!-- Course Filters Button -->
                            <!--<button
                                @click="courseFiltersSetup.filtersVisible = !courseFiltersSetup.filtersVisible"
                                class="flex items-center gap-2 rounded-lg border border-purple-200 px-3 py-2 text-sm shadow-sm transition-all duration-300 hover:shadow-md dark:border-purple-800"
                            >
                                <Filter class="h-4 w-4" />
                                {{ courseFiltersSetup.filtersVisible ? 'Hide Filters' : 'Show Filters' }}
                            </button>-->
                        </div>

                        <!-- Course Filters Panel -->
                        <!--<CourseFilters
                            v-if="courseFiltersSetup.filtersVisible"
                            :filters="courseFiltersSetup.filters"
                            @apply="courseFiltersSetup.applyFilters"
                            @reset="courseFiltersSetup.resetFilters"
                        />-->

                        <p class="animate-fadeIn text-sm text-slate-600 dark:text-slate-400" style="animation-delay: 0.2s">
                            Showing {{ courses.length }} of {{ totalCourses }} courses
                        </p>

                        <!-- No results message -->
                        <div
                            v-if="courses.length === 0"
                            class="animate-fadeIn rounded-xl border border-purple-100 bg-white p-8 text-center shadow-lg dark:border-purple-900/30 dark:bg-slate-800"
                            style="animation-delay: 0.3s"
                        >
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                                <Search class="h-8 w-8 text-purple-400 dark:text-purple-500" />
                            </div>
                            <h3 class="mb-1 text-lg font-medium text-slate-900 dark:text-white">No courses found</h3>
                            <p class="text-slate-500 dark:text-slate-400">Try adjusting your filters or search criteria</p>
                        </div>

                        <!-- Course grid -->
                        <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div
                                v-for="(course, index) in courses"
                                :key="course.id"
                                class="animate-fadeIn group relative"
                                :style="`animation-delay: ${0.3 + index * 0.1}s`"
                            >
                                <CourseCard :course="course" />

                                <!-- Action buttons overlay -->
                                <div class="absolute right-2 top-2 flex gap-1 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                    <button
                                        @click.stop="openShareCourseDialog(course)"
                                        class="rounded-md bg-purple-600 p-1.5 text-white shadow-sm hover:bg-purple-700"
                                        title="Share course"
                                    >
                                        <Share2 class="h-4 w-4" />
                                    </button>
                                    <button
                                        @click.stop="openLessonOrderingDialog(course)"
                                        class="rounded-md bg-purple-600 p-1.5 text-white shadow-sm hover:bg-purple-700"
                                        title="Reorder lessons"
                                    >
                                        <GripVertical class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <Pagination
                            v-if="coursesPagination.lastPage > 1"
                            :current-page="coursesPagination.currentPage"
                            :last-page="coursesPagination.lastPage"
                            @page-change="changePage('courses', $event)"
                            class="animate-fadeIn mt-6"
                            style="animation-delay: 0.5s"
                        />
                    </div>

                    <!-- Sidebar with Groups and Events -->
                    <div class="space-y-6">
                        <!-- Groups Section -->
                        <div
                            class="animate-fadeIn overflow-hidden rounded-xl border border-purple-100 bg-white shadow-lg dark:border-purple-900/30 dark:bg-slate-800"
                            style="animation-delay: 0.2s"
                        >
                            <div class="border-b border-purple-100 p-5 dark:border-purple-900/30">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                        <Users class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">My Groups</h2>
                                </div>
                            </div>

                            <div class="p-5">
                                <div v-if="groups.length === 0" class="py-8 text-center">
                                    <p class="text-slate-500 dark:text-slate-400">No groups available</p>
                                </div>

                                <div v-else class="space-y-3">
                                    <div
                                        v-for="(group, index) in groups.slice(0, 5)"
                                        :key="group.id"
                                        class="animate-fadeIn"
                                        :style="`animation-delay: ${0.3 + index * 0.1}s`"
                                    >
                                        <GroupItem :group="group" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Events Section -->
                        <div
                            class="animate-fadeIn overflow-hidden rounded-xl border border-purple-100 bg-white shadow-lg dark:border-purple-900/30 dark:bg-slate-800"
                            style="animation-delay: 0.3s"
                        >
                            <div class="border-b border-purple-100 p-5 dark:border-purple-900/30">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                            <Calendar class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Events</h2>
                                    </div>

                                    <!-- Event Filters Button -->
                                    <button
                                        @click="eventFiltersSetup.filtersVisible = !eventFiltersSetup.filtersVisible"
                                        class="flex items-center gap-1 text-sm text-slate-600 transition-colors hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                                    >
                                        <Filter class="h-4 w-4" />
                                        {{ eventFiltersSetup.filtersVisible ? 'Hide' : 'Filter' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Event Filters Panel -->
                            <EventFilters
                                v-if="eventFiltersSetup.filtersVisible"
                                :filters="eventFiltersSetup.filters"
                                @apply="eventFiltersSetup.applyFilters"
                                @reset="eventFiltersSetup.resetFilters"
                            />

                            <div class="p-5">
                                <p class="mb-3 text-sm text-slate-600 dark:text-slate-400">Showing {{ events.length }} of {{ totalEvents }} events</p>

                                <!-- No results message -->
                                <div v-if="events.length === 0" class="rounded-lg bg-purple-50 p-4 text-center dark:bg-purple-900/20">
                                    <Search class="mx-auto mb-2 h-8 w-8 text-purple-400 dark:text-purple-500" />
                                    <p class="text-sm text-slate-500 dark:text-slate-400">No events match your filters</p>
                                </div>

                                <!-- Events list -->
                                <div v-else class="space-y-3">
                                    <div
                                        v-for="(event, index) in events"
                                        :key="event.id"
                                        class="animate-fadeIn"
                                        :style="`animation-delay: ${0.3 + index * 0.1}s`"
                                    >
                                        <EventItem :event="event" />
                                    </div>
                                </div>

                                <Pagination
                                    v-if="eventsPagination.lastPage > 1"
                                    :current-page="eventsPagination.currentPage"
                                    :last-page="eventsPagination.lastPage"
                                    @page-change="changePage('events', $event)"
                                    class="mt-4"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Dialogs -->
        <AddCourseDialog :open="addCourseDialogOpen" @close="closeAddCourseDialog" @create="addCourse" />
        <LessonEditor :open="LessonEditorOpen" :courses="courses" @close="closeLessonEditor" @add="addLesson" />
        <ShareCourseDialog :open="shareCourseDialogOpen" :course="selectedCourseForSharing" @close="closeShareCourseDialog" @share="shareCourse" />
        <LessonOrderingDialog
            :open="showLessonOrderingDialog"
            :course="selectedCourseForLessons"
            :lessons="courseLessons"
            @close="closeLessonOrderingDialog"
            @save="saveLessonOrder"
        />
    </AppLayout>
</template>

<style>
/* Custom animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.5s ease-out forwards;
}

.animate-slideDown {
    animation: slideDown 0.3s ease-out forwards;
}

/* Purple theme colors */
:root {
    --purple-50: #f5f3ff;
    --purple-100: #ede9fe;
    --purple-200: #ddd6fe;
    --purple-300: #c4b5fd;
    --purple-400: #a78bfa;
    --purple-500: #8b5cf6;
    --purple-600: #7c3aed;
    --purple-700: #6d28d9;
    --purple-800: #5b21b6;
    --purple-900: #4c1d95;
    --purple-950: #2e1065;
}
</style>
