<script setup lang="ts">
import createMessagingService from '@/services/messaging-service';
import { User, Message } from '@/types';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import MessageHeader from './MessageHeader.vue';
import MessageInput from './MessageInput.vue';
import MessageList from './MessageList.vue';
import ScrollToBottomButton from './ScrollToBottomButton.vue';

const props = defineProps<{
    groupId: number;
    user: User;
}>();

// State
const messages = ref<Message[]>([]);
const typingUsers = ref<User[]>([]);
const searchQuery = ref('');
const isSearching = ref(false);
const searchResults = ref<Message[]>([]);
const selectedMessage = ref<Message | null>(null);
const messagesContainer = ref<HTMLElement | null>(null);
const showScrollToBottom = ref(false);
const isAtBottom = ref(true);
const loadingMessages = ref(true);

// Service initialization
const messagingService = createMessagingService(props.groupId);
let unsubscribe: (() => void) | null = null;
let typingTimeout: number | null = null;

// Computed properties
const filteredMessages = computed(() => {
    if (isSearching.value && searchQuery.value) {
        return searchResults.value;
    }
    return messages.value;
});

const hasUnreadMessages = computed(() => {
    return messages.value.some((message) => message.user !== props.user && message.status !== 'read');
});

// Methods
const sendMessage = async (text: string, attachments: File[]) => {
    try {
        // Create a temporary message with 'sending' status
        const message: Message = {
            id: `temp-${Date.now()}`,
            text: text.trim(),
            user: props.user,
            timestamp: new Date().toISOString(),
            status: 'sending',
            attachments:
                attachments.length > 0
                    ? attachments.map((file) => ({
                          type: file.type,
                          url: URL.createObjectURL(file),
                          name: file.name,
                          size: file.size,
                      }))
                    : undefined,
        };

        console.log(message)

        messages.value.push(message);
        await scrollToBottom();

        // Send the actual message
        await messagingService.sendMessage(message);
    } catch (e) {
        console.error('Error sending message: ', e);
    }
};

const sendTypingIndicator = () => {
    if (typingTimeout) {
        clearTimeout(typingTimeout);
    }

    messagingService.sendTypingIndicator(props.user);

    typingTimeout = window.setTimeout(() => {
        messagingService.clearTypingIndicator(props.user);
        typingTimeout = null;
    }, 3000);
};

const searchMessages = async (query: string) => {
    searchQuery.value = query;

    if (!query.trim()) {
        isSearching.value = false;
        searchResults.value = [];
        return;
    }

    isSearching.value = true;
    try {
        // This would typically be a server-side search
        searchResults.value = messages.value.filter((message) => message.text.toLowerCase().includes(query.toLowerCase()));
    } catch (e) {
        console.error('Error searching messages: ', e);
    }
};

const clearSearch = () => {
    searchQuery.value = '';
    isSearching.value = false;
    searchResults.value = [];
};

const addReaction = async (messageId: string, emoji: string) => {
    try {
        await messagingService.addReaction(messageId, emoji, props.user);
    } catch (e) {
        console.error('Error adding reaction: ', e);
    }
};

const scrollToBottom = async (force = false) => {
    if (messagesContainer.value && (isAtBottom.value || force)) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const handleScroll = (event: Event) => {
    const container = event.target as HTMLElement;
    if (!container) return;

    const { scrollTop, scrollHeight, clientHeight } = container;
    const scrollPosition = scrollTop + clientHeight;

    // Consider "at bottom" if within 100px of the bottom
    isAtBottom.value = scrollHeight - scrollPosition < 100;
    showScrollToBottom.value = !isAtBottom.value && hasUnreadMessages.value;
};

const loadMoreMessages = async () => {
    if (messages.value.length === 0) return;

    try {
        const oldestMessage = messages.value[0];
        const olderMessages = await messagingService.getMessagesBefore(oldestMessage.id);

        if (olderMessages.length > 0) {
            messages.value = [...olderMessages, ...messages.value];
        }
    } catch (e) {
        console.error('Error loading more messages: ', e);
    }
};

const handleImagePreview = (message: Message) => {
    selectedMessage.value = message;
};

// Lifecycle hooks
onMounted(async () => {
    try {
        loadingMessages.value = true;
        messages.value = await messagingService.getAllMessages();
        loadingMessages.value = false;

        // Set up listeners
        unsubscribe = messagingService.listenToMessages((message) => {
            // Check if message already exists (to avoid duplicates)
            const existingIndex = messages.value.findIndex((m) => m.id === message.id);

            if (existingIndex >= 0) {
                // Update existing message (e.g., status update)
                messages.value[existingIndex] = message;
            } else {
                messages.value.push(message);
                if (isAtBottom.value) {
                    scrollToBottom();
                } else {
                    showScrollToBottom.value = true;
                }
            }
        });

        // Listen for typing indicators
        messagingService.listenToTypingIndicators((users) => {
            typingUsers.value = users.filter((user) => user.id !== props.user.id);
        });

        // Mark messages as read
        await messagingService.markMessagesAsRead();

        // Initial scroll to bottom
        await scrollToBottom(true);
    } catch (e) {
        console.error('Error loading messages: ', e);
        loadingMessages.value = false;
    }
});

onUnmounted(() => {
    if (unsubscribe) {
        unsubscribe();
    }
    if (typingTimeout) {
        clearTimeout(typingTimeout);
    }
});

// Watch for new messages to scroll to bottom
watch(
    () => messages.value.length,
    () => {
        if (isAtBottom.value) {
            scrollToBottom();
        }
    },
);
</script>

<template>
    <div class="message-panel flex h-full flex-col">
        <!-- Header with search -->
        <MessageHeader :has-unread-messages="hasUnreadMessages" :search-query="searchQuery" @search="searchMessages" @clear-search="clearSearch" />

        <!-- Messages area -->
        <MessageList
            class="h-[60vh]"
            ref="messagesContainer"
            @scroll="handleScroll"
            :messages="filteredMessages"
            :loading="loadingMessages"
            :is-searching="isSearching"
            :search-query="searchQuery"
            :typing-users="typingUsers"
            :current-user="props.user"
            :selected-message="selectedMessage"
            @load-more="loadMoreMessages"
            @clear-search="clearSearch"
            @add-reaction="addReaction"
            @image-preview="handleImagePreview"
            @close-preview="selectedMessage = null"
        />

        <!-- Message input area -->
        <MessageInput @send="sendMessage" @typing="sendTypingIndicator" />

        <!-- Scroll to bottom button -->
        <ScrollToBottomButton v-if="showScrollToBottom" @click="scrollToBottom(true)" />
    </div>
</template>
