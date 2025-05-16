<template>
    <div class="editor-canvas-wrapper" @dragover.prevent @drop="handleDrop" @click="$emit('deselect')" @keydown="handleKeyDown" tabindex="0">
        <!-- Grid -->
        <div
            v-if="showGrid"
            class="editor-grid"
            :style="{
                backgroundSize: `${gridSize}px ${gridSize}px`,
                backgroundImage: isDarkMode
                    ? 'linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px)'
                    : 'linear-gradient(to right, rgba(0, 0, 0, 0.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0, 0, 0, 0.05) 1px, transparent 1px)',
            }"
        ></div>

        <div class="editor-canvas" ref="canvas">
            <template v-if="canvasElements.length === 0">
                <div class="editor-empty-state">
                    <Layout class="editor-empty-icon" />
                    <p class="editor-empty-text">Drag elements here to build your page</p>
                    <p class="editor-empty-subtext">Or select a template to get started</p>
                </div>
            </template>

            <div
                v-for="element in canvasElements"
                :key="element.id"
                :class="['editor-element', selectedElementId === element.id ? 'selected' : '', !element.visible && 'hidden']"
                :style="{
                    top: `${element.top}px`,
                    left: `${element.left}px`,
                    width: `${element.width}px`,
                    height: `${element.height}px`,
                    zIndex: element.zIndex,
                }"
                @click.stop="$emit('select-element', element.id)"
                @mousedown="$emit('start-drag', $event, element.id)"
            >
                <component
                    :is="renderElement(element)"
                    :style="element.styles"
                    :src="element.type === 'image' ? element.src : undefined"
                    class="editor-element-content"
                >
                    {{ ['heading', 'paragraph', 'button'].includes(element.type) ? element.content : '' }}
                </component>

                <!-- Element controls (only shown when selected) -->
                <div v-if="selectedElementId === element.id" class="editor-element-controls">
                    <!-- Alignment guides -->
                    <div v-if="showingAlignmentGuides" class="editor-alignment-guides">
                        <div v-if="alignmentGuides.centerX" class="guide-center-x"></div>
                        <div v-if="alignmentGuides.centerY" class="guide-center-y"></div>
                        <div v-if="alignmentGuides.left" class="guide-left"></div>
                        <div v-if="alignmentGuides.right" class="guide-right"></div>
                        <div v-if="alignmentGuides.top" class="guide-top"></div>
                        <div v-if="alignmentGuides.bottom" class="guide-bottom"></div>
                    </div>

                    <!-- Quick actions -->
                    <div class="editor-element-actions">
                        <button
                            @click.stop="$emit('duplicate-element', element.id)"
                            class="editor-element-action"
                            title="Duplicate (Ctrl+D)"
                            aria-label="Duplicate element"
                        >
                            <Copy class="editor-element-action-icon" />
                        </button>
                        <button
                            @click.stop="$emit('delete-element', element.id)"
                            class="editor-element-action delete"
                            title="Delete (Delete)"
                            aria-label="Delete element"
                        >
                            <Trash2 class="editor-element-action-icon" />
                        </button>
                    </div>

                    <!-- Resize handle -->
                    <div class="editor-resize-handle" @mousedown.stop="$emit('start-resize', $event, element.id)"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Copy, Layout, Trash2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

const props = defineProps({
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
    showGrid: {
        type: Boolean,
        default: true,
    },
    gridSize: {
        type: Number,
        default: 20,
    },
    alignmentGuides: {
        type: Object,
        default: () => ({}),
    },
    showingAlignmentGuides: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['drop', 'deselect', 'select-element', 'start-drag', 'start-resize', 'duplicate-element', 'delete-element']);

const canvas = ref(null);

function handleDrop(event) {
    const canvasRect = canvas.value.getBoundingClientRect();
    emit('drop', event, canvasRect);
}

function handleKeyDown(event) {
    // We'll handle keyboard shortcuts in the parent component
    // This is just to ensure the canvas can receive focus for keyboard events
}

// Render element based on type
function renderElement(element) {
    switch (element.type) {
        case 'heading':
            return `h${element.level}`;
        case 'paragraph':
            return 'p';
        case 'image':
            return 'img';
        case 'button':
            return 'button';
        case 'custom':
            return 'div';
        default:
            return 'div';
    }
}

onMounted(() => {
    // Focus the canvas to enable keyboard shortcuts
    canvas.value.focus();
});
</script>

<style scoped>
.editor-canvas-wrapper {
    flex-grow: 1;
    padding: 1rem;
    overflow: auto;
    position: relative;
    background-color: var(--bg-color);
    outline: none;
}

.editor-grid {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.editor-canvas {
    position: relative;
    min-height: 500px;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    background-color: var(--bg-secondary);
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px var(--shadow-color);
}

.editor-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 300px;
    color: var(--text-secondary);
    text-align: center;
}

.editor-empty-icon {
    width: 3rem;
    height: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.editor-empty-text {
    font-size: 1.125rem;
    margin-bottom: 0.5rem;
}

.editor-empty-subtext {
    font-size: 0.875rem;
    opacity: 0.7;
}

.editor-element {
    position: absolute;
    padding: 0.5rem;
    border: 2px solid transparent;
    border-radius: 0.25rem;
    transition: border-color 0.2s;
}

.editor-element:hover {
    border-color: rgba(99, 102, 241, 0.3);
}

.editor-element.selected {
    border-color: var(--primary-color);
}

.editor-element.hidden {
    opacity: 0.4;
}

.editor-element-content {
    pointer-events: none;
}

.editor-element-controls {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.editor-alignment-guides {
    position: absolute;
    inset: 0;
}

.guide-center-x,
.guide-center-y,
.guide-left,
.guide-right,
.guide-top,
.guide-bottom {
    position: absolute;
    background-color: var(--primary-color);
}

.guide-center-x {
    top: 0;
    bottom: 0;
    width: 1px;
    left: 50%;
}

.guide-center-y {
    left: 0;
    right: 0;
    height: 1px;
    top: 50%;
}

.guide-left {
    top: 0;
    bottom: 0;
    width: 1px;
    left: 0;
}

.guide-right {
    top: 0;
    bottom: 0;
    width: 1px;
    right: 0;
}

.guide-top {
    left: 0;
    right: 0;
    height: 1px;
    top: 0;
}

.guide-bottom {
    left: 0;
    right: 0;
    height: 1px;
    bottom: 0;
}

.editor-element-actions {
    position: absolute;
    top: -40px;
    right: 0;
    display: flex;
    gap: 0.25rem;
    pointer-events: auto;
}

.editor-element-action {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 0.25rem;
    background-color: var(--bg-secondary);
    border: 1px solid var(--border-color);
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 3px var(--shadow-color);
}

.editor-element-action:hover {
    background-color: var(--bg-color);
}

.editor-element-action.delete {
    color: var(--error-color);
}

.editor-element-action.delete:hover {
    background-color: var(--error-color);
    color: white;
    border-color: var(--error-color);
}

.editor-element-action-icon {
    width: 1rem;
    height: 1rem;
}

.editor-resize-handle {
    position: absolute;
    bottom: -4px;
    right: -4px;
    width: 12px;
    height: 12px;
    background-color: var(--primary-color);
    border: 2px solid white;
    border-radius: 50%;
    cursor: se-resize;
    pointer-events: auto;
}
</style>
