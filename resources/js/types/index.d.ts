import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    blank: {
        type: boolean;
        default: false;
    };
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    profile?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Lesson {
    id: number;
    course_id: number;
    title: string;
    type: 'video' | 'quiz' | 'game' | 'html' | 'other';
    content: string;
    order_no: number;
    created_at: string;
    updated_at: string;
    bright: boolean;
}

export interface Group {
    id: number;
    name: string;
    course_id: number;
    created_by: number;
    bright: string;
}

export interface Course {
    id: number;
    title: string;
    description: string;
    text: string;
    bright: string;
    created_by: number;
    lessons: Lesson[];
    groups: Group[];
}

export interface Message {
    id: string;
    text: string;
    user: User;
    timestamp: string;
    status: 'sending' | 'sent' | 'delivered' | 'read';
    attachments?: Array<{
        type: string;
        url: string;
        name: string;
        size?: number;
    }>;
    reactions?: Array<{
        emoji: string;
        users: User[];
    }>;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface AuthPayload {
    token: string;
    displayName: string | null;
    photoUrl: string | null;
    email: string | null;
    emailVerified: boolean;
}
