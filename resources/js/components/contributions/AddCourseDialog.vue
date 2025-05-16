<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
    open: boolean;
}>();

// Define the emit function
const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'create', course: { title: string; description: string; bright: boolean }): void;
}>();

const newCourse = ref({
    title: '',
    description: '',
    bright: false,
});

const handleCreate = () => {
    if (!newCourse.value.title.trim()) return;
    emit('create', { ...newCourse.value });
    newCourse.value = { title: '', description: '', bright: false };
};
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div
            class="animate-fadeIn mx-4 w-full max-w-md rounded-xl border border-purple-100 bg-white shadow-xl dark:border-purple-900/30 dark:bg-slate-800"
        >
            <div class="border-b border-purple-100 p-5 dark:border-purple-900/30">
                <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300">Add New Course</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Create a new course to share with your students.</p>
            </div>

            <div class="space-y-4 p-5">
                <div>
                    <label for="course-title" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Course Title</label>
                    <input
                        id="course-title"
                        v-model="newCourse.title"
                        type="text"
                        placeholder="Enter course title"
                        class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                    />
                </div>

                <div>
                    <label for="course-description" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
                    <textarea
                        id="course-description"
                        v-model="newCourse.description"
                        placeholder="Enter course description"
                        class="min-h-[100px] w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-shadow duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                    ></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="course-highlighted"
                        v-model="newCourse.bright"
                        class="rounded border-purple-300 text-purple-600 focus:ring-purple-500"
                    />
                    <label for="course-highlighted" class="text-sm font-medium text-slate-700 dark:text-slate-300">Highlight this course</label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 border-t border-purple-100 p-5 dark:border-purple-900/30">
                <button
                    @click="emit('close')"
                    class="rounded-lg border border-purple-200 px-4 py-2 text-slate-700 transition-colors duration-300 hover:bg-slate-50 dark:border-purple-800 dark:text-slate-300 dark:hover:bg-slate-700"
                >
                    Cancel
                </button>
                <button
                    @click="handleCreate"
                    class="rounded-lg bg-purple-600 px-4 py-2 text-white shadow-md transition-all duration-300 hover:bg-purple-700 hover:shadow-lg"
                    :disabled="!newCourse.title.trim()"
                >
                    Create Course
                </button>
            </div>
        </div>
    </div>
</template>
