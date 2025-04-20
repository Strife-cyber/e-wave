<template>
    <Head title="Welcome"/>
    <div class="min-h-screen font-sans text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-900 transition-colors duration-300">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 w-full bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg z-50 shadow-sm dark:shadow-gray-800/20 transition-all duration-300">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo Section -->
                    <div class="flex items-center">
                        <AppLogo class="h-8 w-auto text-gray-900 dark:text-white" />
                    </div>

                    <!-- Navigation Links and Actions -->
                    <div class="flex items-center space-x-12">
                        <!-- Desktop Navigation -->
                        <div class="hidden lg:flex items-center space-x-8">
                            <a href="#features" class="relative text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-sky-500 dark:after:bg-sky-400 after:transition-all after:duration-300 hover:after:w-full">
                                Features
                            </a>
                            <a href="#collaboration" class="relative text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-sky-500 dark:after:bg-sky-400 after:transition-all after:duration-300 hover:after:w-full">
                                Collaboration
                            </a>
                            <a href="#gamification" class="relative text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-sky-500 dark:after:bg-sky-400 after:transition-all after:duration-300 hover:after:w-full">
                                Gamification
                            </a>
                            <a href="#echo-chamber" class="relative text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-sky-500 dark:after:bg-sky-400 after:transition-all after:duration-300 hover:after:w-full">
                                Echo Chamber
                            </a>
                        </div>

                        <!-- Auth Conditional Navigation -->
                        <div class="hidden lg:flex items-center">
                            <Link
                                v-if="isUserLoggedIn"
                                :href="route('dashboard')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200"
                            >
                                Dashboard
                            </Link>
                            <div
                                v-else
                                class="flex items-center gap-2"
                            >
                                <Link
                                    :href="route('register')"
                                    class="block w-[120px] text-center px-5 py-2 text-sm font-semibold text-white bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-700 rounded-md transition-all duration-200 hover:shadow-md"
                                >
                                    Get Started
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="block w-[120px] text-center px-5 py-2 text-sm font-semibold text-white bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-700 rounded-md transition-all duration-200 hover:shadow-md"
                                >
                                    Login
                                </Link>
                            </div>
                        </div>

                        <!-- Theme Toggle -->
                        <ThemeToggle class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" />

                        <!-- Mobile Menu Button -->
                        <button class="lg:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" @click="toggleMobileMenu">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu (Hidden by Default) -->
                <div v-if="isMobileMenuOpen" class="lg:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-4">
                    <div class="flex flex-col space-y-4">
                        <a href="#features" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 px-4 py-2">
                            Features
                        </a>
                        <a href="#collaboration" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 px-4 py-2">
                            Collaboration
                        </a>
                        <a href="#gamification" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 px-4 py-2">
                            Gamification
                        </a>
                        <a href="#echo-chamber" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-sky-500 dark:hover:text-sky-400 transition-colors duration-200 px-4 py-2">
                            Echo Chamber
                        </a>
                        <div class="px-4">
                            <Link
                                v-if="isUserLoggedIn"
                                :href="route('dashboard')"
                                class="block w-full text-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200"
                            >
                                Dashboard
                            </Link>
                            <div
                                v-else
                                class="flex items-center gap-2"
                            >
                                <Link
                                    :href="route('register')"
                                    class="block w-full text-center px-5 py-2 text-sm font-semibold text-white bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-700 rounded-md transition-all duration-200 hover:shadow-md"
                                >
                                    Get Started
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="block w-full text-center px-5 py-2 text-sm font-semibold text-white bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-700 rounded-md transition-all duration-200 hover:shadow-md"
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
        <section class="pt-32 pb-16 bg-gradient-to-b from-white to-sky-50 dark:from-gray-900 dark:to-gray-800 relative overflow-hidden transition-colors duration-300">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div ref="heroText" class="opacity-0 transform translate-y-5 transition-all duration-1000 ease-out">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight text-gray-900 dark:text-white">
                            Next-Generation <br />
                            <span class="text-sky-500 dark:text-sky-400 relative inline-block">
                                Collaborative Learning
                                <span class="absolute bottom-1 left-0 w-full h-3 bg-sky-200/50 dark:bg-sky-700/30 -z-10"></span>
                            </span>
                        </h1>
                        <p class="mt-6 text-gray-600 dark:text-gray-300 text-lg max-w-lg">
                            EduWave redefines digital learning with a simple, powerful web-based platform
                            that supports over 1 million concurrent users, delivering real-time features
                            with minimal delay.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-4">
                            <!-- Conditional navigation based on auth status -->
                            <Link
                                v-if="isUserLoggedIn"
                                :href="route('dashboard')"
                                class="bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-semibold transition-all hover:shadow-lg hover:-translate-y-0.5 text-lg"
                            >
                                Go to Dashboard
                            </Link>
                            <button
                                v-else
                                class="bg-sky-500 hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-700 text-white px-6 py-3 rounded-lg font-semibold transition-all hover:shadow-lg hover:-translate-y-0.5 text-lg"
                            >
                                Start Learning
                            </button>
                            <button class="bg-sky-50 hover:bg-sky-100 dark:bg-gray-800 dark:hover:bg-gray-700 text-sky-500 dark:text-sky-400 border-2 border-sky-200 dark:border-sky-700 px-6 py-3 rounded-lg font-semibold transition-all hover:-translate-y-0.5 text-lg">
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

                    <div ref="heroImage" class="relative opacity-0 transform translate-x-5 transition-all duration-1000 ease-out delay-200">
                        <!-- Floating Elements -->
                        <div class="absolute inset-0 z-10">
                            <div class="absolute top-[10%] -left-5 bg-white dark:bg-gray-800 p-3 rounded-2xl flex items-center gap-2 shadow-lg dark:shadow-gray-900/50 animate-float">
                                <BookOpenIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Interactive Lessons</span>
                            </div>
                            <div class="absolute top-[20%] -right-5 bg-white dark:bg-gray-800 p-3 rounded-2xl flex items-center gap-2 shadow-lg dark:shadow-gray-900/50 animate-float animation-delay-500">
                                <UsersIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Real-time Collaboration</span>
                            </div>
                            <div class="absolute bottom-[20%] left-[10%] bg-white dark:bg-gray-800 p-3 rounded-2xl flex items-center gap-2 shadow-lg dark:shadow-gray-900/50 animate-float animation-delay-1000">
                                <TrophyIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Gamification</span>
                            </div>
                            <div class="absolute bottom-[10%] right-[10%] bg-white dark:bg-gray-800 p-3 rounded-2xl flex items-center gap-2 shadow-lg dark:shadow-gray-900/50 animate-float animation-delay-1500">
                                <MessageCircleIcon size="24" class="text-sky-500 dark:text-sky-400" />
                                <span class="font-medium text-gray-800 dark:text-gray-200">Echo Chamber</span>
                            </div>
                        </div>

                        <!-- Platform Preview -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl dark:shadow-gray-900/50 border border-gray-200 dark:border-gray-700 z-20 relative">
                            <!-- Platform Header -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 flex items-center border-b border-gray-200 dark:border-gray-600">
                                <div class="flex gap-2 mr-4">
                                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-green-400"></span>
                                </div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-300">EduWave Platform</div>
                            </div>

                            <!-- Platform Content -->
                            <div class="flex h-[300px]">
                                <!-- Sidebar -->
                                <div class="w-1/3 bg-gray-50 dark:bg-gray-700 p-4 border-r border-gray-200 dark:border-gray-600">
                                    <div class="bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 p-3 rounded-lg flex items-center gap-2 mb-2 cursor-pointer">
                                        <BookOpenIcon size="16" />
                                        <span class="text-sm font-medium">Lessons</span>
                                    </div>
                                    <div class="p-3 rounded-lg flex items-center gap-2 mb-2 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-600 transition-colors">
                                        <UsersIcon size="16" class="text-gray-500 dark:text-gray-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Collaborate</span>
                                    </div>
                                    <div class="p-3 rounded-lg flex items-center gap-2 mb-2 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-600 transition-colors">
                                        <TrophyIcon size="16" class="text-gray-500 dark:text-gray-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Rewards</span>
                                    </div>
                                    <div class="p-3 rounded-lg flex items-center gap-2 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-600 transition-colors">
                                        <MessageCircleIcon size="16" class="text-gray-500 dark:text-gray-400" />
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Echo</span>
                                    </div>
                                </div>

                                <!-- Main Content -->
                                <div class="flex-1 p-4 flex flex-col gap-4">
                                    <!-- Lesson Card -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                        <h3 class="font-semibold mb-2 text-gray-800 dark:text-gray-200">Introduction to Physics</h3>
                                        <div class="h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden mb-1">
                                            <div class="h-full bg-sky-500 dark:bg-sky-400 rounded-full animate-progress-load" style="width: 65%"></div>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">65% Complete</p>
                                    </div>

                                    <!-- Chat Preview -->
                                    <div class="flex flex-col gap-3">
                                        <div class="flex items-start gap-2">
                                            <div class="w-8 h-8 rounded-full bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 flex items-center justify-center font-semibold flex-shrink-0">
                                                S
                                            </div>
                                            <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-2xl rounded-tl-none max-w-[80%] text-gray-800 dark:text-gray-200">
                                                What's momentum?
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <div class="w-8 h-8 rounded-full bg-sky-500 dark:bg-sky-600 text-white flex items-center justify-center font-semibold flex-shrink-0">
                                                E
                                            </div>
                                            <div class="bg-sky-50 dark:bg-sky-900/30 p-3 rounded-2xl rounded-tr-none max-w-[80%] text-gray-800 dark:text-gray-200">
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto">
                    <path fill="currentColor" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" class="text-sky-50 dark:text-gray-800"></path>
                </svg>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-sky-50 dark:bg-gray-800 transition-colors duration-300">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 dark:text-white">Core Learning Infrastructure</h2>
                    <p class="text-gray-600 dark:text-gray-300">Simplified yet powerful features that don't require advanced technical expertise</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="(feature, index) in features"
                        :key="index"
                        ref="featureCards"
                        class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-md dark:shadow-gray-900/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:hover:shadow-gray-900/50 opacity-0 transform translate-y-5"
                    >
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 mb-6">
                            <component :is="feature.icon" size="32" />
                        </div>
                        <h3 class="text-xl font-semibold mb-3 text-gray-800 dark:text-white">{{ feature.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Collaboration Section -->
        <section id="collaboration" class="py-20 bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div ref="collaborationImage" class="opacity-0 transform translate-x-5 transition-all duration-1000 ease-out">
                        <div class="grid grid-cols-3 gap-4 h-[300px]">
                            <!-- Whiteboard -->
                            <div class="col-span-2 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md dark:shadow-gray-900/30 border border-gray-200 dark:border-gray-700 flex flex-col">
                                <div class="flex gap-2 p-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
                                </div>
                                <div class="flex-1 bg-gray-50 dark:bg-gray-700 relative">
                                    <div class="absolute w-2/5 h-1/5 top-[20%] left-[10%] bg-sky-200/50 dark:bg-sky-700/30 rounded-lg"></div>
                                    <div class="absolute w-[30%] h-[30%] top-[50%] left-[30%] bg-sky-300/50 dark:bg-sky-600/30 rounded-full"></div>
                                    <div class="absolute w-1/5 h-1/10 top-[30%] right-[10%] bg-sky-400/50 dark:bg-sky-500/30 rounded-lg"></div>
                                    <div ref="cursor" class="absolute w-4 h-4 bg-sky-500 dark:bg-sky-400 rounded-full transform -translate-x-1/2 -translate-y-1/2 z-10 transition-all duration-300 ease-out"></div>
                                </div>
                            </div>

                            <!-- Video Call -->
                            <div class="bg-slate-800 dark:bg-gray-700 rounded-xl overflow-hidden p-2">
                                <div class="grid grid-cols-2 grid-rows-2 gap-2 h-full">
                                    <div class="bg-slate-700 dark:bg-gray-600 rounded-lg"></div>
                                    <div class="bg-slate-700 dark:bg-gray-600 rounded-lg"></div>
                                    <div class="bg-slate-700 dark:bg-gray-600 rounded-lg"></div>
                                    <div class="bg-slate-700 dark:bg-gray-600 rounded-lg"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ref="collaborationText" class="opacity-0 transform -translate-x-5 transition-all duration-1000 ease-out">
                        <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 dark:text-white">Real-Time Collaboration Suite</h2>
                        <p class="text-gray-600 dark:text-gray-300 text-lg mb-6">
                            Work together live via chat, shared whiteboards, and video calls.
                            Our platform ensures minimal delay with under 500ms latency,
                            making collaboration feel natural and immediate.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="text-sky-500 dark:text-sky-400 flex-shrink-0" />
                                <span class="text-gray-700 dark:text-gray-300">Live chat with instant messaging</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="text-sky-500 dark:text-sky-400 flex-shrink-0" />
                                <span class="text-gray-700 dark:text-gray-300">Interactive whiteboards for visual collaboration</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="text-sky-500 dark:text-sky-400 flex-shrink-0" />
                                <span class="text-gray-700 dark:text-gray-300">Seamless video conferencing integration</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <CheckCircleIcon size="20" class="text-sky-500 dark:text-sky-400 flex-shrink-0" />
                                <span class="text-gray-700 dark:text-gray-300">Automatic session recording and playback</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gamification Section -->
        <section id="gamification" class="py-20 bg-sky-50 dark:bg-gray-800 transition-colors duration-300">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div ref="gamificationText" class="md:order-2 opacity-0 transform translate-x-5 transition-all duration-1000 ease-out">
                        <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 dark:text-white">Gamification Layer</h2>
                        <p class="text-gray-600 dark:text-gray-300 text-lg mb-8">
                            Keep learners motivated with points, badges, and adaptive learning paths.
                            Our simple yet effective reward system makes education engaging and fun.
                        </p>

                        <div class="space-y-4">
                            <div
                                v-for="(badge, index) in badges"
                                :key="index"
                                class="flex items-center gap-4 bg-white dark:bg-gray-700 p-4 rounded-xl shadow-sm dark:shadow-gray-900/30 hover:translate-x-1 transition-transform duration-300"
                            >
                                <div :class="`w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 ${getBadgeColor(index)}`">
                                    <component :is="badge.icon" size="24" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 dark:text-white">{{ badge.name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ badge.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ref="gamificationImage" class="md:order-1 opacity-0 transform -translate-x-5 transition-all duration-1000 ease-out">
                        <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md dark:shadow-gray-900/30 p-6 border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-semibold text-lg text-gray-800 dark:text-white">Your Progress</h3>
                                <div class="bg-sky-500 dark:bg-sky-600 text-white px-3 py-1 rounded-full text-sm font-semibold">Level 3</div>
                            </div>

                            <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded-full mb-1 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-sky-300 to-sky-500 dark:from-sky-600 dark:to-sky-400 rounded-full animate-progress-load" style="width: 80%"></div>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 text-right mb-6">245/300 XP</div>

                            <div class="space-y-4">
                                <!-- Achievement 1 -->
                                <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-900/50 text-sky-500 dark:text-sky-400 flex items-center justify-center flex-shrink-0">
                                        <StarIcon size="20" />
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">Quiz Master</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Complete 10 quizzes with 80%+ score</p>
                                    </div>
                                    <div class="relative w-12 h-12 flex-shrink-0">
                                        <svg viewBox="0 0 36 36" class="w-full h-full">
                                            <path class="stroke-gray-200 dark:stroke-gray-600 fill-none stroke-2"
                                                  d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                            <path class="stroke-sky-500 dark:stroke-sky-400 fill-none stroke-2"
                                                  stroke-dasharray="80, 100"
                                                  d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-gray-800 dark:text-gray-200">8/10</span>
                                    </div>
                                </div>

                                <!-- Achievement 2 -->
                                <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border-l-4 border-green-400 dark:border-green-500">
                                    <div class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-900/50 text-sky-500 dark:text-sky-400 flex items-center justify-center flex-shrink-0">
                                        <MessageSquareIcon size="20" />
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 dark:text-white">Social Learner</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Participate in 5 group discussions</p>
                                    </div>
                                    <div class="relative w-12 h-12 flex-shrink-0">
                                        <svg viewBox="0 0 36 36" class="w-full h-full">
                                            <path class="stroke-gray-200 dark:stroke-gray-600 fill-none stroke-2"
                                                  d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                            <path class="stroke-sky-500 dark:stroke-sky-400 fill-none stroke-2"
                                                  stroke-dasharray="100, 100"
                                                  d="M18 2.0845
                          a 15.9155 15.9155 0 0 1 0 31.831
                          a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                        </svg>
                                        <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-gray-800 dark:text-gray-200">5/5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Echo Chamber Section -->
        <section id="echo-chamber" class="py-20 bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 dark:text-white">The Echo Chamber</h2>
                    <p class="text-gray-600 dark:text-gray-300">Our unique feature that turns student discussions into learning materials</p>
                </div>

                <div ref="echoDemo" class="opacity-0 transform translate-y-5 transition-all duration-1000 ease-out">
                    <!-- Echo Steps -->
                    <div class="flex flex-col md:flex-row items-center justify-between mb-16">
                        <!-- Step 1 -->
                        <div class="flex-1 text-center mb-8 md:mb-0">
                            <div class="w-10 h-10 rounded-full bg-sky-500 dark:bg-sky-600 text-white flex items-center justify-center font-semibold mx-auto mb-4">1</div>
                            <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-md dark:shadow-gray-900/30 max-w-xs mx-auto">
                                <h3 class="font-semibold mb-3 text-gray-800 dark:text-white">Student Discussion</h3>
                                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg text-left text-gray-800 dark:text-gray-200">
                                    "Why does gravity pull things down instead of pushing them up?"
                                </div>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="text-sky-200 dark:text-sky-800 mb-8 md:mb-0 md:mx-4 transform md:rotate-0 rotate-90">
                            <ArrowRightIcon size="32" />
                        </div>

                        <!-- Step 2 -->
                        <div class="flex-1 text-center mb-8 md:mb-0">
                            <div class="w-10 h-10 rounded-full bg-sky-500 dark:bg-sky-600 text-white flex items-center justify-center font-semibold mx-auto mb-4">2</div>
                            <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-md dark:shadow-gray-900/30 max-w-xs mx-auto">
                                <h3 class="font-semibold mb-3 text-gray-800 dark:text-white">Echo Processing</h3>
                                <div class="flex justify-center gap-2 mt-4">
                                    <div class="w-4 h-4 rounded-full bg-sky-500 dark:bg-sky-400 animate-pulse"></div>
                                    <div class="w-4 h-4 rounded-full bg-sky-500 dark:bg-sky-400 animate-pulse animation-delay-200"></div>
                                    <div class="w-4 h-4 rounded-full bg-sky-500 dark:bg-sky-400 animate-pulse animation-delay-400"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="text-sky-200 dark:text-sky-800 mb-8 md:mb-0 md:mx-4 transform md:rotate-0 rotate-90">
                            <ArrowRightIcon size="32" />
                        </div>

                        <!-- Step 3 -->
                        <div class="flex-1 text-center">
                            <div class="w-10 h-10 rounded-full bg-sky-500 dark:bg-sky-600 text-white flex items-center justify-center font-semibold mx-auto mb-4">3</div>
                            <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-md dark:shadow-gray-900/30 max-w-xs mx-auto">
                                <h3 class="font-semibold mb-3 text-gray-800 dark:text-white">Learning Material Created</h3>
                                <div class="group perspective">
                                    <div class="relative preserve-3d transition-transform duration-500 w-full h-[150px] cursor-pointer hover:rotate-y-180">
                                        <div class="absolute inset-0 backface-hidden bg-sky-100 dark:bg-sky-900/50 text-sky-800 dark:text-sky-200 p-4 rounded-lg flex items-center justify-center text-center">
                                            <h4 class="font-semibold">What is the direction of gravitational force?</h4>
                                        </div>
                                        <div class="absolute inset-0 backface-hidden bg-sky-500 dark:bg-sky-600 text-white p-4 rounded-lg flex items-center justify-center text-center rotate-y-180">
                                            <p class="text-sm">Gravity pulls objects toward each other. On Earth, this means objects are pulled toward the center of the planet (downward).</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Echo Benefits -->
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-md dark:shadow-gray-900/30 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <ZapIcon size="24" class="text-sky-500 dark:text-sky-400 mx-auto mb-4" />
                            <h4 class="font-semibold mb-2 text-gray-800 dark:text-white">Community-Powered Evolution</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Student voices become living lessons, making the platform grow with its users</p>
                        </div>
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-md dark:shadow-gray-900/30 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <BrainIcon size="24" class="text-sky-500 dark:text-sky-400 mx-auto mb-4" />
                            <h4 class="font-semibold mb-2 text-gray-800 dark:text-white">Effortless Creativity</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Students don't just consume—they accidentally create valuable learning content</p>
                        </div>
                        <div class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-md dark:shadow-gray-900/30 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <RefreshCwIcon size="24" class="text-sky-500 dark:text-sky-400 mx-auto mb-4" />
                            <h4 class="font-semibold mb-2 text-gray-800 dark:text-white">Organic Engagement</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">A feedback loop where students see their ideas reflected back, keeping them engaged</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-sky-600 to-sky-500 dark:from-sky-800 dark:to-sky-700 text-white">
            <div class="container mx-auto px-4 lg:px-8">
                <div ref="ctaContent" class="max-w-3xl mx-auto text-center opacity-0 transform translate-y-5 transition-all duration-1000 ease-out">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Transform Learning?</h2>
                    <p class="text-sky-100 text-lg mb-8">Join over 1 million users already experiencing the future of education</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <!-- Conditional navigation based on auth status -->
                        <Link
                            v-if="isUserLoggedIn"
                            :href="route('dashboard')"
                            class="bg-white text-sky-600 dark:text-sky-700 px-6 py-3 rounded-lg font-semibold transition-all hover:shadow-lg hover:-translate-y-0.5 text-lg"
                        >
                            Go to Dashboard
                        </Link>
                        <Link
                            v-else
                            :href="route('register')"
                            class="bg-white text-sky-600 dark:text-sky-700 px-6 py-3 rounded-lg font-semibold transition-all hover:shadow-lg hover:-translate-y-0.5 text-lg"
                        >
                            Get Started for Free
                        </Link>
                        <button class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold transition-all hover:bg-white/10 hover:-translate-y-0.5 text-lg">
                            Request Demo
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 dark:bg-gray-950 text-white pt-16 pb-8">
            <div class="container mx-auto px-4 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8 mb-12">
                    <div>
                        <div class="flex items-center mb-4">
                            <AppLogo/>
                        </div>
                        <p class="text-slate-400">Next-Generation Collaborative Learning</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-lg mb-4">Platform</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Features</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Pricing</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">For Educators</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">For Students</a>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-lg mb-4">Resources</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Documentation</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Tutorials</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Blog</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Support</a>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-lg mb-4">Company</h4>
                        <div class="flex flex-col space-y-2">
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">About Us</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Careers</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Contact</a>
                            <a href="#" class="text-slate-400 hover:text-sky-400 transition-colors">Privacy Policy</a>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-slate-800">
                    <p class="text-slate-400 text-sm mb-4 md:mb-0">&copy; 2025 Education Innovation Corp. All rights reserved.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-sky-500 transition-colors">
                            <TwitterIcon size="18" />
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-sky-500 transition-colors">
                            <FacebookIcon size="18" />
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-sky-500 transition-colors">
                            <InstagramIcon size="18" />
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-sky-500 transition-colors">
                            <LinkedinIcon size="18" />
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Link, usePage, Head } from '@inertiajs/vue3'
import {
    BookOpen as BookOpenIcon,
    Users as UsersIcon,
    Trophy as TrophyIcon,
    MessageCircle as MessageCircleIcon,
    CheckCircle as CheckCircleIcon,
    Star as StarIcon,
    MessageSquare as MessageSquareIcon,
    ArrowRight as ArrowRightIcon,
    Zap as ZapIcon,
    Brain as BrainIcon,
    RefreshCw as RefreshCwIcon,
    Twitter as TwitterIcon,
    Facebook as FacebookIcon,
    Instagram as InstagramIcon,
    Linkedin as LinkedinIcon,
    PenTool as PenToolIcon,
    Award as AwardIcon,
    BarChart2 as BarChart2Icon,
    Sun as SunIcon,
    Moon as MoonIcon
} from 'lucide-vue-next'
import AppLogo from '@/components/AppLogo.vue';

// Access page props
const page = usePage()

// Theme state
const isDark = ref(false)

// Authentication status
const isUserLoggedIn = computed(() => !!page.props.auth.user);

// Check for system preference on mount
const checkSystemTheme = () => {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        isDark.value = true
        document.documentElement.classList.add('dark')
    }
}

// Toggle theme function
const toggleTheme = () => {
    isDark.value = !isDark.value
    if (isDark.value) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

// Theme toggle component
const ThemeToggle = {
    setup() {
        return { isDark, toggleTheme }
    },
    template: `
    <button
      @click="toggleTheme"
      class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
      aria-label="Toggle theme"
    >
      <SunIcon v-if="isDark" size="18" />
      <MoonIcon v-else size="18" />
    </button>
  `,
    components: {
        SunIcon,
        MoonIcon
    }
}

// Refs for animation elements
const heroText = ref(null)
const heroImage = ref(null)
const featureCards = ref([])
const collaborationImage = ref(null)
const collaborationText = ref(null)
const gamificationImage = ref(null)
const gamificationText = ref(null)
const echoDemo = ref(null)
const ctaContent = ref(null)
const cursor = ref(null)

// Features data
const features = [
    {
        icon: BookOpenIcon,
        title: 'Interactive Lesson Engine',
        description: 'Bite-sized lessons that adapt to user progress with videos, quizzes, and mini-games.'
    },
    {
        icon: UsersIcon,
        title: 'Real-Time Collaboration',
        description: 'Work together live via chat, shared whiteboards, and video calls with minimal delay.'
    },
    {
        icon: TrophyIcon,
        title: 'Rewards Engine',
        description: 'Keep learners motivated with points and badges for completing activities.'
    },
    {
        icon: BarChart2Icon,
        title: 'Adaptive Learning Paths',
        description: 'Suggests next steps based on performance using simple rule-based recommendations.'
    },
    {
        icon: PenToolIcon,
        title: 'Content Creation Toolkit',
        description: 'Drag-and-drop editor for teachers and students to create and share lessons.'
    },
    {
        icon: MessageCircleIcon,
        title: 'The Echo Chamber',
        description: 'Captures discussions and turns them into mini-lessons or flashcards automatically.'
    }
]

// Badges data
const badges = [
    {
        icon: StarIcon,
        name: 'Explorer Badge',
        description: 'Awarded for completing 100 points worth of content'
    },
    {
        icon: MessageSquareIcon,
        name: 'Collaborator Badge',
        description: 'Earned by participating in 10 group discussions'
    },
    {
        icon: AwardIcon,
        name: 'Creator Badge',
        description: 'Awarded for creating content that others engage with'
    }
]

// Helper function for badge colors
const getBadgeColor = (index) => {
    const colors = [
        'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
        'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
        'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300'
    ]
    return colors[index % colors.length]
}

// Animation for cursor movement
let cursorAnimationInterval = null

const animateCursor = () => {
    if (!cursor.value) return

    const positions = [
        { x: 20, y: 20 },
        { x: 80, y: 40 },
        { x: 60, y: 80 },
        { x: 30, y: 60 },
        { x: 70, y: 30 }
    ]

    let currentIndex = 0

    cursorAnimationInterval = setInterval(() => {
        cursor.value.style.left = `${positions[currentIndex].x}%`
        cursor.value.style.top = `${positions[currentIndex].y}%`
        currentIndex = (currentIndex + 1) % positions.length
    }, 1500)
}

// Intersection Observer for animations
const setupIntersectionObserver = () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in')
            }
        })
    }, { threshold: 0.1 })

    // Observe elements
    if (heroText.value) observer.observe(heroText.value)
    if (heroImage.value) observer.observe(heroImage.value)
    if (featureCards.value) {
        featureCards.value.forEach((card, index) => {
            // Add staggered delay to cards
            card.style.transitionDelay = `${index * 100}ms`
            observer.observe(card)
        })
    }
    if (collaborationImage.value) observer.observe(collaborationImage.value)
    if (collaborationText.value) observer.observe(collaborationText.value)
    if (gamificationImage.value) observer.observe(gamificationImage.value)
    if (gamificationText.value) observer.observe(gamificationText.value)
    if (echoDemo.value) observer.observe(echoDemo.value)
    if (ctaContent.value) observer.observe(ctaContent.value)
}

onMounted(() => {
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme) {
        isDark.value = savedTheme === 'dark'
        if (isDark.value) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    } else {
        checkSystemTheme()
    }

    setupIntersectionObserver()
    animateCursor()
})

onUnmounted(() => {
    if (cursorAnimationInterval) {
        clearInterval(cursorAnimationInterval)
    }
})
</script>

<style>
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom animations and utilities that extend Tailwind */
.animate-in {
    @apply opacity-100 transform-none;
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
