<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    groups: {
        type: Array,
        required: true,
        default: () => [],
    },
});

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(9);

// Search
const searchTerm = ref('');

// Sorting
const sortOption = ref('name-asc');

// Reset to first page when search term changes
watch(searchTerm, () => {
    currentPage.value = 1;
});

// Reset to first page when sort option changes
watch(sortOption, () => {
    currentPage.value = 1;
});

const filteredGroups = computed(() => {
    if (!searchTerm.value) return [...props.groups];

    const term = searchTerm.value.toLowerCase();
    return props.groups.filter((group) => group.name.toLowerCase().includes(term));
});

const sortedGroups = computed(() => {
    const groups = [...filteredGroups.value];

    switch (sortOption.value) {
        case 'name-asc':
            return groups.sort((a, b) => a.name.localeCompare(b.name));
        case 'name-desc':
            return groups.sort((a, b) => b.name.localeCompare(a.name));
        case 'date-newest':
            return groups.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        case 'date-oldest':
            return groups.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        default:
            return groups;
    }
});

const totalPages = computed(() => {
    return Math.ceil(sortedGroups.value.length / itemsPerPage.value);
});

const paginatedGroups = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return sortedGroups.value.slice(startIndex, endIndex);
});

const displayedPages = computed(() => {
    const pages = [];
    const maxPagesToShow = 5;

    if (totalPages.value <= maxPagesToShow) {
        // Show all pages if there are fewer than maxPagesToShow
        for (let i = 1; i <= totalPages.value; i++) {
            pages.push(i);
        }
    } else {
        // Always include first page
        pages.push(1);

        // Calculate start and end of page range
        let startPage = Math.max(2, currentPage.value - 1);
        let endPage = Math.min(totalPages.value - 1, currentPage.value + 1);

        // Adjust if we're at the beginning
        if (currentPage.value <= 2) {
            endPage = 4;
        }

        // Adjust if we're at the end
        if (currentPage.value >= totalPages.value - 1) {
            startPage = totalPages.value - 3;
        }

        // Add ellipsis if needed
        if (startPage > 2) {
            pages.push('...');
        }

        // Add middle pages
        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }

        // Add ellipsis if needed
        if (endPage < totalPages.value - 1) {
            pages.push('...');
        }

        // Always include last page
        pages.push(totalPages.value);
    }

    return pages;
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(date);
};

const breadcrumbs: Breadcrumbs[] = [{ title: 'Groups', href: '/groups' }];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-8">
            <h2 class="mb-6 text-2xl font-bold text-purple-800 dark:text-purple-300">Your Groups</h2>

            <!-- Search and Sort Controls -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row">
                <div class="relative flex-grow">
                    <input
                        v-model="searchTerm"
                        type="text"
                        placeholder="Search groups..."
                        class="w-full rounded-lg border border-purple-200 bg-white py-2 pl-10 pr-4 text-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-purple-900/40 dark:text-purple-100"
                    />
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 transform text-purple-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <div class="flex-shrink-0">
                    <select
                        v-model="sortOption"
                        class="rounded-lg border border-purple-200 bg-white px-4 py-2 text-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-purple-900/40 dark:text-purple-100"
                    >
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="date-newest">Newest First</option>
                        <option value="date-oldest">Oldest First</option>
                    </select>
                </div>
            </div>

            <div
                v-if="paginatedGroups.length === 0"
                class="rounded-lg border border-purple-200 bg-purple-50 p-6 text-center dark:border-purple-800 dark:bg-purple-900/20"
            >
                <p class="text-purple-700 dark:text-purple-300">
                    {{ filteredGroups.length === 0 ? 'No groups match your search.' : "You don't have any groups yet." }}
                </p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="group in paginatedGroups"
                    :key="group.id"
                    :class="[
                        'group-card rounded-lg border p-4 transition-all duration-200 hover:shadow-md',
                        group.bright
                            ? 'border-purple-300 bg-purple-100 hover:bg-purple-200 dark:border-purple-600 dark:bg-purple-800 dark:hover:bg-purple-700'
                            : 'border-purple-200 bg-white hover:bg-purple-50 dark:border-purple-800 dark:bg-purple-900/40 dark:hover:bg-purple-900/60',
                    ]"
                >
                    <div class="flex items-start justify-between">
                        <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100">{{ group.name }}</h3>
                        <span
                            v-if="group.bright"
                            class="rounded-full bg-purple-500 px-2 py-0.5 text-xs text-white dark:bg-purple-300 dark:text-purple-900"
                        >
                            Featured
                        </span>
                    </div>

                    <div class="mt-3 text-sm text-purple-700 dark:text-purple-300">
                        <p>Course ID: {{ group.course_id }}</p>
                        <p class="mt-2 text-xs text-purple-600 dark:text-purple-400">Created: {{ formatDate(group.created_at) }}</p>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <a
                            :href="`/groups/${group.id}`"
                            class="cursor-pointer rounded-md bg-purple-600 px-3 py-1 text-sm text-white transition-colors hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-400"
                        >
                            View Group
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pagination Controls -->
            <div v-if="totalPages > 1" class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-1">
                    <button
                        @click="currentPage = 1"
                        :disabled="currentPage === 1"
                        :class="[
                            'rounded-md px-3 py-1',
                            currentPage === 1
                                ? 'cursor-not-allowed text-purple-400'
                                : 'text-purple-700 hover:bg-purple-100 dark:text-purple-300 dark:hover:bg-purple-800',
                        ]"
                    >
                        First
                    </button>

                    <button
                        @click="currentPage--"
                        :disabled="currentPage === 1"
                        :class="[
                            'rounded-md px-3 py-1',
                            currentPage === 1
                                ? 'cursor-not-allowed text-purple-400'
                                : 'text-purple-700 hover:bg-purple-100 dark:text-purple-300 dark:hover:bg-purple-800',
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div v-for="page in displayedPages" :key="page" class="flex items-center">
                        <button
                            @click="currentPage = page"
                            :class="[
                                'rounded-md px-3 py-1',
                                currentPage === page
                                    ? 'bg-purple-600 text-white dark:bg-purple-500'
                                    : 'text-purple-700 hover:bg-purple-100 dark:text-purple-300 dark:hover:bg-purple-800',
                            ]"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <button
                        @click="currentPage++"
                        :disabled="currentPage === totalPages"
                        :class="[
                            'rounded-md px-3 py-1',
                            currentPage === totalPages
                                ? 'cursor-not-allowed text-purple-400'
                                : 'text-purple-700 hover:bg-purple-100 dark:text-purple-300 dark:hover:bg-purple-800',
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button
                        @click="currentPage = totalPages"
                        :disabled="currentPage === totalPages"
                        :class="[
                            'rounded-md px-3 py-1',
                            currentPage === totalPages
                                ? 'cursor-not-allowed text-purple-400'
                                : 'text-purple-700 hover:bg-purple-100 dark:text-purple-300 dark:hover:bg-purple-800',
                        ]"
                    >
                        Last
                    </button>
                </nav>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Optional: Add a subtle animation on hover */
.group-card {
    @apply relative overflow-hidden;
}

.group-card::before {
    @apply absolute inset-0 bg-gradient-to-r from-purple-500/0 to-purple-500/0 opacity-0 transition-opacity duration-300;
}

.group-card:hover::before {
    @apply opacity-10;
}
</style>
