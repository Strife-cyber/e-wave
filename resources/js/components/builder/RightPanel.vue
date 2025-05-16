<template>
    <div class="right-panel">
        <!-- Panel Tabs -->
        <div class="panel-tabs">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="$emit('set-tab', tab.id)"
                class="panel-tab"
                :class="{ active: activeTab === tab.id }"
                :aria-selected="activeTab === tab.id"
            >
                <component :is="tab.icon" class="panel-tab-icon" />
                <span>{{ tab.label }}</span>
            </button>
        </div>

        <!-- Properties Panel -->
        <div class="panel-content">
            <PropertiesPanel
                v-if="activeTab === 'properties' && selectedElement"
                :is-dark-mode="isDarkMode"
                :selected-element="selectedElement"
                @update-element="$emit('update-element', $event)"
                @delete-element="$emit('delete-element', selectedElement.id)"
                @set-text-align="$emit('set-text-align', $event)"
            />

            <!-- Layers Panel -->
            <LayersPanel
                v-if="activeTab === 'layers'"
                :is-dark-mode="isDarkMode"
                :canvas-elements="canvasElements"
                :selected-element-id="selectedElementId"
                :max-z-index="maxZIndex"
                @select-element="$emit('select-element', $event)"
                @toggle-visibility="$emit('toggle-visibility', $event)"
                @move-element-up="$emit('move-element-up', $event)"
                @move-element-down="$emit('move-element-down', $event)"
                @layer-drag-start="$emit('layer-drag-start', $event)"
                @layer-drop="$emit('layer-drop', $event)"
            />

            <!-- JavaScript Panel -->
            <JavascriptPanel
                v-if="activeTab === 'javascript'"
                :is-dark-mode="isDarkMode"
                :javascript-code="javascriptCode"
                @update-javascript="$emit('update-javascript', $event)"
            />

            <!-- Empty State -->
            <div v-if="activeTab === 'properties' && !selectedElement" class="panel-empty-state">
                <MousePointer class="panel-empty-icon" />
                <p class="panel-empty-text">Select an element to edit its properties</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Code, Layers, MousePointer, Settings } from 'lucide-vue-next';
import JavascriptPanel from './panels/JavascriptPanel.vue';
import LayersPanel from './panels/LayersPanel.vue';
import PropertiesPanel from './panels/PropertiesPanel.vue';

defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    activeTab: {
        type: String,
        default: 'properties',
    },
    selectedElement: {
        type: Object,
        default: null,
    },
    canvasElements: {
        type: Array,
        default: () => [],
    },
    selectedElementId: {
        type: Number,
        default: null,
    },
    javascriptCode: {
        type: String,
        default: '',
    },
    maxZIndex: {
        type: Number,
        default: 0,
    },
});

const tabs = [
    { id: 'properties', label: 'Properties', icon: Settings },
    { id: 'layers', label: 'Layers', icon: Layers },
    { id: 'javascript', label: 'JavaScript', icon: Code },
];

defineEmits([
    'set-tab',
    'update-element',
    'update-javascript',
    'select-element',
    'delete-element',
    'toggle-visibility',
    'move-element-up',
    'move-element-down',
    'set-text-align',
    'layer-drag-start',
    'layer-drop',
]);
</script>

<style scoped>
.right-panel {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    background-color: var(--bg-secondary);
    border-left: 1px solid var(--border-color);
}

@media (min-width: 768px) {
    .right-panel {
        width: 320px;
    }
}

.panel-tabs {
    display: flex;
    border-bottom: 1px solid var(--border-color);
}

.panel-tab {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.75rem 0.5rem;
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
}

.panel-tab:hover {
    background-color: var(--bg-color);
}

.panel-tab.active {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
}

.panel-tab-icon {
    width: 1.25rem;
    height: 1.25rem;
    margin-bottom: 0.25rem;
}

.panel-content {
    flex-grow: 1;
    overflow-y: auto;
    padding: 1rem;
}

.panel-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 200px;
    color: var(--text-secondary);
    text-align: center;
    padding: 2rem 1rem;
}

.panel-empty-icon {
    width: 2.5rem;
    height: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.panel-empty-text {
    font-size: 0.875rem;
    opacity: 0.7;
}
</style>
