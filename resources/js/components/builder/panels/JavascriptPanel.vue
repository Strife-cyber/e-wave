<template>
    <div class="space-y-4">
        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Add JavaScript code to be included in your HTML export.</p>

        <textarea
            v-model="localCode"
            class="h-80 w-full rounded-md border px-3 py-2 font-mono text-sm"
            :class="isDarkMode ? 'border-gray-600 bg-gray-700' : 'border-gray-300'"
            placeholder="// Your JavaScript code here"
            @input="updateCode"
        ></textarea>

        <div class="text-sm text-gray-500 dark:text-gray-400">
            <p>This code will be added to the exported HTML inside a &lt;script&gt; tag.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    javascriptCode: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update-javascript']);

const localCode = ref(props.javascriptCode);

watch(
    () => props.javascriptCode,
    (newValue) => {
        localCode.value = newValue;
    },
);

function updateCode() {
    emit('update-javascript', localCode.value);
}
</script>
