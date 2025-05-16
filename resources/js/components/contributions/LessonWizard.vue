<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

// Props and emits
defineProps<{
    open: boolean;
    courses: any[];
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

// Wizard state management
const currentStep = ref(1);
const totalSteps = 3;

// Form data
const lessonData = useForm({
    courseId: '',
    title: '',
    description: '',
    fileType: '' as 'video' | 'html' | '',
    files: [] as File[],
});

// Step validation
const stepValidation = computed(() => ({
    1: !!lessonData.courseId && !!lessonData.title && !!lessonData.description,
    2: !!lessonData.fileType && lessonData.files.length > 0,
    3: true, // Review step is always valid
}));

const isCurrentStepValid = computed(() => stepValidation.value[currentStep.value as keyof typeof stepValidation.value]);

// File upload states
const uploadProgress = ref<Record<string, number>>({});
const uploadStatus = ref<Record<string, 'pending' | 'uploading' | 'success' | 'error'>>({});
const previewUrls = ref<Record<string, string>>({});
const activePreviewIndex = ref(0);

// Drag and drop states
const isDragging = ref(false);
const dropZoneRef = ref<HTMLElement | null>(null);
const dragCounter = ref(0);
const fileErrors = ref<string[]>([]);

// Navigation functions
const nextStep = () => {
    if (currentStep.value < totalSteps && isCurrentStepValid.value) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const goToStep = (step: number) => {
    // Only allow going to a step if all previous steps are valid
    if (
        step <= currentStep.value ||
        (step > 1 &&
            Object.entries(stepValidation.value)
                .filter(([key]) => parseInt(key) < step)
                .every(([, isValid]) => isValid))
    ) {
        currentStep.value = step;
    }
};

// File handling functions
const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
        const newFiles = Array.from(input.files);
        processFiles(newFiles);
    }
};

const processFiles = (files: File[]) => {
    fileErrors.value = [];

    // Validate file types
    const validFiles = files.filter((file) => {
        const isValid = validateFileType(file);
        if (!isValid) {
            fileErrors.value.push(`"${file.name}" is not a valid ${lessonData.fileType} file.`);
        }
        return isValid;
    });

    if (validFiles.length === 0) return;

    // Add files to the lesson data
    validFiles.forEach((file) => {
        // Check if file already exists
        const fileExists = lessonData.files.some((f) => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);

        if (!fileExists) {
            lessonData.files.push(file);
            uploadStatus.value[file.name] = 'pending';
            uploadProgress.value[file.name] = 0;

            // Simulate upload
            simulateFileUpload(file);

            // Create preview URL
            createPreviewUrl(file);
        }
    });
};

const validateFileType = (file: File): boolean => {
    if (lessonData.fileType === 'video') {
        return file.type.startsWith('video/');
    } else if (lessonData.fileType === 'html') {
        return file.type === 'text/html';
    }
    return false;
};

const simulateFileUpload = (file: File) => {
    uploadStatus.value[file.name] = 'uploading';

    // Simulate upload progress
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            uploadStatus.value[file.name] = 'success';
        }
        uploadProgress.value[file.name] = Math.round(progress);
    }, 300);
};

const createPreviewUrl = (file: File) => {
    previewUrls.value[file.name] = URL.createObjectURL(file);
};

const removeFile = (file: File) => {
    const index = lessonData.files.findIndex((f) => f.name === file.name);
    if (index !== -1) {
        // Revoke object URL
        if (previewUrls.value[file.name]) {
            URL.revokeObjectURL(previewUrls.value[file.name]);
            delete previewUrls.value[file.name];
        }

        // Remove file from arrays
        lessonData.files.splice(index, 1);
        delete uploadProgress.value[file.name];
        delete uploadStatus.value[file.name];

        // Update active preview index if needed
        if (activePreviewIndex.value >= lessonData.files.length) {
            activePreviewIndex.value = Math.max(0, lessonData.files.length - 1);
        }
    }
};

// Drag and drop event handlers
const handleDragEnter = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    dragCounter.value++;

    if (e.dataTransfer) {
        e.dataTransfer.dropEffect = 'copy';
    }

    if (dragCounter.value === 1) {
        isDragging.value = true;
    }
};

const handleDragOver = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.dataTransfer) {
        e.dataTransfer.dropEffect = 'copy';
    }
};

const handleDragLeave = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    dragCounter.value--;

    if (dragCounter.value === 0) {
        isDragging.value = false;
    }
};

const handleDrop = (e: DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = false;
    dragCounter.value = 0;

    if (!e.dataTransfer || !lessonData.fileType) {
        return;
    }

    const files = Array.from(e.dataTransfer.files);
    if (files.length > 0) {
        processFiles(files);
    }
};

// Setup and cleanup drag and drop event listeners
const setupDragAndDrop = () => {
    if (dropZoneRef.value) {
        dropZoneRef.value.addEventListener('dragenter', handleDragEnter);
        dropZoneRef.value.addEventListener('dragover', handleDragOver);
        dropZoneRef.value.addEventListener('dragleave', handleDragLeave);
        dropZoneRef.value.addEventListener('drop', handleDrop);
    }
};

const cleanupDragAndDrop = () => {
    if (dropZoneRef.value) {
        dropZoneRef.value.removeEventListener('dragenter', handleDragEnter);
        dropZoneRef.value.removeEventListener('dragover', handleDragOver);
        dropZoneRef.value.removeEventListener('dragleave', handleDragLeave);
        dropZoneRef.value.removeEventListener('drop', handleDrop);
    }
};

// Preview navigation
const showNextPreview = () => {
    if (activePreviewIndex.value < lessonData.files.length - 1) {
        activePreviewIndex.value++;
    }
};

const showPrevPreview = () => {
    if (activePreviewIndex.value > 0) {
        activePreviewIndex.value--;
    }
};

const setActivePreview = (index: number) => {
    activePreviewIndex.value = index;
};

// Utility functions
const getFileSize = (size: number): string => {
    if (size < 1024) return size + ' bytes';
    else if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB';
    else return (size / (1024 * 1024)).toFixed(1) + ' MB';
};

const getFileIcon = (file: File) => {
    if (file.type.startsWith('video/')) {
        return 'video';
    } else if (file.type === 'text/html') {
        return 'code';
    }
    return 'file';
};

// Lifecycle hooks
onMounted(() => {
    setupDragAndDrop();
});

onBeforeUnmount(() => {
    cleanupDragAndDrop();

    // Clean up all object URLs
    Object.values(previewUrls.value).forEach((url) => {
        URL.revokeObjectURL(url);
    });
});

// Close modal and reset form
const closeModal = () => {
    // Clean up all object URLs
    Object.values(previewUrls.value).forEach((url) => {
        URL.revokeObjectURL(url);
    });

    // Reset state
    currentStep.value = 1;
    lessonData.courseId = '';
    lessonData.title = '';
    lessonData.description = '';
    lessonData.fileType = '';
    lessonData.files = [];
    uploadProgress.value = {};
    uploadStatus.value = {};
    previewUrls.value = {};
    activePreviewIndex.value = 0;

    emit('close');
};

// Submit the form
const submitLesson = () => {
    // Here you would typically send the data to your backend
    console.log('Submitting lesson:', lessonData);
    lessonData.post('/lessons');

    // For demo purposes, just close the modal
    closeModal();
};
</script>

<template>
    <Teleport to="body">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto">
            <!-- Backdrop with blur effect -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

            <!-- Modal container with animation -->
            <div class="relative w-full max-w-5xl transform rounded-2xl bg-white p-0 shadow-2xl transition-all duration-300 dark:bg-slate-900">
                <!-- Modal header with gradient -->
                <div class="relative rounded-t-2xl bg-gradient-to-r from-purple-600 to-violet-500 p-6 text-white">
                    <h2 class="text-2xl font-bold">Create New Lesson</h2>
                    <p class="mt-1 text-purple-100">
                        Step {{ currentStep }} of {{ totalSteps }}:
                        {{ currentStep === 1 ? 'Basic Information' : currentStep === 2 ? 'Upload Content' : 'Preview & Confirm' }}
                    </p>

                    <!-- Close button -->
                    <button
                        @click="closeModal"
                        class="absolute right-4 top-4 rounded-full bg-white/20 p-2 text-white transition-all hover:bg-white/30"
                        aria-label="Close modal"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Progress bar -->
                <div class="relative h-2 w-full bg-slate-200 dark:bg-slate-700">
                    <div
                        class="absolute left-0 top-0 h-full bg-gradient-to-r from-purple-500 to-violet-500 transition-all duration-300"
                        :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
                    ></div>
                </div>

                <!-- Step indicators -->
                <div class="flex justify-center px-6 py-4">
                    <div class="flex items-center">
                        <template v-for="step in totalSteps" :key="step">
                            <!-- Step circle -->
                            <div
                                @click="goToStep(step)"
                                :class="[
                                    'flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border-2 text-sm font-medium transition-all',
                                    step < currentStep
                                        ? 'border-purple-500 bg-purple-500 text-white'
                                        : step === currentStep
                                          ? 'border-purple-500 bg-white text-purple-500 dark:bg-slate-800'
                                          : 'border-slate-300 bg-white text-slate-500 dark:border-slate-600 dark:bg-slate-800',
                                ]"
                            >
                                <svg
                                    v-if="step < currentStep"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span v-else>{{ step }}</span>
                            </div>

                            <!-- Connector line -->
                            <div
                                v-if="step < totalSteps"
                                :class="['h-1 w-10 transition-all', step < currentStep ? 'bg-purple-500' : 'bg-slate-300 dark:bg-slate-600']"
                            ></div>
                        </template>
                    </div>
                </div>

                <!-- Step content -->
                <div class="px-6 pb-6">
                    <!-- Step 1: Basic Information -->
                    <div v-if="currentStep === 1" class="space-y-6">
                        <div class="rounded-xl border border-purple-100 bg-white p-5 shadow-md dark:border-purple-900/30 dark:bg-slate-800">
                            <h3 class="mb-4 text-lg font-semibold text-purple-700 dark:text-purple-300">Lesson Details</h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="lesson-course" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Select Course <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="lesson-course"
                                        v-model="lessonData.courseId"
                                        class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-all duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                                    >
                                        <option value="">Select a course</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
                                    </select>
                                    <p v-if="!lessonData.courseId && currentStep === 1" class="mt-1 text-xs text-red-500">Please select a course</p>
                                </div>

                                <div>
                                    <label for="lesson-title" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Lesson Title <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="lesson-title"
                                        v-model="lessonData.title"
                                        type="text"
                                        placeholder="Enter lesson title"
                                        class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-all duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                                    />
                                    <p v-if="!lessonData.title && currentStep === 1" class="mt-1 text-xs text-red-500">Please enter a lesson title</p>
                                </div>

                                <div>
                                    <label for="lesson-description" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Lesson Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        id="lesson-description"
                                        v-model="lessonData.description"
                                        placeholder="Enter lesson description"
                                        rows="4"
                                        class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-all duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                                    ></textarea>
                                    <p v-if="!lessonData.description && currentStep === 1" class="mt-1 text-xs text-red-500">
                                        Please enter a lesson description
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Upload Content -->
                    <div v-if="currentStep === 2" class="space-y-6">
                        <div class="rounded-xl border border-purple-100 bg-white p-5 shadow-md dark:border-purple-900/30 dark:bg-slate-800">
                            <h3 class="mb-4 text-lg font-semibold text-purple-700 dark:text-purple-300">Upload Content</h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="file-type" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        File Type <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="file-type"
                                        v-model="lessonData.fileType"
                                        class="w-full rounded-lg border border-purple-200 bg-white px-3 py-2 text-slate-900 shadow-sm transition-all duration-300 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-purple-800 dark:bg-slate-800 dark:text-slate-100 dark:focus:ring-purple-400"
                                    >
                                        <option value="">Select file type</option>
                                        <option value="video">Video</option>
                                        <option value="html">HTML</option>
                                    </select>
                                    <p v-if="!lessonData.fileType && currentStep === 2" class="mt-1 text-xs text-red-500">
                                        Please select a file type
                                    </p>
                                </div>

                                <!-- Drag and Drop Zone -->
                                <div v-if="lessonData.fileType">
                                    <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Upload Files <span class="text-red-500">*</span>
                                    </label>

                                    <div
                                        ref="dropZoneRef"
                                        :class="[
                                            'relative flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-6 text-center transition-all',
                                            isDragging
                                                ? 'border-purple-500 bg-purple-100 dark:border-purple-500 dark:bg-purple-900/30'
                                                : lessonData.files.length > 0
                                                  ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-900/20'
                                                  : 'border-purple-300 bg-purple-50 hover:border-purple-400 hover:bg-purple-100 dark:border-purple-700 dark:bg-slate-800/50 dark:hover:border-purple-600 dark:hover:bg-slate-800',
                                        ]"
                                    >
                                        <input
                                            id="file-upload"
                                            type="file"
                                            :accept="lessonData.fileType === 'video' ? 'video/*' : lessonData.fileType === 'html' ? '.html' : ''"
                                            @change="handleFileChange"
                                            class="hidden"
                                        />
                                        <!--multiple-->

                                        <!-- Drag overlay animation -->
                                        <div
                                            v-if="isDragging"
                                            class="absolute inset-0 flex items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30"
                                        >
                                            <div class="text-center">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="mx-auto mb-2 h-12 w-12 animate-bounce text-purple-500"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                >
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="17 8 12 3 7 8"></polyline>
                                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                                </svg>
                                                <p class="text-sm font-medium text-purple-700 dark:text-purple-300">Drop your files here</p>
                                            </div>
                                        </div>

                                        <!-- Default upload state -->
                                        <label for="file-upload" class="flex w-full cursor-pointer flex-col items-center">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mx-auto mb-2 h-10 w-10 text-purple-500"
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                            <p class="text-sm font-medium text-purple-700 dark:text-purple-300">
                                                Drag and drop or click to upload file
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                                {{ lessonData.fileType === 'video' ? 'MP4, WebM, or Ogg video files' : 'HTML files only' }}
                                            </p>
                                        </label>
                                    </div>

                                    <!-- File validation errors -->
                                    <div v-if="fileErrors.length > 0" class="mt-2 rounded-lg bg-red-50 p-3 dark:bg-red-900/20">
                                        <p class="text-sm font-medium text-red-700 dark:text-red-300">The following errors occurred:</p>
                                        <ul class="mt-1 list-inside list-disc text-xs text-red-600 dark:text-red-400">
                                            <li v-for="(error, index) in fileErrors" :key="index">{{ error }}</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Uploaded files list -->
                                <div v-if="lessonData.files.length > 0" class="mt-4 space-y-3">
                                    <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Uploaded Files ({{ lessonData.files.length }})
                                    </h4>

                                    <div class="max-h-60 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700">
                                        <ul class="divide-y divide-slate-200 dark:divide-slate-700">
                                            <li v-for="file in lessonData.files" :key="file.name" class="flex items-center justify-between p-3">
                                                <div class="flex items-center space-x-3">
                                                    <!-- File icon -->
                                                    <div
                                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30"
                                                    >
                                                        <svg
                                                            v-if="getFileIcon(file) === 'video'"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-purple-600"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        >
                                                            <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                                        </svg>
                                                        <svg
                                                            v-else-if="getFileIcon(file) === 'code'"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5 text-purple-600"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        >
                                                            <polyline points="16 18 22 12 16 6"></polyline>
                                                            <polyline points="8 6 2 12 8 18"></polyline>
                                                        </svg>
                                                    </div>

                                                    <!-- File info -->
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-sm font-medium text-slate-700 dark:text-slate-300">{{ file.name }}</p>
                                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ getFileSize(file.size) }}</p>
                                                    </div>
                                                </div>

                                                <!-- Upload status -->
                                                <div class="ml-4 flex items-center space-x-2">
                                                    <!-- Progress indicator -->
                                                    <div v-if="uploadStatus[file.name] === 'uploading'" class="w-24">
                                                        <div class="h-2 w-full rounded-full bg-slate-200 dark:bg-slate-700">
                                                            <div
                                                                class="h-full rounded-full bg-purple-500 transition-all duration-300"
                                                                :style="{ width: `${uploadProgress[file.name]}%` }"
                                                            ></div>
                                                        </div>
                                                        <p class="mt-1 text-right text-xs text-slate-500 dark:text-slate-400">
                                                            {{ uploadProgress[file.name] }}%
                                                        </p>
                                                    </div>

                                                    <!-- Success indicator -->
                                                    <div v-else-if="uploadStatus[file.name] === 'success'" class="flex items-center text-green-500">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        >
                                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                        </svg>
                                                    </div>

                                                    <!-- Error indicator -->
                                                    <div v-else-if="uploadStatus[file.name] === 'error'" class="flex items-center text-red-500">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-5 w-5"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        >
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                        </svg>
                                                    </div>

                                                    <!-- Remove button -->
                                                    <button
                                                        @click.stop="removeFile(file)"
                                                        class="rounded-lg bg-red-100 p-1 text-red-700 transition-colors hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50"
                                                        aria-label="Remove file"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        >
                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Preview and Confirm -->
                    <div v-if="currentStep === 3" class="space-y-6">
                        <!-- Lesson details summary -->
                        <div class="rounded-xl border border-purple-100 bg-white p-5 shadow-md dark:border-purple-900/30 dark:bg-slate-800">
                            <h3 class="mb-4 text-lg font-semibold text-purple-700 dark:text-purple-300">Lesson Summary</h3>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Course</h4>
                                        <p class="text-base text-slate-900 dark:text-white">
                                            {{ courses.find((c) => c.id === lessonData.courseId)?.title || 'Unknown Course' }}
                                        </p>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Lesson Title</h4>
                                        <p class="text-base text-slate-900 dark:text-white">{{ lessonData.title }}</p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Description</h4>
                                        <p class="text-base text-slate-900 dark:text-white">{{ lessonData.description }}</p>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Content Type</h4>
                                        <p class="text-base capitalize text-slate-900 dark:text-white">{{ lessonData.fileType }}</p>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-slate-500 dark:text-slate-400">Files</h4>
                                        <p class="text-base text-slate-900 dark:text-white">{{ lessonData.files.length }} file(s)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content preview -->
                        <div
                            v-if="lessonData.files.length > 0"
                            class="rounded-xl border border-purple-100 bg-white p-5 shadow-md dark:border-purple-900/30 dark:bg-slate-800"
                        >
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-purple-700 dark:text-purple-300">Content Preview</h3>

                                <!-- Preview navigation -->
                                <div v-if="lessonData.files.length > 1" class="flex items-center space-x-2">
                                    <button
                                        @click="showPrevPreview"
                                        :disabled="activePreviewIndex === 0"
                                        :class="[
                                            'rounded-full p-1 transition-colors',
                                            activePreviewIndex === 0
                                                ? 'cursor-not-allowed bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-500'
                                                : 'bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:hover:bg-purple-900/50',
                                        ]"
                                        aria-label="Previous preview"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </button>

                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ activePreviewIndex + 1 }} / {{ lessonData.files.length }}
                                    </span>

                                    <button
                                        @click="showNextPreview"
                                        :disabled="activePreviewIndex === lessonData.files.length - 1"
                                        :class="[
                                            'rounded-full p-1 transition-colors',
                                            activePreviewIndex === lessonData.files.length - 1
                                                ? 'cursor-not-allowed bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-500'
                                                : 'bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:hover:bg-purple-900/50',
                                        ]"
                                        aria-label="Next preview"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Preview content -->
                            <div class="rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50">
                                <!-- Video preview -->
                                <div
                                    v-if="
                                        lessonData.files[activePreviewIndex]?.type.startsWith('video/') &&
                                        previewUrls[lessonData.files[activePreviewIndex]?.name]
                                    "
                                    class="aspect-video w-full overflow-hidden rounded-lg"
                                >
                                    <video controls class="h-full w-full" :src="previewUrls[lessonData.files[activePreviewIndex]?.name]"></video>
                                </div>

                                <!-- HTML preview -->
                                <div
                                    v-else-if="
                                        lessonData.files[activePreviewIndex]?.type === 'text/html' &&
                                        previewUrls[lessonData.files[activePreviewIndex]?.name]
                                    "
                                    class="h-64 w-full overflow-hidden rounded-lg"
                                >
                                    <iframe
                                        :src="previewUrls[lessonData.files[activePreviewIndex]?.name]"
                                        class="h-full w-full border-0"
                                        sandbox="allow-same-origin"
                                        title="HTML Preview"
                                    ></iframe>
                                </div>

                                <!-- Fallback -->
                                <div v-else class="flex h-64 items-center justify-center">
                                    <p class="text-slate-500 dark:text-slate-400">Preview not available</p>
                                </div>
                            </div>

                            <!-- Thumbnail navigation for multiple files -->
                            <div v-if="lessonData.files.length > 1" class="mt-4 flex space-x-2 overflow-x-auto pb-2">
                                <button
                                    v-for="(file, index) in lessonData.files"
                                    :key="file.name"
                                    @click="setActivePreview(index)"
                                    :class="[
                                        'flex-shrink-0 rounded-lg border-2 p-1 transition-all',
                                        activePreviewIndex === index
                                            ? 'border-purple-500 bg-purple-50 dark:border-purple-500 dark:bg-purple-900/30'
                                            : 'border-slate-200 hover:border-purple-300 dark:border-slate-700 dark:hover:border-purple-700',
                                    ]"
                                >
                                    <div class="flex h-14 w-14 items-center justify-center">
                                        <svg
                                            v-if="getFileIcon(file) === 'video'"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8 text-purple-600"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <polygon points="23 7 16 12 23 17 23 7"></polygon>
                                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                        </svg>
                                        <svg
                                            v-else-if="getFileIcon(file) === 'code'"
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8 text-purple-600"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <polyline points="16 18 22 12 16 6"></polyline>
                                            <polyline points="8 6 2 12 8 18"></polyline>
                                        </svg>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div
                    class="flex items-center justify-between rounded-b-2xl border-t border-slate-200 bg-slate-50 p-6 dark:border-slate-700 dark:bg-slate-800/50"
                >
                    <!-- Back button (hidden on first step) -->
                    <button
                        v-if="currentStep > 1"
                        @click="prevStep"
                        class="flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-700 shadow-sm transition-all hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mr-1 h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Back
                    </button>
                    <div v-else></div>

                    <!-- Next/Submit button -->
                    <button
                        v-if="currentStep < totalSteps"
                        @click="nextStep"
                        :disabled="!isCurrentStepValid"
                        :class="[
                            'flex items-center rounded-lg px-6 py-2 font-medium text-white shadow-md transition-all',
                            isCurrentStepValid
                                ? 'bg-gradient-to-r from-purple-600 to-violet-500 hover:from-purple-700 hover:to-violet-600'
                                : 'cursor-not-allowed bg-slate-400',
                        ]"
                    >
                        Next
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="ml-1 h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>

                    <button
                        v-else
                        @click="submitLesson"
                        class="rounded-lg bg-gradient-to-r from-purple-600 to-violet-500 px-6 py-2 font-medium text-white shadow-md transition-all hover:from-purple-700 hover:to-violet-600"
                    >
                        Create Lesson
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
