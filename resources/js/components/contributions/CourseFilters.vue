<script setup lang="ts">
import { Search } from 'lucide-vue-next';

defineProps<{
    filters: { search: string; bright: string; sort: string };
}>();

defineEmits<{
    (e: 'apply'): void;
    (e: 'reset'): void;
}>();
</script>

<template>
    <div class="animate-slideDown rounded-xl border border-purple-100 bg-white p-5 shadow-lg dark:border-purple-900/30 dark:bg-slate-800">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <!-- Search by title -->
            <div>
                <label for="course-search" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <Search class="h-4 w-4 text-slate-400 dark:text-slate-500" />
                    </div>
                    <input
                        id="course-search"
                        v-model="filters.search"
                        type="text"
                        placeholder="Search by title..."
                        class="w-full rounded-lg border border-purple-200 bg-white py-2 pl-10 pr-3 text-slate-900 placeholder-slate-400 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-purple-400"
                    />
                </div>
            </div>

            <!-- Filter by bright status -->
            <div>
                <label for="course-bright" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Highlight Status</label>
                <select
                    id="course-bright"
                    v-model="filters.bright"
                    class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                >
                    <option value="">All</option>
                    <option value="true">Highlighted</option>
                    <option value="false">Not Highlighted</option>
                </select>
            </div>

            <!-- Sort by -->
            <div>
                <label for="course-sort" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Sort By</label>
                <select
                    id="course-sort"
                    v-model="filters.sort"
                    class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="title_asc">Title (A-Z)</option>
                    <option value="title_desc">Title (Z-A)</option>
                </select>
            </div>
        </div>

        <div class="mt-5 flex justify-end space-x-3">
            <button
                @click="$emit('reset')"
                class="rounded-lg border border-purple-200 px-4 py-2 text-slate-700 transition-colors duration-300 hover:bg-slate-50 dark:border-purple-800 dark:text-slate-300 dark:hover:bg-slate-700"
            >
                Reset
            </button>
            <button
                @click="$emit('apply')"
                class="rounded-lg bg-purple-600 px-4 py-2 text-white shadow-md transition-all duration-300 hover:bg-purple-700 hover:shadow-lg"
            >
                Apply Filters
            </button>
        </div>
    </div>
</template>
