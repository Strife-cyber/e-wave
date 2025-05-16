<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const topics = ref([]);

defineProps<{
    contents: object;
}>();

console.log(contents);

const searchQuery = ref('');
const selectedTopic = ref(null);
const newMessage = ref('');
const newTopicTitle = ref('');
const newTopicDescription = ref('');
const showCreateModal = ref<boolean>(false);

// Filter topics based on search query
const filteredTopics = computed(() => {
    if (!searchQuery.value) return topics.value;
    const query = searchQuery.value.toLowerCase();
    return topics.value.filter((topic) => topic.title.toLowerCase().includes(query) || topic.description.toLowerCase().includes(query));
});

// Get last activity time for a topic
const getLastActivity = (topic) => {
    if (topic.messages.length === 0) return 'No activity';
    return topic.messages[topic.messages.length - 1].time;
};

// Select a topic to view
const selectTopic = (topic) => {
    selectedTopic.value = topic;
    newMessage.value = '';
};

// Add a message to the selected topic
const addMessage = () => {
    if (!newMessage.value.trim()) return;

    selectedTopic.value.messages.push({
        content: newMessage.value,
        time: 'Just now',
    });

    newMessage.value = '';
};

// Create a new topic
const createTopic = () => {
    if (!newTopicTitle.value.trim()) return;

    const newTopic = {
        id: topics.value.length + 1,
        title: newTopicTitle.value,
        description: newTopicDescription.value || 'No description provided.',
        messages: [],
    };

    const nwTopic = useForm({
        title: newTopicTitle.value,
        description: newTopicDescription.value,
    });

    topics.value.push(newTopic);
    selectTopic(newTopic);
    newTopicTitle.value = '';
    newTopicDescription.value = '';
    showCreateModal.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-4 sm:p-6">
        <!-- Header and Search -->
        <div class="mx-auto mb-8 max-w-7xl">
            <header class="mb-8 text-center">
                <h1 class="mb-2 text-4xl font-bold text-slate-800">Echo Chamber</h1>
                <p class="mx-auto max-w-2xl text-lg text-slate-600">Join conversations that matter to you or start your own topic</p>
            </header>

            <div class="mx-auto flex max-w-3xl flex-col gap-4 sm:flex-row">
                <div class="relative flex-grow">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search topics..."
                        class="block w-full rounded-lg border-slate-200 bg-white py-3 pl-5 pr-12 text-slate-700 shadow-sm focus:border-transparent focus:ring-2 focus:ring-blue-500"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <button
                    @click="showCreateModal = true"
                    class="flex items-center justify-center whitespace-nowrap rounded-lg bg-blue-600 px-6 py-3 text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Topic
                </button>
            </div>
        </div>

        <!-- Topic Cards Grid -->
        <div class="mx-auto max-w-7xl">
            <div v-if="filteredTopics.length === 0" class="py-12 text-center">
                <div class="mb-4 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                        />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-medium text-slate-700">No topics found</h3>
                <p class="mb-6 text-slate-500">Try a different search or create a new topic</p>
                <button
                    @click="showCreateModal = true"
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Topic
                </button>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="topic in filteredTopics"
                    :key="topic.id"
                    class="cursor-pointer overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md"
                    @click="selectTopic(topic)"
                >
                    <div class="p-6">
                        <h3 class="mb-2 text-xl font-semibold text-slate-800">{{ topic.title }}</h3>
                        <p class="mb-4 line-clamp-2 text-slate-600">{{ topic.description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                {{ topic.messages.length }} {{ topic.messages.length === 1 ? 'message' : 'messages' }}
                            </span>
                            <span class="text-xs text-slate-500">Last activity: {{ getLastActivity(topic) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topic Detail Modal -->
        <div v-if="selectedTopic" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-xl bg-white shadow-xl" @click.stop>
                <div class="flex items-center justify-between bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4 text-white">
                    <h3 class="text-xl font-semibold">{{ selectedTopic.title }}</h3>
                    <button @click="selectedTopic = null" class="rounded-full bg-white/20 p-1.5 text-white transition-colors hover:bg-white/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="border-b border-slate-200 bg-slate-50 px-6 py-3">
                    <p class="text-slate-600">{{ selectedTopic.description }}</p>
                </div>

                <!-- Messages -->
                <div class="flex-grow space-y-4 overflow-y-auto p-6">
                    <div
                        v-for="(message, index) in selectedTopic.messages"
                        :key="index"
                        class="rounded-lg border border-slate-100 bg-slate-50 p-4 shadow-sm"
                    >
                        <div class="text-slate-800">{{ message.content }}</div>
                        <div class="mt-2 flex items-center text-xs text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            {{ message.time }}
                        </div>
                    </div>

                    <div v-if="selectedTopic.messages.length === 0" class="py-8 text-center italic text-slate-500">
                        No messages yet. Be the first to contribute!
                    </div>
                </div>

                <!-- New Message Input -->
                <div class="border-t border-slate-100 bg-white p-4">
                    <div class="flex space-x-2">
                        <input
                            type="text"
                            v-model="newMessage"
                            placeholder="Add to the conversation..."
                            class="flex-grow rounded-lg border-slate-200 py-3 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            @keyup.enter="addMessage"
                        />
                        <button
                            @click="addMessage"
                            :disabled="!newMessage.trim()"
                            class="rounded-lg bg-blue-600 px-5 py-3 text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Topic Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div class="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl" @click.stop>
                <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4 text-white">
                    <h3 class="text-lg font-semibold">Create New Topic</h3>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <label for="modal-topic-title" class="mb-1 block text-sm font-medium text-slate-700">Topic Title</label>
                        <input
                            type="text"
                            id="modal-topic-title"
                            v-model="newTopicTitle"
                            placeholder="What would you like to discuss?"
                            class="block w-full rounded-lg border-slate-200 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="modal-topic-description" class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                        <textarea
                            id="modal-topic-description"
                            v-model="newTopicDescription"
                            placeholder="Provide some context about your topic..."
                            rows="3"
                            class="block w-full rounded-lg border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            @click="showCreateModal = false"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Cancel
                        </button>
                        <button
                            @click="createTopic"
                            :disabled="!newTopicTitle.trim()"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Create Topic
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
