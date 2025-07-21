```vue
<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import NotificationToast from '@/Components/NotificationToast.vue';

defineProps({
    title: String,
});

const isSidebarExpanded = ref(true);
const showingNavigationDropdown = ref(false); // Mantenida por si se usa en el futuro
const theme = ref('light'); // 'light' para blanco, 'dark' para negro
const isLoading = ref(false); // Estado para el spinner de carga
let loadingTimeout = null; // Para manejar el setTimeout

const toggleSidebar = () => {
    isSidebarExpanded.value = !isSidebarExpanded.value;
    localStorage.setItem('isSidebarExpanded', isSidebarExpanded.value);
    console.log('Sidebar toggled to:', isSidebarExpanded.value);
};

const logout = () => {
    router.post(route('logout'));
};

const setTheme = (newTheme) => {
    theme.value = newTheme;
    localStorage.setItem('theme', newTheme);
    console.log('Theme set to:', newTheme);
    document.documentElement.classList.remove('dark');
    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }
};

onMounted(() => {
    const savedSidebarState = localStorage.getItem('isSidebarExpanded');
    if (savedSidebarState !== null) {
        isSidebarExpanded.value = savedSidebarState === 'true';
    }
    console.log('Sidebar mounted, initial state:', isSidebarExpanded.value);

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        theme.value = savedTheme;
    } else {
        theme.value = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    if (theme.value === 'dark') {
        document.documentElement.classList.add('dark');
    }
    console.log('Theme mounted, initial state:', theme.value);

    // Escuchar eventos de Inertia para el spinner
    router.on('start', () => {
        if (loadingTimeout) {
            clearTimeout(loadingTimeout);
        }
        isLoading.value = true;
        console.log('Loading started');
    });
    router.on('finish', () => {
        loadingTimeout = setTimeout(() => {
            isLoading.value = false;
            console.log('Loading finished after 3s delay');
        }, 3000);
    });

    router.on('navigate', () => {
        showingNavigationDropdown.value = false;
        console.log('Navigation occurred, closing mobile menu');
    });
});

watch(isSidebarExpanded, (newValue) => {
    localStorage.setItem('isSidebarExpanded', newValue);
});

watch(theme, (newValue) => {
    localStorage.setItem('theme', newValue);
    document.documentElement.classList.remove('dark');
    if (newValue === 'dark') {
        document.documentElement.classList.add('dark');
    }
});
</script>

<template>
    <div class="overflow-x-hidden">
        <Head :title="title" />
        <Banner />

        <!-- Loading Spinner -->
        <div v-if="isLoading" class="fixed inset-0 flex items-center justify-center z-[1000] bg-gray-900 bg-opacity-50 transition-opacity duration-300">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-t-orange-600 dark:border-t-orange-400 border-gray-300 dark:border-gray-600 rounded-full animate-spin"></div>
                <span class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cargando sistema wei</span>
            </div>
        </div>

        <div :class="{ 'bg-white': theme === 'light', 'bg-gray-900': theme === 'dark' }" class="min-h-screen flex">
            <!-- Sidebar -->
            <nav :class="{ 'w-64 sm:w-48': isSidebarExpanded, 'w-20 sm:w-16': !isSidebarExpanded }" 
                 class="flex-shrink-0 shadow-sm bg-white dark:bg-gray-800 border-r border-orange-200 dark:border-orange-700 fixed top-0 left-0 h-full transition-all duration-300 z-50">
                <div class="flex flex-col h-full px-3 py-4">
                    <!-- Navigation Links -->
                    <div class="flex flex-col space-y-3 flex-grow">
                        <!-- Inicio -->
                        <div class="group relative">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                     class="flex items-center px-3 py-3 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900 transition-colors duration-200">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span v-if="isSidebarExpanded" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Inicio</span>
                                <span v-else class="absolute left-14 top-1/2 -translate-y-1/2 bg-orange-900 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Inicio</span>
                            </NavLink>
                        </div>

                        <!-- Correos -->
                        <div class="group relative">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                     class="flex items-center px-3 py-3 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900 transition-colors duration-200">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span v-if="isSidebarExpanded" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Correos</span>
                                <span v-else class="absolute left-14 top-1/2 -translate-y-1/2 bg-orange-900 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Correos</span>
                            </NavLink>
                        </div>

                        <!-- DB -->
                        <div v-if="$page.props.auth.user?.role === 'encargado'" class="group relative">
                            <NavLink :href="route('databases.index')" :active="route().current('databases.index')"
                                     class="flex items-center px-3 py-3 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900 transition-colors duration-200">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                </svg>
                                <span v-if="isSidebarExpanded" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">DB</span>
                                <span v-else class="absolute left-14 top-1/2 -translate-y-1/2 bg-orange-900 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">DB</span>
                            </NavLink>
                        </div>

                        <!-- Logs -->
                        <div class="group relative">
                            <NavLink :href="route('logs.index')" :active="route().current('logs.index')"
                                     class="flex items-center px-3 py-3 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900 transition-colors duration-200">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span v-if="isSidebarExpanded" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Logs</span>
                                <span v-else class="absolute left-14 top-1/2 -translate-y-1/2 bg-orange-900 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Logs</span>
                            </NavLink>
                        </div>
                    </div>

                    <!-- Bottom Section (User, Theme, and Toggle) -->
                    <div class="mt-auto space-y-3">
                        <!-- Profile Dropdown -->
                        <Dropdown align="right" :width="isSidebarExpanded ? '48' : '40'" class="z-50">
                            <template #trigger>
                                <button class="w-full flex items-center px-3 py-3 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900 transition-colors duration-200"
                                        @click="console.log('Profile dropdown clicked')">
                                    <img v-if="$page.props.jetstream?.managesProfilePhotos && $page.props.auth.user?.profile_photo_url" 
                                         class="h-8 w-8 rounded-full object-cover"
                                         :src="$page.props.auth.user.profile_photo_url"
                                         :alt="$page.props.auth.user?.name || 'Usuario'" />
                                    <svg v-else class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span v-if="isSidebarExpanded" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $page.props.auth.user?.name || 'Usuario' }}</span>
                                    <span v-else class="absolute left-14 top-1/2 -translate-y-1/2 bg-orange-900 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">{{ $page.props.auth.user?.name || 'Usuario' }}</span>
                                </button>
                            </template>
                            <template #content>
                                <div class="bg-white dark:bg-gray-800 border border-orange-200 dark:border-orange-700 rounded-lg shadow-sm min-w-[240px] max-w-[300px] max-h-[300px] overflow-y-auto z-50">
                                    <div class="px-4 py-2 text-xs text-orange-500 dark:text-orange-400 bg-orange-50 dark:bg-orange-900">Cuenta</div>
                                    <DropdownLink :href="route('profile.show')"
                                                  class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-100 dark:hover:bg-orange-900">
                                        <svg class="h-4 w-4 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Perfil
                                    </DropdownLink>
                                    <form @submit.prevent="logout">
                                        <DropdownLink as="button"
                                                      class="flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-orange-100 dark:hover:bg-orange-900">
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Cerrar Sesión
                                        </DropdownLink>
                                    </form>
                                </div>
                            </template>
                        </Dropdown>

                        <!-- Theme Dropdown -->
                        <Dropdown align="right" :width="isSidebarExpanded ? '48' : '40'" class="z-50">
                            <template #trigger>
                                <button class="w-full flex items-center px-3 py-3 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900 transition-colors duration-200"
                                        @click="console.log('Theme dropdown clicked')">
                                    <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span v-if="isSidebarExpanded" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Tema</span>
                                    <span v-else class="absolute left-14 top-1/2 -translate-y-1/2 bg-orange-900 text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Tema</span>
                                </button>
                            </template>
                            <template #content>
                                <div class="bg-white dark:bg-gray-800 border border-orange-200 dark:border-orange-700 rounded-lg shadow-sm min-w-[240px] max-w-[320px] overflow-y-auto z-50">
                                    <div class="px-4 py-2 text-xs text-orange-500 dark:text-orange-400 bg-orange-50 dark:bg-orange-900">Tema</div>
                                    <button @click="setTheme('light'); console.log('Light theme selected')"
                                            class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-100 dark:hover:bg-orange-900">
                                        <svg class="h-4 w-4 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Fondo Blanco
                                    </button>
                                    <button @click="setTheme('dark'); console.log('Dark theme selected')"
                                            class="w-full flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-100 dark:hover:bg-orange-900">
                                        <svg class="h-4 w-4 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                        </svg>
                                        Fondo Negro
                                    </button>
                                </div>
                            </template>
                        </Dropdown>

                        <!-- Toggle Button -->
                        <button @click="toggleSidebar" 
                                class="w-full flex items-center px-3 py-3 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900 transition-colors duration-200">
                            <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="isSidebarExpanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <span v-if="isSidebarExpanded" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Contraer</span>
                        </button>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Page Heading -->
                <header v-if="$slots.header" :class="{ 'bg-white': theme === 'light', 'bg-gray-900': theme === 'dark' }" class="shadow-sm sticky top-0 z-30">
                    <div :class="{ 'ml-64 sm:ml-48 max-w-[calc(100%-16rem)] sm:max-w-[calc(100%-12rem)]': isSidebarExpanded, 'ml-20 sm:ml-16 max-w-[calc(100%-5rem)] sm:max-w-[calc(100%-4rem)]': !isSidebarExpanded }" 
                         class="mx-auto py-6 px-4 sm:px-6 lg:px-8 transition-all duration-300">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main :class="{ 'bg-white': theme === 'light', 'bg-gray-900': theme === 'dark' }" class="flex-1">
                    <div :class="{ 'ml-64 sm:ml-48 max-w-[calc(100%-16rem)] sm:max-w-[calc(100%-12rem)]': isSidebarExpanded, 'ml-20 sm:ml-16 max-w-[calc(100%-5rem)] sm:max-w-[calc(100%-4rem)]': !isSidebarExpanded }" 
                         class="mx-auto py-6 px-4 sm:px-6 lg:px-8 transition-all duration-300">
                        <slot />
                    </div>
                </main>

                <NotificationToast />
            </div>
        </div>
    </div>
</template>
```