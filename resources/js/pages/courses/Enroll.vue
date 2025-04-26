<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, Lesson } from '@/types';
import { onMounted, ref } from 'vue';

// Define props with TypeScript
const props = defineProps<{
    enrolled: boolean;
    lessons: number;
    course?: {
        id: string;
        title?: string;
    };
    firstLesson?: Lesson;
}>();

// Animation states
const isLoaded = ref(false);
const showConfetti = ref(false);
const animateProgress = ref(false);
const showLessonPreview = ref(false);

// For rendering HTML content safely
const contentContainer = ref<HTMLElement | null>(null);

// Emit events
const emit = defineEmits(['startLearning', 'previewLesson']);

const startLearning = () => {
    emit('startLearning');
};

const previewLesson = () => {
    emit('previewLesson', props.firstLesson?.id);
};

// Animation timing
onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);

    setTimeout(() => {
        showConfetti.value = true;
    }, 300);

    setTimeout(() => {
        animateProgress.value = true;
    }, 800);

    setTimeout(() => {
        showLessonPreview.value = true;

        // If we have a first lesson with content, render it
        if (props.firstLesson?.content && contentContainer.value) {
            // Only show a preview snippet of the content (first 150 chars)
            const previewContent = props.firstLesson.content.substring(0, 150) + (props.firstLesson.content.length > 150 ? '...' : '');

            // For security, we're just showing a text preview rather than rendering HTML
            contentContainer.value.textContent = previewContent;
        }
    }, 1200);
});

// Helper function to get icon based on lesson type
const getLessonTypeIcon = (type: 'video' | 'quiz' | 'game') => {
    switch (type) {
        case 'video':
            return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
              </svg>`;
        case 'quiz':
            return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
              </svg>`;
        case 'game':
            return `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z" />
              </svg>`;
    }
};

// Helper function to get lesson type label
const getLessonTypeLabel = (type: 'video' | 'quiz' | 'game') => {
    switch (type) {
        case 'video':
            return 'Video Lesson';
        case 'quiz':
            return 'Interactive Quiz';
        case 'game':
            return 'Learning Game';
    }
};

// Helper function to estimate lesson duration based on content length
const estimateDuration = (content: string) => {
    // Rough estimate: 1 minute per 500 characters of content
    const minutes = Math.max(5, Math.ceil(content.length / 500));
    return `${minutes} min`;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Courses', href: '/courses' },
    { title: props.course?.id, href: `/courses/${props.course?.id}` },
    { title: 'Enrolled', href: `/courses/enroll/${props.course.id}` },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex min-h-screen items-center justify-center bg-gradient-to-b from-gray-50 to-purple-50 p-4 dark:from-gray-900 dark:to-purple-950"
        >
            <!-- Animated background elements -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute left-0 top-0 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-purple-200 opacity-30 blur-3xl filter dark:bg-purple-900/20"
                ></div>
                <div
                    class="absolute bottom-0 right-0 h-96 w-96 translate-x-1/2 translate-y-1/2 rounded-full bg-indigo-200 opacity-30 blur-3xl filter dark:bg-indigo-900/20"
                ></div>
            </div>

            <!-- Confetti animation -->
            <div v-if="showConfetti" class="confetti-container pointer-events-none fixed inset-0">
                <div
                    v-for="i in 60"
                    :key="i"
                    class="confetti"
                    :class="[i % 4 === 0 ? 'bg-purple-500' : i % 4 === 1 ? 'bg-indigo-400' : i % 4 === 2 ? 'bg-pink-400' : 'bg-violet-300']"
                    :style="{
                        left: `${Math.random() * 100}%`,
                        width: `${Math.random() * 10 + 5}px`,
                        height: `${Math.random() * 10 + 5}px`,
                        animationDelay: `${Math.random() * 3}s`,
                        animationDuration: `${Math.random() * 3 + 2}s`,
                    }"
                ></div>
            </div>

            <!-- Success card -->
            <div
                :class="[
                    'w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-gray-800',
                    'transform border border-purple-100 transition-all duration-700 ease-out dark:border-purple-900/50',
                    isLoaded ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0',
                ]"
            >
                <!-- Purple success header with achievement illustration -->
                <div class="relative">
                    <!-- Background gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800"></div>

                    <!-- Decorative patterns -->
                    <div class="absolute inset-0 opacity-10">
                        <div
                            class="absolute left-0 top-0 h-full w-full"
                            style="
                                background-image: url(&quot;data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='1' fill-rule='evenodd'/%3E%3C/svg%3E&quot;);
                            "
                        ></div>
                    </div>

                    <!-- Content -->
                    <div class="relative px-8 pb-20 pt-12 text-center">
                        <!-- Achievement illustration -->
                        <div class="relative mb-6 inline-block">
                            <!-- Outer ring with glow -->
                            <div class="animate-pulse-slow absolute inset-0 rounded-full bg-purple-300 blur-md dark:bg-purple-400"></div>

                            <!-- Achievement badge -->
                            <div class="relative mx-auto h-32 w-32">
                                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500"></div>
                                <div
                                    class="absolute inset-2 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-200 to-purple-50 dark:from-purple-300 dark:to-purple-100"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-purple-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>

                                <!-- Decorative stars -->
                                <div class="animate-ping-slow absolute -right-2 -top-2 h-6 w-6 text-yellow-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div class="animate-ping-slow absolute -top-4 left-6 h-4 w-4 text-yellow-300" style="animation-delay: 0.5s">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div class="animate-ping-slow absolute -bottom-1 -left-2 h-5 w-5 text-yellow-300" style="animation-delay: 1s">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Success message -->
                        <h1 class="mb-2 text-3xl font-bold text-white">Enrollment Successful!</h1>
                        <p class="mx-auto max-w-md text-lg text-purple-100">You're all set to begin your learning journey with this course</p>
                    </div>

                    <!-- Wave divider -->
                    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
                        <svg
                            class="relative block h-12 w-full"
                            data-name="Layer 1"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 1200 120"
                            preserveAspectRatio="none"
                        >
                            <path
                                d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                                class="fill-white dark:fill-gray-800"
                            ></path>
                        </svg>
                    </div>
                </div>

                <!-- Course details -->
                <div class="-mt-2 px-8 py-10">
                    <!-- Course title and info -->
                    <div class="mb-8 text-center">
                        <h2 class="mb-3 text-2xl font-bold text-gray-800 dark:text-gray-100">
                            {{ props.course?.title || 'Your New Course' }}
                        </h2>

                        <!-- Course stats -->
                        <div class="flex flex-wrap justify-center gap-6">
                            <!-- Lessons count -->
                            <div class="flex flex-col items-center">
                                <div class="mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6 text-purple-600 dark:text-purple-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                        />
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Lessons</div>
                                <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ props.lessons }}</div>
                            </div>

                            <!-- Progress -->
                            <div class="flex flex-col items-center">
                                <div
                                    class="relative mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30"
                                >
                                    <svg class="absolute h-12 w-12" viewBox="0 0 36 36">
                                        <circle
                                            cx="18"
                                            cy="18"
                                            r="16"
                                            fill="none"
                                            class="stroke-purple-200 dark:stroke-purple-800/30"
                                            stroke-width="2"
                                        ></circle>
                                        <circle
                                            cx="18"
                                            cy="18"
                                            r="16"
                                            fill="none"
                                            class="stroke-purple-500 dark:stroke-purple-400"
                                            stroke-width="2"
                                            stroke-dasharray="100"
                                            :stroke-dashoffset="animateProgress ? '100' : '0'"
                                            transform="rotate(-90 18 18)"
                                            style="transition: stroke-dashoffset 1.5s ease-in-out"
                                        ></circle>
                                    </svg>
                                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">0%</span>
                                </div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Progress</div>
                                <div class="text-xl font-bold text-gray-800 dark:text-gray-200">Just Started</div>
                            </div>
                        </div>
                    </div>

                    <!-- What's next section -->
                    <div
                        class="mb-8 rounded-2xl border border-purple-100 bg-gradient-to-br from-purple-50 to-indigo-50 p-6 dark:border-purple-800/20 dark:from-purple-900/20 dark:to-indigo-900/20"
                    >
                        <h3 class="mb-4 flex items-center text-lg font-semibold text-gray-800 dark:text-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-purple-500" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Your Learning Path
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div
                                    class="mr-3 mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-800/50"
                                >
                                    <span class="text-sm font-medium text-purple-600 dark:text-purple-300">1</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200">Explore Course Materials</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Get familiar with the course structure and resources</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="mr-3 mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-800/50"
                                >
                                    <span class="text-sm font-medium text-purple-600 dark:text-purple-300">2</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200">
                                        Complete {{ props.lessons }} {{ props.lessons === 1 ? 'Lesson' : 'Lessons' }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Work through the lessons at your own pace</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div
                                    class="mr-3 mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-800/50"
                                >
                                    <span class="text-sm font-medium text-purple-600 dark:text-purple-300">3</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200">Earn Your Certificate</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Complete all lessons to receive your course certificate</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- First Lesson Preview -->
                    <div
                        v-if="props.firstLesson"
                        class="mb-8 transform overflow-hidden rounded-2xl border border-purple-100 transition-all duration-700 dark:border-purple-800/20"
                        :class="[
                            showLessonPreview ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0',
                            props.firstLesson?.bright ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900',
                        ]"
                    >
                        <!-- Lesson header with type indicator -->
                        <div
                            class="flex items-center justify-between px-6 py-3"
                            :class="
                                props.firstLesson?.type === 'video'
                                    ? 'bg-purple-600 text-white'
                                    : props.firstLesson?.type === 'quiz'
                                      ? 'bg-blue-600 text-white'
                                      : 'bg-green-600 text-white'
                            "
                        >
                            <h3 class="flex items-center font-medium">
                                <!-- Dynamically render icon based on lesson type -->
                                <span class="mr-2" v-html="getLessonTypeIcon(props.firstLesson?.type || 'video')"></span>
                                {{ getLessonTypeLabel(props.firstLesson?.type || 'video') }}
                            </h3>
                            <span class="rounded-full bg-white bg-opacity-20 px-2 py-1 text-xs">
                                {{ props.firstLesson?.content ? estimateDuration(props.firstLesson.content) : '15 min' }}
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <!-- Lesson title and order number -->
                            <div class="border-b border-gray-100 p-6 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                                        {{ props.firstLesson?.title || 'Introduction to the Course' }}
                                    </h4>
                                    <span
                                        class="rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-300"
                                    >
                                        Lesson {{ props.firstLesson?.order_no || 1 }}
                                    </span>
                                </div>
                            </div>

                            <!-- Content preview -->
                            <div class="p-6">
                                <!-- Content preview with safe rendering -->
                                <div class="mb-4">
                                    <h5 class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Preview:</h5>
                                    <div
                                        ref="contentContainer"
                                        class="max-h-24 overflow-hidden rounded-lg bg-gray-50 p-4 text-sm text-gray-600 dark:bg-gray-900/50 dark:text-gray-300"
                                    >
                                        <!-- Content will be safely rendered here via the ref -->
                                        <div v-if="!props.firstLesson?.content" class="italic text-gray-400 dark:text-gray-500">
                                            Content will be available when you start the lesson
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview button -->
                                <button
                                    @click="previewLesson"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-purple-100 px-4 py-3 font-medium text-purple-700 transition-colors hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:hover:bg-purple-800/40"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    Preview {{ getLessonTypeLabel(props.firstLesson?.type || 'video') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <button
                            @click="startLearning"
                            class="flex flex-grow transform items-center justify-center rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 font-medium text-white shadow-lg shadow-purple-500/20 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-purple-500/30 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Start Learning Now
                        </button>
                        <a
                            href="#"
                            class="flex flex-grow items-center justify-center rounded-xl border border-gray-200 bg-white px-6 py-4 text-center font-medium text-gray-700 transition-colors duration-150 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"
                                />
                            </svg>
                            View Course Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Confetti animation */
.confetti-container {
    z-index: 0;
}

.confetti {
    position: absolute;
    top: -10px;
    border-radius: 0;
    animation: confetti-fall linear forwards;
}

@keyframes confetti-fall {
    0% {
        transform: translateY(-10px) rotate(0deg) scale(0);
        opacity: 1;
    }
    25% {
        transform: translateY(25vh) rotate(90deg) scale(1);
    }
    100% {
        transform: translateY(100vh) rotate(360deg) scale(0.5);
        opacity: 0;
    }
}

/* Slow bounce animation */
@keyframes bounce-slow {
    0%,
    100% {
        transform: translateY(-5%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

/* Slow ping animation */
@keyframes ping-slow {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    75%,
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}
.animate-ping-slow {
    animation: ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Slow pulse animation */
@keyframes pulse-slow {
    0%,
    100% {
        opacity: 0.8;
    }
    50% {
        opacity: 0.4;
    }
}
.animate-pulse-slow {
    animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
