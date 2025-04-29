<script setup lang="ts">
import { User } from '@/types';
import { formatDistanceToNow } from 'date-fns';

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
}>();

const emit = defineEmits<{
    close: [];
}>();

const formatRelativeTime = (timestamp: string) => {
    return formatDistanceToNow(new Date(timestamp), { addSuffix: true });
};

const imageAttachment = props.message.attachments?.find((a) => a.type.startsWith('image/'));
</script>

<template>
    <div v-if="imageAttachment" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click="emit('close')">
        <div class="max-h-full max-w-4xl" @click.stop>
            <img :src="imageAttachment.url" class="max-h-[80vh] max-w-full rounded-lg object-contain" />
            <div class="mt-2 text-center text-white">
                <p>{{ imageAttachment.name }}</p>
                <p class="text-sm text-gray-300">{{ formatRelativeTime(message.timestamp) }} by {{ message.user.name }}</p>
            </div>
        </div>
    </div>
</template>
