// src/services/MessagingService.ts
import { database } from '@/firebase';
import { Message, User } from '@/types';
import { get, onChildAdded, orderByChild, push, query, ref as dbRef, update } from 'firebase/database';

class MessagingService {
    private readonly messagesRef;
    private readonly typingRef;

    constructor(group: string) {
        if (!group) throw new Error('Group ID is required');
        this.messagesRef = dbRef(database, `messages/${group}`);
        this.typingRef = dbRef(database, `typing/${group}`);
    }

    // Send a message
    async sendMessage(message: Message): Promise<void> {
        if (!message) {
            throw new Error('Message required');
        }
        try {
            console.log(message);
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : String(error);
            throw new Error(`Failed to send message: ${errorMessage}`);
        }
    }

    // Listen for new messages in real-time
    listenToMessages(callback: (message: Message) => void): () => void {
        const messagesQuery = query(this.messagesRef, orderByChild('timestamp'));
        return onChildAdded(
            messagesQuery,
            (snapshot) => {
                const data = snapshot.val();
                callback({
                    id: snapshot.key || '',
                    text: data.text,
                    user: data.user,
                    timestamp: data.timestamp,
                    status: data.status,
                    attachments: data.attachments,
                    reactions: data.reactions,
                });
            },
            (error) => {
                console.error('Failed to listen to messages:', error);
            },
        );
    }

    // Fetch all messages (for initial load)
    async getAllMessages(): Promise<Message[]> {
        try {
            const messagesQuery = query(this.messagesRef, orderByChild('timestamp'));
            const snapshot = await get(messagesQuery);
            const messages: Message[] = [];

            if (snapshot.exists()) {
                snapshot.forEach((childSnapshot) => {
                    const data = childSnapshot.val();
                    messages.push({
                        id: childSnapshot.key || '',
                        text: data.text,
                        user: data.user,
                        timestamp: data.timestamp,
                        status: data.status,
                        attachments: data.attachments,
                        reactions: data.reactions,
                    });
                });
            }
            return messages;
        } catch (error) {
            const message = error instanceof Error ? error.message : String(error);
            throw new Error(`Failed to fetch messages: ${message}`);
        }
    }

    // Fetch messages before a specific message
    async getMessagesBefore(messageId: string): Promise<Message[]> {
        try {
            // In a real implementation, you'd use Firebase queries with limits
            // This is a simplified version
            const snapshot = await get(this.messagesRef);
            const messages: Message[] = [];

            if (snapshot.exists()) {
                snapshot.forEach((childSnapshot) => {
                    if (childSnapshot.key !== messageId) {
                        const data = childSnapshot.val();
                        messages.push({
                            id: childSnapshot.key || '',
                            text: data.text,
                            user: data.user,
                            timestamp: data.timestamp,
                            status: data.status,
                            attachments: data.attachments,
                            reactions: data.reactions,
                        });
                    }
                });
            }
            return messages.sort((a, b) => a.timestamp.localeCompare(b.timestamp));
        } catch (error) {
            const message = error instanceof Error ? error.message : String(error);
            throw new Error(`Failed to fetch older messages: ${message}`);
        }
    }

    // Send typing indicator
    async sendTypingIndicator(user: User): Promise<void> {
        try {
            await update(this.typingRef, {
                [user.id]: {
                    id: user.id,
                    name: user.name,
                    timestamp: Date.now(),
                },
            });
        } catch (error) {
            console.error('Failed to send typing indicator:', error);
        }
    }

    // Clear typing indicator
    async clearTypingIndicator(user: User): Promise<void> {
        try {
            await update(this.typingRef, {
                [user.id]: null,
            });
        } catch (error) {
            console.error('Failed to clear typing indicator:', error);
        }
    }

    // Listen for typing indicators
    listenToTypingIndicators(callback: (users: User[]) => void): () => void {
        return onChildAdded(
            this.typingRef,
            (snapshot) => {
                const users: User[] = [];
                snapshot.forEach((childSnapshot) => {
                    const user = childSnapshot.val();
                    if (user) users.push(user);
                });
                callback(users);
            },
            (error) => {
                console.error('Failed to listen to typing indicators:', error);
            },
        );
    }

    // Add reaction to a message
    async addReaction(messageId: string, emoji: string, user: User): Promise<void> {
        try {
            const messageRef = dbRef(database, `messages/${this.messagesRef.key}/${messageId}`);
            const snapshot = await get(messageRef);
            if (!snapshot.exists()) throw new Error('Message not found');

            const message = snapshot.val();
            const reactions = message.reactions || [];
            const existingReaction = reactions.find((r: any) => r.emoji === emoji);

            if (existingReaction) {
                if (!existingReaction.users.some((u: User) => u.id === user.id)) {
                    existingReaction.users.push(user);
                }
            } else {
                reactions.push({ emoji, users: [user] });
            }

            await update(messageRef, { reactions });
        } catch (error) {
            const message = error instanceof Error ? error.message : String(error);
            throw new Error(`Failed to add reaction: ${message}`);
        }
    }

    // Mark messages as read
    async markMessagesAsRead(): Promise<void> {
        try {
            const snapshot = await get(this.messagesRef);
            const updates: { [key: string]: any } = {};

            snapshot.forEach((childSnapshot) => {
                const message = childSnapshot.val();
                if (message.status !== 'read') {
                    updates[`${childSnapshot.key}/status`] = 'read';
                }
            });

            if (Object.keys(updates).length > 0) {
                await update(this.messagesRef, updates);
            }
        } catch (error) {
            console.error('Failed to mark messages as read:', error);
        }
    }
}

// Export factory function to create messaging service instances
export default function createMessagingService(group: string) {
    return new MessagingService(group);
}
