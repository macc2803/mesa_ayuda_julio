<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    databases: Array,
});
</script>

<template>
    <Head title="Bases de Datos" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-3xl text-gray-900 dark:text-white leading-tight transition-all duration-300 transform hover:scale-[1.01]">
                Bases de Datos
            </h2>
        </template>

        <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-8 border border-gray-200 dark:border-gray-700 transition-all duration-500 hover:shadow-2xl">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">
                            Tablas Disponibles
                        </h3>
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm rounded-full">
                            {{ databases.length }} tablas
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tabla</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Filas</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Cotejamiento</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tamaño</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Residuo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr 
                                    v-for="database in databases" 
                                    :key="database.name" 
                                    class="transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link 
                                            :href="route('databases.show', database.name)" 
                                            class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300 font-medium flex items-center group"
                                        >
                                            {{ database.name }}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ database.rows.toLocaleString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md">
                                            {{ database.type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ database.collation }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
                                        {{ database.size }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            v-if="database.overhead !== '-'" 
                                            class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200"
                                        >
                                            {{ database.overhead }}
                                        </span>
                                        <span v-else class="text-green-600 dark:text-green-400 text-sm">✓ Optimizado</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6 text-sm text-gray-500 dark:text-gray-400 flex justify-end">
                        Última actualización: {{ new Date().toLocaleString() }}
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>