<template>
    <div class="flex flex-wrap items-center rounded-t-md border border-b-0 border-gray-300 bg-gray-100 p-1 dark:border-gray-600 dark:bg-gray-700">
        <button
            v-for="(tool, i) in formattingTools"
            :key="i"
            @click="applyFormatting(tool.command, tool.value)"
            :title="tool.title"
            class="rounded p-1 text-gray-700 hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-600"
            :class="{ 'bg-gray-200 dark:bg-gray-600': isFormatActive(tool.command, tool.value) }"
        >
            <component :is="tool.icon" class="h-4 w-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
import {
    AlignCenterIcon,
    AlignLeftIcon,
    AlignRightIcon,
    BoldIcon,
    CodeIcon,
    Heading1Icon,
    ItalicIcon,
    LinkIcon,
    ListIcon,
    ListOrderedIcon,
    QuoteIcon,
    UnderlineIcon,
} from 'lucide-vue-next';

const formattingTools = [
    { icon: BoldIcon, command: 'bold', title: 'Bold (Ctrl+B)' },
    { icon: ItalicIcon, command: 'italic', title: 'Italic (Ctrl+I)' },
    { icon: UnderlineIcon, command: 'underline', title: 'Underline (Ctrl+U)' },
    { icon: ListIcon, command: 'insertUnorderedList', title: 'Bullet List' },
    { icon: ListOrderedIcon, command: 'insertOrderedList', title: 'Numbered List' },
    { icon: AlignLeftIcon, command: 'justifyLeft', title: 'Align Left' },
    { icon: AlignCenterIcon, command: 'justifyCenter', title: 'Align Center' },
    { icon: AlignRightIcon, command: 'justifyRight', title: 'Align Right' },
    { icon: Heading1Icon, command: 'formatBlock', value: '<h3>', title: 'Heading' },
    { icon: QuoteIcon, command: 'formatBlock', value: '<blockquote>', title: 'Quote' },
    { icon: LinkIcon, command: 'createLink', title: 'Insert Link' },
    { icon: CodeIcon, command: 'formatBlock', value: '<pre>', title: 'Code Block' },
];

const applyFormatting = (command, value = null) => {
    // Handle special cases
    if (command === 'createLink') {
        const url = prompt('Enter the URL:');
        if (url) {
            document.execCommand(command, false, url);
        }
        return;
    }

    // Apply the formatting command
    document.execCommand(command, false, value);
};

const isFormatActive = (command, value = null) => {
    // Check if a formatting is currently active
    if (command === 'formatBlock' && value) {
        return document.queryCommandValue(command) === value;
    }
    return document.queryCommandState(command);
};
</script>
