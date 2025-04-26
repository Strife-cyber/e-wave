<template>
    <div class="group relative rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
        <!-- Note timestamp -->
        <div class="mb-1 flex justify-between text-xs text-gray-400 dark:text-gray-500">
            <span>{{ formatTimestamp(note.timestamp) }}</span>
            <div class="flex space-x-1 opacity-0 transition-opacity group-hover:opacity-100">
                <button @click="$emit('edit', index)" class="hover:text-purple-600 dark:hover:text-purple-400" title="Edit note">
                    <EditIcon class="h-3 w-3" />
                </button>
                <button @click="$emit('delete', index)" class="hover:text-red-600 dark:hover:text-red-400" title="Delete note">
                    <TrashIcon class="h-3 w-3" />
                </button>
            </div>
        </div>

        <!-- Note content -->
        <div v-if="!isEditing" class="formatted-note text-gray-800 dark:text-gray-200" v-html="note.formattedContent || note.content"></div>

        <!-- Edit mode -->
        <div v-else class="mt-1">
            <!-- Rich Text Formatting Toolbar -->
            <FormattingToolbar />

            <!-- Rich Text Editor -->
            <div
                ref="richTextEditor"
                contenteditable="true"
                class="formatted-note max-h-[300px] min-h-[100px] w-full overflow-y-auto rounded-b-md border border-gray-300 bg-white p-2 text-gray-800 focus:border-transparent focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:focus:ring-purple-400"
                @keydown.ctrl.enter="saveEdit"
                v-html="editContent"
            ></div>

            <div class="mt-2 flex justify-end space-x-2">
                <button
                    @click="$emit('cancel-edit')"
                    class="rounded bg-gray-200 px-3 py-1 text-xs text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button
                    @click="saveEdit"
                    class="rounded bg-purple-600 px-3 py-1 text-xs text-white hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800"
                >
                    Save
                </button>
            </div>
        </div>

        <!-- Note reference (if any) -->
        <div
            v-if="note.reference"
            class="mt-2 cursor-pointer text-xs text-purple-600 hover:underline dark:text-purple-400"
            @click="$emit('jump-to-reference', note.reference)"
        >
            Jump to referenced content
        </div>
    </div>
</template>

<script setup lang="ts">
import DOMPurify from 'dompurify';
import { EditIcon, TrashIcon } from 'lucide-vue-next';
import { nextTick, onMounted, ref, watch } from 'vue';
import FormattingToolbar from './FormattingToolbar.vue';

const props = defineProps({
    note: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        required: true,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
    editContent: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['edit', 'delete', 'save-edit', 'cancel-edit', 'jump-to-reference']);

const richTextEditor = ref(null);

const formatTimestamp = (timestamp) => {
    const date = new Date(timestamp);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

const saveEdit = () => {
    if (richTextEditor.value && richTextEditor.value.innerHTML.trim()) {
        // Sanitize the HTML content
        const sanitizedHtml = DOMPurify.sanitize(richTextEditor.value.innerHTML);

        emit('save-edit', {
            content: sanitizedHtml.replace(/<[^>]*>/g, '').trim(), // Plain text version
            formattedContent: sanitizedHtml, // Formatted HTML version
        });
    }
};

// Focus the editor when in edit mode
watch(
    () => props.isEditing,
    (isEditing) => {
        if (isEditing) {
            nextTick(() => {
                if (richTextEditor.value) {
                    richTextEditor.value.focus();
                }
            });
        }
    },
);

onMounted(() => {
    if (props.isEditing && richTextEditor.value) {
        richTextEditor.value.focus();
    }
});
</script>

<style scoped>
/* Rich text formatting styles */
.formatted-note {
    line-height: 1.5;
}

.formatted-note :deep(h3) {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.formatted-note :deep(p) {
    margin-bottom: 0.75rem;
}

.formatted-note :deep(ul),
.formatted-note :deep(ol) {
    margin-left: 1.5rem;
    margin-bottom: 0.75rem;
}

.formatted-note :deep(ul) {
    list-style-type: disc;
}

.formatted-note :deep(ol) {
    list-style-type: decimal;
}

.formatted-note :deep(blockquote) {
    border-left: 3px solid #d1d5db;
    padding-left: 1rem;
    margin-left: 0;
    margin-right: 0;
    font-style: italic;
    color: #6b7280;
}

:deep(.dark) .formatted-note :deep(blockquote) {
    border-left-color: #4b5563;
    color: #9ca3af;
}

.formatted-note :deep(pre) {
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 0.375rem;
    padding: 0.75rem;
    overflow-x: auto;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
    font-size: 0.875em;
}

:deep(.dark) .formatted-note :deep(pre) {
    background-color: rgba(255, 255, 255, 0.05);
}

.formatted-note :deep(a) {
    color: #8b5cf6;
    text-decoration: underline;
}

:deep(.dark) .formatted-note :deep(a) {
    color: #a78bfa;
}

/* Make contenteditable placeholder work */
[contenteditable='true']:empty:before {
    content: attr(placeholder);
    color: #9ca3af;
    font-style: italic;
}
</style>
