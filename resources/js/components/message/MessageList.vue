<script setup lang="ts">
import { Message, User } from '@/types';
import { format, isToday, isYesterday } from 'date-fns';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import ImagePreviewModal from './ImagePreviewModal.vue';
import MessageItem from './MessageItem.vue';

const props = defineProps<{
    messages: Message[];
    loading: boolean;
    isSearching: boolean;
    searchQuery: string;
    typingUsers: User[];
    currentUser: User;
    selectedMessage: Message | null;
}>();

const emit = defineEmits<{
    scroll: [event: Event];
    loadMore: [];
    clearSearch: [];
    addReaction: [messageId: string, emoji: string];
    imagePreview: [message: Message];
    closePreview: [];
}>();

// Local reactive state to ensure updates
const localMessages = ref<Message[]>(props.messages);
const localTypingUsers = ref<User[]>(props.typingUsers);
const messagesContainer = ref<HTMLElement | null>(null);

// Watch props for deep changes
watch(
    () => props.messages,
    async (newMessages) => {
        localMessages.value = newMessages;
        // Scroll to bottom when new messages are added
        await scrollToBottom();
    },
    { deep: true },
);

watch(
    () => props.typingUsers,
    (newUsers) => {
        localTypingUsers.value = newUsers;
    },
    { deep: true },
);

// Methods
const formatMessageDate = (timestamp: string) => {
    const date = new Date(timestamp);

    if (isToday(date)) {
        return 'Today';
    } else if (isYesterday(date)) {
        return 'Yesterday';
    } else {
        return format(date, 'MMMM d, yyyy');
    }
};

const handleScroll = (e: Event) => {
    emit('scroll', e);
};

const loadMoreMessages = () => {
    emit('loadMore');
};

const clearSearch = () => {
    emit('clearSearch');
};

const addReaction = (messageId: string, emoji: string) => {
    emit('addReaction', messageId, emoji);
};

const showImagePreview = (message: Message) => {
    emit('imagePreview', message);
};

const closePreview = () => {
    emit('closePreview');
};

// Scroll to the bottom of the messages container
const scrollToBottom = async () => {
    if (messagesContainer.value) {
        await nextTick(); // Wait for DOM update
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

// Group messages by date reactively
const groupedMessages = computed(() => {
    const groups: Record<string, Message[]> = {};

    localMessages.value.forEach((message) => {
        const date = new Date(message.timestamp);
        const dateKey = format(date, 'yyyy-MM-dd');

        if (!groups[dateKey]) {
            groups[dateKey] = [];
        }
        groups[dateKey].push(message);
    });

    return groups;
});

// Scroll to bottom on initial mount
onMounted(async () => {
    await scrollToBottom();
});
</script>

<template>
    <div ref="messagesContainer" @scroll="handleScroll" class="messages-container flex-grow space-y-4 overflow-y-auto p-4">
        <!-- Loading indicator -->
        <div v-if="loading" class="flex h-20 items-center justify-center">
            <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-purple-300 dark:border-purple-500"></div>
        </div>

        <!-- Load more button -->
        <div v-if="!loading && localMessages.length > 0" class="flex justify-center">
            <button
                @click="loadMoreMessages"
                class="rounded-full px-4 py-2 text-sm text-purple-400 hover:text-purple-500 dark:text-purple-500 dark:hover:text-purple-400"
            >
                Load earlier messages
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="!loading && localMessages.length === 0" class="flex h-full flex-col items-center justify-center text-gray-400 dark:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                />
            </svg>
            <p>No messages yet. Start the conversation!</p>
        </div>

        <!-- Search results indicator -->
        <div
            v-if="isSearching"
            class="sticky top-0 z-10 flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2 text-sm text-gray-600 dark:bg-gray-900 dark:text-gray-400"
        >
            <span>{{ localMessages.length }} search results for "{{ searchQuery }}"</span>
            <button @click="clearSearch" class="text-purple-400 hover:text-purple-500 dark:text-purple-500 dark:hover:text-purple-400">
                Clear search
            </button>
        </div>

        <!-- Messages grouped by date -->
        <template v-for="(dateGroup, date) in groupedMessages" :key="date">
            <!-- Date separator -->
            <div class="date-separator my-4 flex items-center justify-center">
                <div class="h-px flex-grow bg-gray-200 dark:bg-gray-700"></div>
                <span class="px-4 text-xs text-gray-500 dark:text-gray-400">{{ formatMessageDate(dateGroup[0].timestamp) }}</span>
                <div class="h-px flex-grow bg-gray-200 dark:bg-gray-700"></div>
            </div>

            <!-- Messages for this date -->
            <MessageItem
                v-for="message in dateGroup"
                :key="message.id"
                :message="message"
                :current-user="currentUser"
                @add-reaction="addReaction"
                @image-preview="showImagePreview"
            />
        </template>

        <!-- Typing indicator -->
        <div v-if="localTypingUsers.length > 0" class="px-4 py-2 text-sm italic text-gray-500 dark:text-gray-400">
            <span v-if="localTypingUsers.length === 1"> {{ localTypingUsers[0].name }} is typing... </span>
            <span v-else-if="localTypingUsers.length === 2"> {{ localTypingUsers[0].name }} and {{ localTypingUsers[1].name }} are typing... </span>
            <span v-else-if="localTypingUsers.length > 2"> {{ localTypingUsers.length }} people are typing... </span>
        </div>

        <!-- Image preview modal -->
        <ImagePreviewModal v-if="selectedMessage" :message="selectedMessage" @close="closePreview" />
    </div>
</template>

<style scoped>
/* Custom scrollbar for the messages container */
.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    @apply rounded-full bg-gray-100 dark:bg-gray-800;
}

.messages-container::-webkit-scrollbar-thumb {
    @apply rounded-full bg-purple-200 dark:bg-purple-800;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    @apply bg-purple-300 dark:bg-purple-700;
}
</style>
