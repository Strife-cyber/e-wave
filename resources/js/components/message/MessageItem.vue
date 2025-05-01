<script setup lang="ts">
import { Message, User } from '@/types';
import { format } from 'date-fns';
import { ref, watch } from 'vue';

interface Message {
    id: string;
    text: string;
    user: User;
    timestamp: string;
    status: 'sending' | 'sent' | 'delivered' | 'read';
    attachments?: Array<{
        type: string;
        url: string;
        name: string;
        size?: number;
    }>;
    reactions?: Array<{
        emoji: string;
        users: User[];
    }>;
}

const props = defineProps<{
    message: Message;
    currentUser: User;
}>();

const emit = defineEmits<{
    addReaction: [messageId: string, emoji: string];
    imagePreview: [message: Message];
}>();

// Local reactive message to ensure deep reactivity
const localMessage = ref<Message>(props.message);

// Watch for deep changes to message prop
watch(
    () => props.message,
    (newMessage) => {
        localMessage.value = newMessage;
    },
    { deep: true },
);

const formatMessageTime = (timestamp: string) => {
    return format(new Date(timestamp), 'h:mm a');
};

const addReaction = (emoji: string) => {
    emit('addReaction', localMessage.value.id, emoji);
};

const showImagePreview = () => {
    emit('imagePreview', localMessage.value);
};

const isCurrentUser = localMessage.value.user.id === props.currentUser.id;
</script>

<template>
    <div class="message-wrapper group">
        <!-- Message bubble -->
        <div :class="['flex max-w-[85%] gap-2', isCurrentUser ? 'ml-auto flex-row-reverse' : '']">
            <!-- Avatar (only show for others, not current user) -->
            <div
                v-if="!isCurrentUser"
                class="h-8 w-8 flex-shrink-0 rounded-full bg-cover bg-center"
                :style="localMessage.user.avatar ? `background-image: url(${localMessage.user.avatar})` : ''"
            >
                <div
                    v-if="!localMessage.user.avatar"
                    class="flex h-full w-full items-center justify-center rounded-full bg-purple-300 font-medium text-white dark:bg-purple-500"
                >
                    {{ localMessage.user.name.charAt(0) }}
                </div>
            </div>

            <!-- Message content -->
            <div class="flex flex-col">
                <!-- Sender name (only for others) -->
                <span v-if="!isCurrentUser" class="mb-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ localMessage.user.name }}
                </span>

                <!-- Message bubble -->
                <div
                    :class="[
                        'max-w-prose break-words rounded-2xl px-4 py-2',
                        isCurrentUser
                            ? 'rounded-tr-none bg-purple-300 text-gray-800 dark:bg-purple-500 dark:text-white'
                            : 'rounded-tl-none border border-gray-200 bg-white text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100',
                    ]"
                >
                    <!-- Message text -->
                    <p class="whitespace-pre-wrap">{{ localMessage.text }}</p>

                    <!-- Attachments -->
                    <div v-if="localMessage.attachments && localMessage.attachments.length > 0" class="mt-2 space-y-2">
                        <div v-for="(attachment, index) in localMessage.attachments" :key="index" class="attachment">
                            <!-- Image attachment -->
                            <img
                                v-if="attachment.type.startsWith('image/')"
                                :src="attachment.url"
                                :alt="attachment.name"
                                class="max-h-60 max-w-full cursor-pointer rounded-lg object-contain"
                                @click="showImagePreview"
                            />

                            <!-- Audio attachment (for voice messages) -->
                            <div v-else-if="attachment.type.startsWith('audio/')" class="audio-player rounded-lg bg-gray-100 p-2 dark:bg-gray-700">
                                <audio controls class="w-full">
                                    <source :src="attachment.url" :type="attachment.type" />
                                    Your browser does not support the audio element.
                                </audio>
                            </div>

                            <!-- File attachment -->
                            <div v-else class="flex items-center rounded-lg bg-gray-100 p-2 dark:bg-gray-700">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mr-2 h-5 w-5 text-gray-500 dark:text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <div class="flex-grow overflow-hidden">
                                    <div class="truncate text-sm">{{ attachment.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ attachment.size ? Math.round(attachment.size / 1024) + ' KB' : '' }}
                                    </div>
                                </div>
                                <a
                                    :href="attachment.url"
                                    download
                                    class="ml-2 p-1 text-purple-400 hover:text-purple-500 dark:text-purple-500 dark:hover:text-purple-400"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Reactions -->
                    <div v-if="localMessage.reactions && localMessage.reactions.length > 0" class="mt-2 flex flex-wrap gap-1">
                        <div
                            v-for="(reaction, index) in localMessage.reactions"
                            :key="index"
                            class="flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-sm dark:bg-gray-700"
                        >
                            <span class="mr-1">{{ reaction.emoji }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ reaction.users.length }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timestamp and status -->
                <div :class="['mt-1 flex items-center gap-1 text-xs', isCurrentUser ? 'justify-end' : '']">
                    <span class="text-gray-500 dark:text-gray-400">{{ formatMessageTime(localMessage.timestamp) }}</span>

                    <!-- Message status (only for current user's messages) -->
                    <span v-if="isCurrentUser">
                        <!-- Sending -->
                        <svg
                            v-if="localMessage.status === 'sending'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3 w-3 animate-pulse text-gray-400 dark:text-gray-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <!-- Sent -->
                        <svg
                            v-else-if="localMessage.status === 'sent'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3 w-3 text-gray-400 dark:text-gray-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <!-- Delivered -->
                        <svg
                            v-else-if="localMessage.status === 'delivered'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3 w-3 text-purple-400 dark:text-purple-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <!-- Read -->
                        <svg
                            v-else-if="localMessage.status === 'read'"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3 w-3 text-purple-400 dark:text-purple-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Message actions -->
        <div :class="['mt-1 flex gap-2 opacity-0 transition-opacity group-hover:opacity-100', isCurrentUser ? 'justify-end' : 'justify-start']">
            <button
                @click="addReaction('👍')"
                class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
            >
                👍
            </button>
            <button
                @click="addReaction('❤️')"
                class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
            >
                ❤️
            </button>
            <button
                @click="addReaction('😂')"
                class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-gray-800 dark:hover:text-gray-300"
            >
                😂
            </button>
        </div>
    </div>
</template>
