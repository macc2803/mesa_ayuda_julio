```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'
import { Chart, registerables } from 'chart.js'
import { MagnifyingGlassIcon, FunnelIcon, ArrowPathIcon, CheckCircleIcon, PaperAirplaneIcon } from '@heroicons/vue/24/solid'

Chart.register(...registerables)

const props = defineProps({
  peticiones: Array,
})

const page = usePage()
const flash = page.props.flash || {}
const chartCanvas = ref(null)
let chartInstance = null

const filtroActivo = ref('todas')
const terminoBusqueda = ref('')
const ordenPeticiones = ref('recientes')

const peticionesFiltradas = computed(() => {
  let filtradas = props.peticiones
  
  if (filtroActivo.value !== 'todas') {
    filtradas = filtradas.filter(p => p.estado === filtroActivo.value)
  }
  
  if (terminoBusqueda.value) {
    const termino = terminoBusqueda.value.toLowerCase()
    filtradas = filtradas.filter(p => 
      p.titulo.toLowerCase().includes(termino) ||
      p.descripcion.toLowerCase().includes(termino) ||
      (p.user?.name && p.user.name.toLowerCase().includes(termino)) ||
      (p.user?.email && p.user.email.toLowerCase().includes(termino))
    )
  }
  
  if (ordenPeticiones.value === 'recientes') {
    filtradas.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  } else {
    filtradas.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
  }
  
  return filtradas
})

const contarPeticionesPorEstado = () => {
  return {
    enviado: props.peticiones.filter(p => p.estado === 'enviado').length,
    proceso: props.peticiones.filter(p => p.estado === 'proceso').length,
    completado: props.peticiones.filter(p => p.estado === 'completado').length,
    total: props.peticiones.length
  }
}

const actualizarEstado = (peticionId, nuevoEstado) => {
  router.put(`/peticiones/${peticionId}`, {
    estado: nuevoEstado,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      actualizarGrafica()
    }
  })
}

const actualizarGrafica = () => {
  const counts = contarPeticionesPorEstado()
  
  if (chartInstance) {
    chartInstance.data.datasets[0].data = [counts.enviado, counts.proceso, counts.completado]
    chartInstance.update()
    return
  }

  const ctx = chartCanvas.value.getContext('2d')
  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Recibidos', 'En Proceso', 'Completadas'],
      datasets: [{
        label: 'Cantidad de Peticiones',
        data: [counts.enviado, counts.proceso, counts.completado],
        backgroundColor: [
          'rgba(59, 130, 246, 0.7)',
          'rgba(249, 115, 22, 0.7)',
          'rgba(16, 185, 129, 0.7)'
        ],
        borderColor: [
          'rgba(59, 130, 246, 1)',
          'rgba(249, 115, 22, 1)',
          'rgba(16, 185, 129, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            color: 'rgba(255, 255, 255, 0.87)' // Texto blanco en modo oscuro
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.9)', // Fondo oscuro para tooltips
          titleColor: 'rgba(255, 255, 255, 0.87)',
          bodyColor: 'rgba(255, 255, 255, 0.87)',
          callbacks: {
            label: function(context) {
              return `${context.dataset.label}: ${context.raw}`
            }
          }
        },
        title: {
          display: true,
          text: `Total de Peticiones: ${counts.total}`,
          font: {
            size: 16
          },
          color: 'rgba(255, 255, 255, 0.87)' // Título blanco en modo oscuro
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            precision: 0,
            color: 'rgba(255, 255, 255, 0.87)' // Etiquetas del eje Y blancas
          },
          grid: {
            color: 'rgba(255, 255, 255, 0.1)' // Líneas de cuadrícula claras
          }
        },
        x: {
          ticks: {
            color: 'rgba(255, 255, 255, 0.87)' // Etiquetas del eje X blancas
          },
          grid: {
            color: 'rgba(255, 255, 255, 0.1)' // Líneas de cuadrícula claras
          }
        }
      }
    }
  })
}

onMounted(() => {
  actualizarGrafica()
})
</script>

<template>
  <AppLayout title="Todas las Peticiones">
    <template #header>
      <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight">
        Panel de Administración - Peticiones
      </h2>
    </template>

    <div v-if="flash.success" class="max-w-7xl mx-auto mt-4">
      <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded">
        {{ flash.success }}
      </div>
    </div>

    <div class="max-w-7xl mx-auto mt-6 space-y-4 bg-gray-50 dark:bg-black">
      <div class="relative rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400 dark:text-gray-500" />
        </div>
        <input
          type="text"
          v-model="terminoBusqueda"
          class="block w-full pl-10 pr-12 py-2 border border-gray-300 dark:border-gray-700 rounded-md leading-5 bg-white dark:bg-gray-800 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm text-gray-900 dark:text-gray-100"
          placeholder="Buscar peticiones..."
        />
        <div class="absolute inset-y-0 right-0 flex items-center">
          <span class="text-gray-500 dark:text-gray-400 sm:text-sm mr-2">
            {{ peticionesFiltradas.length }} resultados
          </span>
        </div>
      </div>

      <div class="flex flex-wrap gap-2 items-center bg-gray-50 dark:bg-black">
        <div class="flex gap-2 mr-4">
          <button
            @click="ordenPeticiones = 'recientes'"
            :class="{
              'bg-orange-600 text-white': ordenPeticiones === 'recientes',
              'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': ordenPeticiones !== 'recientes'
            }"
            class="px-4 py-2 rounded-md font-medium transition-colors"
          >
            Más recientes
          </button>
          <button
            @click="ordenPeticiones = 'antiguas'"
            :class="{
              'bg-orange-600 text-white': ordenPeticiones === 'antiguas',
              'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': ordenPeticiones !== 'antiguas'
            }"
            class="px-4 py-2 rounded-md font-medium transition-colors"
          >
            Más antiguas
          </button>
        </div>

        <button
          @click="filtroActivo = 'todas'"
          :class="{
            'bg-orange-600 text-white': filtroActivo === 'todas',
            'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': filtroActivo !== 'todas'
          }"
          class="px-4 py-2 rounded-md font-medium transition-colors flex items-center"
        >
          <FunnelIcon class="w-4 h-4 mr-1 text-orange-500 dark:text-orange-400" />
          Todas
        </button>
        <button
          @click="filtroActivo = 'enviado'"
          :class="{
            'bg-blue-600 text-white': filtroActivo === 'enviado',
            'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': filtroActivo !== 'enviado'
          }"
          class="px-4 py-2 rounded-md font-medium transition-colors flex items-center"
        >
          <PaperAirplaneIcon class="w-4 h-4 mr-1 text-blue-500 dark:text-blue-400" />
          Recibidas
        </button>
        <button
          @click="filtroActivo = 'proceso'"
          :class="{
            'bg-yellow-400 text-white': filtroActivo === 'proceso',
            'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': filtroActivo !== 'proceso'
          }"
          class="px-4 py-2 rounded-md font-medium transition-colors flex items-center"
        >
          <ArrowPathIcon class="w-4 h-4 mr-1 text-yellow-500 dark:text-yellow-400" />
          En Proceso
        </button>
        <button
          @click="filtroActivo = 'completado'"
          :class="{
            'bg-green-600 text-white': filtroActivo === 'completado',
            'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600': filtroActivo !== 'completado'
          }"
          class="px-4 py-2 rounded-md font-medium transition-colors flex items-center"
        >
          <CheckCircleIcon class="w-4 h-4 mr-1 text-green-500 dark:text-green-400" />
          Completadas
        </button>
      </div>
    </div>

    <div class="py-8 max-w-7xl mx-auto bg-gray-50 dark:bg-black">
      <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-orange-600 text-white uppercase">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">Usuario</th>
              <th class="px-6 py-3">Correo</th>
              <th class="px-6 py-3">Título</th>
              <th class="px-6 py-3">Descripción</th>
              <th class="px-6 py-3">Fecha/Hora Llegada</th>
              <th class="px-6 py-3">Última Modificación</th>
              <th class="px-6 py-3">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr 
              v-for="peticion in peticionesFiltradas" 
              :key="peticion.id" 
              class="border-b hover:bg-orange-100 dark:hover:bg-gray-800"
            >
              <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ peticion.id }}</td>
              <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                {{ peticion.user?.name || 'Sin usuario' }}
              </td>
              <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                {{ peticion.user?.email || '—' }}
              </td>
              <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ peticion.titulo }}</td>
              <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ peticion.descripcion }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                {{ new Date(peticion.created_at).toLocaleDateString('es-ES', {
                  day: '2-digit',
                  month: '2-digit',
                  year: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit',
                }) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                {{ new Date(peticion.updated_at).toLocaleDateString('es-ES', {
                  day: '2-digit',
                  month: '2-digit',
                  year: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit'
                }) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <span 
                    :class="{
                      'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': peticion.estado === 'enviado',
                      'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200': peticion.estado === 'proceso',
                      'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200': peticion.estado === 'completado'
                    }" 
                    class="px-3 py-1 rounded-full text-xs font-semibold mr-2"
                  >
                    {{
                      peticion.estado === 'enviado' ? 'Recibido' :
                      peticion.estado === 'proceso' ? 'En Proceso' : 'Completado'
                    }}
                    <span v-if="peticion.estado === 'proceso'" class="text-xs block text-orange-600 dark:text-orange-400">
                      
                    </span>
                  </span>
                  <select
                    v-model="peticion.estado"
                    @change="actualizarEstado(peticion.id, peticion.estado)"
                    class="border border-gray-300 dark:border-gray-700 rounded px-2 py-1 bg-white dark:bg-gray-800 focus:ring-orange-500 text-gray-900 dark:text-gray-100 text-sm"
                  >
                    <option value="enviado">Enviado</option>
                    <option value="proceso">En Proceso</option>
                    <option value="completado">Completado</option>
                  </select>
                </div>
              </td>
            </tr>

            <tr v-if="peticionesFiltradas.length === 0">
              <td colspan="8" class="text-center text-gray-500 dark:text-gray-400 px-6 py-4">
                No se encontraron peticiones {{ 
                  filtroActivo === 'todas' ? '' : 
                  filtroActivo === 'enviado' ? 'enviadas' :
                  filtroActivo === 'proceso' ? 'en proceso' : 'completadas'
                }} que coincidan con la búsqueda.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="max-w-7xl mx-auto mb-8 bg-gray-50 dark:bg-black">
      <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700">
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
          <svg class="w-6 h-6 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          Estadísticas de Peticiones
        </h3>
        <div class="h-96 relative">
          <canvas ref="chartCanvas" class="w-full h-full"></canvas>
        </div>
        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400 flex justify-between items-center">
          <p class="text-gray-600 dark:text-gray-300">Actualizado: {{ new Date().toLocaleDateString('es-ES') }}</p>
          <span class="bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs font-medium px-2.5 py-0.5 rounded">
            Total: {{ contarPeticionesPorEstado().total }} peticiones
          </span>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
