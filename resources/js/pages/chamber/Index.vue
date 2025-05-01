<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Echo Chamber" />
        <div class="min-h-screen w-full transition-colors duration-300" :class="isDarkMode ? 'bg-gray-900 text-white' : 'bg-white text-gray-900'">
            <div class="container dark:bg-gray-900 mx-auto p-8">
                <!-- Header -->
                <header class="mb-8 flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-purple-600 dark:text-purple-400">Academic Echo Chamber</h1>
                </header>

                <!-- Main content -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Chat section -->
                    <ChatInterface :messages="messages" :isLoading="isLoading" :isDarkMode="isDarkMode" @send-message="handleSendMessage" />

                    <!-- Flashcards section -->
                    <FlashCardPanel
                        :flashcards="flashcards"
                        :isDarkMode="isDarkMode"
                        @flip-card="flipCard"
                        @clear-flashcards="clearFlashcards"
                        @export-flashcards="exportFlashcards"
                    />
                </div>

                <!-- Export session and reset buttons -->
                <div class="mt-8 flex justify-center space-x-4">
                    <button
                        @click="exportSession"
                        class="rounded-lg bg-purple-600 px-6 py-2 text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                    >
                        Export Session Summary
                    </button>
                    <button
                        @click="resetSession"
                        class="rounded-lg bg-red-600 px-6 py-2 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Reset Session
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import ChatInterface from '@/components/chamber/ChatInterface.vue';
import FlashCardPanel from '@/components/chamber/FlashCardPanel.vue';
import { useThemeMode } from '@/composables/useThemeMode';
import AppLayout from '@/layouts/AppLayout.vue';
import { sendMessageToOpenRouter } from '@/services/open-router-service';
import type { BreadcrumbItem } from '@/types';
import { generateFlashcardFromConversation } from '@/utils/flash-card-utils';
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';

// Theme management
const { isDarkMode } = useThemeMode();

// Interfaces
interface Message {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    timestamp: string;
}

interface Flashcard {
    question: string;
    answer: string;
    flipped: boolean;
}

// State
const messages = ref<Message[]>([]);
const flashcards = ref<Flashcard[]>([]);
const isLoading = ref(false);

// LocalStorage keys
const STORAGE_KEYS = {
    MESSAGES: 'echo_chamber_messages',
    FLASHCARDS: 'echo_chamber_flashcards',
};

// Load data from LocalStorage on mount
onMounted(() => {
    const storedMessages = localStorage.getItem(STORAGE_KEYS.MESSAGES);
    const storedFlashcards = localStorage.getItem(STORAGE_KEYS.FLASHCARDS);

    if (storedMessages) {
        messages.value = JSON.parse(storedMessages);
    }

    if (storedFlashcards) {
        flashcards.value = JSON.parse(storedFlashcards);
    }

    // Initial MathJax rendering
    if (window.MathJax) {
        window.MathJax.typesetPromise?.().catch((err: unknown) => console.error('MathJax typesetting error:', err));
    }
});

// Save data to LocalStorage
watch(
    messages,
    (newMessages) => {
        localStorage.setItem(STORAGE_KEYS.MESSAGES, JSON.stringify(newMessages));
    },
    { deep: true }
);

watch(
    flashcards,
    (newFlashcards) => {
        localStorage.setItem(STORAGE_KEYS.FLASHCARDS, JSON.stringify(newFlashcards));
    },
    { deep: true }
);

// Normalize LaTeX delimiters
const normalizeLaTeX = (content: string) => {
    // Convert $...$ to \(...\), $$...$$ to \[...\]
    let normalized = content
        .replace(/\$([^$]+)\$/g, '\\($1\\)')
        .replace(/\$\$(.+?)\$\$/g, '\\[$1\\]');

    // Wrap raw LaTeX equations (e.g., R_{\mu\nu}...) in \[...\] if not already wrapped
    if (normalized.match(/=|{|}|_|\\frac|\\sqrt|\\mu|\\nu|\\Lambda/) && !normalized.match(/\\\[|\\\(|\\]|\$/)) {
        normalized = `\\[${normalized}\\]`;
    }

    return normalized;
};

// Handle sending messages
const handleSendMessage = async (userMessage: string) => {
    if (!userMessage.trim() || isLoading.value) return;

    const userMsg: Message = {
        id: crypto.randomUUID(),
        role: 'user',
        content: userMessage,
        timestamp: new Date().toISOString(),
    };
    messages.value.push(userMsg);

    isLoading.value = true;

    try {
        const assistantMessage = await sendMessageToOpenRouter(messages.value.map((msg) => ({ role: msg.role, content: msg.content })));
        messages.value.push({
            id: crypto.randomUUID(),
            role: 'assistant',
            content: normalizeLaTeX(assistantMessage),
            timestamp: new Date().toISOString(),
        });

        const newFlashcard = generateFlashcardFromConversation(userMessage, assistantMessage);
        flashcards.value.push({
            ...newFlashcard,
            flipped: false,
        });
    } catch (error) {
        console.error('Error sending message:', error);
        messages.value.push({
            id: crypto.randomUUID(),
            role: 'assistant',
            content: 'Sorry, I encountered an error processing your request. Please try again.',
            timestamp: new Date().toISOString(),
        });
    } finally {
        isLoading.value = false;
    }
};

// Flip flashcard
const flipCard = (index: number) => {
    flashcards.value[index].flipped = !flashcards.value[index].flipped;
};

// Clear flashcards
const clearFlashcards = () => {
    flashcards.value = [];
    localStorage.removeItem(STORAGE_KEYS.FLASHCARDS);
};

// Reset session
const resetSession = () => {
    messages.value = [];
    flashcards.value = [];
    localStorage.removeItem(STORAGE_KEYS.MESSAGES);
    localStorage.removeItem(STORAGE_KEYS.FLASHCARDS);
};

// Export flashcards
const exportFlashcards = () => {
    if (flashcards.value.length === 0) return;

    const flashcardsData = JSON.stringify(flashcards.value, null, 2);
    const blob = new Blob([flashcardsData], { type: 'application/json' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = `flashcards-${new Date().toISOString().slice(0, 10)}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};

// Export session
const exportSession = () => {
    if (messages.value.length === 0) return;

    const sessionData = {
        timestamp: new Date().toISOString(),
        conversation: messages.value,
        flashcards: flashcards.value.map((card) => ({
            question: card.question,
            answer: card.answer,
        })),
    };

    const jsonData = JSON.stringify(sessionData, null, 2);
    const blob = new Blob([jsonData], { type: 'application/json' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = `echo-chamber-session-${new Date().toISOString().slice(0, 10)}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Chamber', href: '/echo' }];
</script>

<style scoped>
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
