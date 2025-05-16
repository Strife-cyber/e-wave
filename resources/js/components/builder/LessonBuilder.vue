<template>
    <div class="builder-app" :class="{ 'dark-mode': isDarkMode }">
        <!-- Header -->
        <AppHeader
            :is-dark-mode="isDarkMode"
            :preview-mode="previewMode"
            :can-undo="canUndo"
            :can-redo="canRedo"
            @toggle-dark-mode="toggleDarkMode"
            @toggle-templates="showTemplates = !showTemplates"
            @toggle-preview="togglePreviewMode"
            @export-html="exportHTML"
            @undo="undo"
            @redo="redo"
            @save-project="saveProject"
            @load-project="loadProject"
        />

        <!-- Templates Modal -->
        <TemplatesModal
            v-if="showTemplates"
            :is-dark-mode="isDarkMode"
            :templates="templates"
            @close="showTemplates = false"
            @apply-template="applyTemplate"
        />

        <!-- Component Library Modal -->
        <ComponentLibraryModal
            v-if="showComponentLibrary"
            :is-dark-mode="isDarkMode"
            :component-categories="componentCategories"
            :active-category="activeComponentCategory"
            :filtered-components="filteredComponents"
            @close="showComponentLibrary = false"
            @set-category="activeComponentCategory = $event"
            @add-component="addComponentToCanvas"
        />

        <!-- Main Content -->
        <main class="builder-main">
            <!-- Toolbox (hidden in preview mode) -->
            <ElementToolbox
                v-if="!previewMode"
                :is-dark-mode="isDarkMode"
                :toolbox-elements="toolboxElements"
                :show-grid="showGrid"
                :snap-to-grid="snapToGrid"
                :grid-size="gridSize"
                :can-undo="canUndo"
                :can-redo="canRedo"
                @drag-start="onDragStart"
                @toggle-component-library="showComponentLibrary = true"
                @toggle-grid="showGrid = !showGrid"
                @toggle-snap="snapToGrid = !snapToGrid"
                @update-grid-size="gridSize = $event"
                @undo="undo"
                @redo="redo"
            />

            <!-- Canvas and Preview -->
            <div class="builder-canvas-container">
                <!-- Responsive Preview Controls (shown in preview mode) -->
                <ResponsiveControls
                    v-if="previewMode"
                    :is-dark-mode="isDarkMode"
                    :preview-devices="previewDevices"
                    :current-device="currentPreviewDevice"
                    :is-landscape="isLandscape"
                    @set-device="currentPreviewDevice = $event"
                    @toggle-orientation="toggleOrientation"
                />

                <!-- Canvas (hidden in preview mode) -->
                <EditorCanvas
                    v-if="!previewMode"
                    :is-dark-mode="isDarkMode"
                    :canvas-elements="canvasElements"
                    :selected-element-id="selectedElementId"
                    :show-grid="showGrid"
                    :grid-size="gridSize"
                    :alignment-guides="alignmentGuides"
                    :showing-alignment-guides="showingAlignmentGuides"
                    @drop="onDrop"
                    @deselect="selectedElementId = null"
                    @select-element="selectElement"
                    @start-drag="startDrag"
                    @start-resize="startResize"
                    @duplicate-element="duplicateElement"
                    @delete-element="deleteElement"
                    ref="canvasContainer"
                />

                <!-- Preview (shown in preview mode) -->
                <PagePreview
                    v-if="previewMode"
                    :is-dark-mode="isDarkMode"
                    :preview-width="previewWidth"
                    :preview-height="previewHeight"
                    :is-landscape="isLandscape"
                    :current-device="currentPreviewDevice"
                    :html-content="generateHTML(true)"
                />
            </div>

            <!-- Right Panel (Properties or Layers) -->
            <RightPanel
                v-if="!previewMode"
                :is-dark-mode="isDarkMode"
                :active-tab="activeTab"
                :selected-element="selectedElement"
                :canvas-elements="sortedCanvasElements"
                :selected-element-id="selectedElementId"
                :javascript-code="javascriptCode"
                :max-z-index="maxZIndex"
                @set-tab="activeTab = $event"
                @update-element="updateElement"
                @update-javascript="updateJavascript"
                @select-element="selectElement"
                @delete-element="deleteElement"
                @toggle-visibility="toggleElementVisibility"
                @move-element-up="moveElementUp"
                @move-element-down="moveElementDown"
                @set-text-align="setTextAlign"
                @layer-drag-start="onLayerDragStart"
                @layer-drop="onLayerDrop"
            />
        </main>

        <!-- Notification Toast -->
        <div v-if="notification.show" class="notification-toast" :class="notification.type">
            <div class="notification-content">
                <CheckCircle v-if="notification.type === 'success'" class="notification-icon" />
                <AlertCircle v-else-if="notification.type === 'error'" class="notification-icon" />
                <Info v-else class="notification-icon" />
                <span>{{ notification.message }}</span>
            </div>
            <button @click="hideNotification" class="notification-close" aria-label="Close notification">
                <X class="notification-close-icon" />
            </button>
        </div>

        <!-- Keyboard Shortcuts Help Modal -->
        <div v-if="showKeyboardShortcuts" class="keyboard-shortcuts-modal">
            <div class="keyboard-shortcuts-content">
                <div class="keyboard-shortcuts-header">
                    <h2>Keyboard Shortcuts</h2>
                    <button @click="showKeyboardShortcuts = false" class="keyboard-shortcuts-close" aria-label="Close keyboard shortcuts">
                        <X />
                    </button>
                </div>
                <div class="keyboard-shortcuts-body">
                    <div class="keyboard-shortcut">
                        <div class="keyboard-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Z</kbd></div>
                        <div class="keyboard-shortcut-description">Undo</div>
                    </div>
                    <div class="keyboard-shortcut">
                        <div class="keyboard-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>Y</kbd></div>
                        <div class="keyboard-shortcut-description">Redo</div>
                    </div>
                    <div class="keyboard-shortcut">
                        <div class="keyboard-shortcut-keys">
                            <kbd>Delete</kbd>
                        </div>
                        <div class="keyboard-shortcut-description">Delete selected element</div>
                    </div>
                    <div class="keyboard-shortcut">
                        <div class="keyboard-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>D</kbd></div>
                        <div class="keyboard-shortcut-description">Duplicate selected element</div>
                    </div>
                    <div class="keyboard-shortcut">
                        <div class="keyboard-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>S</kbd></div>
                        <div class="keyboard-shortcut-description">Save project</div>
                    </div>
                    <div class="keyboard-shortcut">
                        <div class="keyboard-shortcut-keys"><kbd>Ctrl</kbd> + <kbd>P</kbd></div>
                        <div class="keyboard-shortcut-description">Toggle preview mode</div>
                    </div>
                    <div class="keyboard-shortcut">
                        <div class="keyboard-shortcut-keys">
                            <kbd>?</kbd>
                        </div>
                        <div class="keyboard-shortcut-description">Show keyboard shortcuts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import AppHeader from './AppHeader.vue';
import ComponentLibraryModal from './ComponentLibraryModal.vue';
import EditorCanvas from './EditorCanvas.vue';
import ElementToolbox from './ElementToolbox.vue';
import PagePreview from './PagePreview.vue';
import ResponsiveControls from './ResponsiveControls.vue';
import RightPanel from './RightPanel.vue';
import TemplatesModal from './TemplatesModal.vue';

// Import icons
import {
    AlertCircle,
    CheckCircle,
    Code,
    Image as ImageIcon,
    Info,
    LayoutGrid,
    Monitor,
    Smartphone,
    Square,
    Tablet,
    TextIcon,
    Type,
    X,
} from 'lucide-vue-next';

// ==================== STATE MANAGEMENT ====================

// Theme state
const isDarkMode = ref(localStorage.getItem('builderDarkMode') === 'true');

// Canvas elements
const canvasElements = ref([]);
const nextId = ref(1);
const selectedElementId = ref(null);
const previewMode = ref(false);
const showTemplates = ref(false);
const showComponentLibrary = ref(false);
const activeTab = ref('properties');
const activeComponentCategory = ref('navigation');
const javascriptCode = ref(`// Add your JavaScript code here

document.addEventListener("DOMContentLoaded", function() {
  console.log("Page loaded!");

  // Example: Add click event to buttons
  const buttons = document.querySelectorAll("button");
  buttons.forEach(button => {
    button.addEventListener("click", function() {
      console.log("Button clicked:", this.textContent);
    });
  });
});`);

// Grid and alignment
const showGrid = ref(true);
const snapToGrid = ref(true);
const gridSize = ref(20);
const alignmentGuides = ref({
    centerX: false,
    centerY: false,
    left: false,
    right: false,
    top: false,
    bottom: false,
});
const showingAlignmentGuides = ref(false);

// Responsive preview
const currentPreviewDevice = ref('desktop');
const isLandscape = ref(false);

// History for undo/redo - improved implementation
const history = ref([]);
const historyIndex = ref(-1);
const isUndoRedo = ref(false);
const MAX_HISTORY_LENGTH = 50; // Limit history length to prevent memory issues

// Notification system
const notification = ref({
    show: false,
    message: '',
    type: 'info', // 'info', 'success', 'error'
    timeout: null,
});

// Keyboard shortcuts
const showKeyboardShortcuts = ref(false);

// Dragging state
const isDragging = ref(false);
const dragStartX = ref(0);
const dragStartY = ref(0);
const elementStartX = ref(0);
const elementStartY = ref(0);

// Layer dragging state
const draggedLayerId = ref(null);

// Resizing state
const isResizing = ref(false);
const resizeStartWidth = ref(0);
const resizeStartHeight = ref(0);

// Canvas and container refs
const canvasContainer = ref(null);

// Project state
const projectName = ref('Untitled Project');
const lastSaved = ref(null);
const isModified = ref(false);

// ==================== COMPUTED PROPERTIES ====================

// Get the selected element
const selectedElement = computed(() => {
    return canvasElements.value.find((el) => el.id === selectedElementId.value);
});

// Sorted canvas elements (for layers panel)
const sortedCanvasElements = computed(() => {
    return [...canvasElements.value].sort((a, b) => b.zIndex - a.zIndex);
});

// Max z-index
const maxZIndex = computed(() => {
    if (canvasElements.value.length === 0) return 0;
    return Math.max(...canvasElements.value.map((el) => el.zIndex));
});

// Undo/redo availability
const canUndo = computed(() => historyIndex.value > 0);
const canRedo = computed(() => historyIndex.value < history.value.length - 1);

// Filtered components based on active category
const filteredComponents = computed(() => {
    return componentLibrary.filter((component) => component.category === activeComponentCategory.value);
});

// Preview dimensions based on selected device
const previewWidth = computed(() => {
    const device = previewDevices.find((d) => d.id === currentPreviewDevice.value);
    return isLandscape.value && device.id !== 'desktop' ? device.height : device.width;
});

const previewHeight = computed(() => {
    const device = previewDevices.find((d) => d.id === currentPreviewDevice.value);
    return isLandscape.value && device.id !== 'desktop' ? device.width : device.height;
});

// ==================== WATCHERS ====================

// Watch for changes to save to history
watch(
    [canvasElements, javascriptCode],
    () => {
        if (!isUndoRedo.value) {
            recordChange();
            isModified.value = true;
        }
    },
    { deep: true },
);

// Save dark mode preference
watch(isDarkMode, (newValue) => {
    localStorage.setItem('builderDarkMode', newValue);
});

// ==================== LIFECYCLE HOOKS ====================

// Initialize history and load from localStorage if available
onMounted(() => {
    // Try to load last project from localStorage
    const savedState = localStorage.getItem('builderLastProject');
    if (savedState) {
        try {
            const parsedState = JSON.parse(savedState);
            canvasElements.value = parsedState.canvasElements || [];
            javascriptCode.value = parsedState.javascriptCode || '';
            projectName.value = parsedState.projectName || 'Untitled Project';
            nextId.value = parsedState.nextId || 1;
            lastSaved.value = parsedState.lastSaved ? new Date(parsedState.lastSaved) : null;

            // Initialize history with the loaded state
            recordChange();
            isModified.value = false;

            showNotification('Project loaded from local storage', 'success');
        } catch (error) {
            console.error('Error loading project from localStorage:', error);
            // Initialize with empty state
            recordChange();
        }
    } else {
        // Initialize with empty state
        recordChange();
    }

    // Set up keyboard shortcuts
    document.addEventListener('keydown', handleKeyDown);

    // Set up auto-save
    const autoSaveInterval = setInterval(() => {
        if (isModified.value) {
            saveProject(true); // Auto-save
        }
    }, 60000); // Auto-save every minute if modified

    // Clean up on unmount
    onUnmounted(() => {
        document.removeEventListener('keydown', handleKeyDown);
        clearInterval(autoSaveInterval);
        if (notification.value.timeout) {
            clearTimeout(notification.value.timeout);
        }
    });
});

// ==================== METHODS ====================

// Keyboard shortcuts
function handleKeyDown(event) {
    // Don't trigger shortcuts when typing in inputs or textareas
    if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
        return;
    }

    // Undo: Ctrl+Z
    if (event.ctrlKey && event.key === 'z') {
        event.preventDefault();
        if (canUndo.value) undo();
    }

    // Redo: Ctrl+Y
    if (event.ctrlKey && event.key === 'y') {
        event.preventDefault();
        if (canRedo.value) redo();
    }

    // Delete selected element: Delete
    if (event.key === 'Delete' && selectedElementId.value) {
        event.preventDefault();
        deleteElement(selectedElementId.value);
    }

    // Duplicate selected element: Ctrl+D
    if (event.ctrlKey && event.key === 'd' && selectedElementId.value) {
        event.preventDefault();
        duplicateElement(selectedElementId.value);
    }

    // Save project: Ctrl+S
    if (event.ctrlKey && event.key === 's') {
        event.preventDefault();
        saveProject();
    }

    // Toggle preview mode: Ctrl+P
    if (event.ctrlKey && event.key === 'p') {
        event.preventDefault();
        togglePreviewMode();
    }

    // Show keyboard shortcuts: ?
    if (event.key === '?') {
        event.preventDefault();
        showKeyboardShortcuts.value = true;
    }
}

// Theme toggle
function toggleDarkMode() {
    isDarkMode.value = !isDarkMode.value;
}

// Toggle preview mode
function togglePreviewMode() {
    previewMode.value = !previewMode.value;
}

// Notification system
function showNotification(message, type = 'info', duration = 3000) {
    // Clear any existing timeout
    if (notification.value.timeout) {
        clearTimeout(notification.value.timeout);
    }

    // Show new notification
    notification.value = {
        show: true,
        message,
        type,
        timeout: setTimeout(() => {
            hideNotification();
        }, duration),
    };
}

function hideNotification() {
    notification.value.show = false;
    if (notification.value.timeout) {
        clearTimeout(notification.value.timeout);
        notification.value.timeout = null;
    }
}

// Project management
function saveProject(isAutoSave = false) {
    const currentState = {
        canvasElements: canvasElements.value,
        javascriptCode: javascriptCode.value,
        projectName: projectName.value,
        nextId: nextId.value,
        lastSaved: new Date().toISOString(),
    };

    // Save to localStorage
    localStorage.setItem('builderLastProject', JSON.stringify(currentState));

    // Update last saved timestamp
    lastSaved.value = new Date();
    isModified.value = false;

    if (!isAutoSave) {
        showNotification('Project saved successfully', 'success');
    }
}

function loadProject() {
    // In a real app, this would open a file picker or project list
    // For now, we'll just reload the last saved project
    const savedState = localStorage.getItem('builderLastProject');
    if (savedState) {
        try {
            const parsedState = JSON.parse(savedState);
            canvasElements.value = parsedState.canvasElements || [];
            javascriptCode.value = parsedState.javascriptCode || '';
            projectName.value = parsedState.projectName || 'Untitled Project';
            nextId.value = parsedState.nextId || 1;
            lastSaved.value = parsedState.lastSaved ? new Date(parsedState.lastSaved) : null;

            // Reset history with the loaded state
            history.value = [];
            historyIndex.value = -1;
            recordChange();
            isModified.value = false;

            showNotification('Project loaded successfully', 'success');
        } catch (error) {
            console.error('Error loading project:', error);
            showNotification('Error loading project', 'error');
        }
    } else {
        showNotification('No saved project found', 'error');
    }
}

// History management - improved implementation
function recordChange() {
    if (isUndoRedo.value) return;

    // Create a deep copy of the current state
    const currentState = {
        canvasElements: JSON.parse(JSON.stringify(canvasElements.value)),
        javascriptCode: javascriptCode.value,
    };

    // Truncate history if we're not at the end
    if (historyIndex.value < history.value.length - 1) {
        history.value = history.value.slice(0, historyIndex.value + 1);
    }

    // Add current state to history
    history.value.push(currentState);
    historyIndex.value = history.value.length - 1;

    // Limit history length to prevent memory issues
    if (history.value.length > MAX_HISTORY_LENGTH) {
        history.value = history.value.slice(history.value.length - MAX_HISTORY_LENGTH);
        historyIndex.value = history.value.length - 1;
    }
}

function undo() {
    if (!canUndo.value) return;

    isUndoRedo.value = true;
    historyIndex.value--;

    const state = history.value[historyIndex.value];
    canvasElements.value = JSON.parse(JSON.stringify(state.canvasElements));
    javascriptCode.value = state.javascriptCode;

    // If the selected element no longer exists, deselect it
    if (selectedElementId.value && !canvasElements.value.find((el) => el.id === selectedElementId.value)) {
        selectedElementId.value = null;
    }

    isUndoRedo.value = false;
}

function redo() {
    if (!canRedo.value) return;

    isUndoRedo.value = true;
    historyIndex.value++;

    const state = history.value[historyIndex.value];
    canvasElements.value = JSON.parse(JSON.stringify(state.canvasElements));
    javascriptCode.value = state.javascriptCode;

    // If the selected element no longer exists, deselect it
    if (selectedElementId.value && !canvasElements.value.find((el) => el.id === selectedElementId.value)) {
        selectedElementId.value = null;
    }

    isUndoRedo.value = false;
}

// Element management
function updateElement(updatedElement) {
    const index = canvasElements.value.findIndex((el) => el.id === updatedElement.id);
    if (index !== -1) {
        canvasElements.value[index] = updatedElement;
    }
}

function updateJavascript(code) {
    javascriptCode.value = code;
}

// Duplicate element
function duplicateElement(id) {
    const element = canvasElements.value.find((el) => el.id === id);
    if (!element) return;

    // Create a deep copy of the element
    const newElement = JSON.parse(JSON.stringify(element));
    newElement.id = nextId.value++;
    newElement.top += 20; // Offset slightly to make it visible
    newElement.left += 20;
    newElement.zIndex = maxZIndex.value + 1;

    canvasElements.value.push(newElement);
    selectedElementId.value = newElement.id;
    recordChange();

    showNotification('Element duplicated', 'success');
}

// Drag and drop handlers
function onDragStart(event, element) {
    event.dataTransfer.setData('text/plain', element.type);
    event.dataTransfer.effectAllowed = 'copy';
}

function onDrop(event, canvasRect) {
    const type = event.dataTransfer.getData('text/plain');
    const toolboxElement = toolboxElements.find((el) => el.type === type);

    if (toolboxElement) {
        let x = event.clientX - canvasRect.left;
        let y = event.clientY - canvasRect.top;

        // Apply grid snapping if enabled
        if (snapToGrid.value) {
            x = Math.round(x / gridSize.value) * gridSize.value;
            y = Math.round(y / gridSize.value) * gridSize.value;
        }

        // Create a new element
        const newElement = createNewElement(toolboxElement, x, y);

        canvasElements.value.push(newElement);
        selectedElementId.value = newElement.id;
        recordChange();
    }
}

function createNewElement(toolboxElement, x, y) {
    const newElement = {
        id: nextId.value++,
        type: toolboxElement.type,
        top: y,
        left: x,
        width: 200,
        height: toolboxElement.type === 'image' ? 150 : 'auto',
        zIndex: maxZIndex.value + 1,
        visible: true,
        responsive: 'fixed',
        styles: {
            color: isDarkMode.value ? '#ffffff' : '#000000',
            backgroundColor: toolboxElement.type === 'button' ? '#6366f1' : 'transparent',
            fontSize: toolboxElement.type === 'heading' ? '24' : '16',
            fontWeight: toolboxElement.type === 'heading' ? 'bold' : 'normal',
            textAlign: 'left',
            paddingTop: 8,
            paddingRight: 8,
            paddingBottom: 8,
            paddingLeft: 8,
            borderWidth: toolboxElement.type === 'button' ? 1 : 0,
            borderStyle: 'solid',
            borderColor: '#e2e8f0',
            borderRadius: toolboxElement.type === 'button' ? 4 : 0,
        },
    };

    // Add type-specific properties
    if (toolboxElement.type === 'heading') {
        newElement.content = toolboxElement.defaultContent;
        newElement.level = 2;
    } else if (toolboxElement.type === 'paragraph') {
        newElement.content = toolboxElement.defaultContent;
    } else if (toolboxElement.type === 'image') {
        newElement.src = toolboxElement.defaultSrc;
    } else if (toolboxElement.type === 'button') {
        newElement.content = toolboxElement.defaultContent;
        newElement.styles.color = '#ffffff';
    } else if (toolboxElement.type === 'custom') {
        newElement.content = toolboxElement.defaultContent;
    }

    return newElement;
}

// Add component to canvas
function addComponentToCanvas(component) {
    // Create a new element
    const newElement = {
        id: nextId.value++,
        type: 'custom',
        top: 100,
        left: 100,
        width: 400,
        height: 'auto',
        zIndex: maxZIndex.value + 1,
        visible: true,
        responsive: 'fluid',
        content: component.content,
        styles: {
            color: '#000000',
            backgroundColor: 'transparent',
            fontSize: '16',
            fontWeight: 'normal',
            textAlign: 'left',
            paddingTop: 0,
            paddingRight: 0,
            paddingBottom: 0,
            paddingLeft: 0,
            borderWidth: 0,
            borderStyle: 'none',
            borderColor: '#000000',
            borderRadius: 0,
        },
    };

    canvasElements.value.push(newElement);
    selectedElementId.value = newElement.id;
    showComponentLibrary.value = false;
    recordChange();

    showNotification(`Added ${component.name} component`, 'success');
}

// Layer panel drag and drop
function onLayerDragStart(event, id) {
    draggedLayerId.value = id;
    event.dataTransfer.setData('text/plain', id);
    event.dataTransfer.effectAllowed = 'move';
}

function onLayerDrop(event, targetId) {
    if (!targetId || targetId === draggedLayerId.value) return;

    const draggedElement = canvasElements.value.find((el) => el.id === draggedLayerId.value);
    const targetElement = canvasElements.value.find((el) => el.id === targetId);

    if (draggedElement && targetElement) {
        // Swap z-indices
        const temp = draggedElement.zIndex;
        draggedElement.zIndex = targetElement.zIndex;
        targetElement.zIndex = temp;

        recordChange();
    }

    draggedLayerId.value = null;
}

// Element selection
function selectElement(id) {
    selectedElementId.value = id;
    activeTab.value = 'properties';
}

// Element dragging
function startDrag(event, id) {
    if (isResizing.value) return;

    const element = canvasElements.value.find((el) => el.id === id);
    if (!element) return;

    isDragging.value = true;
    dragStartX.value = event.clientX;
    dragStartY.value = event.clientY;
    elementStartX.value = element.left;
    elementStartY.value = element.top;

    document.addEventListener('mousemove', onDragMove);
    document.addEventListener('mouseup', stopDrag);
}

function onDragMove(event) {
    if (!isDragging.value) return;

    const element = canvasElements.value.find((el) => el.id === selectedElementId.value);
    if (!element) return;

    const dx = event.clientX - dragStartX.value;
    const dy = event.clientY - dragStartY.value;

    let newLeft = elementStartX.value + dx;
    let newTop = elementStartY.value + dy;

    // Apply grid snapping if enabled
    if (snapToGrid.value) {
        newLeft = Math.round(newLeft / gridSize.value) * gridSize.value;
        newTop = Math.round(newTop / gridSize.value) * gridSize.value;
    }

    element.left = newLeft;
    element.top = newTop;

    // Check for alignment with other elements
    checkAlignment(element);
}

function checkAlignment(element) {
    // Reset alignment guides
    Object.keys(alignmentGuides.value).forEach((key) => {
        alignmentGuides.value[key] = false;
    });

    if (!snapToGrid.value) return;

    const threshold = 5; // Snap threshold in pixels
    const elementCenterX = element.left + element.width / 2;
    const elementCenterY = element.top + (element.height === 'auto' ? 0 : element.height / 2);

    // Check alignment with other elements
    canvasElements.value.forEach((otherElement) => {
        if (otherElement.id === element.id) return;

        const otherCenterX = otherElement.left + otherElement.width / 2;
        const otherCenterY = otherElement.top + (otherElement.height === 'auto' ? 0 : otherElement.height / 2);

        // Center X alignment
        if (Math.abs(elementCenterX - otherCenterX) < threshold) {
            element.left = otherElement.left + (otherElement.width - element.width) / 2;
            alignmentGuides.value.centerX = true;
        }

        // Center Y alignment
        if (Math.abs(elementCenterY - otherCenterY) < threshold) {
            if (element.height !== 'auto' && otherElement.height !== 'auto') {
                element.top = otherElement.top + (otherElement.height - element.height) / 2;
                alignmentGuides.value.centerY = true;
            }
        }

        // Left alignment
        if (Math.abs(element.left - otherElement.left) < threshold) {
            element.left = otherElement.left;
            alignmentGuides.value.left = true;
        }

        // Right alignment
        if (Math.abs(element.left + element.width - (otherElement.left + otherElement.width)) < threshold) {
            element.left = otherElement.left + otherElement.width - element.width;
            alignmentGuides.value.right = true;
        }

        // Top alignment
        if (Math.abs(element.top - otherElement.top) < threshold) {
            element.top = otherElement.top;
            alignmentGuides.value.top = true;
        }

        // Bottom alignment
        if (element.height !== 'auto' && otherElement.height !== 'auto') {
            if (Math.abs(element.top + element.height - (otherElement.top + otherElement.height)) < threshold) {
                element.top = otherElement.top + otherElement.height - element.height;
                alignmentGuides.value.bottom = true;
            }
        }
    });

    // Show alignment guides if any are active
    showingAlignmentGuides.value = Object.values(alignmentGuides.value).some((value) => value);
}

function stopDrag() {
    if (isDragging.value) {
        recordChange();
        showingAlignmentGuides.value = false;
    }

    isDragging.value = false;
    document.removeEventListener('mousemove', onDragMove);
    document.removeEventListener('mouseup', stopDrag);
}

// Element resizing
function startResize(event, id) {
    const element = canvasElements.value.find((el) => el.id === id);
    if (!element) return;

    isResizing.value = true;
    dragStartX.value = event.clientX;
    dragStartY.value = event.clientY;
    resizeStartWidth.value = element.width;
    resizeStartHeight.value = element.height;

    document.addEventListener('mousemove', onResizeMove);
    document.addEventListener('mouseup', stopResize);

    event.preventDefault();
    event.stopPropagation();
}

function onResizeMove(event) {
    if (!isResizing.value) return;

    const element = canvasElements.value.find((el) => el.id === selectedElementId.value);
    if (!element) return;

    const dx = event.clientX - dragStartX.value;
    const dy = event.clientY - dragStartY.value;

    let newWidth = Math.max(50, resizeStartWidth.value + dx);

    // Apply grid snapping if enabled
    if (snapToGrid.value) {
        newWidth = Math.round(newWidth / gridSize.value) * gridSize.value;
    }

    element.width = newWidth;

    if (element.height !== 'auto') {
        let newHeight = Math.max(50, resizeStartHeight.value + dy);

        // Apply grid snapping if enabled
        if (snapToGrid.value) {
            newHeight = Math.round(newHeight / gridSize.value) * gridSize.value;
        }

        element.height = newHeight;
    }
}

function stopResize() {
    if (isResizing.value) {
        recordChange();
    }

    isResizing.value = false;
    document.removeEventListener('mousemove', onResizeMove);
    document.removeEventListener('mouseup', stopResize);
}

// Delete element
function deleteElement(id) {
    const index = canvasElements.value.findIndex((el) => el.id === id);
    if (index !== -1) {
        canvasElements.value.splice(index, 1);
        selectedElementId.value = null;
        recordChange();
        showNotification('Element deleted', 'info');
    }
}

// Layer panel functions
function toggleElementVisibility(id) {
    const element = canvasElements.value.find((el) => el.id === id);
    if (element) {
        element.visible = !element.visible;
        recordChange();
    }
}

function moveElementUp(id) {
    const element = canvasElements.value.find((el) => el.id === id);
    if (!element || element.zIndex === maxZIndex.value) return;

    const aboveElement = canvasElements.value.find((el) => el.zIndex === element.zIndex + 1);
    if (aboveElement) {
        aboveElement.zIndex--;
        element.zIndex++;
        recordChange();
    }
}

function moveElementDown(id) {
    const element = canvasElements.value.find((el) => el.id === id);
    if (!element || element.zIndex === 1) return;

    const belowElement = canvasElements.value.find((el) => el.zIndex === element.zIndex - 1);
    if (belowElement) {
        belowElement.zIndex++;
        element.zIndex--;
        recordChange();
    }
}

// Style functions
function setTextAlign(align) {
    if (selectedElement.value) {
        selectedElement.value.styles.textAlign = align;
        recordChange();
    }
}

// Template functions
function applyTemplate(template) {
    // Clear canvas
    canvasElements.value = [];

    // Clone template elements and assign new IDs
    template.elements.forEach((element) => {
        const newElement = JSON.parse(JSON.stringify(element));
        newElement.id = nextId.value++;
        canvasElements.value.push(newElement);
    });

    showTemplates.value = false;
    selectedElementId.value = null;
    recordChange();

    showNotification(`Applied ${template.name} template`, 'success');
}

// Responsive preview functions
function toggleOrientation() {
    isLandscape.value = !isLandscape.value;
}

// Generate HTML for preview and export
function generateHTML(isPreview = false) {
    // Sort elements by z-index
    const sortedElements = [...canvasElements.value].filter((element) => element.visible).sort((a, b) => a.zIndex - b.zIndex);

    // Generate HTML for each element
    const elementsHTML = sortedElements
        .map((element) => {
            // Apply responsive behavior
            let responsiveStyles = '';
            if (element.responsive === 'fluid') {
                responsiveStyles = 'max-width: 100%;';
            } else if (element.responsive === 'hidden-mobile') {
                responsiveStyles = '@media (max-width: 768px) { display: none !important; }';
            }

            const styles = Object.entries(element.styles)
                .map(([key, value]) => {
                    // Convert camelCase to kebab-case
                    const cssKey = key.replace(/([A-Z])/g, '-$1').toLowerCase();
                    return `${cssKey}: ${value}${typeof value === 'number' ? 'px' : ''};`;
                })
                .join(' ');

            // Add position styles for preview
            const positionStyles = isPreview
                ? `position: absolute; top: ${element.top}px; left: ${element.left}px; width: ${element.width}px; ${element.height !== 'auto' ? `height: ${element.height}px;` : ''}`
                : '';

            const combinedStyles = `${styles} ${positionStyles} ${responsiveStyles}`;

            switch (element.type) {
                case 'heading':
                    return `<h${element.level} style="${combinedStyles}">${element.content}</h${element.level}>`;
                case 'paragraph':
                    return `<p style="${combinedStyles}">${element.content}</p>`;
                case 'image':
                    return `<img src="${element.src}" alt="Image" style="${combinedStyles}">`;
                case 'button':
                    return `<button style="${combinedStyles}">${element.content}</button>`;
                case 'custom':
                    if (isPreview) {
                        return `<div style="${positionStyles}">${element.content}</div>`;
                    } else {
                        return element.content;
                    }
                default:
                    return '';
            }
        })
        .join('\n');

    if (isPreview) {
        // For iframe preview
        return `
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preview</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      margin: 0;
      padding: 0;
      position: relative;
      min-height: 100vh;
      ${isDarkMode.value ? 'background-color: #1f2937; color: #f9fafb;' : 'background-color: #ffffff; color: #111827;'}
    }
    * {
      box-sizing: border-box;
    }
    @media (max-width: 768px) {
      [data-responsive="hidden-mobile"] {
        display: none !important;
      }
    }
  </style>
</head>
<body>
${elementsHTML}
<script>
${javascriptCode.value}
<script>
</body>
</html>
`;
    } else {
        // Full HTML document for export
        return `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${projectName.value}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        * {
            box-sizing: border-box;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            min-height: 100vh;
        }
        @media (max-width: 768px) {
            [data-responsive="hidden-mobile"] {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="container">
    ${elementsHTML}
</div>
<script>
    ${javascriptCode.value}
<script>
</body>
</html>
`;
    }
}

// Export HTML
function exportHTML() {
    const html = generateHTML(false);

    const blob = new Blob([html], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${projectName.value.replace(/\s+/g, '-').toLowerCase()}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    showNotification('HTML exported successfully', 'success');
}

// ==================== DATA DEFINITIONS ====================

// Toolbox elements
const toolboxElements = [
    { type: 'heading', label: 'Heading', icon: Type, defaultContent: 'Heading Text' },
    {
        type: 'paragraph',
        label: 'Paragraph',
        icon: TextIcon,
        defaultContent: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies.',
    },
    { type: 'image', label: 'Image', icon: ImageIcon, defaultSrc: 'https://via.placeholder.com/300x200' },
    { type: 'button', label: 'Button', icon: Square, defaultContent: 'Click Me' },
    { type: 'custom', label: 'Custom HTML', icon: Code, defaultContent: '<div>Custom HTML</div>' },
];

// Component Library Categories
const componentCategories = [
    { id: 'navigation', name: 'Navigation' },
    { id: 'cards', name: 'Cards' },
    { id: 'forms', name: 'Forms' },
    { id: 'media', name: 'Media' },
    { id: 'ecommerce', name: 'E-commerce' },
];

// Component Library (abbreviated for brevity)
const componentLibrary = [
    // Navigation Components
    {
        id: 'navbar',
        name: 'Navigation Bar',
        category: 'navigation',
        description: 'Responsive navigation bar with logo and links',
        preview: `<nav style="background-color: #6366f1; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; width: 100%;">
<div style="font-weight: bold;">Logo</div>
<div style="display: flex; gap: 1rem;">
    <a style="color: white;">Home</a>
    <a style="color: white;">About</a>
    <a style="color: white;">Contact</a>
</div>
</nav>`,
        content: `<nav style="background-color: #6366f1; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; width: 100%;">
<div style="font-weight: bold;">Logo</div>
<div style="display: flex; gap: 1rem;">
    <a href="#" style="color: white; text-decoration: none;">Home</a>
    <a href="#" style="color: white; text-decoration: none;">About</a>
    <a href="#" style="color: white; text-decoration: none;">Contact</a>
</div>
</nav>`,
    },
    // Additional components would be defined here
];

// Templates (abbreviated for brevity)
const templates = [
    {
        name: 'Hero Section',
        icon: LayoutGrid,
        description: 'A hero section with heading, text and call-to-action button',
        elements: [
            {
                type: 'heading',
                content: 'Welcome to My Website',
                level: 1,
                top: 50,
                left: 50,
                width: 500,
                height: 'auto',
                zIndex: 3,
                visible: true,
                responsive: 'fluid',
                styles: {
                    color: '#ffffff',
                    backgroundColor: 'transparent',
                    fontSize: '36',
                    fontWeight: 'bold',
                    textAlign: 'center',
                    paddingTop: 10,
                    paddingRight: 10,
                    paddingBottom: 10,
                    paddingLeft: 10,
                    borderWidth: 0,
                    borderStyle: 'none',
                    borderColor: '#000000',
                    borderRadius: 0,
                },
            },
            // Additional elements would be defined here
        ],
    },
    // Additional templates would be defined here
];

// Responsive Preview Devices
const previewDevices = [
    { id: 'desktop', name: 'Desktop', width: 1280, height: 800, icon: Monitor },
    { id: 'tablet', name: 'Tablet', width: 768, height: 1024, icon: Tablet },
    { id: 'mobile', name: 'Mobile', width: 375, height: 667, icon: Smartphone },
];
</script>

<style>
/* Base Styles */
.builder-app {
    --primary-color: #6366f1;
    --primary-hover: #4f46e5;
    --success-color: #10b981;
    --error-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --text-color: #111827;
    --text-secondary: #4b5563;
    --bg-color: #f9fafb;
    --bg-secondary: #ffffff;
    --border-color: #e5e7eb;
    --shadow-color: rgba(0, 0, 0, 0.1);

    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: var(--text-color);
    background-color: var(--bg-color);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.builder-app.dark-mode {
    --primary-color: #818cf8;
    --primary-hover: #6366f1;
    --text-color: #f9fafb;
    --text-secondary: #d1d5db;
    --bg-color: #111827;
    --bg-secondary: #1f2937;
    --border-color: #374151;
    --shadow-color: rgba(0, 0, 0, 0.3);
}

/* Layout */
.builder-main {
    display: flex;
    flex-grow: 1;
    flex-direction: column;
}

@media (min-width: 768px) {
    .builder-main {
        flex-direction: row;
    }
}

.builder-canvas-container {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    background-color: var(--bg-color);
}

/* Notification Toast */
.notification-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 12px 16px;
    border-radius: 6px;
    box-shadow: 0 4px 6px var(--shadow-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 400px;
    z-index: 1000;
    animation: slideIn 0.3s ease-out;
}

.notification-toast.success {
    background-color: var(--success-color);
    color: white;
}

.notification-toast.error {
    background-color: var(--error-color);
    color: white;
}

.notification-toast.info {
    background-color: var(--info-color);
    color: white;
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.notification-icon {
    width: 20px;
    height: 20px;
}

.notification-close {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 4px;
    margin-left: 8px;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.notification-close:hover {
    opacity: 1;
}

.notification-close-icon {
    width: 16px;
    height: 16px;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Keyboard Shortcuts Modal */
.keyboard-shortcuts-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.keyboard-shortcuts-content {
    background-color: var(--bg-secondary);
    border-radius: 8px;
    box-shadow: 0 4px 20px var(--shadow-color);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.keyboard-shortcuts-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}

.keyboard-shortcuts-header h2 {
    margin: 0;
    font-size: 1.25rem;
    color: var(--text-color);
}

.keyboard-shortcuts-close {
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 4px;
}

.keyboard-shortcuts-body {
    padding: 20px;
}

.keyboard-shortcut {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
}

.keyboard-shortcut:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.keyboard-shortcut-keys {
    display: flex;
    align-items: center;
    gap: 4px;
}

kbd {
    background-color: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    box-shadow: 0 2px 0 var(--border-color);
    color: var(--text-color);
    display: inline-block;
    font-size: 0.85rem;
    font-family: monospace;
    line-height: 1;
    padding: 4px 6px;
    white-space: nowrap;
}

.keyboard-shortcut-description {
    color: var(--text-secondary);
}
</style>
