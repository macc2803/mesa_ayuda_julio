<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Bienvenido" />

    <div class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 overflow-hidden">

        <!-- Efecto de partículas mejorado -->
        <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
            <div v-for="i in 30" :key="i"
                class="absolute rounded-full bg-gradient-to-r from-orange-100/30 to-orange-200/20 dark:from-orange-900/10 dark:to-orange-800/10 blur-[1px]"
                :style="{
                    width: `${Math.random() * 15 + 5}px`,
                    height: `${Math.random() * 15 + 5}px`,
                    top: `${Math.random() * 100}%`,
                    left: `${Math.random() * 100}%`,
                    animationDuration: `${Math.random() * 25 + 15}s`,
                    animationDelay: `${Math.random() * 5}s`,
                    opacity: `${Math.random() * 0.4 + 0.1}`
                }"
                :class="`animate-float-${Math.floor(Math.random() * 4) + 1}`">
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="relative w-full max-w-md mx-4 p-8 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700/50 transition-all duration-500 hover:shadow-2xl hover:scale-[1.02]">
            <div class="flex flex-col items-center text-center space-y-8">
                <!-- Logo y título -->
                <div class="space-y-4">
                    <!-- Icono premium de soporte técnico -->
                                    
                    <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-400 dark:to-orange-300">
                        Mesa de Ayuda
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300/90">
                        Soluciones técnicas rápidas y eficientes
                    </p>
                </div>

                <!-- Botones -->
                <div v-if="canLogin" class="flex flex-col gap-4 w-full">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="route('dashboard')"
                            class="group relative bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium py-3 px-6 rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 flex items-center justify-center gap-2 overflow-hidden"
                        >
                            <span class="absolute inset-0 bg-white/10 group-hover:bg-white/5 transition duration-300"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="z-10">Panel Principal</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="group relative bg-gradient-to-r from-orange-500 to-orange-600 text-white font-medium py-3 px-6 rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-300 flex items-center justify-center gap-2 overflow-hidden"
                        >
                            <span class="absolute inset-0 bg-white/10 group-hover:bg-white/5 transition duration-300"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span class="z-10">Iniciar Sesión</span>
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="group relative bg-white/90 dark:bg-gray-700/90 border border-orange-400/50 dark:border-orange-500/30 text-orange-500 dark:text-orange-300 font-medium py-3 px-6 rounded-xl hover:bg-orange-50/80 dark:hover:bg-gray-600/80 transition-all duration-300 flex items-center justify-center gap-2 overflow-hidden"
                        >
                            <span class="absolute inset-0 bg-orange-500/0 group-hover:bg-orange-500/5 transition duration-300"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <span class="z-10">Registrarse</span>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes float-1 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(-12px, -18px) rotate(-2deg); }
}
@keyframes float-2 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(18px, -12px) rotate(2deg); }
}
@keyframes float-3 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(-10px, 15px) rotate(-1deg); }
}
@keyframes float-4 {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    50% { transform: translate(15px, 10px) rotate(1deg); }
}
.animate-float-1 { animation: float-1 infinite ease-in-out; }
.animate-float-2 { animation: float-2 infinite ease-in-out; }
.animate-float-3 { animation: float-3 infinite ease-in-out; }
.animate-float-4 { animation: float-4 infinite ease-in-out; }
</style>