<template>
    <div class="overflow-hidden rounded-lg bg-white shadow-lg dark:bg-gray-800">
        <!-- Content Type Indicator -->
        <div class="flex items-center border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <component
                :is="getTypeIcon(lesson.type)"
                class="mr-2 h-5 w-5"
                :class="[
                    lesson.type === 'video'
                        ? 'text-purple-600 dark:text-purple-400'
                        : lesson.type === 'quiz'
                          ? 'text-indigo-600 dark:text-indigo-400'
                          : 'text-pink-600 dark:text-pink-400',
                ]"
            />
            <span class="font-medium text-gray-900 dark:text-white"> Lesson {{ lesson.order_no }}: {{ lesson.title }} </span>
        </div>

        <!-- Actual Content -->
        <div ref="contentRef" class="prose dark:prose-invert max-w-none px-6 py-4">
            <!-- Render content based on lesson type -->
            <div v-if="lesson.type === 'video'" class="mb-6">
                <!-- Video placeholder - in a real app, this would be an actual video player -->
                <div class="aspect-w-16 aspect-h-9 flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700">
                    <PlayCircleIcon class="h-16 w-16 text-purple-600 dark:text-purple-400" />
                </div>
            </div>

            <!-- Rendered HTML Content -->
            <div v-if="lesson.type === 'html'" class="lesson-content">
                <iframe :src="`/${lesson.html}`" class="min-h-[100vh] w-full border-0" title="Lesson Content"></iframe>
            </div>
            <div v-else class="lesson-content" v-html="sanitizedContent"></div>
        </div>
    </div>
</template>

<script setup lang="ts">
import DOMPurify from 'dompurify';
import { GamepadIcon, HelpCircleIcon, PlayCircleIcon } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
    lesson: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['progress-update']);

const contentRef = ref(null);
const lastScrollPosition = ref(0);
const scrollThreshold = 100; // Pixels to scroll before updating progress

// Computed
const sanitizedContent = computed(() => {
    // Sanitize HTML content to prevent XSS attacks
    return DOMPurify.sanitize(props.lesson.content, {
        ADD_TAGS: ['script', 'style'],
        ADD_ATTR: ['onclick', 'onload'],
    });
});

const getTypeIcon = (type) => {
    switch (type) {
        case 'video':
            return PlayCircleIcon;
        case 'quiz':
            return HelpCircleIcon;
        case 'game':
            return GamepadIcon;
        default:
            return PlayCircleIcon;
    }
};

const updateProgress = () => {
    if (!contentRef.value) return;

    const element = contentRef.value;
    const windowHeight = window.innerHeight;
    const documentHeight = element.scrollHeight;
    const scrollTop = window.scrollY || document.documentElement.scrollTop;

    // Calculate how far down the page the user has scrolled
    const scrolled = scrollTop + windowHeight;

    // Calculate percentage scrolled
    const percentage = Math.min(100, Math.max(0, (scrolled / documentHeight) * 100));

    // Emit progress update
    emit('progress-update', percentage);
};

const handleScroll = () => {
    // Throttle scroll events
    const currentScrollPosition = window.scrollY || document.documentElement.scrollTop;

    if (Math.abs(currentScrollPosition - lastScrollPosition.value) > scrollThreshold) {
        lastScrollPosition.value = currentScrollPosition;
        updateProgress();
    }
};

// Lifecycle hooks
onMounted(() => {
    window.addEventListener('scroll', handleScroll);

    // Initial progress calculation
    setTimeout(updateProgress, 500);
});

watch(
    () => props.lesson,
    () => {
        // Reset when lesson changes
        lastScrollPosition.value = 0;

        // Recalculate progress
        setTimeout(updateProgress, 500);
    },
    { deep: true },
);
</script>

<style scoped>
/* Lesson content styles */
.lesson-content {
    /* Base styles for lesson content */
    line-height: 1.6;
}

.lesson-content h1,
.lesson-content h2,
.lesson-content h3,
.lesson-content h4,
.lesson-content h5,
.lesson-content h6 {
    margin-top: 1.5em;
    margin-bottom: 0.5em;
    font-weight: 600;
    color: inherit;
}

.lesson-content p {
    margin-bottom: 1em;
}

.lesson-content pre {
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 0.375rem;
    padding: 1rem;
    overflow-x: auto;
    margin: 1rem 0;
}

.lesson-content code {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
    font-size: 0.875em;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    background-color: rgba(0, 0, 0, 0.05);
}

:deep .lesson-content code {
    background-color: rgba(255, 255, 255, 0.1);
}

.lesson-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    margin: 1rem 0;
}

/* Add styles for interactive elements */
.lesson-content button,
.lesson-content input,
.lesson-content select,
.lesson-content textarea {
    margin: 0.5rem 0;
}

/* Style for embedded iframes */
.lesson-content iframe {
    width: 100%;
    border: none;
    border-radius: 0.375rem;
    margin: 1rem 0;
}

/* Aspect ratio container for responsive videos */
.aspect-w-16 {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
}

.aspect-w-16 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
