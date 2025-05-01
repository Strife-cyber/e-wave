<script setup lang="ts">
import createMessagingService from '@/services/messaging-service';
import { Message, User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import MessageHeader from './MessageHeader.vue';
import MessageInput from './MessageInput.vue';
import MessageList from './MessageList.vue';
import ScrollToBottomButton from './ScrollToBottomButton.vue';

const props = defineProps<{
    groupId: number; // Corrected to string to match MessagingService
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
const errorMessage = ref<string | null>(null);

// Inertia page for flash messages
const page = usePage();

// Service initialization
const messagingService = createMessagingService(props.groupId);
let unsubscribeMessages: (() => void) | null = null;
let unsubscribeTyping: (() => void) | null = null;
let typingTimeout: number | null = null;

// Computed properties
const filteredMessages = computed(() => {
    if (isSearching.value && searchQuery.value) {
        return searchResults.value;
    }
    return messages.value;
});

const hasUnreadMessages = computed(() => {
    return messages.value.some((message) => message.user.id !== props.user.id && message.status !== 'read');
});

// Methods
const sendMessage = async (text: string, attachments: File[]) => {
    errorMessage.value = null;
    if (!text.trim() && attachments.length === 0) {
        errorMessage.value = 'Please provide a message or attachment';
        return;
    }

    try {
        // Create a temporary message with 'sending' status
        const tempId = `temp-${Date.now()}`;
        const message: Message = {
            id: tempId,
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

        // Use spread to trigger reactivity
        messages.value = [...messages.value, message];
        await scrollToBottom();

        // Send the actual message
        await messagingService.sendMessage(message, attachments);

        // Remove temporary message
        messages.value = messages.value.filter((m) => m.id !== tempId);
    } catch (error) {
        const errorMsg = error instanceof Error ? error.message : String(error);
        errorMessage.value = `Failed to send message: ${errorMsg}`;
        console.error('Error sending message:', error);
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
        searchResults.value = messages.value.filter((message) => message.text.toLowerCase().includes(query.toLowerCase()));
    } catch (error) {
        console.error('Error searching messages:', error);
        errorMessage.value = 'Failed to search messages';
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
    } catch (error) {
        console.error('Error adding reaction:', error);
        errorMessage.value = 'Failed to add reaction';
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
    } catch (error) {
        console.error('Error loading more messages:', error);
        errorMessage.value = 'Failed to load more messages';
    }
};

const handleAttachmentPreview = (message: Message) => {
    selectedMessage.value = message;
};

// Initialize real-time listeners after data load
const setupListeners = () => {
    unsubscribeMessages = messagingService.listenToMessages((updatedMessages) => {
        // Replace entire messages array to ensure reactivity
        messages.value = updatedMessages;
        if (isAtBottom.value) {
            scrollToBottom();
        } else {
            showScrollToBottom.value = true;
        }
    });

    unsubscribeTyping = messagingService.listenToTypingIndicators((users) => {
        // Replace entire typingUsers array
        typingUsers.value = users.filter((user) => user.id !== props.user.id);
    });
};

// Lifecycle hooks
onMounted(async () => {
    try {
        loadingMessages.value = true;
        messages.value = await messagingService.getAllMessages();
        loadingMessages.value = false;

        // Set up listeners only after initial data load
        setupListeners();

        // Mark messages as read
        await messagingService.markMessagesAsRead();

        // Initial scroll to bottom
        await scrollToBottom(true);

        // Watch for flash errors from Inertia
        watch(
            () => page.props.flash,
            (flash) => {
                if (flash?.error) {
                    errorMessage.value = flash.error;
                }
            },
            { immediate: true },
        );
    } catch (error) {
        console.error('Error loading messages:', error);
        errorMessage.value = 'Failed to load messages';
        loadingMessages.value = false;
    }
});

onUnmounted(() => {
    if (unsubscribeMessages) {
        unsubscribeMessages();
    }
    if (unsubscribeTyping) {
        unsubscribeTyping();
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
        <!-- Loading indicator -->
        <div v-if="loadingMessages" class="flex h-full items-center justify-center">
            <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-t-2 border-blue-500"></div>
        </div>

        <!-- Main content, hidden until data is loaded -->
        <div v-else class="flex h-full flex-col">
            <!-- Error message -->
            <div v-if="errorMessage" class="mb-2 rounded bg-red-100 p-2 text-red-700">
                {{ errorMessage }}
            </div>

            <!-- Header with search -->
            <MessageHeader
                :has-unread-messages="hasUnreadMessages"
                :search-query="searchQuery"
                @search="searchMessages"
                @clear-search="clearSearch"
            />

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
                @attachment-preview="handleAttachmentPreview"
                @close-preview="selectedMessage = null"
            />

            <!-- Message input area -->
            <MessageInput @send="sendMessage" @typing="sendTypingIndicator" />

            <!-- Scroll to bottom button -->
            <ScrollToBottomButton v-if="showScrollToBottom" @click="scrollToBottom(true)" />
        </div>
    </div>
</template>
