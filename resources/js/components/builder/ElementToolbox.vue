<template>
    <div class="element-toolbox">
        <div class="toolbox-section">
            <h2 class="toolbox-heading">Elements</h2>
            <div class="toolbox-elements">
                <div
                    v-for="element in toolboxElements"
                    :key="element.type"
                    class="toolbox-element"
                    draggable="true"
                    @dragstart="$emit('drag-start', $event, element)"
                    role="button"
                    :aria-label="`Drag ${element.label} element`"
                >
                    <component :is="element.icon" class="toolbox-element-icon" />
                    <span class="toolbox-element-label">{{ element.label }}</span>
                </div>

                <!-- Component Library Button -->
                <button @click="$emit('toggle-component-library')" class="toolbox-library-button" aria-label="Open component library">
                    <Package class="toolbox-library-icon" />
                    <span>Component Library</span>
                </button>
            </div>
        </div>

        <!-- Grid Controls -->
        <div class="toolbox-section">
            <h2 class="toolbox-heading">Grid & Alignment</h2>
            <div class="toolbox-controls">
                <div class="toolbox-control">
                    <label class="toolbox-control-label" for="toggle-grid">Show Grid</label>
                    <div class="toolbox-toggle">
                        <input type="checkbox" :checked="showGrid" class="toolbox-toggle-input" id="toggle-grid" @change="$emit('toggle-grid')" />
                        <label for="toggle-grid" class="toolbox-toggle-label" :class="{ active: showGrid }"></label>
                    </div>
                </div>

                <div class="toolbox-control">
                    <label class="toolbox-control-label" for="toggle-snap">Snap to Grid</label>
                    <div class="toolbox-toggle">
                        <input type="checkbox" :checked="snapToGrid" class="toolbox-toggle-input" id="toggle-snap" @change="$emit('toggle-snap')" />
                        <label for="toggle-snap" class="toolbox-toggle-label" :class="{ active: snapToGrid }"></label>
                    </div>
                </div>

                <div class="toolbox-control">
                    <label class="toolbox-control-label" for="grid-size">Grid Size: {{ gridSize }}px</label>
                    <input
                        type="range"
                        id="grid-size"
                        :value="gridSize"
                        min="5"
                        max="50"
                        step="5"
                        class="toolbox-range"
                        @input="$emit('update-grid-size', parseInt($event.target.value))"
                    />
                </div>
            </div>
        </div>

        <!-- History Controls -->
        <div class="toolbox-section">
            <h2 class="toolbox-heading">History</h2>
            <div class="toolbox-history">
                <button @click="$emit('undo')" :disabled="!canUndo" class="toolbox-history-button" :class="{ disabled: !canUndo }" aria-label="Undo">
                    <Undo2 class="toolbox-history-icon" />
                    <span>Undo</span>
                </button>
                <button @click="$emit('redo')" :disabled="!canRedo" class="toolbox-history-button" :class="{ disabled: !canRedo }" aria-label="Redo">
                    <Redo2 class="toolbox-history-icon" />
                    <span>Redo</span>
                </button>
            </div>
        </div>

        <!-- Keyboard Shortcuts -->
        <div class="toolbox-section">
            <button class="toolbox-shortcuts-button" @click="showShortcuts = !showShortcuts">
                <Keyboard class="toolbox-shortcuts-icon" />
                <span>Keyboard Shortcuts</span>
                <ChevronDown class="toolbox-shortcuts-chevron" :class="{ open: showShortcuts }" />
            </button>

            <div v-if="showShortcuts" class="toolbox-shortcuts">
                <div class="toolbox-shortcut">
                    <div class="toolbox-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Z</kbd></div>
                    <div class="toolbox-shortcut-desc">Undo</div>
                </div>
                <div class="toolbox-shortcut">
                    <div class="toolbox-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Y</kbd></div>
                    <div class="toolbox-shortcut-desc">Redo</div>
                </div>
                <div class="toolbox-shortcut">
                    <div class="toolbox-shortcut-keys">
                        <kbd>Delete</kbd>
                    </div>
                    <div class="toolbox-shortcut-desc">Delete element</div>
                </div>
                <div class="toolbox-shortcut">
                    <div class="toolbox-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>D</kbd></div>
                    <div class="toolbox-shortcut-desc">Duplicate element</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ChevronDown, Keyboard, Package, Redo2, Undo2 } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    toolboxElements: {
        type: Array,
        default: () => [],
    },
    showGrid: {
        type: Boolean,
        default: true,
    },
    snapToGrid: {
        type: Boolean,
        default: true,
    },
    gridSize: {
        type: Number,
        default: 20,
    },
    canUndo: {
        type: Boolean,
        default: false,
    },
    canRedo: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['drag-start', 'toggle-component-library', 'toggle-grid', 'toggle-snap', 'update-grid-size', 'undo', 'redo']);

const showShortcuts = ref(false);
</script>

<style scoped>
.element-toolbox {
    width: 100%;
    height: 100%;
    overflow-y: auto;
    background-color: var(--bg-secondary);
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    padding: 1rem;
}

@media (min-width: 768px) {
    .element-toolbox {
        width: 280px;
    }
}

.toolbox-section {
    margin-bottom: 1.5rem;
}

.toolbox-heading {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--text-color);
}

.toolbox-elements {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
}

.toolbox-element {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.75rem;
    background-color: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    cursor: move;
    transition: all 0.2s;
}

.toolbox-element:hover {
    border-color: var(--primary-color);
    box-shadow: 0 2px 4px var(--shadow-color);
}

.toolbox-element-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.toolbox-element-label {
    font-size: 0.875rem;
    text-align: center;
}

.toolbox-library-button {
    grid-column: span 2;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem;
    margin-top: 0.5rem;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s;
}

.toolbox-library-button:hover {
    background-color: var(--primary-hover);
}

.toolbox-library-icon {
    width: 1.25rem;
    height: 1.25rem;
}

.toolbox-controls {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.toolbox-control {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.toolbox-control-label {
    font-size: 0.875rem;
    color: var(--text-color);
}

.toolbox-toggle {
    position: relative;
    width: 2.5rem;
    height: 1.25rem;
}

.toolbox-toggle-input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toolbox-toggle-label {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--border-color);
    border-radius: 1rem;
    transition: 0.4s;
}

.toolbox-toggle-label:before {
    position: absolute;
    content: '';
    height: 0.875rem;
    width: 0.875rem;
    left: 0.25rem;
    bottom: 0.1875rem;
    background-color: white;
    border-radius: 50%;
    transition: 0.4s;
}

.toolbox-toggle-input:checked + .toolbox-toggle-label {
    background-color: var(--primary-color);
}

.toolbox-toggle-input:checked + .toolbox-toggle-label:before {
    transform: translateX(1.125rem);
}

.toolbox-range {
    width: 100%;
    height: 0.5rem;
    background-color: var(--border-color);
    border-radius: 0.25rem;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
    margin-top: 0.5rem;
}

.toolbox-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: var(--primary-color);
    cursor: pointer;
}

.toolbox-range::-moz-range-thumb {
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: var(--primary-color);
    cursor: pointer;
    border: none;
}

.toolbox-history {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.toolbox-history-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.25rem;
    padding: 0.5rem;
    background-color: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.2s;
}

.toolbox-history-button:hover:not(.disabled) {
    border-color: var(--primary-color);
}

.toolbox-history-button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.toolbox-history-icon {
    width: 1rem;
    height: 1rem;
}

.toolbox-shortcuts-button {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 0.75rem;
    background-color: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.2s;
}

.toolbox-shortcuts-button:hover {
    border-color: var(--primary-color);
}

.toolbox-shortcuts-icon {
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.5rem;
}

.toolbox-shortcuts-chevron {
    width: 1rem;
    height: 1rem;
    margin-left: auto;
    transition: transform 0.2s;
}

.toolbox-shortcuts-chevron.open {
    transform: rotate(180deg);
}

.toolbox-shortcuts {
    margin-top: 0.75rem;
    padding: 0.75rem;
    background-color: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
}

.toolbox-shortcut {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}

.toolbox-shortcut:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.toolbox-shortcut-keys {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.toolbox-shortcut-keys kbd {
    display: inline-block;
    padding: 0.25rem 0.375rem;
    font-size: 0.75rem;
    font-family: monospace;
    line-height: 1;
    color: var(--text-color);
    background-color: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 0.25rem;
    box-shadow: 0 1px 0 var(--border-color);
}

.toolbox-shortcut-desc {
    font-size: 0.75rem;
    color: var(--text-secondary);
}
</style>
