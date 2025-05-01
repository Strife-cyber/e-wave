<script setup lang="ts">
import MessagePanel from '@/components/message/MessagePanel.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { defineEmits, defineProps, ref } from 'vue';

const props = defineProps({
    groupMembers: {
        type: Array,
        required: true,
        default: () => [],
    },
    group: {
        type: Object,
        required: true,
    },
    currentUser: {
        type: Object as () => User,
        required: true,
    },
    success: {
        type: String,
        required: false,
    },
    attachment: {
        type: Object,
        required: false,
    },
});

const emit = defineEmits(['generate-echo-cards']);

// Mobile sidebar state
const showSidebar = ref(false);

const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value;
};

// Format date to relative time
const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
        return 'Today';
    } else if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
        }).format(date);
    }
};

// Format role with capitalization
const formatRole = (role) => {
    return role.charAt(0).toUpperCase() + role.slice(1);
};

// Check if user joined recently (within last 3 days)
const isRecentlyActive = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    return diffDays < 3;
};

// Generate a consistent color based on user ID with lighter colors
const getAvatarColor = (userId) => {
    const colors = [
        '#B39DDB', // Lighter purple
        '#9FA8DA', // Lighter indigo
        '#90CAF9', // Lighter blue
        '#81D4FA', // Lighter light blue
        '#80DEEA', // Lighter cyan
        '#80CBC4', // Lighter teal
        '#A5D6A7', // Lighter green
        '#C5E1A5', // Lighter light green
    ];

    return colors[userId % colors.length];
};

// Function to generate echo cards (emit event to parent)
const generateEchoCards = () => {
    emit('generate-echo-cards');
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Groups', href: `/groups/${props.group.id}` }];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="group-panel-container">
            <!-- Mobile Toggle Button -->
            <button
                @click="toggleSidebar"
                class="fixed bottom-4 right-4 z-50 rounded-full bg-purple-300 p-3 text-white shadow-lg transition-colors hover:bg-purple-400 dark:bg-purple-500 dark:hover:bg-purple-600 md:hidden"
            >
                <svg v-if="showSidebar" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                </svg>
            </button>

            <div class="flex h-full flex-col md:flex-row">
                <!-- Main Content Area -->
                <div class="flex-grow p-4 md:p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Group Conversation</h1>

                        <button
                            @click="generateEchoCards"
                            class="flex items-center gap-2 rounded-lg bg-purple-300 px-4 py-2 text-white transition-colors hover:bg-purple-400 dark:bg-purple-500 dark:hover:bg-purple-600"
                            title="Generate Echo Cards from Recent Conversation"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                            <span>Generate Echo Cards</span>
                        </button>
                    </div>

                    <!-- Content Area for Messages -->
                    <div class="min-h-[600px] rounded-lg border border-gray-200 bg-white p-0 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <!-- Integrated Message Panel -->
                        <MessagePanel :groupId="group.id" :user="currentUser" />
                    </div>
                </div>

                <!-- Side Panel for Group Members -->
                <div
                    :class="[
                        'overflow-hidden border-l border-gray-200 bg-gray-50 transition-all duration-300 dark:border-gray-700 dark:bg-gray-900',
                        showSidebar ? 'w-full opacity-100 md:w-80' : 'w-0 opacity-0 md:w-80 md:opacity-100',
                    ]"
                >
                    <div class="h-full p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Group Members</h2>
                            <span class="rounded-full bg-purple-100 px-2 py-1 text-sm text-gray-700 dark:bg-purple-900/40 dark:text-gray-300">
                                {{ groupMembers.length }}
                            </span>
                        </div>

                        <div class="max-h-[calc(100vh-120px)] space-y-3 overflow-y-auto">
                            <div
                                v-for="member in groupMembers"
                                :key="member.id"
                                class="rounded-lg border border-gray-200 bg-white p-3 transition-colors hover:border-purple-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-purple-700"
                            >
                                <div class="flex items-center gap-3">
                                    <!-- Avatar with first letter of name -->
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full font-medium text-white"
                                        :style="{ backgroundColor: getAvatarColor(member.user.id) }"
                                    >
                                        {{ member.user.name.charAt(0) }}
                                    </div>

                                    <div class="flex-grow">
                                        <div class="font-medium text-gray-800 dark:text-gray-100">
                                            {{ member.user.name }}
                                        </div>
                                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span
                                                :class="[
                                                    'inline-block h-2 w-2 rounded-full',
                                                    isRecentlyActive(member.joined_at) ? 'bg-green-500' : 'bg-gray-400',
                                                ]"
                                            ></span>
                                            <span>{{ formatRole(member.user.role) }}</span>
                                        </div>
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(member.joined_at) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Empty state -->
                            <div v-if="groupMembers.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                No members in this group
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.group-panel-container {
    @apply h-full min-h-screen bg-gray-50 dark:bg-gray-900;
}

/* Custom scrollbar for the members list */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    @apply rounded-full bg-gray-100 dark:bg-gray-800;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    @apply rounded-full bg-purple-200 dark:bg-purple-800;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    @apply bg-purple-300 dark:bg-purple-700;
}

/* Animation for sidebar */
@media (max-width: 768px) {
    .w-0 {
        max-width: 0;
        padding: 0;
        overflow: hidden;
    }

    .w-full {
        max-width: 100%;
        position: fixed;
        top: 0;
        right: 0;
        height: 100vh;
        z-index: 40;
    }
}
</style>
