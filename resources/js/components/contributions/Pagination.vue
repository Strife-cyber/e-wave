<script setup lang="ts">

import { computed } from 'vue';

const props = defineProps<{
    currentPage: {
        type: number,
        required: true
    },
    lastPage: {
        type: number,
        required: true
    }
}>();

const emit = defineEmits(['page-change'])

const pages = computed(() => {
    const range = [];
    const maxVisiblePages = 5;

    if (props.lastPage <= maxVisiblePages) {
        // Show all pages if there are few pages
        for (let i = 1; i <= props.lastPage; i++) {
            range.push(i);
        }
    } else {
        // Always include first page
        range.push(1);

        // Calculate start and end of visible pages
        let start = Math.max(2, props.currentPage - 1);
        let end = Math.min(props.lastPage - 1, props.currentPage + 1);

        // Adjust if at the beginning
        if (props.currentPage <= 3) {
            end = 4;
        }

        // Adjust if at the end
        if (props.currentPage >= props.lastPage - 2) {
            start = props.lastPage - 3;
        }

        // Add ellipsis if needed
        if (start > 2) {
            range.push('...');
        }

        // Add middle pages
        for (let i = start; i <= end; i++) {
            range.push(i);
        }

        // Add ellipsis if needed
        if (end < props.lastPage - 1) {
            range.push('...');
        }

        // Always include last page
        range.push(props.lastPage);
    }

    return range;
});

const goToPage = (page) => {
    if (typeof page === 'number' && page !== props.currentPage) {
        emit('page-change', page);
    }
};

</script>

<template>
    <div class="flex justify-center mt-4">
        <nav class="inline-flex rounded-lg shadow-sm overflow-hidden" aria-label="Pagination">
            <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage"
                class="relative inline-flex items-center px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-300"
            >
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>

            <button
                v-for="(page, index) in pages"
                :key="index"
                @click="goToPage(page)"
                :disabled="page === '...'"
                :class="[
            'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-300',
            page === currentPage
              ? 'z-10 bg-teal-600 dark:bg-teal-700 border-teal-600 dark:border-teal-700 text-white'
              : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700',
            page === '...' ? 'cursor-default' : ''
          ]"
            >
                {{ page }}
            </button>

            <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === lastPage"
                class="relative inline-flex items-center px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-300"
            >
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </nav>
    </div>
</template>

<style scoped>

</style>
