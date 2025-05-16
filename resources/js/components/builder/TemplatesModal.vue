<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="max-h-[80vh] w-full max-w-4xl overflow-y-auto rounded-lg p-6 shadow-xl" :class="isDarkMode ? 'bg-gray-800' : 'bg-white'">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Templates</h2>
                <button @click="$emit('close')" class="rounded-full p-1 hover:bg-gray-200 dark:hover:bg-gray-700" aria-label="Close templates">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div
                    v-for="(template, index) in templates"
                    :key="index"
                    class="cursor-pointer overflow-hidden rounded-md border transition-shadow hover:shadow-md"
                    :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'"
                    @click="$emit('apply-template', template)"
                    role="button"
                    tabindex="0"
                    :aria-label="`Apply ${template.name} template`"
                >
                    <div class="flex aspect-video items-center justify-center bg-gray-100 dark:bg-gray-700">
                        <component :is="template.icon" class="h-12 w-12 text-purple-500 opacity-70" />
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium">{{ template.name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ template.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { X } from 'lucide-vue-next';

defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    templates: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['close', 'apply-template']);
</script>
