<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-white font-sans text-gray-800 transition-colors duration-300 dark:bg-gray-900 dark:text-gray-200">
        <!-- Navigation -->
        <nav
            class="fixed left-0 top-0 z-50 w-full bg-white/95 shadow-sm backdrop-blur-lg transition-all duration-300 dark:bg-gray-900/95 dark:shadow-gray-800/20"
        >
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
                    <!-- Logo Section -->
                    <div class="flex items-center">
                        <AppLogo class="h-8 w-auto text-gray-900 dark:text-white" />
                    </div>

                    <!-- Navigation Links and Actions -->
                    <div class="flex items-center space-x-12">
                        <!-- Desktop Navigation -->
                        <div class="hidden items-center space-x-8 lg:flex">
                            <a
                                href="#features"
                                class="relative text-sm font-medium text-gray-600 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-sky-500 after:transition-all after:duration-300 hover:text-sky-500 hover:after:w-full dark:text-gray-300 dark:after:bg-sky-400 dark:hover:text-sky-400"
                            >
                                Features
                            </a>
                            <a
                                href="#collaboration"
                                class="relative text-sm font-medium text-gray-600 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-sky-500 after:transition-all after:duration-300 hover:text-sky-500 hover:after:w-full dark:text-gray-300 dark:after:bg-sky-400 dark:hover:text-sky-400"
                            >
                                Collaboration
                            </a>
                            <a
                                href="#gamification"
                                class="relative text-sm font-medium text-gray-600 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-sky-500 after:transition-all after:duration-300 hover:text-sky-500 hover:after:w-full dark:text-gray-300 dark:after:bg-sky-400 dark:hover:text-sky-400"
                            >
                                Gamification
                            </a>
                            <a
                                href="#echo-chamber"
                                class="relative text-sm font-medium text-gray-600 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-sky-500 after:transition-all after:duration-300 hover:text-sky-500 hover:after:w-full dark:text-gray-300 dark:after:bg-sky-400 dark:hover:text-sky-400"
                            >
                                Echo Chamber
                            </a>
                        </div>

                        <!-- Auth Conditional Navigation -->
                        <div class="hidden items-center lg:flex">
                            <Link
                                v-if="isUserLoggedIn"
                                :href="route('dashboard')"
                                class="inline-flex items-center rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                            >
                                Dashboard
                            </Link>
                            <div v-else class="flex items-center gap-2">
                                <Link
                                    :href="route('register')"
                                    class="block w-[120px] rounded-md bg-sky-500 px-5 py-2 text-center text-sm font-semibold text-white transition-all duration-200 hover:bg-sky-600 hover:shadow-md dark:bg-sky-600 dark:hover:bg-sky-700"
                                >
                                    Get Started
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="block w-[120px] rounded-md bg-sky-500 px-5 py-2 text-center text-sm font-semibold text-white transition-all duration-200 hover:bg-sky-600 hover:shadow-md dark:bg-sky-600 dark:hover:bg-sky-700"
                                >
                                    Login
                                </Link>
                            </div>
                        </div>

                        <!-- Mobile Menu Button -->
                        <button
                            class="rounded-md p-2 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-800 lg:hidden"
                            @click="toggleMobileMenu"
                        >
                            <svg
                                class="h-6 w-6 text-gray-600 dark:text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu (Hidden by Default) -->
                <div v-if="isMobileMenuOpen" class="border-t border-gray-200 bg-white py-4 dark:border-gray-800 dark:bg-gray-900 lg:hidden">
                    <div class="flex flex-col space-y-4">
                        <a
                            href="#features"
                            class="px-4 py-2 text-sm font-medium text-gray-600 transition-colors duration-200 hover:text-sky-500 dark:text-gray-300 dark:hover:text-sky-400"
                        >
                            Features
                        </a>
                        <a
                            href="#collaboration"
                            class="px-4 py-2 text-sm font-medium text-gray-600 transition-colors duration-200 hover:text-sky-500 dark:text-gray-300 dark:hover:text-sky-400"
                        >
                            Collaboration
                        </a>
                        <a
                            href="#gamification"
                            class="px-4 py-2 text-sm font-medium text-gray-600 transition-colors duration-200 hover:text-sky-500 dark:text-gray-300 dark:hover:text-sky-400"
                        >
                            Gamification
                        </a>
                        <a
                            href="#echo-chamber"
                            class="px-4 py-2 text-sm font-medium text-gray-600 transition-colors duration-200 hover:text-sky-500 dark:text-gray-300 dark:hover:text-sky-400"
                        >
                            Echo Chamber
                        </a>
                        <div class="px-4">
                            <Link
                                v-if="isUserLoggedIn"
                                :href="route('dashboard')"
                                class="block w-full rounded-md border border-gray-200 px-4 py-2 text-center text-sm font-medium text-gray-700 transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                            >
                                Dashboard
                            </Link>
                            <div v-else class="flex items-center gap-2">
                                <Link
                                    :href="route('register')"
                                    class="block w-full rounded-md bg-sky-500 px-5 py-2 text-center text-sm font-semibold text-white transition-all duration-200 hover:bg-sky-600 hover:shadow-md dark:bg-sky-600 dark:hover:bg-sky-700"
                                >
                                    Get Started
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="block w-full rounded-md bg-sky-500 px-5 py-2 text-center text-sm font-semibold text-white transition-all duration-200 hover:bg-sky-600 hover:shadow-md dark:bg-sky-600 dark:hover:bg-sky-700"
                                >
                                    Login
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section
            class="relative overflow-hidden bg-gradient-to-b from-white to-sky-50 pb-16 pt-32 transition-colors duration-300 dark:from-gray-900 dark:to-gray-800"
        >
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid items-center gap-12 md:grid-cols-2">
                    <div ref="heroText" class="translate-y-5 transform opacity-0 transition-all duration-1000 ease-out">
                        <h1 class="text-4xl font-extrabold leading-tight text-gray-900 dark:text-white md:text-5xl lg:text-6xl">
                            Next-Generation <br />
                            <span class="relative inline-block text-sky-500 dark:text-sky-400">
                                Collaborative Learning
                                <span class="absolute bottom-1 left-0 -z-10 h-3 w-full bg-sky-200/50 dark:bg-sky-700/30"></span>
                            </span>
                        </h1>
                        <p class="mt-6 max-w-lg text-lg text-gray-600 dark:text-gray-300">
                            EduWave redefines digital learning with a simple, powerful web-based platform that supports over 1 million concurrent
                            users, delivering real-time features with minimal delay.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <!-- Conditional navigation based on auth status -->
                            <Link
                                v-if="isUserLoggedIn"
                                :href="route('dashboard')"
                                class="rounded-lg bg-sky-500 px-6 py-3 text-lg font-semibold text-white transition-all hover:-translate-y-0.5 hover:bg-sky-600 hover:shadow-lg dark:bg-sky-600 dark:hover:bg-sky-700"
                            >
                                Go to Dashboard
                            </Link>
                            <button
                                v-else
                                class="rounded-lg bg-sky-500 px-6 py-3 text-lg font-semibold text-white transition-all hover:-translate-y-0.5 hover:bg-sky-600 hover:shadow-lg dark:bg-sky-600 dark:hover:bg-sky-700"
                            >
                                Start Learning
                            </button>
                            <button
                                class="rounded-lg border-2 border-sky-200 bg-sky-50 px-6 py-3 text-lg font-semibold text-sky-500 transition-all hover:-translate-y-0.5 hover:bg-sky-100 dark:border-sky-700 dark:bg-gray-800 dark:text-sky-400 dark:hover:bg-gray-700"
                            >
                                For Educators
                            </button>
                        </div>
                        <div class="mt-12 flex flex-wrap gap-8">
                            <div class="flex flex-col">
                                <span class="text-2xl font-bold text-sky-500 dark:text-sky-400">1M+</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Concurrent Users</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-2xl font-bold text-sky-500 dark:text-sky-400">500ms</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Collaboration Delay</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-2xl font-bold text-sky-500 dark:text-sky-400">3.2</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Current Version</span>
                            </div>
                        </div>
                    </div>

                    <div ref="heroImage" class="relative translate-x-5 transform opacity-0 transition-all delay-200 duration-1000 ease-out">
                        <!-- Floating Elements -->
                        <div class="absolute inset-0 z-10">
                            <div
                                class="animate-float absolute -left-5 top-[10%] flex items-center gap-2 rounded-2xl bg-white p-3 shadow-lg dark:bg-gray-800 dark:shadow-gray-900/50"
                            >
                                <BookOpenIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Interactive Lessons</span>
                            </div>
                            <div
                                class="animate-float animation-delay-500 absolute -right-5 top-[20%] flex items-center gap-2 rounded-2xl bg-white p-3 shadow-lg dark:bg-gray-800 dark:shadow-gray-900/50"
                            >
                                <UsersIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Real-time Collaboration</span>
                            </div>
                            <div
                                class="animate-float animation-delay-1000 absolute bottom-[20%] left-[10%] flex items-center gap-2 rounded-2xl bg-white p-3 shadow-lg dark:bg-gray-800 dark:shadow-gray-900/50"
                            >
                                <TrophyIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Gamification</span>
                            </div>
                            <div
                                class="animate-float animation-delay-1500 absolute bottom-[10%] right-[10%] flex items-center gap-2 rounded-2xl bg-white p-3 shadow-lg dark:bg-gray-800 dark:shadow-gray-900/50"
                            >
                                <MessageCircleIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Echo Chamber</span>
                            </div>
                        </div>

                        <!-- Platform Preview -->
                        <div
                            class="relative z-20 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:shadow-gray-900/50"
                        >
                            <!-- Platform Header -->
                            <div class="flex items-center border-b border-gray-200 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700">
                                <div class="mr-4 flex gap-2">
                                    <span class="h-3 w-3 rounded-full bg-red-400"></span>
                                    <span class="h-3 w-3 rounded-full bg-yellow-400"></span>
                                    <span class="h-3 w-3 rounded-full bg-green-400"></span>
                                </div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-300">EduWave Platform</div>
                            </div>

                            <!-- Platform Content -->
                            <div class="flex h-[300px]">
                                <!-- Sidebar -->
                                <div class="w-1/3 border-r border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-700">
                                    <div
                                        class="mb-2 flex cursor-pointer items-center gap-2 rounded-lg bg-sky-100 p-3 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300"
                                    >
                                        <BookOpenIcon size="16" />
                                        <span class="text-sm font-medium">Lessons</span>
                                    </div>
                                    <div
                                        class="mb-2 flex cursor-pointer items-center gap-2 rounded-lg p-3 transition-colors hover:bg-sky-50 dark:hover:bg-gray-600"
                                    >
                                        <UsersIcon size="16" class="text-gray-500 dark:text-gray-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Collaborate</span>
                                    </div>
                                    <div
                                        class="mb-2 flex cursor-pointer items-center gap-2 rounded-lg p-3 transition-colors hover:bg-sky-50 dark:hover:bg-gray-600"
                                    >
                                        <TrophyIcon size="16" class="text-gray-500 dark:text-gray-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Rewards</span>
                                    </div>
                                    <div
                                        class="flex cursor-pointer items-center gap-2 rounded-lg p-3 transition-colors hover:bg-sky-50 dark:hover:bg-gray-600"
                                    >
                                        <MessageCircleIcon size="16" class="text-gray-500 dark:text-gray-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Echo</span>
                                    </div>
                                </div>

                                <!-- Main Content -->
                                <div class="flex flex-1 flex-col gap-4 p-4">
                                    <!-- Lesson Card -->
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-700">
                                        <h3 class="mb-2 font-semibold text-gray-800 dark:text-gray-200">Introduction to Physics</h3>
                                        <div class="mb-1 h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600">
                                            <div
                                                class="animate-progress-load h-full rounded-full bg-sky-500 dark:bg-sky-400"
                                                style="width: 65%"
                                            ></div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">65% Complete</p>
                                    </div>

                                    <!-- Chat Preview -->
                                    <div class="flex flex-col gap-3">
                                        <div class="flex items-start gap-2">
                                            <div
                                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 font-semibold text-sky-700 dark:bg-sky-900/50 dark:text-sky-300"
                                            >
                                                S
                                            </div>
                                            <div
                                                class="max-w-[80%] rounded-2xl rounded-tl-none bg-gray-100 p-3 text-gray-800 dark:bg-gray-700 dark:text-gray-200"
                                            >
                                                What's momentum?
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <div
                                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-sky-500 font-semibold text-white dark:bg-sky-600"
                                            >
                                                E
                                            </div>
                                            <div
                                                class="max-w-[80%] rounded-2xl rounded-tr-none bg-sky-50 p-3 text-gray-800 dark:bg-sky-900/30 dark:text-gray-200"
                                            >
                                                <strong>Echo Card Created:</strong> "Momentum is the product of mass and velocity."
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wave Divider -->
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="h-auto w-full">
                    <path
                        fill="currentColor"
                        fill-opacity="1"
                        d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"
                        class="text-sky-50 dark:text-gray-800"
                    ></path>
                </svg>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="bg-sky-50 py-20 transition-colors duration-300 dark:bg-gray-800">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="mx-auto mb-16 max-w-3xl text-center">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white md:text-4xl">Core Learning Infrastructure</h2>
                    <p class="text-gray-600 dark:text-gray-300">Simplified yet powerful features that don't require advanced technical expertise</p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(feature, index) in features"
                        :key="index"
                        ref="featureCards"
                        class="translate-y-5 transform rounded-xl bg-white p-8 opacity-0 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-700 dark:shadow-gray-900/30 dark:hover:shadow-gray-900/50"
                    >
                        <div
                            class="mb-6 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300"
                        >
                            <component :is="feature.icon" size="32" />
                        </div>
                        <h3 class="mb-3 text-xl font-semibold text-gray-800 dark:text-white">{{ feature.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Collaboration Section -->
        <section id="collaboration" class="bg-white py-20 transition-colors duration-300 dark:bg-gray-900">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid items-center gap-12 md:grid-cols-2">
                    <div ref="collaborationImage" class="translate-x-5 transform opacity-0 transition-all duration-1000 ease-out">
                        <div class="grid h-[300px] grid-cols-3 gap-4">
                            <!-- Whiteboard -->
                            <div
                                class="col-span-2 flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800 dark:shadow-gray-900/30"
                            >
                                <div class="flex gap-2 border-b border-gray-200 p-3 dark:border-gray-700">
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
                                    <div class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
                                </div>
                                <div class="relative flex-1 bg-gray-50 dark:bg-gray-700">
                                    <div class="absolute left-[10%] top-[20%] h-1/5 w-2/5 rounded-lg bg-sky-200/50 dark:bg-sky-700/30"></div>
                                    <div class="absolute left-[30%] top-[50%] h-[30%] w-[30%] rounded-full bg-sky-300/50 dark:bg-sky-600/30"></div>
                                    <div class="h-1/10 absolute right-[10%] top-[30%] w-1/5 rounded-lg bg-sky-400/50 dark:bg-sky-500/30"></div>
                                    <div
                                        ref="cursor"
                                        class="absolute z-10 h-4 w-4 -translate-x-1/2 -translate-y-1/2 transform rounded-full bg-sky-500 transition-all duration-300 ease-out dark:bg-sky-400"
                                    ></div>
                                </div>
                            </div>

                            <!-- Video Call -->
                            <div class="overflow-hidden rounded-xl bg-slate-800 p-2 dark:bg-gray-700">
                                <div class="grid h-full grid-cols-2 grid-rows-2 gap-2">
                                    <div class="rounded-lg bg-slate-700 dark:bg-gray-600"></div>
                                    <div class="rounded-lg bg-slate-700 dark:bg-gray-600"></div>
                                    <div class="rounded-lg bg-slate-700 dark:bg-gray-600"></div>
                                    <div class="rounded-lg bg-slate-700 dark:bg-gray-600"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ref="collaborationText" class="-translate-x-5 transform opacity-0 transition-all duration-1000 ease-out">
                        <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white md:text-4xl">Real-Time Collaboration Suite</h2>
                        <p class="mb-6 text-lg text-gray-600 dark:text-gray-300">
                            Work together live via chat, shared whiteboards, and video calls. Our platform ensures minimal delay with under 500ms
                            latency, making collaboration feel natural and immediate.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="flex-shrink-0 text-sky-500 dark:text-sky-400" />
                                <span class="text-gray-700 dark:text-gray-300">Live chat with instant messaging</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="flex-shrink-0 text-sky-500 dark:text-sky-400" />
                                <span class="text-gray-700 dark:text-gray-300">Interactive whiteboards for visual collaboration</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="flex-shrink-0 text-sky-500 dark:text-sky-400" />
                                <span class="text-gray-700 dark:text-gray-300">Seamless video conferencing integration</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="flex-shrink-0 text-sky-500 dark:text-sky-400" />
                                <span class="text-gray-700 dark:text-gray-300">Automatic session recording and playback</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gamification Section -->
        <section id="gamification" class="bg-sky-50 py-20 transition-colors duration-300 dark:bg-gray-800">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid items-center gap-12 md:grid-cols-2">
                    <div ref="gamificationText" class="translate-x-5 transform opacity-0 transition-all duration-1000 ease-out md:order-2">
                        <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white md:text-4xl">Gamification Layer</h2>
                        <p class="mb-8 text-lg text-gray-600 dark:text-gray-300">
                            Keep learners motivated with points, badges, and adaptive learning paths. Our simple yet effective reward system makes
                            education engaging and fun.
                        </p>

                        <div class="space-y-4">
                            <div
                                v-for="(badge, index) in badges"
                                :key="index"
                                class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm transition-transform duration-300 hover:translate-x-1 dark:bg-gray-700 dark:shadow-gray-900/30"
                            >
                                <div :class="`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full ${getBadgeColor(index)}`">
                                    <component :is="badge.icon" size="24" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 dark:text-white">{{ badge.name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ badge.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ref="gamificationImage" class="-translate-x-5 transform opacity-0 transition-all duration-1000 ease-out md:order-1">
                        <div
                            class="rounded-xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-600 dark:bg-gray-700 dark:shadow-gray-900/30"
                        >
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Your Progress</h3>
                                <div class="rounded-full bg-sky-500 px-3 py-1 text-sm font-semibold text-white dark:bg-sky-600">Level 3</div>
                            </div>

                            <div class="mb-1 h-3 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600">
                                <div
                                    class="animate-progress-load h-full rounded-full bg-gradient-to-r from-sky-300 to-sky-500 dark:from-sky-600 dark:to-sky-400"
                                    style="width: 80%"
                                ></div>
                            </div>
                            <div class="mb-6 text-right text-xs text-gray-500 dark:text-gray-400">245/300 XP</div>

                            <div class="space-y-4">
                                <!-- Achievement 1 -->
                                <div
                                    class="flex items-center gap-4 rounded-lg bg-gray-50 p-4 transition-colors hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700"
                                >
                                    <div
                                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-500 dark:bg-sky-900/50 dark:text-sky-400"
                                    >
                                        <StarIcon size="20" />
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">Quiz Master</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Complete 10 quizzes with 80%+ score</p>
                                    </div>
                                    <div class="relative h-12 w-12 flex-shrink-0">
                                        <svg viewBox="0 0 36 36" class="h-full w-full">
                                            <path
                                                class="fill-none stroke-gray-200 stroke-2 dark:stroke-gray-600"
                                                d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                            <path
                                                class="fill-none stroke-sky-500 stroke-2 dark:stroke-sky-400"
                                                stroke-dasharray="80, 100"
                                                d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                        </svg>
                                        <span
                                            class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-gray-800 dark:text-gray-200"
                                            >8/10</span
                                        >
                                    </div>
                                </div>

                                <!-- Achievement 2 -->
                                <div
                                    class="flex items-center gap-4 rounded-lg border-l-4 border-green-400 bg-gray-50 p-4 transition-colors hover:bg-gray-100 dark:border-green-500 dark:bg-gray-800 dark:hover:bg-gray-700"
                                >
                                    <div
                                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-500 dark:bg-sky-900/50 dark:text-sky-400"
                                    >
                                        <MessageSquareIcon size="20" />
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">Social Learner</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Participate in 5 group discussions</p>
                                    </div>
                                    <div class="relative h-12 w-12 flex-shrink-0">
                                        <svg viewBox="0 0 36 36" class="h-full w-full">
                                            <path
                                                class="fill-none stroke-gray-200 stroke-2 dark:stroke-gray-600"
                                                d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                            <path
                                                class="fill-none stroke-sky-500 stroke-2 dark:stroke-sky-400"
                                                stroke-dasharray="100, 100"
                                                d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                        </svg>
                                        <span
                                            class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-gray-800 dark:text-gray-200"
                                            >5/5</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Echo Chamber Section -->
        <section id="echo-chamber" class="bg-white py-20 transition-colors duration-300 dark:bg-gray-900">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="mx-auto mb-16 max-w-3xl text-center">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white md:text-4xl">The Echo Chamber</h2>
                    <p class="text-gray-600 dark:text-gray-300">Our unique feature that turns student discussions into learning materials</p>
                </div>

                <div ref="echoDemo" class="translate-y-5 transform opacity-0 transition-all duration-1000 ease-out">
                    <!-- Echo Steps -->
                    <div class="mb-16 flex flex-col items-center justify-between md:flex-row">
                        <!-- Step 1 -->
                        <div class="mb-8 flex-1 text-center md:mb-0">
                            <div
                                class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-sky-500 font-semibold text-white dark:bg-sky-600"
                            >
                                1
                            </div>
                            <div class="mx-auto max-w-xs rounded-xl bg-white p-6 shadow-md dark:bg-gray-700 dark:shadow-gray-900/30">
                                <h3 class="mb-3 font-semibold text-gray-800 dark:text-white">Student Discussion</h3>
                                <div class="rounded-lg bg-gray-100 p-4 text-left text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                    "Why does gravity pull things down instead of pushing them up?"
                                </div>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="mb-8 rotate-90 transform text-sky-200 dark:text-sky-800 md:mx-4 md:mb-0 md:rotate-0">
                            <ArrowRightIcon size="32" />
                        </div>

                        <!-- Step 2 -->
                        <div class="mb-8 flex-1 text-center md:mb-0">
                            <div
                                class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-sky-500 font-semibold text-white dark:bg-sky-600"
                            >
                                2
                            </div>
                            <div class="mx-auto max-w-xs rounded-xl bg-white p-6 shadow-md dark:bg-gray-700 dark:shadow-gray-900/30">
                                <h3 class="mb-3 font-semibold text-gray-800 dark:text-white">Echo Processing</h3>
                                <div class="mt-4 flex justify-center gap-2">
                                    <div class="h-4 w-4 animate-pulse rounded-full bg-sky-500 dark:bg-sky-400"></div>
                                    <div class="animation-delay-200 h-4 w-4 animate-pulse rounded-full bg-sky-500 dark:bg-sky-400"></div>
                                    <div class="animation-delay-400 h-4 w-4 animate-pulse rounded-full bg-sky-500 dark:bg-sky-400"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="mb-8 rotate-90 transform text-sky-200 dark:text-sky-800 md:mx-4 md:mb-0 md:rotate-0">
                            <ArrowRightIcon size="32" />
                        </div>

                        <!-- Step 3 -->
                        <div class="flex-1 text-center">
                            <div
                                class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-sky-500 font-semibold text-white dark:bg-sky-600"
                            >
                                3
                            </div>
                            <div class="mx-auto max-w-xs rounded-xl bg-white p-6 shadow-md dark:bg-gray-700 dark:shadow-gray-900/30">
                                <h3 class="mb-3 font-semibold text-gray-800 dark:text-white">Learning Material Created</h3>
                                <div class="perspective group">
                                    <div
                                        class="preserve-3d hover:rotate-y-180 relative h-[150px] w-full cursor-pointer transition-transform duration-500"
                                    >
                                        <div
                                            class="backface-hidden absolute inset-0 flex items-center justify-center rounded-lg bg-sky-100 p-4 text-center text-sky-800 dark:bg-sky-900/50 dark:text-sky-200"
                                        >
                                            <h4 class="font-semibold">What is the direction of gravitational force?</h4>
                                        </div>
                                        <div
                                            class="backface-hidden rotate-y-180 absolute inset-0 flex items-center justify-center rounded-lg bg-sky-500 p-4 text-center text-white dark:bg-sky-600"
                                        >
                                            <p class="text-sm">
                                                Gravity pulls objects toward each other. On Earth, this means objects are pulled toward the center of
                                                the planet (downward).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Echo Benefits -->
                    <div class="grid gap-8 md:grid-cols-3">
                        <div
                            class="rounded-xl bg-white p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-700 dark:shadow-gray-900/30"
                        >
                            <ZapIcon size="24" class="mx-auto mb-4 text-sky-500 dark:text-sky-400" />
                            <h4 class="mb-2 font-semibold text-gray-800 dark:text-white">Community-Powered Evolution</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Student voices become living lessons, making the platform grow with its users
                            </p>
                        </div>
                        <div
                            class="rounded-xl bg-white p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-700 dark:shadow-gray-900/30"
                        >
                            <BrainIcon size="24" class="mx-auto mb-4 text-sky-500 dark:text-sky-400" />
                            <h4 class="mb-2 font-semibold text-gray-800 dark:text-white">Effortless Creativity</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Students don't just consume—they accidentally create valuable learning content
                            </p>
                        </div>
                        <div
                            class="rounded-xl bg-white p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-700 dark:shadow-gray-900/30"
                        >
                            <RefreshCwIcon size="24" class="mx-auto mb-4 text-sky-500 dark:text-sky-400" />
                            <h4 class="mb-2 font-semibold text-gray-800 dark:text-white">Organic Engagement</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                A feedback loop where students see their ideas reflected back, keeping them engaged
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-sky-600 to-sky-500 py-20 text-white dark:from-sky-800 dark:to-sky-700">
            <div class="container mx-auto px-4 lg:px-8">
                <div ref="ctaContent" class="mx-auto max-w-3xl translate-y-5 transform text-center opacity-0 transition-all duration-1000 ease-out">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">Ready to Transform Learning?</h2>
                    <p class="mb-8 text-lg text-sky-100">Join over 1 million users already experiencing the future of education</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <!-- Conditional navigation based on auth status -->
                        <Link
                            v-if="isUserLoggedIn"
                            :href="route('dashboard')"
                            class="rounded-lg bg-white px-6 py-3 text-lg font-semibold text-sky-600 transition-all hover:-translate-y-0.5 hover:shadow-lg dark:text-sky-700"
                        >
                            Go to Dashboard
                        </Link>
                        <Link
                            v-else
                            :href="route('register')"
                            class="rounded-lg bg-white px-6 py-3 text-lg font-semibold text-sky-600 transition-all hover:-translate-y-0.5 hover:shadow-lg dark:text-sky-700"
                        >
                            Get Started for Free
                        </Link>
                        <button
                            class="rounded-lg border-2 border-white bg-transparent px-6 py-3 text-lg font-semibold text-white transition-all hover:-translate-y-0.5 hover:bg-white/10"
                        >
                            Request Demo
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 pb-8 pt-16 text-white dark:bg-gray-950">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="mb-12 grid gap-8 md:grid-cols-4">
                    <div>
                        <div class="mb-4 flex items-center">
                            <AppLogo />
                        </div>
                        <p class="text-slate-400">Next-Generation Collaborative Learning</p>
                    </div>

                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Platform</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Features</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Pricing</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">For Educators</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">For Students</a>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Resources</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Documentation</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Tutorials</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Blog</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Support</a>
                        </div>
                    </div>

                    <div>
                        <h4 class="mb-4 text-lg font-semibold">Company</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">About Us</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Careers</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Contact</a>
                            <a href="#" class="text-slate-400 transition-colors hover:text-sky-400">Privacy Policy</a>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-between border-t border-slate-800 pt-8 md:flex-row">
                    <p class="mb-4 text-sm text-slate-400 md:mb-0">&copy; 2025 Education Innovation Corp. All rights reserved.</p>
                    <div class="flex space-x-4">
                        <a
                            href="#"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 text-white transition-colors hover:bg-sky-500"
                        >
                            <TwitterIcon size="18" />
                        </a>
                        <a
                            href="#"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 text-white transition-colors hover:bg-sky-500"
                        >
                            <FacebookIcon size="18" />
                        </a>
                        <a
                            href="#"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 text-white transition-colors hover:bg-sky-500"
                        >
                            <InstagramIcon size="18" />
                        </a>
                        <a
                            href="#"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-800 text-white transition-colors hover:bg-sky-500"
                        >
                            <LinkedinIcon size="18" />
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight as ArrowRightIcon,
    Award as AwardIcon,
    BarChart2 as BarChart2Icon,
    BookOpen as BookOpenIcon,
    Brain as BrainIcon,
    CheckCircle as CheckCircleIcon,
    Facebook as FacebookIcon,
    Instagram as InstagramIcon,
    Linkedin as LinkedinIcon,
    MessageCircle as MessageCircleIcon,
    MessageSquare as MessageSquareIcon,
    PenTool as PenToolIcon,
    RefreshCw as RefreshCwIcon,
    Star as StarIcon,
    Trophy as TrophyIcon,
    Twitter as TwitterIcon,
    Users as UsersIcon,
    Zap as ZapIcon,
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

// Access page props
const page = usePage();

// Authentication status
const isUserLoggedIn = computed(() => !!page.props.auth.user);

// Refs for animation elements
const heroText = ref(null);
const heroImage = ref(null);
const featureCards = ref([]);
const collaborationImage = ref(null);
const collaborationText = ref(null);
const gamificationImage = ref(null);
const gamificationText = ref(null);
const echoDemo = ref(null);
const ctaContent = ref(null);
const cursor = ref(null);

// Features data
const features = [
    {
        icon: BookOpenIcon,
        title: 'Interactive Lesson Engine',
        description: 'Bite-sized lessons that adapt to user progress with videos, quizzes, and mini-games.',
    },
    {
        icon: UsersIcon,
        title: 'Real-Time Collaboration',
        description: 'Work together live via chat, shared whiteboards, and video calls with minimal delay.',
    },
    {
        icon: TrophyIcon,
        title: 'Rewards Engine',
        description: 'Keep learners motivated with points and badges for completing activities.',
    },
    {
        icon: BarChart2Icon,
        title: 'Adaptive Learning Paths',
        description: 'Suggests next steps based on performance using simple rule-based recommendations.',
    },
    {
        icon: PenToolIcon,
        title: 'Content Creation Toolkit',
        description: 'Drag-and-drop editor for teachers and students to create and share lessons.',
    },
    {
        icon: MessageCircleIcon,
        title: 'The Echo Chamber',
        description: 'Captures discussions and turns them into mini-lessons or flashcards automatically.',
    },
];

// Badges data
const badges = [
    {
        icon: StarIcon,
        name: 'Explorer Badge',
        description: 'Awarded for completing 100 points worth of content',
    },
    {
        icon: MessageSquareIcon,
        name: 'Collaborator Badge',
        description: 'Earned by participating in 10 group discussions',
    },
    {
        icon: AwardIcon,
        name: 'Creator Badge',
        description: 'Awarded for creating content that others engage with',
    },
];

// Helper function for badge colors
const getBadgeColor = (index) => {
    const colors = [
        'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
        'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
        'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
    ];
    return colors[index % colors.length];
};

// Animation for cursor movement
let cursorAnimationInterval = null;

const animateCursor = () => {
    if (!cursor.value) return;

    const positions = [
        { x: 20, y: 20 },
        { x: 80, y: 40 },
        { x: 60, y: 80 },
        { x: 30, y: 60 },
        { x: 70, y: 30 },
    ];

    let currentIndex = 0;

    cursorAnimationInterval = setInterval(() => {
        cursor.value.style.left = `${positions[currentIndex].x}%`;
        cursor.value.style.top = `${positions[currentIndex].y}%`;
        currentIndex = (currentIndex + 1) % positions.length;
    }, 1500);
};

// Intersection Observer for animations
const setupIntersectionObserver = () => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        },
        { threshold: 0.1 },
    );

    // Observe elements
    if (heroText.value) observer.observe(heroText.value);
    if (heroImage.value) observer.observe(heroImage.value);
    if (featureCards.value) {
        featureCards.value.forEach((card, index) => {
            // Add staggered delay to cards
            card.style.transitionDelay = `${index * 100}ms`;
            observer.observe(card);
        });
    }
    if (collaborationImage.value) observer.observe(collaborationImage.value);
    if (collaborationText.value) observer.observe(collaborationText.value);
    if (gamificationImage.value) observer.observe(gamificationImage.value);
    if (gamificationText.value) observer.observe(gamificationText.value);
    if (echoDemo.value) observer.observe(echoDemo.value);
    if (ctaContent.value) observer.observe(ctaContent.value);
};

onMounted(() => {
    setupIntersectionObserver();
    animateCursor();
});

onUnmounted(() => {
    if (cursorAnimationInterval) {
        clearInterval(cursorAnimationInterval);
    }
});
</script>

<style>
/* Custom animations and utilities that extend Tailwind */
.animate-in {
    @apply transform-none opacity-100;
}

.perspective {
    perspective: 1000px;
}

.preserve-3d {
    transform-style: preserve-3d;
}

.backface-hidden {
    backface-visibility: hidden;
}

.rotate-y-180 {
    transform: rotateY(180deg);
}

/* Animation delays */
.animation-delay-200 {
    animation-delay: 200ms;
}

.animation-delay-400 {
    animation-delay: 400ms;
}

.animation-delay-500 {
    animation-delay: 500ms;
}

.animation-delay-1000 {
    animation-delay: 1000ms;
}

.animation-delay-1500 {
    animation-delay: 1500ms;
}

/* Smooth theme transitions */
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
