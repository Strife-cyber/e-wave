<script lang="ts" setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3';

const topics = ref([

])

defineProps<{
    contents: object
}>();

console.log(contents)

const searchQuery = ref('')
const selectedTopic = ref(null)
const newMessage = ref('')
const newTopicTitle = ref('')
const newTopicDescription = ref('')
const showCreateModal = ref<boolean>(false)

// Filter topics based on search query
const filteredTopics = computed(() => {
    if (!searchQuery.value) return topics.value
    const query = searchQuery.value.toLowerCase()
    return topics.value.filter(topic =>
        topic.title.toLowerCase().includes(query) ||
        topic.description.toLowerCase().includes(query)
    )
})

// Get last activity time for a topic
const getLastActivity = (topic) => {
    if (topic.messages.length === 0) return 'No activity'
    return topic.messages[topic.messages.length - 1].time
}

// Select a topic to view
const selectTopic = (topic) => {
    selectedTopic.value = topic
    newMessage.value = ''
}

// Add a message to the selected topic
const addMessage = () => {
    if (!newMessage.value.trim()) return

    selectedTopic.value.messages.push({
        content: newMessage.value,
        time: 'Just now'
    })

    newMessage.value = ''
}

// Create a new topic
const createTopic = () => {
    if (!newTopicTitle.value.trim()) return

    const newTopic = {
        id: topics.value.length + 1,
        title: newTopicTitle.value,
        description: newTopicDescription.value || 'No description provided.',
        messages: []
    }

    const nwTopic = useForm({
        title: newTopicTitle.value,
        description: newTopicDescription.value,
    })

    topics.value.push(newTopic)
    selectTopic(newTopic)
    newTopicTitle.value = ''
    newTopicDescription.value = ''
    showCreateModal.value = false
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-4 sm:p-6">
        <!-- Header and Search -->
        <div class="max-w-7xl mx-auto mb-8">
            <header class="text-center mb-8">
                <h1 class="text-4xl font-bold text-slate-800 mb-2">Echo Chamber</h1>
                <p class="text-slate-600 text-lg max-w-2xl mx-auto">
                    Join conversations that matter to you or start your own topic
                </p>
            </header>

            <div class="flex flex-col sm:flex-row gap-4 max-w-3xl mx-auto">
                <div class="relative flex-grow">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search topics..."
                        class="block w-full rounded-lg border-slate-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent pl-5 pr-12 py-3 text-slate-700 shadow-sm"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <button
                    @click="showCreateModal = true"
                    class="py-3 px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors flex items-center justify-center whitespace-nowrap shadow-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Topic
                </button>
            </div>
        </div>

        <!-- Topic Cards Grid -->
        <div class="max-w-7xl mx-auto">
            <div v-if="filteredTopics.length === 0" class="text-center py-12">
                <div class="text-slate-400 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-slate-700 mb-2">No topics found</h3>
                <p class="text-slate-500 mb-6">Try a different search or create a new topic</p>
                <button
                    @click="showCreateModal = true"
                    class="py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors inline-flex items-center justify-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Topic
                </button>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="topic in filteredTopics"
                    :key="topic.id"
                    class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-200 cursor-pointer"
                    @click="selectTopic(topic)"
                >
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-slate-800 mb-2">{{ topic.title }}</h3>
                        <p class="text-slate-600 mb-4 line-clamp-2">{{ topic.description }}</p>
                        <div class="flex items-center justify-between">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ topic.messages.length }} {{ topic.messages.length === 1 ? 'message' : 'messages' }}
              </span>
                            <span class="text-xs text-slate-500">Last activity: {{ getLastActivity(topic) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Topic Detail Modal -->
        <div v-if="selectedTopic" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden" @click.stop>
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white flex justify-between items-center">
                    <h3 class="text-xl font-semibold">{{ selectedTopic.title }}</h3>
                    <button
                        @click="selectedTopic = null"
                        class="p-1.5 rounded-full bg-white/20 hover:bg-white/30 text-white transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-3 bg-slate-50 border-b border-slate-200">
                    <p class="text-slate-600">{{ selectedTopic.description }}</p>
                </div>

                <!-- Messages -->
                <div class="flex-grow overflow-y-auto p-6 space-y-4">
                    <div
                        v-for="(message, index) in selectedTopic.messages"
                        :key="index"
                        class="p-4 rounded-lg bg-slate-50 border border-slate-100 shadow-sm"
                    >
                        <div class="text-slate-800">{{ message.content }}</div>
                        <div class="text-xs text-slate-500 mt-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ message.time }}
                        </div>
                    </div>

                    <div v-if="selectedTopic.messages.length === 0" class="py-8 text-slate-500 italic text-center">
                        No messages yet. Be the first to contribute!
                    </div>
                </div>

                <!-- New Message Input -->
                <div class="p-4 border-t border-slate-100 bg-white">
                    <div class="flex space-x-2">
                        <input
                            type="text"
                            v-model="newMessage"
                            placeholder="Add to the conversation..."
                            class="flex-grow rounded-lg border-slate-200 focus:ring-blue-500 focus:border-blue-500 py-3 shadow-sm"
                            @keyup.enter="addMessage"
                        />
                        <button
                            @click="addMessage"
                            :disabled="!newMessage.trim()"
                            class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Topic Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden" @click.stop>
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
                    <h3 class="text-lg font-semibold">Create New Topic</h3>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <label for="modal-topic-title" class="block text-sm font-medium text-slate-700 mb-1">Topic Title</label>
                        <input
                            type="text"
                            id="modal-topic-title"
                            v-model="newTopicTitle"
                            placeholder="What would you like to discuss?"
                            class="block w-full rounded-lg border-slate-200 focus:ring-blue-500 focus:border-blue-500 py-2 shadow-sm"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="modal-topic-description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea
                            id="modal-topic-description"
                            v-model="newTopicDescription"
                            placeholder="Provide some context about your topic..."
                            rows="3"
                            class="block w-full rounded-lg border-slate-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                        ></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            @click="showCreateModal = false"
                            class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="createTopic"
                            :disabled="!newTopicTitle.trim()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Create Topic
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
