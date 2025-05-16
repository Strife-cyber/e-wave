<script setup lang="ts">
import { GripVertical } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    open: boolean;
    course: any;
    lessons: any[];
}>();

defineEmits<{
    (e: 'close'): void;
    (e: 'save', lessons: any[]): void;
}>();

const isDragging = ref(false);
const draggedLesson = ref(null);
const localLessons = ref([...(props.lessons || [])]);

const startDrag = (lesson, event) => {
    isDragging.value = true;
    draggedLesson.value = lesson;
    event.dataTransfer.effectAllowed = 'move';
};

const onDragOver = (event) => {
    event.preventDefault();
};

const onDrop = (targetLesson) => {
    if (!draggedLesson.value || draggedLesson.value.id === targetLesson.id) {
        isDragging.value = false;
        draggedLesson.value = null;
        return;
    }

    const draggedOrder = draggedLesson.value.order;
    const targetOrder = targetLesson.order;

    localLessons.value = localLessons.value.map((lesson) => {
        if (lesson.id === draggedLesson.value.id) {
            return { ...lesson, order: targetOrder };
        } else if (draggedOrder < targetOrder && lesson.order > draggedOrder && lesson.order <= targetOrder) {
            return { ...lesson, order: lesson.order - 1 };
        } else if (draggedOrder > targetOrder && lesson.order < draggedOrder && lesson.order >= targetOrder) {
            return { ...lesson, order: lesson.order + 1 };
        }
        return lesson;
    });

    localLessons.value.sort((a, b) => a.order - b.order);

    isDragging.value = false;
    draggedLesson.value = null;
};

const handleSave = () => {
    emit('save', localLessons.value);
};
</script>

<template>
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div
            class="animate-fadeIn mx-4 w-full max-w-md rounded-xl border border-purple-100 bg-white shadow-xl dark:border-purple-900/30 dark:bg-slate-800"
        >
            <div class="border-b border-purple-100 p-5 dark:border-purple-900/30">
                <h3 class="text-xl font-bold text-purple-700 dark:text-purple-300">Reorder Lessons</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Drag and drop to reorder lessons for "{{ course?.title }}"</p>
            </div>

            <div class="p-5">
                <p class="mb-3 text-sm text-slate-600 dark:text-slate-400">Drag lessons to change their order</p>

                <div v-if="lessons.length === 0" class="py-4 text-center">
                    <p class="text-slate-500 dark:text-slate-400">No lessons available for this course</p>
                </div>

                <ul v-else class="space-y-2">
                    <li
                        v-for="lesson in localLessons"
                        :key="lesson.id"
                        draggable="true"
                        @dragstart="startDrag(lesson, $event)"
                        @dragover.prevent="onDragOver"
                        @drop="onDrop(lesson)"
                        :class="[
                            'flex cursor-move items-center rounded-lg border p-3 transition-colors duration-200',
                            isDragging && draggedLesson?.id === lesson.id
                                ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30'
                                : 'border-purple-200 hover:bg-purple-50 dark:border-purple-800 dark:hover:bg-purple-900/20',
                        ]"
                    >
                        <GripVertical class="mr-3 h-5 w-5 flex-shrink-0 text-slate-400 dark:text-slate-500" />
                        <span class="font-medium text-slate-900 dark:text-white">{{ lesson.title }}</span>
                    </li>
                </ul>
            </div>

            <div class="flex justify-end space-x-3 border-t border-purple-100 p-5 dark:border-purple-900/30">
                <button
                    @click="$emit('close')"
                    class="rounded-lg border border-purple-200 px-4 py-2 text-slate-700 transition-colors duration-300 hover:bg-slate-50 dark:border-purple-800 dark:text-slate-300 dark:hover:bg-slate-700"
                >
                    Cancel
                </button>
                <button
                    @click="handleSave"
                    class="rounded-lg bg-purple-600 px-4 py-2 text-white shadow-md transition-all duration-300 hover:bg-purple-700 hover:shadow-lg"
                >
                    Save Order
                </button>
            </div>
        </div>
    </div>
</template>
