<template>
    <aside
        :class="[
            'fixed right-0 top-0 z-10 h-full overflow-hidden bg-white shadow-lg transition-all duration-300 ease-in-out dark:bg-gray-800 md:relative',
            isOpen ? 'w-full md:w-1/3 lg:w-1/4' : 'w-0',
        ]"
        style="margin-top: 65px; height: calc(100vh - 65px)"
    >
        <div class="flex h-full flex-col">
            <!-- Notes Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <h2 class="flex items-center font-semibold text-gray-900 dark:text-white">
                    <NotebookPenIcon class="mr-2 h-4 w-4 text-purple-600 dark:text-purple-400" />
                    My Notes
                </h2>
                <div class="flex items-center space-x-2">
                    <!-- Export Dropdown -->
                    <div class="relative" v-if="notes.length > 0">
                        <button
                            @click="toggleExportMenu"
                            class="rounded-full p-1 text-gray-500 hover:bg-gray-100 hover:text-purple-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-purple-400"
                            title="Export notes"
                        >
                            <DownloadIcon class="h-5 w-5" />
                        </button>

                        <!-- Export Menu -->
                        <div
                            v-if="showExportMenu"
                            class="absolute right-0 z-10 mt-2 w-48 rounded-md border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800"
                        >
                            <button
                                @click="exportNotes('pdf')"
                                class="flex w-full items-center px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                <FileIcon class="mr-2 h-4 w-4 text-red-500" />
                                Export as PDF
                            </button>
                            <button
                                @click="exportNotes('markdown')"
                                class="flex w-full items-center px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                <FileTextIcon class="mr-2 h-4 w-4 text-blue-500" />
                                Export as Markdown
                            </button>
                        </div>
                    </div>

                    <button
                        @click="addNewNote"
                        class="rounded-full p-1 text-gray-500 hover:bg-gray-100 hover:text-purple-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-purple-400"
                        title="Add new note"
                    >
                        <PlusIcon class="h-5 w-5" />
                    </button>
                    <button
                        @click="$emit('toggle')"
                        class="rounded-full p-1 text-gray-500 hover:bg-gray-100 hover:text-purple-600 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-purple-400 md:hidden"
                        title="Close notes"
                    >
                        <XIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Notes List -->
            <div class="flex-1 space-y-4 overflow-y-auto p-4">
                <div v-if="!notes.length" class="py-8 text-center">
                    <NotebookPenIcon class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
                    <p class="mt-2 text-gray-500 dark:text-gray-400">No notes yet</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Click the + button to add a note</p>
                </div>

                <NoteItem
                    v-for="(note, index) in notes"
                    :key="index"
                    :note="note"
                    :index="index"
                    :is-editing="editingNoteIndex === index"
                    :edit-content="editingNoteContent"
                    @edit="editNote"
                    @delete="deleteNote"
                    @save-edit="saveEditedNote"
                    @cancel-edit="cancelEditNote"
                    @jump-to-reference="scrollToReference"
                />
            </div>

            <!-- New Note Input with Rich Text Formatting -->
            <div v-if="isAddingNote" class="border-t border-gray-200 p-4 dark:border-gray-700">
                <!-- Rich Text Formatting Toolbar -->
                <FormattingToolbar />

                <!-- Rich Text Editor -->
                <div
                    ref="newNoteEditor"
                    contenteditable="true"
                    class="formatted-note max-h-[300px] min-h-[100px] w-full overflow-y-auto rounded-b-md border border-gray-300 bg-white p-3 text-gray-800 focus:border-transparent focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:focus:ring-purple-400"
                    placeholder="Type your note here..."
                    @keydown.ctrl.enter="saveNewNote"
                ></div>

                <div class="mt-2 flex items-center justify-between">
                    <div class="text-xs text-gray-500 dark:text-gray-400">Press Ctrl+Enter to save</div>
                    <div class="flex space-x-2">
                        <button
                            @click="cancelNewNote"
                            class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveNewNote"
                            class="rounded bg-purple-600 px-3 py-1 text-sm text-white hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup lang="ts">
import DOMPurify from 'dompurify';
import { DownloadIcon, FileIcon, FileTextIcon, NotebookPenIcon, PlusIcon, XIcon } from 'lucide-vue-next';
import { nextTick, ref, watch } from 'vue';
import FormattingToolbar from './FormattingToolbar.vue';
import NoteItem from './NoteItem.vue';

const props = defineProps({
    lessonId: {
        type: [String, Number],
        required: true,
    },
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['toggle', 'export', 'jump-to-reference']);

// Notes functionality
const notes = ref([]);
const isAddingNote = ref(false);
const newNoteEditor = ref(null);
const editingNoteIndex = ref(null);
const editingNoteContent = ref('');
const showExportMenu = ref(false);

// Methods
const toggleExportMenu = () => {
    showExportMenu.value = !showExportMenu.value;

    // Close the menu when clicking outside
    if (showExportMenu.value) {
        setTimeout(() => {
            document.addEventListener('click', closeExportMenu);
        }, 0);
    }
};

const closeExportMenu = () => {
    showExportMenu.value = false;
    document.removeEventListener('click', closeExportMenu);
};

const exportNotes = (type) => {
    showExportMenu.value = false;
    emit('export', type);
};

const addNewNote = () => {
    isAddingNote.value = true;
    editingNoteIndex.value = null;

    // Focus the editor on the next tick
    nextTick(() => {
        if (newNoteEditor.value) {
            newNoteEditor.value.focus();
        }
    });
};

const saveNewNote = () => {
    if (newNoteEditor.value && newNoteEditor.value.innerHTML.trim()) {
        // Get current scroll position as a reference
        const scrollPosition = window.scrollY || document.documentElement.scrollTop;

        // Sanitize the HTML content
        const sanitizedHtml = DOMPurify.sanitize(newNoteEditor.value.innerHTML);

        // Add the new note
        notes.value.unshift({
            content: sanitizedHtml.replace(/<[^>]*>/g, '').trim(), // Plain text version
            formattedContent: sanitizedHtml, // Formatted HTML version
            timestamp: new Date(),
            reference: scrollPosition > 100 ? scrollPosition : null, // Only add reference if we're not at the top
        });

        // Save notes to localStorage
        saveNotesToStorage();

        // Reset the form
        newNoteEditor.value.innerHTML = '';
        isAddingNote.value = false;
    }
};

const cancelNewNote = () => {
    if (newNoteEditor.value) {
        newNoteEditor.value.innerHTML = '';
    }
    isAddingNote.value = false;
};

const editNote = (index) => {
    editingNoteIndex.value = index;
    editingNoteContent.value = notes.value[index].formattedContent || notes.value[index].content;
};

const saveEditedNote = (noteData) => {
    if (editingNoteIndex.value !== null) {
        // Update the note content
        notes.value[editingNoteIndex.value].content = noteData.content;
        notes.value[editingNoteIndex.value].formattedContent = noteData.formattedContent;

        // Save notes to localStorage
        saveNotesToStorage();

        // Reset editing state
        editingNoteIndex.value = null;
        editingNoteContent.value = '';
    }
};

const cancelEditNote = () => {
    editingNoteIndex.value = null;
    editingNoteContent.value = '';
};

const deleteNote = (index) => {
    if (confirm('Are you sure you want to delete this note?')) {
        notes.value.splice(index, 1);
        saveNotesToStorage();
    }
};

const scrollToReference = (reference) => {
    emit('jump-to-reference', reference);
};

const loadNotesFromStorage = () => {
    try {
        const storedNotes = localStorage.getItem(`lesson_notes_${props.lessonId}`);
        if (storedNotes) {
            notes.value = JSON.parse(storedNotes);
        }
    } catch (error) {
        console.error('Error loading notes from localStorage:', error);
    }
};

const saveNotesToStorage = () => {
    try {
        localStorage.setItem(`lesson_notes_${props.lessonId}`, JSON.stringify(notes.value));
    } catch (error) {
        console.error('Error saving notes to localStorage:', error);
    }
};

// Watch for lesson ID changes to load the appropriate notes
watch(
    () => props.lessonId,
    () => {
        loadNotesFromStorage();
    },
    { immediate: true },
);
</script>

<style scoped>
/* Rich text formatting styles */
.formatted-note {
    line-height: 1.5;
}

/* Make contenteditable placeholder work */
[contenteditable='true']:empty:before {
    content: attr(placeholder);
    color: #9ca3af;
    font-style: italic;
}
</style>
