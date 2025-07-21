<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

import { UserIcon, AtSymbolIcon, LockClosedIcon } from '@heroicons/vue/20/solid';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <Head title="Registro - Acceso a Mesa de Ayuda" />

  <!-- Contenedor principal con fondo -->
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-200 via-gray-100 to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700 p-6 relative overflow-hidden">

    <!-- Fondo con patrones sutiles -->
    <div class="absolute inset-0 -z-10">
      <div class="absolute inset-0 bg-gradient-to-br from-white to-gray-50 opacity-80"></div>
      <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px]"></div>
      <div class="absolute inset-0 animate-pulse">
        <div class="h-full w-full bg-[linear-gradient(to_right,#f0f0f0_1px,transparent_1px),linear-gradient(to_bottom,#f0f0f0_1px,transparent_1px)] bg-[size:40px_40px] opacity-10"></div>
      </div>
    </div>

    <!-- Formulario -->
    <AuthenticationCard class="w-full max-w-md bg-white shadow-xl rounded-xl overflow-hidden transition-all duration-300 hover:shadow-2xl border border-gray-100 backdrop-blur-sm z-10">
      <template #logo>
        <div class="bg-white p-6 text-center border-b border-gray-100">
          <div class="flex justify-center">
            <svg class="h-16 w-16 text-orange-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 14C19 15.105 19.895 16 21 16C22.105 16 23 15.105 23 14C23 12.895 22.105 12 21 12C19.895 12 19 12.895 19 14Z" fill="currentColor"/>
              <path d="M1 14C1 15.105 1.895 16 3 16C4.105 16 5 15.105 5 14C5 12.895 4.105 12 3 12C1.895 12 1 12.895 1 14Z" fill="currentColor"/>
              <path d="M12 22C17.523 22 22 17.523 22 12C22 6.477 17.523 2 12 2C6.477 2 2 6.477 2 12C2 17.523 6.477 22 12 22Z" fill="currentColor" fill-opacity="0.2"/>
              <path d="M12 18C15.314 18 18 15.314 18 12C18 8.686 15.314 6 12 6C8.686 6 6 8.686 6 12C6 15.314 8.686 18 12 18Z" fill="currentColor"/>
            </svg>
          </div>
          <h2 class="mt-4 text-2xl font-bold text-gray-800">Crear Cuenta</h2>
          <p class="mt-1 text-gray-600 font-medium">Sistema de Mesa de Ayuda</p>
        </div>
      </template>

      <form @submit.prevent="submit" class="p-8 space-y-5">
        <!-- Nombre -->
        <div>
          <InputLabel for="name" value="Nombre completo" class="text-gray-700 font-medium" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <UserIcon class="h-5 w-5 text-gray-400" />
            </div>
            <TextInput
              id="name"
              v-model="form.name"
              type="text"
              class="block w-full pl-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition placeholder-gray-400"
              placeholder="Ingrese su nombre completo"
              required
              autofocus
              autocomplete="name"
            />
          </div>
          <InputError class="mt-1 text-sm text-red-600" :message="form.errors.name" />
        </div>

        <!-- Email -->
        <div>
          <InputLabel for="email" value="Correo electrónico" class="text-gray-700 font-medium" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <AtSymbolIcon class="h-5 w-5 text-gray-400" />
            </div>
            <TextInput
              id="email"
              v-model="form.email"
              type="email"
              class="block w-full pl-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition placeholder-gray-400"
              placeholder="ejemplo@dominio.com"
              required
              autocomplete="username"
            />
          </div>
          <InputError class="mt-1 text-sm text-red-600" :message="form.errors.email" />
        </div>

        <!-- Contraseña -->
        <div>
          <InputLabel for="password" value="Contraseña" class="text-gray-700 font-medium" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <LockClosedIcon class="h-5 w-5 text-gray-400" />
            </div>
            <TextInput
              id="password"
              v-model="form.password"
              type="password"
              class="block w-full pl-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition placeholder-gray-400"
              placeholder="Mínimo 8 caracteres"
              required
              autocomplete="new-password"
            />
          </div>
          <InputError class="mt-1 text-sm text-red-600" :message="form.errors.password" />
        </div>

        <!-- Confirmación -->
        <div>
          <InputLabel for="password_confirmation" value="Confirmar contraseña" class="text-gray-700 font-medium" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <LockClosedIcon class="h-5 w-5 text-gray-400" />
            </div>
            <TextInput
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              class="block w-full pl-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition placeholder-gray-400"
              placeholder="Repita su contraseña"
              required
              autocomplete="new-password"
            />
          </div>
          <InputError class="mt-1 text-sm text-red-600" :message="form.errors.password_confirmation" />
        </div>

        <!-- Términos -->
        <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="pt-2">
          <div class="flex items-start">
            <div class="flex items-center h-5">
              <Checkbox id="terms" v-model:checked="form.terms" name="terms" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" />
            </div>
            <div class="ml-3 text-sm">
              <label for="terms" class="font-medium text-gray-700">
                Acepto los <a :href="route('terms.show')" class="text-orange-600 hover:text-orange-500 hover:underline">Términos de servicio</a> y la <a :href="route('policy.show')" class="text-orange-600 hover:text-orange-500 hover:underline">Política de privacidad</a>
              </label>
            </div>
          </div>
          <InputError class="mt-1 text-sm text-red-600" :message="form.errors.terms" />
        </div>

        <!-- Botón -->
        <div class="pt-4">
          <PrimaryButton
            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150"
            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            :disabled="form.processing"
          >
            <span v-if="!form.processing">Registrarse ahora</span>
            <span v-else class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              Procesando...
            </span>
          </PrimaryButton>
        </div>

        <!-- Link login -->
        <div class="text-center pt-3 text-sm text-gray-600">
          ¿Ya tienes una cuenta?
          <Link :href="route('login')" class="font-medium text-orange-600 hover:text-orange-500 hover:underline">
            Iniciar sesión
          </Link>
        </div>
      </form>
    </AuthenticationCard>
  </div>
</template>

<style scoped>
@keyframes float {
  0% {
    transform: translateY(0) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(-100vh) rotate(360deg);
    opacity: 0;
  }
}
</style>
