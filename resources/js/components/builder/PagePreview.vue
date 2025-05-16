<template>
    <div class="flex flex-grow items-center justify-center overflow-auto p-4" :class="isDarkMode ? 'bg-gray-900' : 'bg-gray-100'">
        <div
            class="overflow-hidden rounded-md shadow-md transition-all duration-300"
            :style="{
                width: previewWidth + 'px',
                height: previewHeight + 'px',
                transform: isLandscape && currentDevice !== 'desktop' ? 'rotate(90deg)' : 'none',
                maxWidth: '100%',
                maxHeight: isLandscape && currentDevice !== 'desktop' ? '100vw' : '80vh',
            }"
        >
            <iframe
                :srcdoc="htmlContent"
                class="h-full w-full border-0"
                :class="isDarkMode ? 'bg-gray-800' : 'bg-white'"
                title="Page preview"
            ></iframe>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    previewWidth: {
        type: Number,
        default: 1280,
    },
    previewHeight: {
        type: Number,
        default: 800,
    },
    isLandscape: {
        type: Boolean,
        default: false,
    },
    currentDevice: {
        type: String,
        default: 'desktop',
    },
    htmlContent: {
        type: String,
        default: '',
    },
});
</script>
