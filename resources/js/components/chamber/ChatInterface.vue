<template>
    <div class="flex h-screen flex-col" :class="{ 'dark': isDarkMode }">
        <!-- Chat Header -->
        <div class="bg-purple-100 p-4 dark:bg-gray-800">
            <h1 class="text-lg dark:text-white font-semibold">Echo Chamber</h1>
        </div>

        <!-- Chat Messages -->
        <div ref="chatContainer" class="flex-grow container-fluid overflow-y-auto p-4">
            <div class="space-y-4">
                <div v-for="message in messages" :key="message.id" class="flex flex-col">
                    <div
                        class="max-w-[80%] rounded-lg p-3 break-words"
                        :class="[
                            message.role === 'user' ? 'ml-auto bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white',
                            message.role === 'user' ? 'rounded-br-none' : 'rounded-bl-none',
                        ]"
                    >
                        <!-- Render message content with KaTeX -->
                        <div class="mathjax-content" v-html="renderMessageContent(message.content)"></div>
                    </div>
                    <div
                        :class="message.role === 'user' ? 'self-end' : 'self-start'"
                        class="text-xs text-gray-500 dark:text-gray-400"
                    >
                        {{ message.timestamp }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="bg-gray-100 p-4 dark:bg-gray-800">
            <div class="flex space-x-2">
                <textarea
                    v-model="userInput"
                    @keydown.enter.prevent="sendMessage"
                    placeholder="Type your message..."
                    class="flex-grow rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-700 dark:text-white"
                />
                <button
                    @click="sendMessage"
                    :disabled="isLoading"
                    class="rounded-md min-w-[120px] bg-purple-600 p-2 text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:bg-gray-400"
                >
                    {{ isLoading ? 'Sending...' : 'Send' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import katex from 'katex';
import DOMPurify from 'dompurify';

// Interfaces
interface Message {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    timestamp: string;
}

const props = defineProps<{
    messages: Message[];
    isLoading: boolean;
    isDarkMode: boolean;
}>();

const emit = defineEmits<{
    (e: 'send-message', message: string): void;
}>();

const userInput = ref('');
const chatContainer = ref<HTMLElement | null>(null);

// Detect LaTeX content
const hasLaTeX = (content: string) => /\\\[|\\\(|\\]|\$|{|}|_|\\frac|\\sqrt|\\mu|\\nu|\\Lambda|\\pi|\\sum/g.test(content);

// Render LaTeX with KaTeX
const renderMessageContent = (content: string) => {
    if (!hasLaTeX(content)) {
        return DOMPurify.sanitize(content);
    }

    // Handle escaped backslashes (e.g., \\mu -> \mu)
    const normalized = content.replace(/\\{2}([^\s])/g, '\\$1');

    // Split content into lines
    const lines = normalized.split('\n');
    const renderedLines = lines.map((line) => {
        // Handle display math (e.g., \[...\], $$...$$, or raw equations)
        if (line.match(/^\s*\\\[.*?\\\]\s*$/) || line.match(/^\s*\$\$.*?\$$\s*$/) || line.match(/=|{|}|_|\\frac|\\sqrt|\\mu|\\nu|\\Lambda/)) {
            const math = line.replace(/^\s*\\\[|\]\s*$|^\s*\$\$|\$$\s*$/g, '').trim();
            if (math) {
                try {
                    return katex.renderToString(math, { displayMode: true, throwOnError: false });
                } catch (e) {
                    console.error('KaTeX rendering error:', e);
                    return DOMPurify.sanitize(line);
                }
            }
        }
        // Handle inline math (e.g., \(...\), $...$)
        return line.replace(/\\\(.*?\\\)|\$[^\$]+\$/g, (match) => {
            const math = match.replace(/^\\\(|\)\s*$|^\$|\$/g, '');
            try {
                return katex.renderToString(math, { displayMode: false, throwOnError: false });
            } catch (e) {
                console.error('KaTeX rendering error:', e);
                return DOMPurify.sanitize(match);
            }
        });
    });

    const renderedContent = renderedLines.join('<br>');
    return DOMPurify.sanitize(renderedContent, {
        ALLOWED_TAGS: ['p', 'br', 'span', 'div', 'b', 'i', 'u', 'sup', 'sub', 'katex', 'annotation', 'semantics'],
        ALLOWED_ATTR: ['class', 'style', 'display'],
    });
};

// Send message
const sendMessage = (event?: KeyboardEvent) => {
    if (!userInput.value.trim() || props.isLoading) return;
    if (event && event.shiftKey) return; // Allow newlines with Shift+Enter
    emit('send-message', userInput.value.trim());
    userInput.value = '';
};

// Auto-scroll
watch(
    () => props.messages,
    () => {
        nextTick(() => {
            if (chatContainer.value) {
                chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
            }
        });
    },
    { deep: true }
);
</script>

<style scoped>
.break-words {
    overflow-wrap: break-word;
}
.mathjax-content {
    line-height: 1.6;
}
</style>
