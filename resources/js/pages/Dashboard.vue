<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { BarChart2, Award, Shield, Users, BookOpen, MessageCircle, Calendar } from 'lucide-vue-next';

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
const pendingTasks = ref(Math.floor(Math.random() * 5) + 1);
const profileDropdown = ref(null);

// Computed properties
const completedCount = computed(() => {
    return props.progress.filter(p => p.completed).length;
});

const completionPercentage = computed(() => {
    if (!props.progress || props.progress.length === 0) return 0;
    return Math.round((completedCount.value / props.progress.length) * 100);
});

const hasMessages = computed(() => {
    return props.groups.some(group => group.latest_message);
});

const groupsWithMessages = computed(() => {
    return props.groups.filter(group => group.latest_message);
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
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

// Click outside to close profile menu
const handleClickOutside = (event) => {
    if (profileDropdown.value && !profileDropdown.value.contains(event.target)) {
        showProfileMenu.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];
</script>

<template>
    <Head title="Dashboard" />
    <div class="relative min-h-screen bg-gray-50 dark:bg-black">
        <div class="background-rays"></div>
        <AppLayout :breadcrumbs="breadcrumbs">
            <div class="flex h-full flex-1 flex-col gap-6 rounded-xl bg-white/80 dark:bg-black p-6 backdrop-blur-sm">
                <!-- Welcome Section -->
                <div class="mb-8 p-4 animate-fade-in">
                    <h2 class="text-2xl font-pacifico text-gray-800 dark:text-gray-100">
                        Welcome back, <span class="text-purple-500 dark:text-purple-400">{{ user.name }}</span>!
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        Continue your learning journey. You have
                        <span class="font-medium text-purple-500 dark:text-purple-400">{{ pendingTasks }} pending tasks</span>.
                    </p>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Progress Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-all animate-fade-in">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Learning Progress</h3>
                            <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900/30">
                                <BarChart2 class="h-5 w-5 text-purple-600 dark:text-purple-400" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="flex justify-between mb-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>{{ completionPercentage }}% Complete</span>
                                <span>{{ completedCount }}/{{ progress.length }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 progress-bar-container">
                                <div class="bg-purple-600 h-2.5 rounded-full progress-bar" :style="{ width: `${completionPercentage}%` }"></div>
                            </div>
                        </div>
                        <button class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                            View details
                            <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Rewards Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-all animate-fade-in">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Rewards</h3>
                            <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                                <Award class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                            </div>
                        </div>
                        <div class="flex items-end mb-4">
                            <span class="text-3xl font-bold text-gray-800 dark:text-gray-100 rewards-count">{{ rewards }}</span>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">points</span>
                        </div>
                        <button class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                            Redeem rewards
                            <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Badges Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-all animate-fade-in">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Badges</h3>
                            <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900/30">
                                <Shield class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                        </div>
                        <div class="flex items-end mb-4">
                            <span class="text-3xl font-bold text-gray-800 dark:text-gray-100 badges-count">{{ badges.length }}</span>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">earned</span>
                        </div>
                        <button class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                            View all badges
                            <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Groups Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 hover:shadow-lg transition-all animate-fade-in">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Study Groups</h3>
                            <div class="p-2 rounded-full bg-green-100 dark:bg-green-900/30">
                                <Users class="h-5 w-5 text-green-600 dark:text-green-400" />
                            </div>
                        </div>
                        <div class="flex items-end mb-4">
                            <span class="text-3xl font-bold text-gray-800 dark:text-gray-100 groups-count">{{ groups.length }}</span>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">active</span>
                        </div>
                        <button class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                            Join new group
                            <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Courses Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md animate-fade-in">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Your Courses</h3>
                            <button class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                                View all
                                <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <div v-if="courses.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                No courses enrolled yet.
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="course in courses" :key="course.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors course-card">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-800 dark:text-gray-100">{{ course.title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ course.description }}</p>
                                            <div class="flex items-center mt-3 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="mr-2">Created by:</span>
                                                <span class="font-medium">Instructor #{{ course.created_by }}</span>
                                            </div>
                                        </div>
                                        <button class="text-purple-600 hover:text-purple-800 h-8 w-8 rounded-full hover:bg-purple-50 dark:hover:bg-purple-900/30 transition-colors">
                                            <BookOpen class="h-5 w-5" />
                                        </button>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <div class="flex items-center">
                                            <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                                <div class="bg-purple-600 h-1.5 rounded-full" :style="{ width: `${getRandomProgress()}%` }"></div>
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
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md animate-fade-in">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Echo Cards</h3>
                            <button class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                                Create new
                                <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <div v-if="echoCards.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                No echo cards available.
                            </div>
                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="(card, index) in echoCards" :key="card.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all echo-card" :class="{ 'bg-purple-50 dark:bg-purple-900/20': index === 0 }">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium" :class="index === 0 ? 'text-purple-600 dark:text-purple-400' : 'text-gray-600 dark:text-gray-300'">Echo Card</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(card.created_at) }}</span>
                                    </div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-100 mb-2">{{ card.title || `Review Card #${card.id}` }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ card.content || 'No content available' }}</p>
                                    <div class="mt-4 flex justify-between items-center">
                                        <button class="text-xs text-purple-600 hover:text-purple-800 font-medium">Edit</button>
                                        <button class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">Review</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md animate-fade-in">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Recent Messages</h3>
                            <button class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center">
                                View all
                                <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <div v-if="groups.length === 0 || !hasMessages" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                No recent messages.
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="group in groupsWithMessages" :key="group.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors message-card">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-800 dark:text-gray-100 flex items-center">
                                            <MessageCircle class="h-4 w-4 mr-2 text-purple-600 dark:text-purple-400" />
                                            Group #{{ group.group_id }}
                                        </h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatTimeAgo(group.latest_message.sent_at) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ group.latest_message.content }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">From: User #{{ group.latest_message.user_id }}</span>
                                        <button class="text-xs text-purple-600 hover:text-purple-800 font-medium">Reply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Badges -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md animate-fade-in">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Your Badges</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="badges.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                No badges earned yet.
                            </div>
                            <div v-else class="grid grid-cols-2 gap-4">
                                <div v-for="badge in badges" :key="badge.id" class="flex flex-col items-center badge-item">
                                    <div class="h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-2 badge-icon">
                                        <Shield class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-100 text-center">{{ badge.name || 'Achievement Badge' }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ formatDate(badge.created_at) }}</span>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <button class="text-sm text-purple-600 hover:text-purple-800 font-medium">View all badges</button>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Deadlines -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md animate-fade-in">
                        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center">
                            <Calendar class="h-5 w-5 mr-2 text-purple-600 dark:text-purple-400" />
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Upcoming Deadlines</h3>
                        </div>
                        <div class="p-6">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">Assignment #1</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Course: {{ courses[0]?.title || 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs font-medium text-red-600 dark:text-red-400">2 days left</span>
                                </li>
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">Group Project</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Group #{{ groups[0]?.group_id || 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">5 days left</span>
                                </li>
                                <li class="py-3 flex justify-between items-center">
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
    background: radial-gradient(circle at top left, rgba(124, 58, 237, 0.15), transparent 50%),
    radial-gradient(circle at bottom right, rgba(124, 58, 237, 0.15), transparent 50%);
    pointer-events: none;
    z-index: 0;
}

.dark .background-rays {
    background: radial-gradient(circle at top left, rgba(167, 139, 250, 0.25), transparent 50%),
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
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out forwards;
}

.course-card:hover, .echo-card:hover, .message-card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.badge-item:hover .badge-icon {
    transform: scale(1.1);
    box-shadow: 0 0 15px rgba(124, 58, 237, 0.3);
}

.badge-icon {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.rewards-count:hover, .badges-count:hover, .groups-count:hover {
    transform: scale(1.1);
    transition: transform 0.3s ease-in-out;
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
