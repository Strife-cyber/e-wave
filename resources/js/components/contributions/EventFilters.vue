<script setup lang="ts">
defineProps<{
    filters: { status: string; collaborationId: string; dateFrom: string; dateTo: string };
}>();

defineEmits<{
    (e: 'apply'): void;
    (e: 'reset'): void;
}>();
</script>

<template>
    <div class="animate-slideDown bg-purple-50 p-4 dark:bg-purple-900/20">
        <div class="space-y-3">
            <!-- Filter by status -->
            <div>
                <label for="event-status" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                <select
                    id="event-status"
                    v-model="filters.status"
                    class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                >
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Filter by collaboration ID -->
            <div>
                <label for="event-collaboration" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Collaboration ID</label>
                <input
                    id="event-collaboration"
                    v-model="filters.collaborationId"
                    type="number"
                    min="1"
                    placeholder="Enter ID..."
                    class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-purple-400"
                />
            </div>

            <!-- Date range -->
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Date Range</label>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="event-date-from" class="block text-xs text-slate-500 dark:text-slate-400">From</label>
                        <input
                            id="event-date-from"
                            v-model="filters.dateFrom"
                            type="date"
                            class="w-full rounded-lg border border-purple-200 bg-white px-3 py-1 text-sm text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                        />
                    </div>
                    <div>
                        <label for="event-date-to" class="block text-xs text-slate-500 dark:text-slate-400">To</label>
                        <input
                            id="event-date-to"
                            v-model="filters.dateTo"
                            type="date"
                            class="w-full rounded-lg border border-purple-200 bg-white px-3 py-1 text-sm text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex justify-end space-x-2">
            <button
                @click="$emit('reset')"
                class="rounded-lg border border-purple-200 px-3 py-1 text-sm text-slate-700 transition-colors duration-300 hover:bg-slate-50 dark:border-purple-800 dark:text-slate-300 dark:hover:bg-slate-700"
            >
                Reset
            </button>
            <button
                @click="$emit('apply')"
                class="rounded-lg bg-purple-600 px-3 py-1 text-sm text-white shadow-md transition-all duration-300 hover:bg-purple-700 hover:shadow-lg"
            >
                Apply
            </button>
        </div>
    </div>
</template>
