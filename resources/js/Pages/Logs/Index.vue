```vue
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';

defineProps({
    logs: {
        type: Object,
        default: () => ({ data: [], total: 0, from: 0, to: 0, prev_page_url: null, next_page_url: null }),
    },
    error: {
        type: String,
        default: null,
    },
});

const debugLogs = ref(null);

onMounted(() => {
    console.log('Logs received in frontend:', JSON.stringify(logs, null, 2));
    debugLogs.value = logs.data || [];
});
</script>

<template>
    <Head title="Visualización de Logs" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-3xl text-orange-600 dark:text-orange-400 leading-tight">
                Visualización de Logs
            </h2>
        </template>

        <div class="py-12 bg-orange-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 border border-orange-200 dark:border-orange-700">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">
                            Entradas de Log
                        </h3>
                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-sm rounded-full">
                            {{ logs.total || 0 }} entradas
                        </span>
                    </div>

                    <div v-if="error" class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded">
                        {{ error }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-orange-200 dark:divide-orange-700">
                            <thead class="bg-orange-100 dark:bg-orange-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-700 dark:text-orange-300 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-700 dark:text-orange-300 uppercase">Nivel</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-700 dark:text-orange-300 uppercase">Contexto</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-700 dark:text-orange-300 uppercase">Mensaje</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-orange-700 dark:text-orange-300 uppercase">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-orange-200 dark:divide-orange-700">
                                <tr v-for="(log, index) in logs.data" :key="index" class="hover:bg-orange-50 dark:hover:bg-orange-900/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ log.timestamp || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ log.level || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ log.context || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ log.message || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ log.details || 'N/A' }}
                                    </td>
                                </tr>
                                <tr v-if="logs.data.length === 0 && !error">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay logs disponibles.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando {{ logs.from || 0 }} a {{ logs.to || 0 }} de {{ logs.total || 0 }} entradas
                        </span>
                        <div class="flex space-x-2">
                            <Link v-if="logs.prev_page_url" :href="logs.prev_page_url" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                                Anterior
                            </Link>
                            <Link v-if="logs.next_page_url" :href="logs.next_page_url" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                                Siguiente
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```

