<script setup lang="ts">
defineProps<{
    hasUnreadMessages: boolean;
    searchQuery: string;
}>();

const emit = defineEmits<{
    search: [query: string];
    clearSearch: [];
}>();

const handleSearch = (e: Event) => {
    const target = e.target as HTMLInputElement;
    emit('search', target.value);
};

const clearSearch = () => {
    emit('clearSearch');
};
</script>

<template>
    <div class="message-header flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
        <div class="flex items-center">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Group Messages</h2>
            <span v-if="hasUnreadMessages" class="ml-2 h-2 w-2 rounded-full bg-purple-400 dark:bg-purple-500"></span>
        </div>

        <div class="flex items-center">
            <div class="relative">
                <input
                    :value="searchQuery"
                    @input="handleSearch"
                    type="text"
                    placeholder="Search messages..."
                    class="rounded-full border border-gray-200 bg-white py-1 pl-8 pr-4 text-sm text-gray-800 focus:outline-none focus:ring-1 focus:ring-purple-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:ring-purple-500"
                />
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400 dark:text-gray-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button
                    v-if="searchQuery"
                    @click="clearSearch"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 transform text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
