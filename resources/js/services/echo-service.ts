import { database } from '@/firebase';
import { Message, User } from '@/types';
import axios from 'axios';
import { get, onValue, orderByChild, push, ref as dbRef, update } from 'firebase/database';

// Assuming Message and User types are defined in '@/types'
interface Message {
    id?: string;
    content: string;
    userId: string;
    timestamp: number;
    processedContent?: string; // For AI-generated flashcards or lessons
}

interface User {
    id: string;
    role: string; // e.g., 'student', 'teacher', 'admin'
}

class EchoService {
    private readonly messagesRef;
    private readonly apiToken = process.env.VUE_APP_AI_API_TOKEN; // Securely store AI API token

    constructor(echo: string) {
        if (!echo) throw new Error('Echo Id is required');
        this.messagesRef = dbRef(database, `messages/${echo}`);
    }

    /**
     * Send a message to the Echo Chamber and process it with AI based on user role
     * @param message The message content to send
     * @param user The authenticated user
     * @returns Promise<void>
     */
    async sendMessage(message: string, user: User): Promise<void> {
        if (!message.trim()) throw new Error('Message content is required');

        const newMessageRef = push(this.messagesRef);
        const messageData: Message = {
            content: message,
            userId: user.id,
            timestamp: Date.now(),
        };

        // Send message to Firebase
        await update(newMessageRef, messageData);

        // Process message with AI based on user role
        await this.processMessage(newMessageRef.key!, messageData, user.role);
    }

    /**
     * Retrieve all messages for the Echo Chamber, ordered by timestamp
     * @returns Promise<Message[]>
     */
    async getMessages(): Promise<Message[]> {
        const messagesQuery = query(this.messagesRef, orderByChild('timestamp'));
        const snapshot = await get(messagesQuery);

        const messages: Message[] = [];
        snapshot.forEach((childSnapshot) => {
            messages.push({
                id: childSnapshot.key,
                ...childSnapshot.val(),
            } as Message);
        });

        return messages.sort((a, b) => a.timestamp - b.timestamp);
    }

    /**
     * Subscribe to real-time updates of messages
     * @param callback Function to call with updated messages
     */
    subscribeToMessages(callback: (messages: Message[]) => void): () => void {
        const messagesQuery = query(this.messagesRef, orderByChild('timestamp'));
        return onValue(messagesQuery, (snapshot) => {
            const messages: Message[] = [];
            snapshot.forEach((childSnapshot) => {
                messages.push({
                    id: childSnapshot.key,
                    ...childSnapshot.val(),
                } as Message);
            });
            callback(messages.sort((a, b) => a.timestamp - b.timestamp));
        });
    }

    /**
     * Process message content with AI and update with processed content
     * @param messageId The ID of the message in Firebase
     * @param message The message data
     * @param role The user's role
     * @returns Promise<void>
     */
    private async processMessage(messageId: string, message: Message, role: string): Promise<void> {
        try {
            const response = await axios.post(
                'https://api-inference.huggingface.co/models/facebook/bart-large-cnn',
                { inputs: message.content },
                {
                    headers: { Authorization: `Bearer ${this.apiToken}` },
                },
            );

            const summary = response.data[0]?.summary_text || message.content;
            let processedContent: string | object;

            // Role-based processing
            switch (role) {
                case 'student':
                    processedContent = this.generateFlashcards(summary);
                    break;
                case 'teacher':
                    processedContent = `Summary for teacher: ${summary}`;
                    break;
                default:
                    processedContent = summary;
            }

            // Update message with processed content
            await update(dbRef(database, `messages/${messageId}`), {
                processedContent,
            });
        } catch (error) {
            console.error('AI processing failed:', error);
            // Optionally store raw content if AI fails
            await update(dbRef(database, `messages/${messageId}`), {
                processedContent: 'Processing failed',
            });
        }
    }

    /**
     * Generate simple flashcards from text (mimics Echo Chamber logic)
     * @param text The text to process
     * @returns Object with flashcards
     */
    private generateFlashcards(text: string): { questions: string[] } {
        const keywords = text.split(' ').slice(0, 5); // Take first 5 words as questions
        return {
            questions: keywords.map((keyword) => `What is ${keyword}?`),
        };
    }
}

export default EchoService;
