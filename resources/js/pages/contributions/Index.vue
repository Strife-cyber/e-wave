<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { usePage, router, Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import CourseCard from '@/components/contributions/CourseCard.vue';
import EventItem from '@/components/contributions/EventItem.vue';
import GroupItem from '@/components/contributions/GroupItem.vue';
import { PlusCircle, BookOpen, Users, Calendar, Filter, Search, Share2, GripVertical } from 'lucide-vue-next';
import Pagination from '@/components/contributions/Pagination.vue';

// Filter states
const courseFiltersVisible = ref(false);
const eventFiltersVisible = ref(false);

const courseFilters = ref({
    search: '',
    bright: '',
    sort: 'newest'
});

const eventFilters = ref({
    status: '',
    collaborationId: '',
    dateFrom: '',
    dateTo: ''
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

// Dialog states for adding courses and lessons
const addCourseDialogOpen = ref(false);
const addLessonDialogOpen = ref(false);
const shareCourseDialogOpen = ref(false);
const selectedCourseForSharing = ref(null);

// Drag and drop state
const isDragging = ref(false);
const draggedLesson = ref(null);
const courseLessons = ref([]);
const selectedCourseForLessons = ref(null);
const showLessonOrderingDialog = ref(false);

// Mock lessons for demonstration
const mockLessons = {
    1: [
        { id: 1, title: "Introduction to React Basics", order: 1 },
        { id: 2, title: "Components and Props", order: 2 },
        { id: 3, title: "State and Lifecycle", order: 3 },
    ],
    2: [
        { id: 4, title: "Advanced Types", order: 1 },
        { id: 5, title: "Generics", order: 2 },
        { id: 6, title: "Type Guards", order: 3 },
    ],
    3: [
        { id: 7, title: "Next.js Setup", order: 1 },
        { id: 8, title: "Routing in Next.js", order: 2 },
        { id: 9, title: "API Routes", order: 3 },
    ]
};

const initializeFilters = () => {
    const params = new URLSearchParams(window.location.search);

    // Course filters
    if (params.has('course_search')) courseFilters.value.search = params.get('course_search');
    if (params.has('course_bright')) courseFilters.value.bright = params.get('course_bright');
    if (params.has('course_sort')) courseFilters.value.sort = params.get('course_sort');

    // Event filters
    if (params.has('event_status')) eventFilters.value.status = params.get('event_status');
    if (params.has('event_collaboration_id')) eventFilters.value.collaborationId = params.get('event_collaboration_id');
    if (params.has('event_date_from')) eventFilters.value.dateFrom = params.get('event_date_from');
    if (params.has('event_date_to')) eventFilters.value.dateTo = params.get('event_date_to');
};

// Filter application functions
const applyCourseFilters = () => {
    const params = {
        courses_page: 1, // Reset to first page when applying filters
    };

    if (courseFilters.value.search) params.course_search = courseFilters.value.search;
    if (courseFilters.value.bright) params.course_bright = courseFilters.value.bright;
    if (courseFilters.value.sort) params.course_sort = courseFilters.value.sort;

    router.get(
        router.page.url,
        params,
        {
            preserveState: true,
            preserveScroll: true,
            only: ['courses', 'auth']
        }
    );
};

const resetCourseFilters = () => {
    courseFilters.value = {
        search: '',
        bright: '',
        sort: 'newest'
    };

    router.get(
        router.page.url,
        { courses_page: 1 },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['courses', 'auth']
        }
    );
};

const applyEventFilters = () => {
    const params = {
        events_page: 1, // Reset to first page when applying filters
    };

    if (eventFilters.value.status) params.event_status = eventFilters.value.status;
    if (eventFilters.value.collaborationId) params.event_collaboration_id = eventFilters.value.collaborationId;
    if (eventFilters.value.dateFrom) params.event_date_from = eventFilters.value.dateFrom;
    if (eventFilters.value.dateTo) params.event_date_to = eventFilters.value.dateTo;

    router.get(
        router.page.url,
        params,
        {
            preserveState: true,
            preserveScroll: true,
            only: ['events', 'auth']
        }
    );
};

const resetEventFilters = () => {
    eventFilters.value = {
        status: '',
        collaborationId: '',
        dateFrom: '',
        dateTo: ''
    };

    router.get(
        router.page.url,
        { events_page: 1 },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['events', 'auth']
        }
    );
};

const changePage = (type, page) => {
    const params = { [`${type}_page`]: page };

    // Preserve existing filters when changing pages
    if (type === 'courses') {
        if (courseFilters.value.search) params.course_search = courseFilters.value.search;
        if (courseFilters.value.bright) params.course_bright = courseFilters.value.bright;
        if (courseFilters.value.sort) params.course_sort = courseFilters.value.sort;
    } else if (type === 'events') {
        if (eventFilters.value.status) params.event_status = eventFilters.value.status;
        if (eventFilters.value.collaborationId) params.event_collaboration_id = eventFilters.value.collaborationId;
        if (eventFilters.value.dateFrom) params.event_date_from = eventFilters.value.dateFrom;
        if (eventFilters.value.dateTo) params.event_date_to = eventFilters.value.dateTo;
    }

    router.get(
        router.page.url,
        params,
        {
            preserveState: true,
            preserveScroll: true,
            only: [type, 'auth']
        }
    );
};

// New course dialog functions
const openAddCourseDialog = () => {
    addCourseDialogOpen.value = true;
};

const closeAddCourseDialog = () => {
    addCourseDialogOpen.value = false;
};

const newCourse = ref({
    title: '',
    description: '',
    bright: false
});

const addCourse = () => {
    if (!newCourse.value.title.trim()) return;

    // In a real app, you would submit this to your backend
    // For demo purposes, we'll just close the dialog
    closeAddCourseDialog();

    // Reset form
    newCourse.value = {
        title: '',
        description: '',
        bright: false
    };
};

// New lesson dialog functions
const openAddLessonDialog = () => {
    addLessonDialogOpen.value = true;
};

const closeAddLessonDialog = () => {
    addLessonDialogOpen.value = false;
};

const newLesson = ref({
    courseId: '',
    title: '',
    content: ''
});

const addLesson = () => {
    if (!newLesson.value.courseId || !newLesson.value.title.trim()) return;

    // In a real app, you would submit this to your backend
    // For demo purposes, we'll just close the dialog
    closeAddLessonDialog();

    // Reset form
    newLesson.value = {
        courseId: '',
        title: '',
        content: ''
    };
};

// Course sharing functions
const openShareCourseDialog = (course) => {
    selectedCourseForSharing.value = course;
    shareCourseDialogOpen.value = true;
};

const closeShareCourseDialog = () => {
    shareCourseDialogOpen.value = false;
    selectedCourseForSharing.value = null;
};

const shareEmail = ref('');
const sharePermission = ref('view');

const shareCourse = () => {
    if (!shareEmail.value.trim() || !selectedCourseForSharing.value) return;

    // In a real app, you would submit this to your backend
    // For demo purposes, we'll just close the dialog
    closeShareCourseDialog();

    // Reset form
    shareEmail.value = '';
    sharePermission.value = 'view';
};

// Lesson ordering functions
const openLessonOrderingDialog = (course) => {
    selectedCourseForLessons.value = course;
    // In a real app, you would fetch lessons for this course
    // For demo purposes, we'll use mock data
    courseLessons.value = [...(mockLessons[course.id] || [])].sort((a, b) => a.order - b.order);
    showLessonOrderingDialog.value = true;
};

const closeLessonOrderingDialog = () => {
    showLessonOrderingDialog.value = false;
    selectedCourseForLessons.value = null;
    courseLessons.value = [];
};

const startDrag = (lesson, event) => {
    isDragging.value = true;
    draggedLesson.value = lesson;
    event.dataTransfer.effectAllowed = 'move';
};

const onDragOver = (event) => {
    event.preventDefault();
};

const onDrop = (targetLesson) => {
    if (!draggedLesson.value || draggedLesson.value.id === targetLesson.id) {
        isDragging.value = false;
        draggedLesson.value = null;
        return;
    }

    // Reorder lessons
    const draggedOrder = draggedLesson.value.order;
    const targetOrder = targetLesson.order;

    // Update orders
    courseLessons.value = courseLessons.value.map(lesson => {
        if (lesson.id === draggedLesson.value.id) {
            return { ...lesson, order: targetOrder };
        } else if (draggedOrder < targetOrder && lesson.order > draggedOrder && lesson.order <= targetOrder) {
            return { ...lesson, order: lesson.order - 1 };
        } else if (draggedOrder > targetOrder && lesson.order < draggedOrder && lesson.order >= targetOrder) {
            return { ...lesson, order: lesson.order + 1 };
        }
        return lesson;
    });

    // Sort by new order
    courseLessons.value.sort((a, b) => a.order - b.order);

    isDragging.value = false;
    draggedLesson.value = null;
};

const saveLessonOrder = () => {
    // In a real app, you would submit the new order to your backend
    // For demo purposes, we'll just close the dialog
    closeLessonOrderingDialog();
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Contributions', href: '/contributions' }];

onMounted(() => {
    initializeFilters();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Contributions" />
        <div class="min-h-screen bg-gradient-to-br from-purple-50 to-white dark:from-purple-950 dark:to-slate-900 text-slate-800 dark:text-slate-200 transition-colors duration-300">
            <!-- Main content -->
            <main class="container mx-auto p-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-purple-800 dark:text-purple-300">Contributions</h1>

                    <div class="flex gap-3">
                        <button
                            @click="openAddCourseDialog"
                            class="flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                        >
                            <PlusCircle class="h-4 w-4" />
                            Add Course
                        </button>

                        <button
                            @click="openAddLessonDialog"
                            class="flex items-center gap-2 px-4 py-2 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-300"
                        >
                            <PlusCircle class="h-4 w-4" />
                            Add Lesson
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Courses Section -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Courses Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-fadeIn" style="animation-delay: 0.1s">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                    <BookOpen class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">My Courses</h2>
                            </div>

                            <!-- Course Filters Button -->
                            <button
                                @click="courseFiltersVisible = !courseFiltersVisible"
                                class="flex items-center gap-2 px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 text-sm"
                            >
                                <Filter class="h-4 w-4" />
                                {{ courseFiltersVisible ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                        </div>

                        <!-- Course Filters Panel -->
                        <div
                            v-if="courseFiltersVisible"
                            class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-5 border border-purple-100 dark:border-purple-900/30 animate-slideDown"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Search by title -->
                                <div>
                                    <label for="course-search" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <Search class="h-4 w-4 text-slate-400 dark:text-slate-500" />
                                        </div>
                                        <input
                                            id="course-search"
                                            v-model="courseFilters.search"
                                            type="text"
                                            placeholder="Search by title..."
                                            class="w-full pl-10 pr-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                                        />
                                    </div>
                                </div>

                                <!-- Filter by bright status -->
                                <div>
                                    <label for="course-bright" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Highlight Status</label>
                                    <select
                                        id="course-bright"
                                        v-model="courseFilters.bright"
                                        class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                                    >
                                        <option value="">All</option>
                                        <option value="true">Highlighted</option>
                                        <option value="false">Not Highlighted</option>
                                    </select>
                                </div>

                                <!-- Sort by -->
                                <div>
                                    <label for="course-sort" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Sort By</label>
                                    <select
                                        id="course-sort"
                                        v-model="courseFilters.sort"
                                        class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                                    >
                                        <option value="newest">Newest First</option>
                                        <option value="oldest">Oldest First</option>
                                        <option value="title_asc">Title (A-Z)</option>
                                        <option value="title_desc">Title (Z-A)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-end mt-5 space-x-3">
                                <button
                                    @click="resetCourseFilters"
                                    class="px-4 py-2 border border-purple-200 dark:border-purple-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-300"
                                >
                                    Reset
                                </button>
                                <button
                                    @click="applyCourseFilters"
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                                >
                                    Apply Filters
                                </button>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600 dark:text-slate-400 animate-fadeIn" style="animation-delay: 0.2s">
                            Showing {{ courses.length }} of {{ totalCourses }} courses
                        </p>

                        <!-- No results message -->
                        <div
                            v-if="courses.length === 0"
                            class="bg-white dark:bg-slate-800 rounded-xl shadow-lg p-8 text-center animate-fadeIn border border-purple-100 dark:border-purple-900/30"
                            style="animation-delay: 0.3s"
                        >
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <Search class="h-8 w-8 text-purple-400 dark:text-purple-500" />
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-1">No courses found</h3>
                            <p class="text-slate-500 dark:text-slate-400">Try adjusting your filters or search criteria</p>
                        </div>

                        <!-- Course grid -->
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div
                                v-for="(course, index) in courses"
                                :key="course.id"
                                class="animate-fadeIn relative group"
                                :style="`animation-delay: ${0.3 + index * 0.1}s`"
                            >
                                <CourseCard :course="course" />

                                <!-- Action buttons overlay -->
                                <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button
                                        @click.stop="openShareCourseDialog(course)"
                                        class="p-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-md shadow-sm"
                                        title="Share course"
                                    >
                                        <Share2 class="h-4 w-4" />
                                    </button>
                                    <button
                                        @click.stop="openLessonOrderingDialog(course)"
                                        class="p-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-md shadow-sm"
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
                            class="mt-6 animate-fadeIn"
                            style="animation-delay: 0.5s"
                        />
                    </div>

                    <!-- Sidebar with Groups and Events -->
                    <div class="space-y-6">
                        <!-- Groups Section -->
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden animate-fadeIn border border-purple-100 dark:border-purple-900/30" style="animation-delay: 0.2s">
                            <div class="p-5 border-b border-purple-100 dark:border-purple-900/30">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                        <Users class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                                    </div>
                                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">My Groups</h2>
                                </div>
                            </div>

                            <div class="p-5">
                                <div v-if="groups.length === 0" class="text-center py-8">
                                    <p class="text-slate-500 dark:text-slate-400">No groups available</p>
                                </div>

                                <div v-else class="space-y-3">
                                    <div
                                        v-for="(group, index) in groups"
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
                        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden animate-fadeIn border border-purple-100 dark:border-purple-900/30" style="animation-delay: 0.3s">
                            <div class="p-5 border-b border-purple-100 dark:border-purple-900/30">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                            <Calendar class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Events</h2>
                                    </div>

                                    <!-- Event Filters Button -->
                                    <button
                                        @click="eventFiltersVisible = !eventFiltersVisible"
                                        class="flex items-center gap-1 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
                                    >
                                        <Filter class="h-4 w-4" />
                                        {{ eventFiltersVisible ? 'Hide' : 'Filter' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Event Filters Panel -->
                            <div v-if="eventFiltersVisible" class="bg-purple-50 dark:bg-purple-900/20 p-4 animate-slideDown">
                                <div class="space-y-3">
                                    <!-- Filter by status -->
                                    <div>
                                        <label for="event-status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                                        <select
                                            id="event-status"
                                            v-model="eventFilters.status"
                                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                                        >
                                            <option value="">All</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                    <!-- Filter by collaboration ID -->
                                    <div>
                                        <label for="event-collaboration" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Collaboration ID</label>
                                        <input
                                            id="event-collaboration"
                                            v-model="eventFilters.collaborationId"
                                            type="number"
                                            min="1"
                                            placeholder="Enter ID..."
                                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm placeholder-slate-400 dark:placeholder-slate-500 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                                        />
                                    </div>

                                    <!-- Date range -->
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Date Range</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label for="event-date-from" class="block text-xs text-slate-500 dark:text-slate-400">From</label>
                                                <input
                                                    id="event-date-from"
                                                    v-model="eventFilters.dateFrom"
                                                    type="date"
                                                    class="w-full px-3 py-1 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                                                />
                                            </div>
                                            <div>
                                                <label for="event-date-to" class="block text-xs text-slate-500 dark:text-slate-400">To</label>
                                                <input
                                                    id="event-date-to"
                                                    v-model="eventFilters.dateTo"
                                                    type="date"
                                                    class="w-full px-3 py-1 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end mt-4 space-x-2">
                                    <button
                                        @click="resetEventFilters"
                                        class="px-3 py-1 border border-purple-200 dark:border-purple-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-300 text-sm"
                                    >
                                        Reset
                                    </button>
                                    <button
                                        @click="applyEventFilters"
                                        class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 text-sm"
                                    >
                                        Apply
                                    </button>
                                </div>
                            </div>

                            <div class="p-5">
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                                    Showing {{ events.length }} of {{ totalEvents }} events
                                </p>

                                <!-- No results message -->
                                <div v-if="events.length === 0" class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                                    <Search class="h-8 w-8 mx-auto text-purple-400 dark:text-purple-500 mb-2" />
                                    <p class="text-slate-500 dark:text-slate-400 text-sm">No events match your filters</p>
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

        <!-- Add Course Dialog -->
        <div v-if="addCourseDialogOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full mx-4 animate-fadeIn border border-purple-100 dark:border-purple-900/30">
                <div class="p-5 border-b border-purple-100 dark:border-purple-900/30">
                    <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300">Add New Course</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Create a new course to share with your students.</p>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label for="course-title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Course Title</label>
                        <input
                            id="course-title"
                            v-model="newCourse.title"
                            type="text"
                            placeholder="Enter course title"
                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                        />
                    </div>

                    <div>
                        <label for="course-description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description</label>
                        <textarea
                            id="course-description"
                            v-model="newCourse.description"
                            placeholder="Enter course description"
                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300 min-h-[100px]"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="course-highlighted"
                            v-model="newCourse.bright"
                            class="rounded border-purple-300 text-purple-600 focus:ring-purple-500"
                        />
                        <label for="course-highlighted" class="text-sm font-medium text-slate-700 dark:text-slate-300">Highlight this course</label>
                    </div>
                </div>

                <div class="p-5 border-t border-purple-100 dark:border-purple-900/30 flex justify-end space-x-3">
                    <button
                        @click="closeAddCourseDialog"
                        class="px-4 py-2 border border-purple-200 dark:border-purple-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-300"
                    >
                        Cancel
                    </button>
                    <button
                        @click="addCourse"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                        :disabled="!newCourse.title.trim()"
                    >
                        Create Course
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Lesson Dialog -->
        <div v-if="addLessonDialogOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full mx-4 animate-fadeIn border border-purple-100 dark:border-purple-900/30">
                <div class="p-5 border-b border-purple-100 dark:border-purple-900/30">
                    <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300">Add New Lesson</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Create a new lesson for an existing course.</p>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label for="lesson-course" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Select Course</label>
                        <select
                            id="lesson-course"
                            v-model="newLesson.courseId"
                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                        >
                            <option value="">Select a course</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="lesson-title" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lesson Title</label>
                        <input
                            id="lesson-title"
                            v-model="newLesson.title"
                            type="text"
                            placeholder="Enter lesson title"
                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                        />
                    </div>

                    <div>
                        <label for="lesson-content" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lesson Content</label>
                        <textarea
                            id="lesson-content"
                            v-model="newLesson.content"
                            placeholder="Enter lesson content or description"
                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300 min-h-[100px]"
                        ></textarea>
                    </div>
                </div>

                <div class="p-5 border-t border-purple-100 dark:border-purple-900/30 flex justify-end space-x-3">
                    <button
                        @click="closeAddLessonDialog"
                        class="px-4 py-2 border border-purple-200 dark:border-purple-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-300"
                    >
                        Cancel
                    </button>
                    <button
                        @click="addLesson"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                        :disabled="!newLesson.courseId || !newLesson.title.trim()"
                    >
                        Add Lesson
                    </button>
                </div>
            </div>
        </div>

        <!-- Share Course Dialog -->
        <div v-if="shareCourseDialogOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full mx-4 animate-fadeIn border border-purple-100 dark:border-purple-900/30">
                <div class="p-5 border-b border-purple-100 dark:border-purple-900/30">
                    <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300">Share Course</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Share "{{ selectedCourseForSharing?.title }}" with others
                    </p>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label for="share-email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input
                            id="share-email"
                            v-model="shareEmail"
                            type="email"
                            placeholder="Enter email address"
                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                        />
                    </div>

                    <div>
                        <label for="share-permission" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Permission</label>
                        <select
                            id="share-permission"
                            v-model="sharePermission"
                            class="w-full px-3 py-2 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-shadow duration-300"
                        >
                            <option value="view">View only</option>
                            <option value="edit">Can edit</option>
                            <option value="admin">Admin access</option>
                        </select>
                    </div>
                </div>

                <div class="p-5 border-t border-purple-100 dark:border-purple-900/30 flex justify-end space-x-3">
                    <button
                        @click="closeShareCourseDialog"
                        class="px-4 py-2 border border-purple-200 dark:border-purple-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-300"
                    >
                        Cancel
                    </button>
                    <button
                        @click="shareCourse"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                        :disabled="!shareEmail.trim()"
                    >
                        Share
                    </button>
                </div>
            </div>
        </div>

        <!-- Lesson Ordering Dialog -->
        <div v-if="showLessonOrderingDialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl max-w-md w-full mx-4 animate-fadeIn border border-purple-100 dark:border-purple-900/30">
                <div class="p-5 border-b border-purple-100 dark:border-purple-900/30">
                    <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300">Reorder Lessons</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Drag and drop to reorder lessons for "{{ selectedCourseForLessons?.title }}"
                    </p>
                </div>

                <div class="p-5">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                        Drag lessons to change their order
                    </p>

                    <div v-if="courseLessons.length === 0" class="text-center py-4">
                        <p class="text-slate-500 dark:text-slate-400">No lessons available for this course</p>
                    </div>

                    <ul v-else class="space-y-2">
                        <li
                            v-for="lesson in courseLessons"
                            :key="lesson.id"
                            draggable="true"
                            @dragstart="startDrag(lesson, $event)"
                            @dragover.prevent="onDragOver"
                            @drop="onDrop(lesson)"
                            :class="[
                                'p-3 rounded-lg border flex items-center cursor-move transition-colors duration-200',
                                isDragging && draggedLesson?.id === lesson.id
                                    ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30'
                                    : 'border-purple-200 dark:border-purple-800 hover:bg-purple-50 dark:hover:bg-purple-900/20'
                            ]"
                        >
                            <GripVertical class="h-5 w-5 text-slate-400 dark:text-slate-500 mr-3 flex-shrink-0" />
                            <span class="font-medium text-slate-900 dark:text-white">{{ lesson.title }}</span>
                        </li>
                    </ul>
                </div>

                <div class="p-5 border-t border-purple-100 dark:border-purple-900/30 flex justify-end space-x-3">
                    <button
                        @click="closeLessonOrderingDialog"
                        class="px-4 py-2 border border-purple-200 dark:border-purple-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors duration-300"
                    >
                        Cancel
                    </button>
                    <button
                        @click="saveLessonOrder"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300"
                    >
                        Save Order
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Custom animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
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
