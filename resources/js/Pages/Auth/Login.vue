<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { WrenchScrewdriverIcon, Cog6ToothIcon } from '@heroicons/vue/24/outline';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// Toggle dark/light mode
const toggleTheme = () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 p-6">
        <!-- Botón de cambio de tema -->
        <button
            @click="toggleTheme"
            class="fixed top-4 right-4 p-2.5 bg-gray-700 text-white rounded-full shadow-md hover:bg-gray-800 transition-colors z-20"
            aria-label="Toggle Theme"
        >
            <Cog6ToothIcon class="h-6 w-6" />
        </button>

        <AuthenticationCard class="w-full max-w-sm p-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-300 dark:border-gray-700">
            <!-- Logo -->
            <template #logo>
                <div class="flex flex-col items-center mb-6">
                    <div class="bg-orange-600 rounded-full p-3 shadow-lg">
                        <WrenchScrewdriverIcon class="h-12 w-12 text-white" />
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold text-gray-800 dark:text-gray-200">Mesa de Ayuda</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Ingresa tus credenciales para continuar</p>
                </div>
            </template>

            <!-- Estado de éxito -->
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 p-3 rounded-md">
                {{ status }}
            </div>

            <!-- Formulario -->
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <InputLabel for="email" value="Correo electrónico" class="text-gray-700 dark:text-gray-300" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="w-full mt-1 p-3 border border-gray-300 dark:border-gray-600 rounded-md 
                            bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 
                            focus:ring-orange-500 focus:border-orange-500 placeholder-gray-400 dark:placeholder-gray-300"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="tucorreo@ejemplo.com"
                    />
                    <InputError class="mt-1 text-sm" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Contraseña" class="text-gray-700 dark:text-gray-300" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="w-full mt-1 p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-1 text-sm" :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                        <Checkbox v-model:checked="form.remember" name="remember" class="text-orange-600 border-gray-300 dark:border-gray-600 focus:ring-orange-500 h-4 w-4 rounded" />
                        <span class="ml-2">Recordar usuario</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 underline"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>

                <div>
                    <PrimaryButton
                        class="w-full flex justify-center bg-orange-600 hover:bg-orange-700 text-white p-3 rounded-md transition-colors font-medium"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Procesando...
                        </span>
                        <span v-else>Iniciar sesión</span>
                    </PrimaryButton>
                </div>
            </form>

            <div class="text-center mt-6 text-sm text-gray-600 dark:text-gray-400">
                ¿No tienes una cuenta?
                <Link
                    :href="route('register')"
                    class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 ml-1 underline"
                >
                    Regístrate
                </Link>
            </div>
        </AuthenticationCard>
    </div>
</template>
