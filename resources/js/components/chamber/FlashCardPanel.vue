<template>
    <div class="md:col-span-1">
        <div class="rounded-lg bg-purple-100 p-4 shadow-md dark:bg-purple-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-purple-800 dark:text-purple-300">Flashcards</h2>
                <button
                    v-if="flashcards.length > 0"
                    @click="$emit('export-flashcards')"
                    class="rounded bg-purple-200 px-2 py-1 text-sm text-purple-800 hover:bg-purple-300 focus:outline-none dark:bg-purple-800 dark:text-purple-200 dark:hover:bg-purple-700"
                >
                    Export
                </button>
            </div>

            <div v-if="flashcards.length === 0" class="py-8 text-center text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
                <p>Flashcards will appear here after your conversation</p>
            </div>
            <div v-else>
                <div
                    v-for="(card, index) in flashcards"
                    :key="index"
                    class="mb-4 cursor-pointer rounded-lg bg-white p-3 shadow dark:bg-gray-800"
                    @click="$emit('flip-card', index)"
                >
                    <div v-if="card.flipped" class="text-purple-700 dark:text-purple-300">
                        <div class="mb-1 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Answer</div>
                        {{ card.answer }}
                    </div>
                    <div v-else class="font-medium">
                        <div class="mb-1 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Question</div>
                        {{ card.question }}
                    </div>
                </div>
                <button
                    @click="$emit('clear-flashcards')"
                    class="mt-4 w-full rounded-lg bg-purple-200 px-4 py-2 text-purple-800 hover:bg-purple-300 focus:outline-none dark:bg-purple-800 dark:text-purple-200 dark:hover:bg-purple-700"
                >
                    Clear Flashcards
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineEmits, defineProps } from 'vue';

defineProps({
    flashcards: {
        type: Array,
        required: true,
    },
    isDarkMode: {
        type: Boolean,
        required: true,
    },
});

defineEmits(['flip-card', 'clear-flashcards', 'export-flashcards']);
</script>

<style scoped>
/* Card flip animation */
@keyframes cardFlip {
    0% {
        transform: rotateY(0deg);
    }
    50% {
        transform: rotateY(90deg);
    }
    100% {
        transform: rotateY(0deg);
    }
}

.mb-4:active {
    animation: cardFlip 0.3s ease-out;
}
</style>
