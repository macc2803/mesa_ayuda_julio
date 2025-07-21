<script setup>
import { ref, onMounted } from 'vue'
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'  // Importo tanto el Sol como la Luna

const isMenuOpen = ref(false)
const currentTheme = ref(localStorage.getItem('theme') || 'light')

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
  console.log('Menú abierto:', isMenuOpen.value)
}

const setTheme = (theme) => {
  const html = document.documentElement
  if (theme === 'dark') {
    html.classList.add('dark')
    currentTheme.value = 'dark'
  } else {
    html.classList.remove('dark')
    currentTheme.value = 'light'
  }
  localStorage.setItem('theme', currentTheme.value)
  isMenuOpen.value = false
}

onMounted(() => {
  const html = document.documentElement
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    html.classList.add('dark')
    currentTheme.value = 'dark'
  } else {
    html.classList.remove('dark')
    currentTheme.value = 'light'
  }
})
</script>

<template>
  <div class="relative">
    <!-- Botón para cambiar tema -->
    <button
      @click="toggleMenu"
      class="fixed top-4 right-4 p-2 bg-orange-600 text-white rounded-full shadow-md hover:bg-orange-700 transition-colors z-20"
      aria-label="Cambiar Tema"
    >
      <!-- Alterna entre el Sol y la Luna -->
      <component :is="currentTheme === 'light' ? SunIcon : MoonIcon" class="h-6 w-6" />
    </button>

    <!-- Menú para seleccionar el tema -->
    <div
      v-if="isMenuOpen"
      class="absolute top-12 right-4 w-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-2 z-30"
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
</template>
