<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/solid'

const page = usePage()
const show = ref(false)
const message = ref('')
const type = ref('success')

watch(() => page.props.flash, (flash) => {
  if (flash?.success || flash?.error) {
    message.value = flash.success || flash.error
    type.value = flash.success ? 'success' : 'error'
    show.value = true
    setTimeout(() => show.value = false, 4000)
  }
}, { immediate: true })
</script>

<template>
  <transition
    enter-active-class="transition ease-out duration-500"
    enter-from-class="-translate-y-4 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition ease-in duration-300"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      :class="[
        'fixed top-6 right-6 z-50 flex items-start gap-3 p-4 border-l-4 rounded-lg shadow-lg max-w-sm w-full text-sm',
        type === 'success' ? 'bg-orange-50 border-orange-600 text-orange-800' : 'bg-red-50 border-red-600 text-red-800'
      ]"
    >
      <!-- Icono -->
      <div class="shrink-0 pt-1">
        <component
          :is="type === 'success' ? CheckCircleIcon : ExclamationCircleIcon"
          :class="type === 'success' ? 'text-orange-600' : 'text-red-600'"
          class="w-6 h-6"
        />
      </div>

      <!-- Mensaje -->
      <div class="flex-1">
        <strong class="block mb-1 font-semibold">
          {{ type === 'success' ? 'Éxito' : 'Error' }}
        </strong>
        <p class="leading-snug">{{ message }}</p>
      </div>
    </div>
  </transition>
</template>
