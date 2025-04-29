<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="relative min-h-screen bg-gray-50 dark:bg-gray-900">
            <!-- Lesson Header -->
            <LessonHeader :lesson="lesson" :completionPercentage="completionPercentage" @back="goBack" />

            <!-- Main Content Area with Notes Panel -->
            <div class="flex">
                <!-- Lesson Content -->
                <main :class="['transition-all duration-300 ease-in-out', isNotesOpen ? 'w-full md:w-2/3 lg:w-3/4' : 'w-full']">
                    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                        <LessonContent :lesson="lesson" @progress-update="updateProgress" />

                        <!-- Navigation -->
                        <LessonNavigation
                            :hasPreviousLesson="hasPreviousLesson"
                            :hasNextLesson="hasNextLesson"
                            @previous="navigateToPrevious"
                            @next="navigateToNext"
                            @complete="finishCourse"
                        />
                    </div>
                </main>

                <!-- Notes Panel -->
                <NotesPanel
                    :lessonId="lesson.id"
                    :isOpen="isNotesOpen"
                    @toggle="toggleNotes"
                    @export="showExportModal"
                    @jump-to-reference="scrollToReference"
                />
            </div>

            <!-- Floating Notes Button -->
            <button
                @click="toggleNotes"
                class="fixed bottom-6 right-6 z-30 rounded-full bg-purple-600 p-3 text-white shadow-lg transition-all duration-300 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800"
                :class="{ 'rotate-45': isNotesOpen }"
            >
                <component :is="isNotesOpen ? 'XIcon' : 'NotebookPenIcon'" class="h-6 w-6" />
            </button>

            <!-- Export Modal -->
            <ExportModal
                :show="showExportModalFlag"
                :notes="notes"
                :exportType="exportType"
                @close="showExportModalFlag = false"
                @export="handleExport"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import ExportModal from '@/components/lesson/ExportModal.vue';
import LessonContent from '@/components/lesson/LessonContent.vue';
import LessonHeader from '@/components/lesson/LessonHeader.vue';
import LessonNavigation from '@/components/lesson/LessonNavigation.vue';
import NotesPanel from '@/components/lesson/NotesPanel.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { exportToMarkdown, exportToPdf } from '@/utils/utils';
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    lesson: {
        type: Object,
        required: true,
    },
    hasPreviousLesson: {
        type: Boolean,
        default: false,
    },
    hasNextLesson: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['back', 'previous', 'next', 'complete', 'progress-update']);

// State
const completionPercentage = ref(0);
const isCompleted = ref(false);
const isNotesOpen = ref(false);
const notes = ref([]);
const showExportModalFlag = ref(false);
const exportType = ref('pdf');

// Methods
const goBack = () => {
    emit('back');
};

const navigateToPrevious = () => {
    emit('previous');
};

const navigateToNext = () => {
    emit('next');
};

const finishCourse = () => {
    emit('complete');
};

const updateProgress = (percentage) => {
    completionPercentage.value = percentage;

    // Emit progress update
    emit('progress-update', {
        lessonId: props.lesson.id,
        progress: percentage,
    });

    // Mark as completed if scrolled to 90% or more
    if (percentage >= 90 && !isCompleted.value) {
        isCompleted.value = true;
        // Here you would typically call an API to mark the lesson as completed
        console.log('Lesson completed:', props.lesson.id);
    }
};

const toggleNotes = () => {
    isNotesOpen.value = !isNotesOpen.value;

    // If opening notes, and we're on mobile, we might want to scroll to the top of the notes panel
    if (isNotesOpen.value && window.innerWidth < 768) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const scrollToReference = (reference) => {
    if (reference) {
        window.scrollTo({
            top: reference,
            behavior: 'smooth',
        });
    }
};

const showExportModal = (type) => {
    exportType.value = type;
    showExportModalFlag.value = true;

    // Load notes for the export modal
    loadNotesFromStorage();
};

const handleExport = async (exportData) => {
    try {
        if (exportData.type === 'pdf') {
            await exportToPdf(exportData.notes, props.lesson.title);
        } else if (exportData.type === 'markdown') {
            exportToMarkdown(exportData.notes, props.lesson.title);
        }

        showExportModalFlag.value = false;
    } catch (error) {
        console.error('Error exporting notes:', error);
        alert('An error occurred while exporting notes. Please try again.');
    }
};

const loadNotesFromStorage = () => {
    try {
        const storedNotes = localStorage.getItem(`lesson_notes_${props.lesson.id}`);
        if (storedNotes) {
            notes.value = JSON.parse(storedNotes);
        }
    } catch (error) {
        console.error('Error loading notes from localStorage:', error);
    }
};

// Keyboard shortcuts
const handleKeyboardShortcuts = (event) => {
    // Ctrl+N to toggle notes panel
    if (event.ctrlKey && event.key === 'n') {
        event.preventDefault();
        toggleNotes();
    }

    // Ctrl+E to export notes
    if (event.ctrlKey && event.key === 'e') {
        event.preventDefault();
        if (notes.value.length > 0 && isNotesOpen.value) {
            showExportModal('pdf');
        }
    }
};

// Lifecycle hooks
onMounted(() => {
    window.addEventListener('keydown', handleKeyboardShortcuts);
    loadNotesFromStorage();
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyboardShortcuts);
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'My Courses', href: '/user/courses' },
    { title: props.lesson.course_id, href: `/user/courses/${props.lesson.course_id}` },
    { title: 'Lessons', href: `/user/courses/${props.lesson.course_id}/lessons` },
    { title: props.lesson.id, href: `/lessons/${props.lesson.id}` },
];
</script>
