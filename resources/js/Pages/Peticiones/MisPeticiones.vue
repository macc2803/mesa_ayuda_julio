```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

// Importando íconos de Heroicons
import { 
  DocumentTextIcon, 
  ChatBubbleLeftRightIcon, 
  ClockIcon, 
  CalendarDaysIcon,
  PaperAirplaneIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  ChevronDownIcon,
  ListBulletIcon,
  MagnifyingGlassIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  PlusIcon
} from '@heroicons/vue/24/solid'

const props = defineProps({
  peticiones: Array,
})

// Estados para filtros y búsqueda
const filtroActivo = ref('todas')
const mostrarMenuFiltro = ref(false)
const terminoBusqueda = ref('')
const orden = ref('recientes')

// Función para redirigir
const nuevaPeticion = () => {
  router.get('/peticiones/crear')
}

// Filtrar y ordenar peticiones
const peticionesFiltradas = computed(() => {
  let resultado = props.peticiones
  
  if (filtroActivo.value !== 'todas') {
    resultado = resultado.filter(p => p.estado === filtroActivo.value)
  }
  
  if (terminoBusqueda.value) {
    const termino = terminoBusqueda.value.toLowerCase()
    resultado = resultado.filter(p => 
      p.titulo.toLowerCase().includes(termino) || 
      p.descripcion.toLowerCase().includes(termino)
    )
  }
  
  resultado = [...resultado].sort((a, b) => {
    const fechaA = new Date(a.created_at)
    const fechaB = new Date(b.created_at)
    return orden.value === 'recientes' ? 
      fechaB - fechaA : 
      fechaA - fechaB
  })
  
  return resultado
})
</script>

<template>
  <AppLayout title="Mis Peticiones">
    <template #header>
      <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight">Mis Peticiones</h2>
    </template>

    <div class="py-6 bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
          
          <!-- Controles superiores: Filtro, Búsqueda y Ordenación -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <!-- Filtro desplegable -->
            <div class="relative">
              <button 
                @click="mostrarMenuFiltro = !mostrarMenuFiltro"
                class="flex items-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-500 transition-colors shadow-sm"
              >
                <div class="flex items-center gap-2">
                  <ListBulletIcon class="w-5 h-5" />
                  <span class="font-medium">
                    {{ 
                      filtroActivo === 'todas' ? 'Todas las peticiones' :
                      filtroActivo === 'enviado' ? 'Peticiones enviadas' :
                      filtroActivo === 'proceso' ? 'Peticiones en proceso' : 'Peticiones completadas'
                    }}
                  </span>
                </div>
                <ChevronDownIcon class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': mostrarMenuFiltro }" />
              </button>
              
              <div 
                v-show="mostrarMenuFiltro"
                @click.away="mostrarMenuFiltro = false"
                class="absolute z-20 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
              >
                <div class="py-1">
                  <button
                    @click="filtroActivo = 'todas'; mostrarMenuFiltro = false"
                    class="flex items-center gap-3 w-full text-left px-5 py-3 hover:bg-orange-50 dark:hover:bg-gray-700 transition-colors duration-150"
                    :class="{ 'bg-orange-100 text-orange-800 font-medium': filtroActivo === 'todas' }"
                  >
                    <ListBulletIcon class="w-5 h-5 text-orange-500" />
                    <span>Todas las peticiones</span>
                  </button>
                  
                  <button
                    @click="filtroActivo = 'enviado'; mostrarMenuFiltro = false"
                    class="flex items-center gap-3 w-full text-left px-5 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors duration-150"
                    :class="{ 'bg-blue-100 text-blue-800 font-medium': filtroActivo === 'enviado' }"
                  >
                    <PaperAirplaneIcon class="w-5 h-5 text-blue-500" />
                    <span>Peticiones enviadas</span>
                  </button>
                  
                  <button
                    @click="filtroActivo = 'proceso'; mostrarMenuFiltro = false"
                    class="flex items-center gap-3 w-full text-left px-5 py-3 hover:bg-amber-50 dark:hover:bg-gray-700 transition-colors duration-150"
                    :class="{ 'bg-amber-100 text-amber-800 font-medium': filtroActivo === 'proceso' }"
                  >
                    <ArrowPathIcon class="w-5 h-5 text-amber-500" />
                    <span>Peticiones en proceso</span>
                  </button>
                  
                  <button
                    @click="filtroActivo = 'completado'; mostrarMenuFiltro = false"
                    class="flex items-center gap-3 w-full text-left px-5 py-3 hover:bg-green-50 dark:hover:bg-gray-700 transition-colors duration-150"
                    :class="{ 'bg-green-100 text-green-800 font-medium': filtroActivo === 'completado' }"
                  >
                    <CheckCircleIcon class="w-5 h-5 text-green-500" />
                    <span>Peticiones completadas</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Barra de búsqueda -->
            <div class="relative flex-grow max-w-md">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 dark:text-gray-500" />
              </div>
              <input
                v-model="terminoBusqueda"
                type="text"
                class="block w-full pl-10 pr-3 py-2.5 border border-orange-600 rounded-lg shadow-sm focus:ring-orange-600 focus:ring-2 focus:ring-orange-600 focus:border-orange-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                placeholder="Buscar peticiones..."
              />
            </div>

            <!-- Botones de ordenación -->
            <div class="flex space-x-2">
              <button
                @click="nuevaPeticion"
                class="flex items-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-500 transition-colors shadow-sm"
              >
                <PlusIcon class="w-5 h-5" />
                <span>Nueva Petición</span>
              </button>
              
              <button
                @click="orden = 'recientes'"
                class="flex items-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-500 transition-colors shadow-sm"
                :class="{ 'bg-orange-100 border-orange-300': orden === 'recientes' }"
              >
                <ArrowUpIcon class="w-5 h-5" />
                <span>Más recientes</span>
              </button>
              <button
                @click="orden = 'antiguos'"
                class="flex items-center gap-2 px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-500 transition-colors shadow-sm"
                :class="{ 'bg-orange-100 border-orange-300': orden === 'antiguos' }"
              >
                <ArrowDownIcon class="w-5 h-5" />
                <span>Más antiguos</span>
              </button>
            </div>
          </div>

          <!-- Tabla de peticiones -->
          <table class="w-full text-left table-auto border-collapse rounded-xl overflow-hidden shadow-md bg-white dark:bg-gray-900">
            <thead>
              <tr class="bg-orange-600 text-white">
                <th class="px-5 py-3 text-sm font-semibold tracking-wide text-left">
                  <div class="flex items-center gap-2">
                    <DocumentTextIcon class="w-5 h-5" />
                    Título
                  </div>
                </th>
                <th class="px-5 py-3 text-sm font-semibold tracking-wide text-left">
                  <div class="flex items-center gap-2">
                    <ChatBubbleLeftRightIcon class="w-5 h-5" />
                    Descripción
                  </div>
                </th>
                <th class="px-5 py-3 text-sm font-semibold tracking-wide text-left">
                  <div class="flex items-center gap-2">
                    <ClockIcon class="w-5 h-5" />
                    Estado
                  </div>
                </th>
                <th class="px-5 py-3 text-sm font-semibold tracking-wide text-left">
                  <div class="flex items-center gap-2">
                    <CalendarDaysIcon class="w-5 h-5" />
                    Fecha de Creación
                  </div>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <tr v-if="peticionesFiltradas.length === 0">
                <td colspan="4" class="text-center text-gray-500 dark:text-gray-400 py-6 italic">
                  {{
                    filtroActivo === 'todas' ? 'No se encontraron peticiones.' :
                    filtroActivo === 'enviado' ? 'No se encontraron peticiones enviadas.' :
                    filtroActivo === 'proceso' ? 'No se encontraron peticiones en proceso.' : 'No se encontraron peticiones completadas.'
                  }}
                </td>
              </tr>

              <tr
                v-for="peticion in peticionesFiltradas"
                :key="peticion.id"
                class="hover:bg-orange-50 dark:hover:bg-gray-800 transition-colors duration-150"
              >
                <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ peticion.titulo }}
                </td>
                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                  {{ peticion.descripcion }}
                </td>
                <td class="px-5 py-4 text-sm font-semibold">
                  <span :class="{
                    'text-green-600 dark:text-green-400': peticion.estado === 'completado',
                    'text-orange-500 dark:text-orange-400': peticion.estado === 'proceso',
                    'text-blue-500 dark:text-blue-400': peticion.estado === 'enviado'
                  }">
                    <span v-if="peticion.estado === 'completado'">
                      <CheckCircleIcon class="w-5 h-5 inline-block mr-1 text-green-600 dark:text-green-400" />
                    </span>
                    <span v-if="peticion.estado === 'proceso'">
                      <ArrowPathIcon class="w-5 h-5 inline-block mr-1 animate-spin text-orange-500 dark:text-orange-400" />
                    </span>
                    <span v-if="peticion.estado === 'enviado'">
                      <PaperAirplaneIcon class="w-5 h-5 inline-block mr-1 text-blue-500 dark:text-blue-400" />
                    </span>
                    {{ peticion.estado.charAt(0).toUpperCase() + peticion.estado.slice(1) }}
                  </span>
                </td>
                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                  {{ new Date(peticion.created_at).toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  }) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
