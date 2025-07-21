<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { CheckCircleIcon, ClockIcon, PaperAirplaneIcon, EyeIcon } from '@heroicons/vue/24/solid';
import { Cog6ToothIcon } from '@heroicons/vue/24/outline';
import { ref, onMounted } from 'vue';

const { props } = usePage();
const user = props.auth.user;

const pendientes = props.pendientes ?? 0;
const enProceso = props.enProceso ?? 0;
const completadas = props.completadas ?? 0;

// Depuración: Log del rol del usuario
console.log('Rol del usuario:', user.role);

const getStatusText = () => {
  return user.role === 'encargado'
    ? { recibidos: 'Recibidos', enProceso: 'En Proceso', completadas: 'Completadas' }
    : { recibidos: 'Enviados', enProceso: 'En Proceso', completadas: 'Completadas' };
};

const statusText = getStatusText();

const isMenuOpen = ref(false);
const currentTheme = ref(localStorage.getItem('theme') || 'light');

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const setTheme = (theme) => {
  const html = document.documentElement;
  if (theme === 'dark') {
    html.classList.add('dark');
    currentTheme.value = 'dark';
  } else {
    html.classList.remove('dark');
    currentTheme.value = 'light';
  }
  localStorage.setItem('theme', currentTheme.value);
  isMenuOpen.value = false;
};

onMounted(() => {
  const html = document.documentElement;
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    html.classList.add('dark');
    currentTheme.value = 'dark';
  } else {
    html.classList.remove('dark');
    currentTheme.value = 'light';
  }
});
</script>

<template>
  <AppLayout title="Página Principal">
    <template #header>
      <h2 class="font-semibold text-3xl text-gray-900 dark:text-white animate__animated animate__fadeIn">Mesa de Ayuda</h2>
    </template>

    <div class="py-10 bg-white dark:bg-gray-900 min-h-screen animate__animated animate__fadeIn">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        <!-- Botón de cambio de tema -->
        <div class="relative">
          <button
            @click="toggleMenu"
            class="fixed top-4 right-4 p-2 bg-orange-600 text-white rounded-full shadow-lg hover:bg-orange-700 transition duration-300 ease-in-out z-20 animate__animated animate__fadeIn"
            aria-label="Cambiar Tema"
          >
            <Cog6ToothIcon class="h-6 w-6" />
          </button>

          <!-- Menú desplegable -->
          <div
            v-if="isMenuOpen"
            class="absolute top-12 right-4 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-2 z-30 animate__animated animate__fadeInUp"
          >
            <button
              @click="setTheme('light')"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Fondo Blanco
            </button>
            <button
              @click="setTheme('dark')"
              class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              Fondo Negro
            </button>
          </div>
        </div>

        <!-- Tarjeta Principal -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8 border border-gray-200 dark:border-gray-700 animate__animated animate__fadeInUp">
          <!-- Encabezado -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
              <p class="text-2xl font-bold text-gray-800 dark:text-white animate__animated animate__fadeIn">{{ user.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 animate__animated animate__fadeIn">
                Rol: 
                <span class="capitalize font-semibold text-orange-600">{{ user.role }}</span>
                <span v-if="user.role === 'encargado'" class="ml-2">
                  (Área: {{ user.area?.nombre || 'No asignada' }})
                </span>
              </p>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row gap-4 mt-4 sm:mt-0 animate__animated animate__fadeIn">
              <Link
                v-if="user.role === 'cliente'"
                href="/peticiones/crear"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 text-white rounded-lg shadow-md hover:bg-orange-500 transition duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Petición
              </Link>

              <Link
                v-if="user.role === 'cliente'"
                href="/mis-peticiones"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 text-white rounded-lg shadow-md hover:bg-orange-500 transition duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405M19.5 12A7.5 7.5 0 105 12a7.5 7.5 0 0014.5 0z"></path>
                </svg>
                Mis Peticiones
              </Link>

              <Link
                v-if="user.role === 'encargado'"
                href="/peticiones"
                class="inline-flex items-center gap-2 px-6 py-3 bg-orange-600 text-white rounded-lg shadow-lg hover:bg-orange-700 transition duration-200"
              >
                <EyeIcon class="w-5 h-5" />
                <span class="font-semibold">Ver peticiones de mi área</span>
              </Link>
            </div>
          </div>

          <!-- Estado de Peticiones -->
          <div class="animate__animated animate__fadeInUp">
            <h3 class="text-lg font-bold text-gray-700 dark:text-white mb-4">Estado de Peticiones</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
              <!-- Estado: Pendientes -->
              <Link
                :href="user.role === 'encargado' ? '/peticiones?estado=enviado' : '/mis-peticiones'"
                class="group bg-red-50 dark:bg-red-800/30 hover:bg-red-100 dark:hover:bg-red-800/50 p-6 rounded-lg border border-red-300 dark:border-red-700/50 shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-4 animate__animated animate__fadeInUp"
              >
                <PaperAirplaneIcon class="w-10 h-10 text-red-400 dark:text-red-500 group-hover:text-red-600" />
                <div>
                  <p class="text-sm font-medium text-red-700 dark:text-red-300 uppercase">{{ statusText.recibidos }}</p>
                  <p class="text-3xl font-bold text-red-800 dark:text-red-400 group-hover:text-red-900">{{ pendientes }}</p>
                </div>
              </Link>

              <!-- Estado: En Proceso -->
              <Link
                :href="user.role === 'encargado' ? '/peticiones?estado=proceso' : '/mis-peticiones?estado=proceso'"
                class="group bg-yellow-50 dark:bg-yellow-800/30 hover:bg-yellow-100 dark:hover:bg-yellow-800/50 p-6 rounded-lg border border-yellow-300 dark:border-yellow-700/50 shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-4 animate__animated animate__fadeInUp"
              >
                <ClockIcon class="w-10 h-10 text-yellow-400 dark:text-yellow-500 group-hover:text-yellow-600" />
                <div>
                  <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300 uppercase">{{ statusText.enProceso }}</p>
                  <p class="text-3xl font-bold text-yellow-800 dark:text-yellow-400 group-hover:text-yellow-900">{{ enProceso }}</p>
                </div>
              </Link>

              <!-- Estado: Completadas -->
              <Link
                :href="user.role === 'encargado' ? '/peticiones?estado=completado' : '/mis-peticiones?estado=completado'"
                class="group bg-green-50 dark:bg-green-800/30 hover:bg-green-100 dark:hover:bg-green-800/50 p-6 rounded-lg border border-green-300 dark:border-green-700/50 shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-4 animate__animated animate__fadeInUp"
              >
                <CheckCircleIcon class="w-10 h-10 text-green-400 dark:text-green-500 group-hover:text-green-600" />
                <div>
                  <p class="text-sm font-medium text-green-700 dark:text-green-300 uppercase">{{ statusText.completadas }}</p>
                  <p class="text-3xl font-bold text-green-800 dark:text-green-400 group-hover:text-green-900">{{ completadas }}</p>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>