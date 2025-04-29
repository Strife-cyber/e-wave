<script setup lang="ts">
const emit = defineEmits<{
    select: [emoji: string];
    close: [];
}>();

// Simple emoji list for demonstration
const emojis = [
    '😀',
    '😃',
    '😄',
    '😁',
    '😆',
    '😅',
    '😂',
    '🤣',
    '😊',
    '😇',
    '🙂',
    '🙃',
    '😉',
    '😌',
    '😍',
    '🥰',
    '😘',
    '😗',
    '😙',
    '😚',
    '👍',
    '👎',
    '👏',
    '🙌',
    '👌',
    '✌️',
    '🤞',
    '🤟',
    '🤘',
    '👊',
    '❤️',
    '🧡',
    '💛',
    '💚',
    '💙',
    '💜',
    '🖤',
    '❣️',
    '💕',
    '💞',
];

const selectEmoji = (emoji: string) => {
    emit('select', emoji);
};
</script>

<template>
    <div class="rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <div class="grid grid-cols-5 gap-2">
            <button
                v-for="emoji in emojis"
                :key="emoji"
                @click="selectEmoji(emoji)"
                class="flex h-8 w-8 items-center justify-center rounded text-xl hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                {{ emoji }}
            </button>
        </div>
    </div>
</template>
