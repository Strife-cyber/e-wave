<template>
    <header class="app-header">
        <div class="header-container">
            <a href="/" class="header-logo">
                <AppLogo />
                <span class="header-logo-text font-pacifico">Lesson Builder</span>
            </a>

            <div class="header-actions">
                <div class="header-action-group">
                    <button
                        @click="$emit('undo')"
                        :disabled="!canUndo"
                        class="header-button"
                        :class="{ disabled: !canUndo }"
                        aria-label="Undo"
                        title="Undo (Ctrl+Z)"
                    >
                        <Undo2 class="header-button-icon" />
                    </button>
                    <button
                        @click="$emit('redo')"
                        :disabled="!canRedo"
                        class="header-button"
                        :class="{ disabled: !canRedo }"
                        aria-label="Redo"
                        title="Redo (Ctrl+Y)"
                    >
                        <Redo2 class="header-button-icon" />
                    </button>
                </div>

                <div class="header-action-group">
                    <button @click="$emit('toggle-templates')" class="header-button" aria-label="Show templates" title="Templates">
                        <LayoutTemplate class="header-button-icon" />
                        <span class="header-button-text">Templates</span>
                    </button>
                    <button
                        @click="$emit('toggle-preview')"
                        class="header-button"
                        :class="{ active: previewMode }"
                        aria-label="Toggle preview mode"
                        title="Preview (Ctrl+P)"
                    >
                        <Eye v-if="!previewMode" class="header-button-icon" />
                        <Edit v-else class="header-button-icon" />
                        <span class="header-button-text">{{ previewMode ? 'Edit' : 'Preview' }}</span>
                    </button>
                </div>

                <div class="header-action-group">
                    <button @click="$emit('save-project')" class="header-button" aria-label="Save project" title="Save project (Ctrl+S)">
                        <Save class="header-button-icon" />
                        <span class="header-button-text">Save</span>
                    </button>
                    <button @click="$emit('load-project')" class="header-button" aria-label="Load project" title="Load project">
                        <FolderOpen class="header-button-icon" />
                        <span class="header-button-text">Load</span>
                    </button>
                    <button @click="$emit('export-html')" class="header-button primary" aria-label="Export HTML" title="Export HTML">
                        <Download class="header-button-icon" />
                        <span class="header-button-text">Export</span>
                    </button>
                </div>

                <button @click="$emit('toggle-dark-mode')" class="header-button icon-only" aria-label="Toggle dark mode" title="Toggle dark mode">
                    <Sun v-if="isDarkMode" class="header-button-icon" />
                    <Moon v-else class="header-button-icon" />
                </button>
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Download, Edit, Eye, FolderOpen, LayoutTemplate, Moon, Redo2, Save, Sun, Undo2 } from 'lucide-vue-next';

defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    previewMode: {
        type: Boolean,
        default: false,
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

defineEmits(['toggle-dark-mode', 'toggle-templates', 'toggle-preview', 'export-html', 'undo', 'redo', 'save-project', 'load-project']);
</script>

<style scoped>
.app-header {
    background-color: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    box-shadow: 0 1px 3px var(--shadow-color);
    padding: 0.75rem 1rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

.header-container {
    max-width: 1600px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-logo-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: var(--primary-color);
}

.header-logo-text {
    font-weight: 700;
    font-size: 1.25rem;
    color: var(--text-color);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-action-group {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    border-right: 1px solid var(--border-color);
    padding-right: 0.5rem;
    margin-right: 0.5rem;
}

.header-action-group:last-child {
    border-right: none;
    padding-right: 0;
    margin-right: 0;
}

.header-button {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    background-color: transparent;
    border: none;
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.2s;
}

.header-button:hover {
    background-color: var(--bg-color);
}

.header-button.active {
    background-color: var(--primary-color);
    color: white;
}

.header-button.primary {
    background-color: var(--primary-color);
    color: white;
}

.header-button.primary:hover {
    background-color: var(--primary-hover);
}

.header-button.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.header-button-icon {
    width: 1.25rem;
    height: 1.25rem;
}

.header-button-text {
    display: none;
}

.header-button.icon-only {
    padding: 0.5rem;
}

@media (min-width: 640px) {
    .header-button {
        padding: 0.5rem 0.75rem;
    }

    .header-button-text {
        display: inline;
    }
}
</style>
