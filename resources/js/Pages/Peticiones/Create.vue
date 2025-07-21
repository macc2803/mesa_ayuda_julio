<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import {
  PencilSquareIcon,
  DocumentTextIcon,
  BuildingOfficeIcon,
  PaperAirplaneIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  areas: Array
})

const notification = ref({
  show: false,
  message: '',
  type: ''
})

const form = useForm({
  titulo: '',
  descripcion: '',
  area_id: ''
})

const showNotification = (type, message) => {
  notification.value = {
    show: true,
    type,
    message
  }
  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

const submit = () => {
  form.post('/peticiones', {
    onSuccess: () => {
      form.reset()
      showNotification('success', '¡Petición enviada correctamente!')
    },
    onError: () => {
      showNotification('error', 'Error al enviar la petición. Revisa los campos e intenta de nuevo.')
    }
  })
}
</script>

<template>
  <AppLayout title="Nueva Petición">
    <template #header>
      <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
        <PencilSquareIcon class="w-6 h-6 text-orange-600" />
        Crear Nueva Petición
      </h2>
    </template>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen"> <!-- Cambiado a gray-900 -->
      <div class="max-w-3xl mx-auto px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-8"> <!-- Cambiado a gray-800 -->
          <form @submit.prevent="submit" class="space-y-6">

            <!-- Título -->
            <div>
              <label class="block text-sm font-semibold text-orange-600 dark:text-orange-400 mb-1 flex items-center gap-1">
                <PencilSquareIcon class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                Título
              </label>
              <input
                v-model="form.titulo"
                type="text"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                       placeholder-gray-400 dark:placeholder-gray-500
                       focus:ring-2 focus:ring-orange-600 focus:outline-none transition"
                placeholder="Escribe el título de la petición"
              />
              <p v-if="form.errors.titulo" class="text-sm text-red-600 dark:text-red-400 mt-1">
                {{ form.errors.titulo }}
              </p>
            </div>

            <!-- Descripción -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1 flex items-center gap-1">
                <DocumentTextIcon class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                Descripción
              </label>
              <textarea
                v-model="form.descripcion"
                rows="4"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                       placeholder-gray-400 dark:placeholder-gray-500
                       focus:ring-2 focus:ring-orange-600 focus:outline-none transition"
                placeholder="Describe brevemente tu petición"
              ></textarea>
              <p v-if="form.errors.descripcion" class="text-sm text-red-600 dark:text-red-400 mt-1">
                {{ form.errors.descripcion }}
              </p>
            </div>

            <!-- Área -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1 flex items-center gap-1">
                <BuildingOfficeIcon class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                Área
              </label>
              <select
                v-model="form.area_id"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2
                       bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                       focus:ring-2 focus:ring-orange-600 focus:outline-none transition"
              >
                <option value="" disabled>Selecciona un área</option>
                <option v-for="area in areas" :key="area.id" :value="area.id">
                  {{ area.nombre }}
                </option>
              </select>
              <p v-if="form.errors.area_id" class="text-sm text-red-600 dark:text-red-400 mt-1">
                {{ form.errors.area_id }}
              </p>
            </div>

            <!-- Botón de Enviar -->
            <div class="flex justify-end">
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-3 rounded-lg focus:outline-none focus:ring-4 focus:ring-orange-300 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <PaperAirplaneIcon class="w-5 h-5 transform rotate-45" />
                <span v-if="!form.processing">Enviar Petición</span>
                <span v-else>Enviando...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Notificación flotante -->
    <transition
      enter-active-class="transition ease-out duration-300 transform"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="notification.show"
        :class="{
          'bg-green-600 dark:bg-green-800': notification.type === 'success',
          'bg-red-600 dark:bg-red-800': notification.type === 'error'
        }"
        class="fixed bottom-4 right-4 z-[9999] text-white px-6 py-3 rounded-lg shadow-lg flex items-center"
      >
        <span>{{ notification.message }}</span>
      </div>
    </transition>
  </AppLayout>
</template>
