<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PencilIcon, TrashIcon, PlusIcon, ArrowLeftIcon, TableCellsIcon } from '@heroicons/vue/24/solid';

// Definir props
const props = defineProps({
    table: String,
    columns: Array,
    records: Array,
    errors: Object,
    areas: Array,
});

// Depuración
console.log('Props recibidos:', {
    table: props.table,
    table_lower: props.table?.toLowerCase(),
    table_raw: JSON.stringify(props.table),
    columns: props.columns,
    columns_count: props.columns?.length || 0,
    records_count: props.records?.length || 0,
    areas: props.areas,
    areas_count: props.areas?.length || 0,
    errors: props.errors,
});

// Verificar si la tabla es 'users'
const isUsers = computed(() => {
    const isTableUsers = props.table?.toLowerCase() === 'users';
    console.log('isUsers:', isTableUsers, 'table:', props.table);
    return isTableUsers;
});

// Formatear fechas
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('es-MX', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};

// Inicializar formulario
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
    area_id: null,
    nombre: '', // Para areas
    descripcion: '', // Para peticions
});
const showForm = ref(false);
const editingRecord = ref(null);
const tableError = ref(null);

// Abrir formulario para crear
const openCreateForm = () => {
    form.reset();
    if (!isUsers.value && props.table?.toLowerCase() === 'users') {
        tableError.value = `Tabla actual: "${props.table}". Se esperaba "users".`;
    }
    showForm.value = true;
    editingRecord.value = null;
    console.log('Formulario inicializado (crear):', form.data());
};

// Abrir formulario para editar
const openEditForm = (record) => {
    form.reset();
    form.name = record.name || '';
    form.email = record.email || '';
    form.password = '';
    form.role = record.role || '';
    form.area_id = record.area_id || null;
    form.nombre = record.nombre || '';
    form.descripcion = record.descripcion || '';
    if (!isUsers.value && props.table?.toLowerCase() === 'users') {
        tableError.value = `Tabla actual: "${props.table}". Se esperaba "users".`;
    }
    showForm.value = true;
    editingRecord.value = record;
    console.log('Formulario inicializado (editar):', form.data());
};

// Enviar formulario
const submitForm = () => {
    console.log('Enviando datos:', form.data());
    form.clearErrors();
    if (props.table?.toLowerCase() === 'users') {
        if (!form.name) form.errors.name = 'El nombre es obligatorio.';
        if (!form.email) form.errors.email = 'El correo es obligatorio.';
        if (!form.password && !editingRecord.value) form.errors.password = 'La contraseña es obligatoria.';
        if (!form.role) form.errors.role = 'El rol es obligatorio.';
        
    } else if (props.table?.toLowerCase() === 'areas') {
        if (!form.nombre) form.errors.nombre = 'El nombre es obligatorio.';
    } else if (props.table?.toLowerCase() === 'peticions') {
    }
    if (Object.keys(form.errors).length) {
        console.log('Errores en frontend:', form.errors);
        return;
    }
    const requestData = props.table?.toLowerCase() === 'users'
        ? { name: form.name, email: form.email, password: form.password, role: form.role, area_id: form.area_id }
        : props.table?.toLowerCase() === 'areas'
        ? { nombre: form.nombre }
        : props.table?.toLowerCase() === 'peticions'
        ? { area_id: form.area_id, descripcion: form.descripcion }
        : form.data();
    console.log('Datos enviados al backend:', requestData);

    if (editingRecord.value) {
        form.put(route('databases.update', { table: props.table, id: editingRecord.value.id }), {
            preserveState: true,
            preserveScroll: true,
            data: requestData,
            onSuccess: () => {
                showForm.value = false;
                form.reset();
                tableError.value = null;
            },
            onError: (errors) => {
                console.log('Errores del backend:', errors);
            },
        });
    } else {
        form.post(route('databases.store', props.table), {
            preserveState: true,
            preserveScroll: true,
            data: requestData,
            onSuccess: () => {
                showForm.value = false;
                form.reset();
                tableError.value = null;
            },
            onError: (errors) => {
                console.log('Errores del backend:', errors);
            },
        });
    }
};

// Eliminar registro
const deleteRecord = (id) => {
    if (confirm('¿Estás seguro de eliminar este registro?')) {
        form.delete(route('databases.destroy', { table: props.table, id }), {
            preserveState: true,
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="`Tabla: ${table}`" />

    <AppLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <TableCellsIcon class="w-8 h-8 text-orange-600 dark:text-orange-400" />
                <h2 class="font-semibold text-3xl text-gray-900 dark:text-white">
                    Tabla: {{ table }}
                </h2>
            </div>
        </template>

        <div class="py-10 bg-white dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Errores generales -->
                <div v-if="errors.error" class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg">
                    {{ errors.error }}
                </div>
                <!-- Error de tabla -->
                <div v-if="tableError" class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-200 px-4 py-3 rounded-lg">
                    {{ tableError }}
                </div>
                <!-- Errores de validación -->
                <div v-if="Object.keys(form.errors).length" class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg">
                    <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
                </div>

                <!-- Botones y descripción -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                        <Link :href="route('databases.index')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500">
                            <ArrowLeftIcon class="w-5 h-5" />
                            Volver a Tablas
                        </Link>
                        <button @click="openCreateForm" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-500">
                            <PlusIcon class="w-5 h-5" />
                            Agregar Registro
                        </button>
                    </div>
                    <p class="text-base text-gray-700 dark:text-gray-300">
                        Administra los registros de la tabla <span class="font-semibold text-orange-600 dark:text-orange-400">{{ table }}</span>.
                    </p>
                </div>

                <!-- Formulario -->
                <div v-if="showForm" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        {{ editingRecord ? 'Editar Registro' : 'Agregar Registro' }}
                    </h3>
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Campos para users -->
                            <div v-if="isUsers">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nombre
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.name }"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div v-if="isUsers">
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Correo Electrónico
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.email }"
                                    required
                                />
                                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                            </div>
                            <div v-if="isUsers">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Contraseña
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.password }"
                                    :required="!editingRecord"
                                />
                                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                            </div>
                            <div v-if="isUsers">
                                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Rol
                                </label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.role }"
                                    required
                                >
                                    <option value="" disabled>Selecciona un rol</option>
                                    <option value="encargado">Encargado</option>
                                    <option value="cliente">Cliente</option>
                                </select>
                                <p v-if="form.errors.role" class="mt-1 text-sm text-red-600">{{ form.errors.role }}</p>
                            </div>
                            <div v-if="isUsers">
                                <label for="area_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Área
                                </label>
                                <select
                                    id="area_id"
                                    v-model="form.area_id"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.area_id }"
                                    required
                                >
                                    <option value="" disabled>Selecciona un área</option>
                                    <option v-for="area in areas" :key="area.id" :value="area.id">
                                        {{ area.nombre }}
                                    </option>
                                </select>
                                <p v-if="form.errors.area_id" class="mt-1 text-sm text-red-600">{{ form.errors.area_id }}</p>
                                <p v-if="!areas || areas.length === 0" class="mt-1 text-sm text-yellow-600">
                                    No hay áreas disponibles.
                                </p>
                            </div>
                            <!-- Campo para areas -->
                            <div v-if="table?.toLowerCase() === 'areas'">
                                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nombre
                                </label>
                                <input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.nombre }"
                                    required
                                />
                                <p v-if="form.errors.nombre" class="mt-1 text-sm text-red-600">{{ form.errors.nombre }}</p>
                            </div>
                            <!-- Campos para peticions -->
                            <div v-if="table?.toLowerCase() === 'peticions'">
                                <label for="area_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Área
                                </label>
                                <select
                                    id="area_id"
                                    v-model="form.area_id"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.area_id }"
                                    required
                                >
                                    <option value="" disabled>Selecciona un área</option>
                                    <option v-for="area in areas" :key="area.id" :value="area.id">
                                        {{ area.nombre }}
                                    </option>
                                </select>
                                <p v-if="form.errors.area_id" class="mt-1 text-sm text-red-600">{{ form.errors.area_id }}</p>
                                <p v-if="!areas || areas.length === 0" class="mt-1 text-sm text-yellow-600">
                                    No hay áreas disponibles.
                                </p>
                            </div>
                            <div v-if="table?.toLowerCase() === 'peticions'">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Descripción
                                </label>
                                <input
                                    id="descripcion"
                                    v-model="form.descripcion"
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500"
                                    :class="{ 'border-red-500': form.errors.descripcion }"
                                />
                                <p v-if="form.errors.descripcion" class="mt-1 text-sm text-red-600">{{ form.errors.descripcion }}</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                {{ editingRecord ? 'Actualizar' : 'Crear' }}
                            </button>
                            <button type="button" @click="showForm = false" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de registros -->
                <div v-if="records.length" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th v-for="column in columns" :key="column" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ column.replace('_', ' ') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="record in records" :key="record.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td v-for="column in columns" :key="column" class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <span v-if="column === 'password'">••••••••</span>
                                    <span v-else-if="column === 'created_at' || column === 'updated_at'">{{ formatDate(record[column]) }}</span>
                                    <span v-else>{{ record[column] || '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm flex gap-3">
                                    <button @click="openEditForm(record)" class="text-orange-600 hover:text-orange-800 dark:text-orange-400 dark:hover:text-orange-300">
                                        <PencilIcon class="w-5 h-5" />
                                    </button>
                                    <button @click="deleteRecord(record.id)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <TrashIcon class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <p class="text-base text-gray-700 dark:text-gray-300">No hay registros en esta tabla.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>