<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="$emit('close')">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Export Notes</h3>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <XIcon class="h-5 w-5" />
                </button>
            </div>

            <div class="mb-4">
                <p class="mb-2 text-gray-600 dark:text-gray-300">Select notes to export:</p>

                <div class="mb-2 flex items-center">
                    <input
                        type="checkbox"
                        id="select-all"
                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700"
                        v-model="selectAllNotes"
                        @change="toggleSelectAll"
                    />
                    <label for="select-all" class="ml-2 text-gray-700 dark:text-gray-300">Select All</label>
                </div>

                <div class="max-h-60 overflow-y-auto rounded-md border border-gray-200 p-2 dark:border-gray-700">
                    <div v-for="(note, index) in notes" :key="index" class="flex items-center py-1">
                        <input
                            type="checkbox"
                            :id="`note-${index}`"
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700"
                            v-model="selectedNotes[index]"
                        />
                        <label :for="`note-${index}`" class="ml-2 truncate text-gray-700 dark:text-gray-300">
                            {{ note.content.substring(0, 50) + (note.content.length > 50 ? '...' : '') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button
                    @click="$emit('close')"
                    class="rounded bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    @click="confirmExport"
                    class="flex items-center rounded bg-purple-600 px-4 py-2 text-white hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800"
                    :disabled="isExporting"
                >
                    <span v-if="isExporting" class="mr-2">
                        <LoaderIcon class="h-4 w-4 animate-spin" />
                    </span>
                    Export {{ exportType === 'pdf' ? 'PDF' : 'Markdown' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { LoaderIcon, XIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    notes: {
        type: Array,
        default: () => [],
    },
    exportType: {
        type: String,
        default: 'pdf',
    },
});

const emit = defineEmits(['close', 'export']);

const selectedNotes = ref({});
const selectAllNotes = ref(true);
const isExporting = ref(false);

// Initialize selected notes when modal opens
watch(
    () => props.show,
    (isVisible) => {
        if (isVisible) {
            selectedNotes.value = {};
            props.notes.forEach((_, index) => {
                selectedNotes.value[index] = true;
            });
            selectAllNotes.value = true;
        }
    },
);

// Watch for changes in selected notes to update selectAll state
watch(
    selectedNotes,
    (newVal) => {
        const allSelected = Object.values(newVal).every((val) => val === true);
        const noneSelected = Object.values(newVal).every((val) => val === false);

        if (allSelected) {
            selectAllNotes.value = true;
        } else if (!noneSelected) {
            selectAllNotes.value = false;
        }
    },
    { deep: true },
);

const toggleSelectAll = () => {
    const newValue = selectAllNotes.value;
    props.notes.forEach((_, index) => {
        selectedNotes.value[index] = newValue;
    });
};

const confirmExport = async () => {
    isExporting.value = true;

    try {
        // Get selected notes
        const notesToExport = props.notes.filter((_, index) => selectedNotes.value[index]);

        if (notesToExport.length === 0) {
            alert('Please select at least one note to export.');
            isExporting.value = false;
            return;
        }

        // Emit export event with selected notes
        emit('export', {
            type: props.exportType,
            notes: notesToExport,
        });
    } catch (error) {
        console.error('Error preparing notes for export:', error);
        alert('An error occurred while preparing notes for export. Please try again.');
    } finally {
        isExporting.value = false;
    }
};
</script>
