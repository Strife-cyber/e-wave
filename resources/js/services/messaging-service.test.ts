import { database } from '@/firebase';
import createMessagingService, { Message } from '@/services/MessagingService';
import { User } from '@/types';
import { ref as dbRef, get, onChildAdded, push, update } from 'firebase/database';

describe('MessagingService', () => {
    let messagingService: ReturnType<typeof createMessagingService>;
    const groupId = 'test-group';
    const user: User = { id: 'user1', name: 'Test User' };
    const mockMessage: Message = {
        id: 'msg1',
        text: 'Hello',
        user,
        timestamp: '2025-04-30T12:00:00Z',
        status: 'sent',
    };

    beforeEach(() => {
        jest.clearAllMocks();
        messagingService = createMessagingService(groupId);
    });

    describe('constructor', () => {
        it('throws error if group ID is empty', () => {
            expect(() => createMessagingService('')).toThrow('Group ID is required');
        });

        it('initializes messagesRef and typingRef correctly', () => {
            expect(dbRef).toHaveBeenCalledWith(database, `messages/${groupId}`);
            expect(dbRef).toHaveBeenCalledWith(database, `typing/${groupId}`);
        });
    });

    describe('sendMessage', () => {
        it('throws error if user or text is missing', async () => {
            await expect(messagingService.sendMessage(null as any, 'text')).rejects.toThrow('User and text are required');
            await expect(messagingService.sendMessage(user, '')).rejects.toThrow('User and text are required');
        });

        it('sends a message without attachments successfully', async () => {
            const mockKey = 'msg1';
            (push as jest.Mock).mockResolvedValue({ key: mockKey });
            const result = await messagingService.sendMessage(user, 'Hello');
            expect(push).toHaveBeenCalledWith(expect.anything(), {
                text: 'Hello',
                user,
                timestamp: expect.any(String),
                status: 'sent',
            });
            expect(result).toBe(mockKey);
        });

        it('sends a message with attachments', async () => {
            const file = new File(['content'], 'test.txt', { type: 'text/plain' });
            (push as jest.Mock).mockResolvedValue({ key: 'msg1' });
            const result = await messagingService.sendMessage(user, 'Hello', [file]);
            expect(push).toHaveBeenCalledWith(expect.anything(), {
                text: 'Hello',
                user,
                timestamp: expect.any(String),
                status: 'sent',
                attachments: [{ type: 'text/plain', url: 'mocked-url', name: 'test.txt', size: file.size }],
            });
            expect(URL.createObjectURL).toHaveBeenCalledWith(file);
            expect(result).toBe('msg1');
        });

        it('handles Firebase errors', async () => {
            (push as jest.Mock).mockRejectedValue(new Error('Firebase error'));
            await expect(messagingService.sendMessage(user, 'Hello')).rejects.toThrow('Failed to send message: Firebase error');
        });
    });

    describe('listenToMessages', () => {
        it('subscribes to new messages and calls callback', () => {
            const callback = jest.fn();
            (onChildAdded as jest.Mock).mockImplementation((_, callback) => {
                callback({ key: 'msg1', val: () => mockMessage });
                return jest.fn();
            });
            const unsubscribe = messagingService.listenToMessages(callback);
            expect(onChildAdded).toHaveBeenCalledWith(expect.anything(), expect.any(Function), expect.any(Function));
            expect(callback).toHaveBeenCalledWith(mockMessage);
            expect(unsubscribe).toBeInstanceOf(Function);
        });

        it('handles errors in listener', () => {
            const consoleError = jest.spyOn(console, 'error').mockImplementation();
            (onChildAdded as jest.Mock).mockImplementation((_, __, errorCallback) => {
                errorCallback(new Error('Listener error'));
                return jest.fn();
            });
            messagingService.listenToMessages(jest.fn());
            expect(console.error).toHaveBeenCalledWith('Failed to listen to messages:', expect.any(Error));
            consoleError.mockRestore();
        });
    });

    describe('getAllMessages', () => {
        it('fetches all messages successfully', async () => {
            (get as jest.Mock).mockResolvedValue({
                exists: () => true,
                forEach: (cb: any) => {
                    cb({ key: 'msg1', val: () => mockMessage });
                },
            });
            const messages = await messagingService.getAllMessages();
            expect(get).toHaveBeenCalledWith(expect.anything());
            expect(messages).toEqual([mockMessage]);
        });

        it('returns empty array if no messages exist', async () => {
            (get as jest.Mock).mockResolvedValue({ exists: () => false });
            const messages = await messagingService.getAllMessages();
            expect(messages).toEqual([]);
        });

        it('handles Firebase errors', async () => {
            (get as jest.Mock).mockRejectedValue(new Error('Fetch error'));
            await expect(messagingService.getAllMessages()).rejects.toThrow('Failed to fetch messages: Fetch error');
        });
    });

    describe('getMessagesBefore', () => {
        it('fetches messages before a specific message', async () => {
            (get as jest.Mock).mockResolvedValue({
                exists: () => true,
                forEach: (cb: any) => {
                    cb({ key: 'msg2', val: () => ({ ...mockMessage, id: 'msg2', timestamp: '2025-04-29T12:00:00Z' }) });
                },
            });
            const messages = await messagingService.getMessagesBefore('msg1');
            expect(messages).toEqual([{ ...mockMessage, id: 'msg2', timestamp: '2025-04-29T12:00:00Z' }]);
        });

        it('returns empty array if no messages exist', async () => {
            (get as jest.Mock).mockResolvedValue({ exists: () => false });
            const messages = await messagingService.getMessagesBefore('msg1');
            expect(messages).toEqual([]);
        });

        it('handles Firebase errors', async () => {
            (get as jest.Mock).mockRejectedValue(new Error('Fetch error'));
            await expect(messagingService.getMessagesBefore('msg1')).rejects.toThrow('Failed to fetch older messages: Fetch error');
        });
    });

    describe('sendTypingIndicator', () => {
        it('sends typing indicator successfully', async () => {
            (update as jest.Mock).mockResolvedValue(undefined);
            await messagingService.sendTypingIndicator(user);
            expect(update).toHaveBeenCalledWith(expect.anything(), {
                [user.id]: { id: user.id, name: user.name, timestamp: expect.any(Number) },
            });
        });

        it('logs error on failure', async () => {
            const consoleError = jest.spyOn(console, 'error').mockImplementation();
            (update as jest.Mock).mockRejectedValue(new Error('Update error'));
            await messagingService.sendTypingIndicator(user);
            expect(console.error).toHaveBeenCalledWith('Failed to send typing indicator:', expect.any(Error));
            consoleError.mockRestore();
        });
    });

    describe('clearTypingIndicator', () => {
        it('clears typing indicator successfully', async () => {
            (update as jest.Mock).mockResolvedValue(undefined);
            await messagingService.clearTypingIndicator(user);
            expect(update).toHaveBeenCalledWith(expect.anything(), { [user.id]: null });
        });

        it('logs error on failure', async () => {
            const consoleError = jest.spyOn(console, 'error').mockImplementation();
            (update as jest.Mock).mockRejectedValue(new Error('Update error'));
            await messagingService.clearTypingIndicator(user);
            expect(console.error).toHaveBeenCalledWith('Failed to clear typing indicator:', expect.any(Error));
            consoleError.mockRestore();
        });
    });

    describe('listenToTypingIndicators', () => {
        it('subscribes to typing indicators and calls callback', () => {
            const callback = jest.fn();
            (onChildAdded as jest.Mock).mockImplementation((_, callback) => {
                callback({
                    forEach: (cb: any) => cb({ val: () => user }),
                });
                return jest.fn();
            });
            const unsubscribe = messagingService.listenToTypingIndicators(callback);
            expect(callback).toHaveBeenCalledWith([user]);
            expect(unsubscribe).toBeInstanceOf(Function);
        });

        it('handles errors in listener', () => {
            const consoleError = jest.spyOn(console, 'error').mockImplementation();
            (onChildAdded as jest.Mock).mockImplementation((_, __, errorCallback) => {
                errorCallback(new Error('Listener error'));
                return jest.fn();
            });
            messagingService.listenToTypingIndicators(jest.fn());
            expect(console.error).toHaveBeenCalledWith('Failed to listen to typing indicators:', expect.any(Error));
            consoleError.mockRestore();
        });
    });

    describe('addReaction', () => {
        it('adds new reaction to a message', async () => {
            (get as jest.Mock).mockResolvedValue({
                exists: () => true,
                val: () => ({ ...mockMessage, reactions: [] }),
            });
            (update as jest.Mock).mockResolvedValue(undefined);
            await messagingService.addReaction('msg1', '😊', user);
            expect(update).toHaveBeenCalledWith(expect.anything(), {
                reactions: [{ emoji: '😊', users: [user] }],
            });
        });

        it('adds user to existing reaction', async () => {
            (get as jest.Mock).mockResolvedValue({
                exists: () => true,
                val: () => ({ ...mockMessage, reactions: [{ emoji: '😊', users: [] }] }),
            });
            (update as jest.Mock).mockResolvedValue(undefined);
            await messagingService.addReaction('msg1', '😊', user);
            expect(update).toHaveBeenCalledWith(expect.anything(), {
                reactions: [{ emoji: '😊', users: [user] }],
            });
        });

        it('does not duplicate user in existing reaction', async () => {
            (get as jest.Mock).mockResolvedValue({
                exists: () => true,
                val: () => ({ ...mockMessage, reactions: [{ emoji: '😊', users: [user] }] }),
            });
            (update as jest.Mock).mockResolvedValue(undefined);
            await messagingService.addReaction('msg1', '😊', user);
            expect(update).toHaveBeenCalledWith(expect.anything(), {
                reactions: [{ emoji: '😊', users: [user] }],
            });
        });

        it('throws error if message not found', async () => {
            (get as jest.Mock).mockResolvedValue({ exists: () => false });
            await expect(messagingService.addReaction('msg1', '😊', user)).rejects.toThrow('Message not found');
        });

        it('handles Firebase errors', async () => {
            (get as jest.Mock).mockRejectedValue(new Error('Fetch error'));
            await expect(messagingService.addReaction('msg1', '😊', user)).rejects.toThrow('Failed to add reaction: Fetch error');
        });
    });

    describe('markMessagesAsRead', () => {
        it('marks unread messages as read', async () => {
            (get as jest.Mock).mockResolvedValue({
                forEach: (cb: any) => {
                    cb({ key: 'msg1', val: () => ({ ...mockMessage, status: 'sent' }) });
                },
            });
            (update as jest.Mock).mockResolvedValue(undefined);
            await messagingService.markMessagesAsRead();
            expect(update).toHaveBeenCalledWith(expect.anything(), { 'msg1/status': 'read' });
        });

        it('skips messages already read', async () => {
            (get as jest.Mock).mockResolvedValue({
                forEach: (cb: any) => {
                    cb({ key: 'msg1', val: () => ({ ...mockMessage, status: 'read' }) });
                },
            });
            (update as jest.Mock).mockResolvedValue(undefined);
            await messagingService.markMessagesAsRead();
            expect(update).not.toHaveBeenCalled();
        });

        it('logs error on failure', async () => {
            const consoleError = jest.spyOn(console, 'error').mockImplementation();
            (get as jest.Mock).mockRejectedValue(new Error('Fetch error'));
            await messagingService.markMessagesAsRead();
            expect(console.error).toHaveBeenCalledWith('Failed to mark messages as read:', expect.any(Error));
            consoleError.mockRestore();
        });
    });
});
