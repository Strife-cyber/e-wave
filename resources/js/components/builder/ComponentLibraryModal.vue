<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div class="max-h-[80vh] w-full max-w-4xl overflow-y-auto rounded-lg p-6 shadow-xl" :class="isDarkMode ? 'bg-gray-800' : 'bg-white'">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Component Library</h2>
                <button
                    @click="$emit('close')"
                    class="rounded-full p-1 hover:bg-gray-200 dark:hover:bg-gray-700"
                    aria-label="Close component library"
                >
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- Component Categories -->
            <div class="mb-4 flex overflow-x-auto border-b" :class="isDarkMode ? 'border-gray-700' : 'border-gray-200'">
                <button
                    v-for="category in componentCategories"
                    :key="category.id"
                    @click="$emit('set-category', category.id)"
                    class="whitespace-nowrap px-4 py-2 font-medium"
                    :class="activeCategory === category.id ? 'border-b-2 border-purple-500 text-purple-600' : ''"
                    :aria-selected="activeCategory === category.id"
                >
                    {{ category.name }}
                </button>
            </div>

            <!-- Component Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                <div
                    v-for="component in filteredComponents"
                    :key="component.id"
                    class="cursor-pointer overflow-hidden rounded-md border transition-shadow hover:shadow-md"
                    :class="isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white'"
                    @click="$emit('add-component', component)"
                    role="button"
                    tabindex="0"
                    :aria-label="`Add ${component.name} component`"
                >
                    <div class="flex aspect-video items-center justify-center bg-gray-100 p-2 dark:bg-gray-700">
                        <div v-html="component.preview" class="pointer-events-none scale-75 transform"></div>
                    </div>
                    <div class="p-3">
                        <h3 class="font-medium">{{ component.name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ component.description }}</p>
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
    componentCategories: {
        type: Array,
        default: () => [],
    },
    activeCategory: {
        type: String,
        default: 'navigation',
    },
    filteredComponents: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['close', 'set-category', 'add-component']);
</script>
