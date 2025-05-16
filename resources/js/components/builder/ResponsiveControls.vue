<template>
    <div
        class="flex items-center justify-center gap-2 overflow-x-auto border-b p-2"
        :class="isDarkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-white'"
    >
        <button
            v-for="device in previewDevices"
            :key="device.id"
            @click="$emit('set-device', device.id)"
            class="flex items-center gap-1 rounded-md p-2"
            :class="
                currentDevice === device.id
                    ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300'
                    : isDarkMode
                      ? 'bg-gray-700 hover:bg-gray-600'
                      : 'bg-gray-100 hover:bg-gray-200'
            "
            :aria-pressed="currentDevice === device.id"
            :aria-label="`Preview on ${device.name}`"
        >
            <component :is="device.icon" class="h-4 w-4" />
            <span>{{ device.name }}</span>
        </button>

        <button
            v-if="currentDevice !== 'desktop'"
            @click="$emit('toggle-orientation')"
            class="rounded-md p-2"
            :class="isDarkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-100 hover:bg-gray-200'"
            aria-label="Toggle orientation"
        >
            <RotateCcw class="h-4 w-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
import { RotateCcw } from 'lucide-vue-next';

defineProps({
    isDarkMode: {
        type: Boolean,
        default: false,
    },
    previewDevices: {
        type: Array,
        default: () => [],
    },
    currentDevice: {
        type: String,
        default: 'desktop',
    },
    isLandscape: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['set-device', 'toggle-orientation']);
</script>
