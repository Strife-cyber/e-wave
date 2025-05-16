<template>
    <div class="space-y-4">
        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Drag to reorder layers. Click to select.</p>

        <div class="space-y-1" @dragover.prevent @drop="handleLayerDrop">
            <div
                v-for="element in canvasElements"
                :key="element.id"
                :class="[
                    'flex items-center justify-between rounded border p-2',
                    selectedElementId === element.id
                        ? 'border-purple-500 bg-purple-100 dark:border-purple-400 dark:bg-purple-900'
                        : isDarkMode
                          ? 'border-gray-600 bg-gray-700'
                          : 'border-gray-200 bg-white',
                    'cursor-pointer',
                ]"
                draggable="true"
                @dragstart="$emit('layer-drag-start', $event, element.id)"
                @click="$emit('select-element', element.id)"
                :data-id="element.id"
                role="button"
                :aria-selected="selectedElementId === element.id"
                :aria-label="`Layer: ${getElementName(element)}`"
            >
                <div class="flex items-center gap-2 overflow-hidden">
                    <component :is="getElementIcon(element.type)" class="h-4 w-4 flex-shrink-0 text-purple-500" />
                    <span class="truncate">{{ getElementName(element) }}</span>
                </div>
                <div class="flex items-center">
                    <button
                        @click.stop="$emit('toggle-visibility', element.id)"
                        class="rounded p-1 hover:bg-gray-200 dark:hover:bg-gray-600"
                        :aria-label="element.visible ? 'Hide element' : 'Show element'"
                    >
                        <Eye v-if="element.visible" class="h-4 w-4" />
                        <EyeOff v-else class="h-4 w-4" />
                    </button>
                    <button
                        @click.stop="$emit('move-element-up', element.id)"
                        class="rounded p-1 hover:bg-gray-200 dark:hover:bg-gray-600"
                        :disabled="element.zIndex === maxZIndex"
                        aria-label="Move layer up"
                    >
                        <ChevronUp class="h-4 w-4" :class="element.zIndex === maxZIndex ? 'opacity-30' : ''" />
                    </button>
                    <button
                        @click.stop="$emit('move-element-down', element.id)"
                        class="rounded p-1 hover:bg-gray-200 dark:hover:bg-gray-600"
                        :disabled="element.zIndex === 1"
                        aria-label="Move layer down"
                    >
                        <ChevronDown class="h-4 w-4" :class="element.zIndex === 1 ? 'opacity-30' : ''" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ChevronDown, ChevronUp, Code, Eye, EyeOff, Image as ImageIcon, Layout, Square, TextIcon, Type } from 'lucide-vue-next';

defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    canvasElements: {
        type: Array,
        default: () => [],
    },
    selectedElementId: {
        type: Number,
        default: null,
    },
    maxZIndex: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(['select-element', 'toggle-visibility', 'move-element-up', 'move-element-down', 'layer-drag-start', 'layer-drop']);

function handleLayerDrop(event) {
    const targetElement = event.target.closest('[draggable="true"]');
    if (!targetElement) return;

    const targetId = parseInt(targetElement.getAttribute('data-id') || 0);
    if (!targetId) return;

    emit('layer-drop', event, targetId);
}

function getElementIcon(type) {
    switch (type) {
        case 'heading':
            return Type;
        case 'paragraph':
            return TextIcon;
        case 'image':
            return ImageIcon;
        case 'button':
            return Square;
        case 'custom':
            return Code;
        default:
            return Layout;
    }
}

function getElementName(element) {
    switch (element.type) {
        case 'heading':
            return `H${element.level}: ${element.content.substring(0, 15)}${element.content.length > 15 ? '...' : ''}`;
        case 'paragraph':
            return `P: ${element.content.substring(0, 15)}${element.content.length > 15 ? '...' : ''}`;
        case 'image':
            return 'Image';
        case 'button':
            return `Button: ${element.content.substring(0, 15)}${element.content.length > 15 ? '...' : ''}`;
        case 'custom':
            return 'Custom HTML';
        default:
            return `Element ${element.id}`;
    }
}
</script>
