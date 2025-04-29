<script setup lang="ts">
import { onUnmounted, ref } from 'vue';
import EmojiPicker from './EmojiPicker.vue';

const emit = defineEmits<{
    send: [text: string, attachments: File[]];
    typing: [];
}>();

// State
const newMessage = ref('');
const showEmojiPicker = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const attachments = ref<File[]>([]);

// Voice recording state
const isRecording = ref(false);
const recordingTime = ref(0);
const mediaRecorder = ref<MediaRecorder | null>(null);
const audioChunks = ref<Blob[]>([]);
const recordingInterval = ref<number | null>(null);

// Methods
const sendMessage = () => {
    if (!newMessage.value.trim() && attachments.value.length === 0) return;

    emit('send', newMessage.value, attachments.value);

    // Clear input and attachments
    newMessage.value = '';
    attachments.value = [];
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    } else {
        emit('typing');
    }
};

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        attachments.value = [...attachments.value, ...files];
    }
};

const removeAttachment = (index: number) => {
    attachments.value.splice(index, 1);
};

const openEmojiPicker = () => {
    showEmojiPicker.value = !showEmojiPicker.value;
};

const addEmoji = (emoji: string) => {
    newMessage.value += emoji;
    showEmojiPicker.value = false;
};

// Voice recording methods
const startRecording = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder.value = new MediaRecorder(stream);

        mediaRecorder.value.ondataavailable = (e) => {
            audioChunks.value.push(e.data);
        };

        mediaRecorder.value.onstop = () => {
            const audioBlob = new Blob(audioChunks.value, { type: 'audio/webm' });
            const audioFile = new File([audioBlob], `voice-message-${Date.now()}.webm`, {
                type: 'audio/webm',
                lastModified: Date.now(),
            });

            attachments.value.push(audioFile);
            audioChunks.value = [];

            // Stop all tracks
            stream.getTracks().forEach((track) => track.stop());
        };

        // Start recording
        mediaRecorder.value.start();
        isRecording.value = true;
        recordingTime.value = 0;

        // Update recording time
        recordingInterval.value = window.setInterval(() => {
            recordingTime.value++;

            // Auto-stop after 60 seconds
            if (recordingTime.value >= 60) {
                stopRecording();
            }
        }, 1000);
    } catch (err) {
        console.error('Error accessing microphone:', err);
        alert('Could not access microphone. Please check permissions.');
    }
};

const stopRecording = () => {
    if (mediaRecorder.value && isRecording.value) {
        mediaRecorder.value.stop();
        isRecording.value = false;

        if (recordingInterval.value) {
            clearInterval(recordingInterval.value);
            recordingInterval.value = null;
        }
    }
};

const formatRecordingTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

// Clean up
onUnmounted(() => {
    if (recordingInterval.value) {
        clearInterval(recordingInterval.value);
    }

    if (mediaRecorder.value && isRecording.value) {
        mediaRecorder.value.stop();
    }
});
</script>

<template>
    <div class="">
        <!-- Attachments preview -->
        <div v-if="attachments.length > 0" class="flex flex-wrap gap-2 border-t border-gray-200 px-4 py-2 dark:border-gray-700">
            <div v-for="(file, index) in attachments" :key="index" class="group relative">
                <!-- Image preview -->
                <div v-if="file.type.startsWith('image/')" class="h-16 w-16 overflow-hidden rounded-md bg-gray-100 dark:bg-gray-800">
                    <img :src="URL.createObjectURL(file)" class="h-full w-full object-cover" />
                </div>

                <!-- Audio preview -->
                <div
                    v-else-if="file.type.startsWith('audio/')"
                    class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-md bg-gray-100 dark:bg-gray-800"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 text-purple-400 dark:text-purple-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                        />
                    </svg>
                </div>

                <!-- File preview -->
                <div v-else class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-md bg-gray-100 dark:bg-gray-800">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-8 w-8 text-gray-400 dark:text-gray-500"
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
                </div>

                <!-- Remove button -->
                <button
                    @click="removeAttachment(index)"
                    class="absolute -right-2 -top-2 rounded-full bg-gray-100 p-0.5 text-gray-500 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Message input area -->
        <div class="message-input-container border-t border-gray-200 p-4 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <!-- Attachment button -->
                <button
                    @click="() => fileInput?.click()"
                    class="rounded-full p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                        />
                    </svg>
                    <input ref="fileInput" type="file" multiple class="hidden" @change="handleFileUpload" />
                </button>

                <!-- Voice message button -->
                <button
                    v-if="!isRecording"
                    @click="startRecording"
                    class="rounded-full p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"
                        />
                    </svg>
                </button>

                <!-- Recording indicator -->
                <div v-else class="flex items-center gap-2 rounded-full bg-red-100 px-3 py-1 text-red-500 dark:bg-red-900 dark:text-red-300">
                    <span class="animate-pulse">●</span>
                    <span>{{ formatRecordingTime(recordingTime) }}</span>
                    <button @click="stopRecording" class="ml-2 rounded-full p-1 hover:bg-red-200 dark:hover:bg-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Text input (hidden when recording) -->
                <div v-if="!isRecording" class="relative flex-grow">
                    <textarea
                        v-model="newMessage"
                        @keydown="handleKeyDown"
                        placeholder="Type a message..."
                        class="w-full resize-none rounded-2xl border border-gray-200 bg-white px-4 py-2 pr-10 text-gray-800 focus:outline-none focus:ring-1 focus:ring-purple-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:ring-purple-500"
                        rows="1"
                    ></textarea>

                    <!-- Emoji button -->
                    <button
                        @click="openEmojiPicker"
                        class="absolute bottom-4 right-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </button>

                    <!-- Emoji picker -->
                    <div v-if="showEmojiPicker" class="absolute bottom-full right-0 z-10 mb-2">
                        <EmojiPicker @select="addEmoji" @close="showEmojiPicker = false" />
                    </div>
                </div>

                <!-- Send button -->
                <button
                    v-if="!isRecording"
                    @click="sendMessage"
                    :disabled="!newMessage.trim() && attachments.length === 0"
                    :class="[
                        'rounded-full p-2 transition-colors',
                        newMessage.trim() || attachments.length > 0
                            ? 'bg-purple-300 text-white hover:bg-purple-400 dark:bg-purple-500 dark:hover:bg-purple-600'
                            : 'cursor-not-allowed bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500',
                    ]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Textarea auto-resize */
textarea {
    min-height: 40px;
    max-height: 120px;
}
</style>
