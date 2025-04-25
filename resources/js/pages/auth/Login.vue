<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { BookOpen, GraduationCap, LoaderCircle, Lock, Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Welcome to EduWave" description="Your journey to knowledge begins here">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 animate-pulse rounded-md bg-green-50 p-3 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="mt-4 flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid transform gap-2 transition-all duration-300 hover:scale-[1.01]">
                    <Label for="email" class="flex items-center gap-2 text-teal-700">
                        <Mail class="h-4 w-4" />
                        Email address
                    </Label>
                    <div class="relative">
                        <Input
                            id="email"
                            type="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            v-model="form.email"
                            placeholder="email@example.com"
                            class="border-teal-200 pl-10 transition-all duration-300 focus:border-teal-500 focus:ring-teal-500"
                        />
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <BookOpen class="h-5 w-5 text-teal-500" />
                        </div>
                    </div>
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid transform gap-2 transition-all duration-300 hover:scale-[1.01]">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="flex items-center gap-2 text-teal-700">
                            <Lock class="h-4 w-4" />
                            Password
                        </Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-teal-600 transition-colors duration-300 hover:text-teal-800"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Password"
                        class="border-teal-200 transition-all duration-300 focus:border-teal-500 focus:ring-teal-500"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between" :tabindex="3">
                    <Label for="remember" class="flex items-center space-x-3 text-teal-700">
                        <Checkbox id="remember" v-model:checked="form.remember" :tabindex="4" class="text-teal-600 focus:ring-teal-500" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full transform bg-gradient-to-r from-teal-500 to-cyan-600 shadow-lg transition-all duration-300 hover:scale-[1.02] hover:from-teal-600 hover:to-cyan-700 hover:shadow-xl"
                    :tabindex="4"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                    <span v-else class="mr-2">✓</span>
                    Log in
                </Button>
            </div>

            <div class="relative py-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-2 text-gray-500 dark:bg-black dark:text-white">Or continue with</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-50"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4"
                        />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853"
                        />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05"
                        />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335"
                        />
                    </svg>
                    Google
                </button>
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-50"
                >
                    <svg class="h-5 w-5 text-[#24292F]" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                        />
                    </svg>
                    GitHub
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-gray-600 dark:text-white">
                <div class="inline-block animate-bounce">🎓</div>
                Don't have an account?
                <TextLink
                    :href="route('register')"
                    :tabindex="5"
                    class="font-medium text-teal-600 transition-colors duration-300 hover:text-teal-800"
                >
                    Sign up for EduWave
                </TextLink>
            </div>
        </form>

        <div class="mt-8 flex justify-center space-x-6">
            <div class="flex animate-pulse flex-col items-center">
                <div class="rounded-full bg-teal-100 p-3">
                    <BookOpen class="h-6 w-6 text-teal-600" />
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-white">Learn</p>
            </div>
            <div class="flex animate-pulse flex-col items-center" style="animation-delay: 0.2s">
                <div class="rounded-full bg-cyan-100 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                        />
                    </svg>
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-white">Discover</p>
            </div>
            <div class="flex animate-pulse flex-col items-center" style="animation-delay: 0.4s">
                <div class="rounded-full bg-purple-100 p-3">
                    <GraduationCap class="h-6 w-6 text-purple-600" />
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-white">Achieve</p>
            </div>
        </div>
    </AuthBase>
</template>

<style scoped>
@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
    100% {
        transform: translateY(0px);
    }
}

.animate-pulse {
    animation: float 3s ease-in-out infinite;
}
</style>
