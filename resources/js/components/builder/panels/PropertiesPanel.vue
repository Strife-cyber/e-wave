<template>
    <div class="properties-panel">
        <div class="property-group">
            <h3 class="property-group-title">Content</h3>
            <div class="property">
                <label class="property-label" for="element-content">{{ contentLabel }}</label>
                <textarea
                    v-if="['heading', 'paragraph', 'button'].includes(selectedElement.type)"
                    v-model="localElement.content"
                    id="element-content"
                    class="property-textarea"
                    rows="3"
                    @input="updateElement"
                ></textarea>
                <input
                    v-else-if="selectedElement.type === 'image'"
                    v-model="localElement.src"
                    id="element-content"
                    placeholder="Image URL"
                    class="property-input"
                    @input="updateElement"
                />
                <textarea
                    v-else-if="selectedElement.type === 'custom'"
                    v-model="localElement.content"
                    id="element-content"
                    class="property-textarea code"
                    rows="5"
                    @input="updateElement"
                ></textarea>
            </div>

            <div class="property" v-if="selectedElement.type === 'heading'">
                <label class="property-label" for="heading-level">Heading Level</label>
                <select v-model="localElement.level" id="heading-level" class="property-select" @change="updateElement">
                    <option v-for="n in 6" :key="n" :value="n">H{{ n }}</option>
                </select>
            </div>
        </div>

        <!-- Position Controls -->
        <div class="property-group">
            <h3 class="property-group-title">Position & Size</h3>
            <div class="property-grid">
                <div class="property">
                    <label class="property-label" for="element-x">X Position</label>
                    <input type="number" v-model.number="localElement.left" id="element-x" class="property-input" @change="updateElement" />
                </div>
                <div class="property">
                    <label class="property-label" for="element-y">Y Position</label>
                    <input type="number" v-model.number="localElement.top" id="element-y" class="property-input" @change="updateElement" />
                </div>
                <div class="property">
                    <label class="property-label" for="element-width">Width</label>
                    <input type="number" v-model.number="localElement.width" id="element-width" class="property-input" @change="updateElement" />
                </div>
                <div class="property">
                    <label class="property-label" for="element-height">Height</label>
                    <input type="text" v-model="localElement.height" id="element-height" class="property-input" @change="updateElement" />
                </div>
            </div>
        </div>

        <!-- Typography -->
        <div class="property-group">
            <h3 class="property-group-title">Typography</h3>
            <div class="property">
                <label class="property-label" for="font-size">Font Size: {{ localElement.styles.fontSize }}px</label>
                <input
                    type="range"
                    v-model.number="localElement.styles.fontSize"
                    id="font-size"
                    min="8"
                    max="72"
                    class="property-range"
                    @change="updateElement"
                />
            </div>

            <div class="property">
                <label class="property-label" for="font-weight">Font Weight</label>
                <select v-model="localElement.styles.fontWeight" id="font-weight" class="property-select" @change="updateElement">
                    <option value="normal">Normal</option>
                    <option value="bold">Bold</option>
                    <option value="lighter">Lighter</option>
                    <option value="bolder">Bolder</option>
                </select>
            </div>

            <div class="property">
                <label class="property-label">Text Align</label>
                <div class="text-align-buttons">
                    <button
                        v-for="align in ['left', 'center', 'right', 'justify']"
                        :key="align"
                        @click="setTextAlign(align)"
                        class="text-align-button"
                        :class="{ active: localElement.styles.textAlign === align }"
                        :aria-pressed="localElement.styles.textAlign === align"
                        :aria-label="`Align text ${align}`"
                    >
                        <AlignLeft v-if="align === 'left'" class="text-align-icon" />
                        <AlignCenter v-if="align === 'center'" class="text-align-icon" />
                        <AlignRight v-if="align === 'right'" class="text-align-icon" />
                        <AlignJustify v-if="align === 'justify'" class="text-align-icon" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Colors -->
        <div class="property-group">
            <h3 class="property-group-title">Colors</h3>
            <div class="property">
                <label class="property-label" for="text-color">Text Color</label>
                <div class="color-picker">
                    <input type="color" v-model="localElement.styles.color" id="text-color" class="property-color" @change="updateElement" />
                    <input type="text" v-model="localElement.styles.color" class="property-input color-value" @change="updateElement" />
                </div>
            </div>

            <div class="property">
                <label class="property-label" for="bg-color">Background Color</label>
                <div class="color-picker">
                    <input type="color" v-model="localElement.styles.backgroundColor" id="bg-color" class="property-color" @change="updateElement" />
                    <input type="text" v-model="localElement.styles.backgroundColor" class="property-input color-value" @change="updateElement" />
                </div>
            </div>
        </div>

        <!-- Spacing -->
        <div class="property-group">
            <h3 class="property-group-title">Spacing</h3>
            <div class="property">
                <label class="property-label">Padding</label>
                <div class="padding-grid">
                    <div v-for="side in ['Top', 'Right', 'Bottom', 'Left']" :key="side" class="padding-control">
                        <span class="padding-label">{{ side }}</span>
                        <input
                            type="number"
                            v-model.number="localElement.styles[`padding${side}`]"
                            class="property-input padding-input"
                            min="0"
                            @change="updateElement"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Border -->
        <div class="property-group">
            <h3 class="property-group-title">Border</h3>
            <div class="property-grid">
                <div class="property">
                    <label class="property-label" for="border-width">Width</label>
                    <input
                        type="number"
                        v-model.number="localElement.styles.borderWidth"
                        id="border-width"
                        class="property-input"
                        min="0"
                        @change="updateElement"
                    />
                </div>
                <div class="property">
                    <label class="property-label" for="border-style">Style</label>
                    <select v-model="localElement.styles.borderStyle" id="border-style" class="property-select" @change="updateElement">
                        <option value="none">None</option>
                        <option value="solid">Solid</option>
                        <option value="dashed">Dashed</option>
                        <option value="dotted">Dotted</option>
                    </select>
                </div>
            </div>

            <div class="property">
                <label class="property-label" for="border-color">Color</label>
                <div class="color-picker">
                    <input type="color" v-model="localElement.styles.borderColor" id="border-color" class="property-color" @change="updateElement" />
                    <input type="text" v-model="localElement.styles.borderColor" class="property-input color-value" @change="updateElement" />
                </div>
            </div>

            <div class="property">
                <label class="property-label" for="border-radius">Border Radius: {{ localElement.styles.borderRadius }}px</label>
                <input
                    type="range"
                    v-model.number="localElement.styles.borderRadius"
                    id="border-radius"
                    min="0"
                    max="50"
                    class="property-range"
                    @change="updateElement"
                />
            </div>
        </div>

        <!-- Responsive Behavior -->
        <div class="property-group">
            <h3 class="property-group-title">Responsive</h3>
            <div class="property">
                <label class="property-label" for="responsive-behavior">Behavior</label>
                <select v-model="localElement.responsive" id="responsive-behavior" class="property-select" @change="updateElement">
                    <option value="fixed">Fixed Size</option>
                    <option value="fluid">Fluid Width</option>
                    <option value="hidden-mobile">Hide on Mobile</option>
                </select>
            </div>
        </div>

        <!-- Delete Button -->
        <div class="property-group">
            <button @click="$emit('delete-element', selectedElement.id)" class="delete-button" aria-label="Delete element">
                <Trash2 class="delete-icon" />
                Delete Element
            </button>
        </div>
    </div>
</template>

<script setup>
import { AlignCenter, AlignJustify, AlignLeft, AlignRight, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    selectedElement: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update-element', 'delete-element', 'set-text-align']);

// Create a local copy of the element to avoid direct mutation
const localElement = ref(JSON.parse(JSON.stringify(props.selectedElement)));

// Computed property for content label based on element type
const contentLabel = computed(() => {
    switch (localElement.value.type) {
        case 'heading':
            return 'Heading Text';
        case 'paragraph':
            return 'Paragraph Text';
        case 'button':
            return 'Button Text';
        case 'image':
            return 'Image URL';
        case 'custom':
            return 'HTML Content';
        default:
            return 'Content';
    }
});

// Watch for changes in the selected element
watch(
    () => props.selectedElement,
    (newValue) => {
        localElement.value = JSON.parse(JSON.stringify(newValue));
    },
    { deep: true },
);

function updateElement() {
    emit('update-element', localElement.value);
}

function setTextAlign(align) {
    localElement.value.styles.textAlign = align;
    updateElement();
    emit('set-text-align', align);
}
</script>

<style scoped>
.properties-panel {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.property-group {
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    overflow: hidden;
}

.property-group-title {
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.75rem;
    margin: 0;
    background-color: var(--bg-color);
    border-bottom: 1px solid var(--border-color);
}

.property {
    padding: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}

.property:last-child {
    border-bottom: none;
}

.property-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    padding: 0.75rem;
}

.property-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--text-secondary);
}

.property-input,
.property-textarea,
.property-select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid var(--border-color);
    border-radius: 0.25rem;
    background-color: var(--bg-secondary);
    color: var(--text-color);
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.property-input:focus,
.property-textarea:focus,
.property-select:focus {
    border-color: var(--primary-color);
    outline: none;
}

.property-textarea.code {
    font-family: monospace;
    font-size: 0.75rem;
}

.property-range {
    width: 100%;
    height: 0.5rem;
    background-color: var(--border-color);
    border-radius: 0.25rem;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
}

.property-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: var(--primary-color);
    cursor: pointer;
}

.property-range::-moz-range-thumb {
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: var(--primary-color);
    cursor: pointer;
    border: none;
}

.text-align-buttons {
    display: flex;
    gap: 0.25rem;
}

.text-align-button {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    background-color: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 0.25rem;
    color: var(--text-color);
    cursor: pointer;
    transition: all 0.2s;
}

.text-align-button:hover {
    border-color: var(--primary-color);
}

.text-align-button.active {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.text-align-icon {
    width: 1rem;
    height: 1rem;
}

.color-picker {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.property-color {
    width: 2.5rem;
    height: 2.5rem;
    border: 1px solid var(--border-color);
    border-radius: 0.25rem;
    cursor: pointer;
    padding: 0;
}

.color-value {
    flex: 1;
}

.padding-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.padding-control {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.padding-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}

.padding-input {
    width: 100%;
    text-align: center;
}

.delete-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem;
    background-color: rgba(239, 68, 68, 0.1);
    color: var(--error-color);
    border: 1px solid rgba(239, 68, 68, 0.2);
    border-radius: 0.25rem;
    cursor: pointer;
    transition: all 0.2s;
}

.delete-button:hover {
    background-color: var(--error-color);
    color: white;
}

.delete-icon {
    width: 1rem;
    height: 1rem;
}
</style>
