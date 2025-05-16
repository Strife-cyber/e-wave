<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Award, BarChart2, BookOpen, Calendar, Menu, MessageCircle, Shield, Users } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    user: Object,
    progress: Array,
    rewards: Number,
    badges: Array,
    groups: Array,
    echoCards: Array,
    courses: Array,
});
console.log(props.badges);

const showProfileMenu = ref(false);
const showMobileMenu = ref(false);
const pendingTasks = ref(Math.floor(Math.random() * 5) + 1);
const profileDropdown = ref(null);
const mobileMenuRef = ref(null);

// Computed properties
const completedCount = computed(() => {
    return props.progress.filter((p) => p.completed).length;
});

const completionPercentage = computed(() => {
    if (!props.progress || props.progress.length === 0) return 0;
    return Math.round((completedCount.value / props.progress.length) * 100);
});

const hasMessages = computed(() => {
    return props.groups.some((group) => group.latest_message);
});

const groupsWithMessages = computed(() => {
    return props.groups.filter((group) => group.latest_message);
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(date);
};

const formatTimeAgo = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'just now';
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
    return formatDate(dateString);
};

const getRandomProgress = () => {
    return Math.floor(Math.random() * 100);
};

// Toggle mobile menu
const toggleMobileMenu = () => {
    showMobileMenu.value = !showMobileMenu.value;
};

// Check if device is tablet
const isTablet = computed(() => {
    if (typeof window === 'undefined') return false;
    return window.innerWidth >= 768 && window.innerWidth < 1024;
});

// Window resize handler
const handleResize = () => {
    if (window.innerWidth >= 1024 && showMobileMenu.value) {
        showMobileMenu.value = false;
    }
};

// Click outside to close profile menu and mobile menu
const handleClickOutside = (event) => {
    if (profileDropdown.value && !profileDropdown.value.contains(event.target)) {
        showProfileMenu.value = false;
    }
    if (mobileMenuRef.value && !mobileMenuRef.value.contains(event.target) && !event.target.classList.contains('mobile-menu-button')) {
        showMobileMenu.value = false;
    }
};

// Navigation functions
const navigateTo = (path) => {
    window.location.href = path;
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('resize', handleResize);
});

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
</script>

<template>
    <Head title="Dashboard" />
    <div class="relative min-h-screen bg-gray-50 dark:bg-black">
        <div class="background-rays"></div>
        <AppLayout :breadcrumbs="breadcrumbs">
            <!-- Mobile/Tablet Menu Button -->
            <button
                @click="toggleMobileMenu"
                class="mobile-menu-button fixed right-4 top-4 z-50 block rounded-full bg-white p-2 shadow-md dark:bg-gray-800 lg:hidden"
            >
                <Menu class="h-6 w-6 text-purple-600 dark:text-purple-400" />
            </button>

            <!-- Mobile/Tablet Navigation Menu -->
            <div
                v-if="showMobileMenu"
                ref="mobileMenuRef"
                class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
                @click.self="showMobileMenu = false"
            >
                <div class="absolute right-0 top-0 h-full w-64 bg-white p-6 shadow-xl dark:bg-gray-800 md:w-80">
                    <div class="mb-8 flex flex-col space-y-4">
                        <a
                            href="/dashboard"
                            class="flex items-center rounded-lg p-2 text-purple-600 hover:bg-purple-50 dark:text-purple-400 dark:hover:bg-purple-900/20"
                        >
                            <BarChart2 class="mr-2 h-5 w-5" />
                            Dashboard
                        </a>
                        <a
                            href="/user/courses"
                            class="flex items-center rounded-lg p-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-200 dark:hover:bg-purple-900/20 dark:hover:text-purple-400"
                        >
                            <BookOpen class="mr-2 h-5 w-5" />
                            Courses
                        </a>
                        <a
                            href="/echo"
                            class="flex items-center rounded-lg p-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-200 dark:hover:bg-purple-900/20 dark:hover:text-purple-400"
                        >
                            <MessageCircle class="mr-2 h-5 w-5" />
                            Echo Cards
                        </a>
                        <a
                            href="/user/badges"
                            class="flex items-center rounded-lg p-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-200 dark:hover:bg-purple-900/20 dark:hover:text-purple-400"
                        >
                            <Shield class="mr-2 h-5 w-5" />
                            Badges
                        </a>
                        <a
                            href="/user/groups"
                            class="flex items-center rounded-lg p-2 text-gray-700 hover:bg-purple-50 hover:text-purple-600 dark:text-gray-200 dark:hover:bg-purple-900/20 dark:hover:text-purple-400"
                        >
                            <Users class="mr-2 h-5 w-5" />
                            Study Groups
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex h-full flex-1 flex-col gap-6 rounded-xl bg-white/80 p-4 backdrop-blur-sm dark:bg-black md:p-6">
                <!-- Welcome Section -->
                <div class="animate-fade-in mb-6 p-3 md:mb-8 md:p-4">
                    <h2 class="font-pacifico text-xl text-gray-800 dark:text-gray-100 md:text-2xl">
                        Welcome back, <span class="text-purple-500 dark:text-purple-400">{{ user.name }}</span
                        >!
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        Continue your learning journey. You have
                        <span class="font-medium text-purple-500 dark:text-purple-400">{{ pendingTasks }} pending tasks</span>.
                    </p>
                </div>

                <!-- Stats Overview -->
                <div class="mb-6 grid grid-cols-2 gap-3 md:mb-8 md:grid-cols-4 md:gap-4 lg:gap-6">
                    <!-- Progress Card -->
                    <div class="animate-fade-in rounded-xl bg-white p-3 shadow-md transition-all hover:shadow-lg dark:bg-gray-800 md:p-4 lg:p-6">
                        <div class="mb-3 flex items-center justify-between md:mb-4">
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Learning Progress</h3>
                            <div class="rounded-full bg-purple-100 p-1.5 dark:bg-purple-900/30 md:p-2">
                                <BarChart2 class="h-4 w-4 text-purple-600 dark:text-purple-400 md:h-5 md:w-5" />
                            </div>
                        </div>
                        <div class="mb-3 md:mb-4">
                            <div class="mb-2 flex justify-between text-xs text-gray-600 dark:text-gray-400 md:text-sm">
                                <span>{{ completionPercentage }}% Complete</span>
                                <span>{{ completedCount }}/{{ progress.length }}</span>
                            </div>
                            <div class="progress-bar-container h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700 md:h-2.5">
                                <div
                                    class="progress-bar h-2 rounded-full bg-purple-600 md:h-2.5"
                                    :style="{ width: `${completionPercentage}%` }"
                                ></div>
                            </div>
                        </div>
                        <button
                            @click="navigateTo('/user/progress')"
                            class="flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 md:text-sm"
                        >
                            View details
                            <svg class="ml-1 h-3 w-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Rewards Card -->
                    <div class="animate-fade-in rounded-xl bg-white p-3 shadow-md transition-all hover:shadow-lg dark:bg-gray-800 md:p-4 lg:p-6">
                        <div class="mb-3 flex items-center justify-between md:mb-4">
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Rewards</h3>
                            <div class="rounded-full bg-yellow-100 p-1.5 dark:bg-yellow-900/30 md:p-2">
                                <Award class="h-4 w-4 text-yellow-600 dark:text-yellow-400 md:h-5 md:w-5" />
                            </div>
                        </div>
                        <div class="mb-3 flex items-end md:mb-4">
                            <span class="rewards-count text-2xl font-bold text-gray-800 dark:text-gray-100 md:text-3xl">{{ rewards }}</span>
                            <span class="ml-1 text-xs text-gray-500 dark:text-gray-400 md:ml-2 md:text-sm">points</span>
                        </div>
                        <button
                            @click="navigateTo('/user/rewards')"
                            class="flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 md:text-sm"
                        >
                            Redeem rewards
                            <svg class="ml-1 h-3 w-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Badges Card -->
                    <div class="animate-fade-in rounded-xl bg-white p-3 shadow-md transition-all hover:shadow-lg dark:bg-gray-800 md:p-4 lg:p-6">
                        <div class="mb-3 flex items-center justify-between md:mb-4">
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Badges</h3>
                            <div class="rounded-full bg-purple-100 p-1.5 dark:bg-purple-900/30 md:p-2">
                                <Shield class="h-4 w-4 text-purple-600 dark:text-purple-400 md:h-5 md:w-5" />
                            </div>
                        </div>
                        <div class="mb-3 flex items-end md:mb-4">
                            <span class="badges-count text-2xl font-bold text-gray-800 dark:text-gray-100 md:text-3xl">{{ badges.length }}</span>
                            <span class="ml-1 text-xs text-gray-500 dark:text-gray-400 md:ml-2 md:text-sm">earned</span>
                        </div>
                        <button
                            @click="navigateTo('/user/badges')"
                            class="flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 md:text-sm"
                        >
                            View all badges
                            <svg class="ml-1 h-3 w-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Groups Card -->
                    <div class="animate-fade-in rounded-xl bg-white p-3 shadow-md transition-all hover:shadow-lg dark:bg-gray-800 md:p-4 lg:p-6">
                        <div class="mb-3 flex items-center justify-between md:mb-4">
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Study Groups</h3>
                            <div class="rounded-full bg-green-100 p-1.5 dark:bg-green-900/30 md:p-2">
                                <Users class="h-4 w-4 text-green-600 dark:text-green-400 md:h-5 md:w-5" />
                            </div>
                        </div>
                        <div class="mb-3 flex items-end md:mb-4">
                            <span class="groups-count text-2xl font-bold text-gray-800 dark:text-gray-100 md:text-3xl">{{ groups.length }}</span>
                            <span class="ml-1 text-xs text-gray-500 dark:text-gray-400 md:ml-2 md:text-sm">active</span>
                        </div>
                        <button
                            @click="navigateTo('/user/groups')"
                            class="flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 md:text-sm"
                        >
                            View groups
                            <svg class="ml-1 h-3 w-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 gap-6 md:gap-8">
                    <!-- Tablet-optimized top row: Courses and Echo Cards side by side on tablets -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8">
                        <!-- Courses Section -->
                        <div class="animate-fade-in rounded-xl bg-white shadow-md dark:bg-gray-800">
                            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4 dark:border-gray-700 md:px-6 md:py-5">
                                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Your Courses</h3>
                                <a
                                    href="/user/courses"
                                    class="flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 md:text-sm"
                                >
                                    View all
                                    <svg class="ml-1 h-3 w-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </a>
                            </div>
                            <div class="p-4 md:p-6">
                                <div v-if="courses.length === 0" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                    No courses enrolled yet.
                                </div>
                                <div v-else class="space-y-3 md:space-y-4">
                                    <div
                                        v-for="course in courses?.slice(0, 3)"
                                        :key="course.id"
                                        class="course-card rounded-lg border border-gray-200 p-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 md:p-4"
                                        @click="navigateTo(`/courses/${course.id}`)"
                                    >
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4 class="font-medium text-gray-800 dark:text-gray-100">{{ course.title }}</h4>
                                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 md:text-sm">{{ course.description }}</p>
                                                <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400 md:mt-3">
                                                    <span class="mr-2">Created by:</span>
                                                    <span class="font-medium">Instructor #{{ course.created_by }}</span>
                                                </div>
                                            </div>
                                            <button
                                                class="h-7 w-7 rounded-full text-purple-600 transition-colors hover:bg-purple-50 hover:text-purple-800 dark:hover:bg-purple-900/30 md:h-8 md:w-8"
                                            >
                                                <BookOpen class="h-4 w-4 md:h-5 md:w-5" />
                                            </button>
                                        </div>
                                        <div
                                            class="mt-3 flex items-center justify-between border-t border-gray-100 pt-2 dark:border-gray-700 md:mt-4 md:pt-3"
                                        >
                                            <div class="flex items-center">
                                                <div class="h-1.5 w-16 rounded-full bg-gray-200 dark:bg-gray-700 md:w-24">
                                                    <div class="h-1.5 rounded-full bg-purple-600" :style="{ width: `${getRandomProgress()}%` }"></div>
                                                </div>
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ getRandomProgress() }}%</span>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatTimeAgo(course.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Echo Cards Section -->
                        <div class="animate-fade-in rounded-xl bg-white shadow-md dark:bg-gray-800">
                            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4 dark:border-gray-700 md:px-6 md:py-5">
                                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Echo Cards</h3>
                                <a href="/echo" class="flex items-center text-xs font-medium text-purple-600 hover:text-purple-800 md:text-sm">
                                    Create new
                                    <svg class="ml-1 h-3 w-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </a>
                            </div>
                            <div class="p-4 md:p-6">
                                <div v-if="echoCards.length === 0" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                    No echo cards available.
                                </div>
                                <div v-else class="grid grid-cols-1 gap-3 md:gap-4">
                                    <div
                                        v-for="(card, index) in echoCards?.slice(0, 4)"
                                        :key="card.id"
                                        class="echo-card rounded-lg border border-gray-200 p-3 transition-all hover:shadow-md dark:border-gray-700 md:p-4"
                                        :class="{ 'bg-purple-50 dark:bg-purple-900/20': index === 0 }"
                                        @click="navigateTo(`/echo/${card.id}`)"
                                    >
                                        <div class="mb-1 flex items-center justify-between md:mb-2">
                                            <span
                                                class="text-xs font-medium md:text-sm"
                                                :class="index === 0 ? 'text-purple-600 dark:text-purple-400' : 'text-gray-600 dark:text-gray-300'"
                                                >Echo Card</span
                                            >
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(card.created_at) }}</span>
                                        </div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-gray-100 md:mb-2 md:text-base">
                                            {{ card.title || `Review Card #${card.id}` }}
                                        </h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 md:text-sm">
                                            {{ card.content || 'No content available' }}
                                        </p>
                                        <div class="mt-3 flex items-center justify-between md:mt-4">
                                            <button
                                                @click.stop="navigateTo(`/echo/${card.id}/edit`)"
                                                class="text-xs font-medium text-purple-600 hover:text-purple-800"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click.stop="navigateTo(`/echo/${card.id}/review`)"
                                                class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                            >
                                                Review
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tablet-optimized middle row: Messages and Badges side by side on tablets -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8">
                        <!-- Recent Messages -->
                        <div class="animate-fade-in rounded-xl bg-white shadow-md dark:bg-gray-800">
                            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4 dark:border-gray-700 md:px-6 md:py-5">
                                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Recent Messages</h3>
                            </div>
                            <div class="p-4 md:p-6">
                                <div v-if="groups.length === 0 || !hasMessages" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                    No recent messages.
                                </div>
                                <div v-else class="space-y-3 md:space-y-4">
                                    <div
                                        v-for="group in groupsWithMessages"
                                        :key="group.id"
                                        class="message-card rounded-lg border border-gray-200 p-3 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700 md:p-4"
                                        @click="navigateTo(`/groups/${group.group_id}`)"
                                    >
                                        <div class="mb-1 flex items-start justify-between md:mb-2">
                                            <h4 class="flex items-center text-sm font-medium text-gray-800 dark:text-gray-100 md:text-base">
                                                <MessageCircle class="mr-1 h-3 w-3 text-purple-600 dark:text-purple-400 md:mr-2 md:h-4 md:w-4" />
                                                Group #{{ group.group_id }}
                                            </h4>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{
                                                formatTimeAgo(group.latest_message.sent_at)
                                            }}</span>
                                        </div>
                                        <p class="mb-2 text-xs text-gray-600 dark:text-gray-300 md:mb-3 md:text-sm">
                                            {{ group.latest_message.content }}
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500 dark:text-gray-400"
                                                >From: User #{{ group.latest_message.user_id }}</span
                                            >
                                            <button
                                                @click.stop="navigateTo(`/groups/${group.group_id}/reply`)"
                                                class="text-xs font-medium text-purple-600 hover:text-purple-800"
                                            >
                                                Reply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="animate-fade-in rounded-xl bg-white shadow-md dark:bg-gray-800">
                            <div class="border-b border-gray-200 px-4 py-4 dark:border-gray-700 md:px-6 md:py-5">
                                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Your Badges</h3>
                            </div>
                            <div class="p-4 md:p-6">
                                <div v-if="badges.length === 0" class="py-4 text-center text-gray-500 dark:text-gray-400">No badges earned yet.</div>
                                <div v-else class="grid grid-cols-2 gap-3 md:gap-4">
                                    <div
                                        v-for="badge in badges"
                                        :key="badge.id"
                                        class="badge-item flex flex-col items-center"
                                        @click="navigateTo(`/badges/${badge.id}`)"
                                    >
                                        <div
                                            class="badge-icon mb-1 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30 md:mb-2 md:h-16 md:w-16"
                                        >
                                            <Shield class="h-6 w-6 text-purple-600 dark:text-purple-400 md:h-8 md:w-8" />
                                        </div>
                                        <span class="text-center text-xs font-medium text-gray-800 dark:text-gray-100 md:text-sm">{{
                                            badge.name || 'Achievement Badge'
                                        }}</span>
                                        <span class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 md:mt-1">{{
                                            formatDate(badge.created_at)
                                        }}</span>
                                    </div>
                                </div>
                                <div class="mt-3 text-center md:mt-4">
                                    <button
                                        @click="navigateTo('/user/badges')"
                                        class="text-xs font-medium text-purple-600 hover:text-purple-800 md:text-sm"
                                    >
                                        View all badges
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Deadlines - Full width on all screens -->
                    <div class="animate-fade-in rounded-xl bg-white shadow-md dark:bg-gray-800">
                        <div class="flex items-center border-b border-gray-200 px-4 py-4 dark:border-gray-700 md:px-6 md:py-5">
                            <Calendar class="mr-2 h-4 w-4 text-purple-600 dark:text-purple-400 md:h-5 md:w-5" />
                            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200 md:text-lg">Upcoming Deadlines</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                <li
                                    class="flex cursor-pointer items-center justify-between py-2 hover:bg-gray-50 dark:hover:bg-gray-700 md:py-3"
                                    @click="navigateTo('/assignments/1')"
                                >
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">Assignment #1</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Course: {{ courses[0]?.title || 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs font-medium text-red-600 dark:text-red-400">2 days left</span>
                                </li>
                                <li
                                    class="flex cursor-pointer items-center justify-between py-2 hover:bg-gray-50 dark:hover:bg-gray-700 md:py-3"
                                    @click="navigateTo('/groups/projects/1')"
                                >
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">Group Project</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Group #{{ groups[0]?.group_id || 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">5 days left</span>
                                </li>
                                <li
                                    class="flex cursor-pointer items-center justify-between py-2 hover:bg-gray-50 dark:hover:bg-gray-700 md:py-3"
                                    @click="navigateTo('/quizzes/2')"
                                >
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">Quiz #2</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Course: {{ courses[1]?.title || 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs font-medium text-green-600 dark:text-green-400">1 week left</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Pacifico&display=swap');

.background-rays {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
        radial-gradient(circle at top left, rgba(124, 58, 237, 0.15), transparent 50%),
        radial-gradient(circle at bottom right, rgba(124, 58, 237, 0.15), transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.dark .background-rays {
    background:
        radial-gradient(circle at top left, rgba(167, 139, 250, 0.25), transparent 50%),
        radial-gradient(circle at bottom right, rgba(167, 139, 250, 0.25), transparent 50%);
}

.font-pacifico {
    font-family: 'Pacifico', cursive;
}

.progress-bar-container {
    overflow: hidden;
    position: relative;
}

.progress-bar {
    position: relative;
    transition: width 1s ease-in-out;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: progress-shimmer 2s infinite;
}

@keyframes progress-shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out forwards;
}

.course-card:hover,
.echo-card:hover,
.message-card:hover {
    transform: translateY(-2px);
    transition:
        transform 0.2s ease-in-out,
        box-shadow 0.2s ease-in-out;
    cursor: pointer;
}

.badge-item:hover .badge-icon {
    transform: scale(1.1);
    box-shadow: 0 0 15px rgba(124, 58, 237, 0.3);
    cursor: pointer;
}

.badge-icon {
    transition:
        transform 0.2s ease-in-out,
        box-shadow 0.2s ease-in-out;
}

.rewards-count:hover,
.badges-count:hover,
.groups-count:hover {
    transform: scale(1.1);
    transition: transform 0.3s ease-in-out;
}

/* Tablet-specific styles */
@media (min-width: 768px) and (max-width: 1023px) {
    .mobile-menu-button {
        top: 1rem;
        right: 1rem;
    }

    .course-card,
    .echo-card,
    .message-card {
        transition: all 0.2s ease;
    }

    .course-card:active,
    .echo-card:active,
    .message-card:active {
        transform: scale(0.98);
    }
}

::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c4b5fd;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #7c3aed;
}
</style>
