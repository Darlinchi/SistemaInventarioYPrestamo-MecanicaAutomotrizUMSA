<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Package, Wrench, SquarePen, Ban, Search, List, Image, Plus, CircleCheck, XIcon } from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import items from '@/routes/items';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventario',
        href: items.index.url(), // Usa la función de tu archivo de rutas
    },
];

// PARA AGREGAR HERRAMIENTAAS Y EQUIPOS
const page = usePage();
const showSuccess = ref(false);
const successMessage = ref('');

// Funci0n para el mensaje flash
const handleFlash = () => {
    const msg = (page.props as any).flash?.success;
    if (msg) {
        successMessage.value = msg;
        showSuccess.value = true;
        setTimeout(() => {
            showSuccess.value = false;
        }, 5000);
    }
};

// Vigila los cambios en las props
watch(() => (page.props as any).flash?.success, () => {
    handleFlash();
}, { immediate: true });

// TABLA DE EQUIPOS Y HERRAMIENTAS
const props = defineProps<{
    items: Array<{
        id: number;
        nombre_item: string;
        foto: string;
        descripcion_item: string;
        estado: string;
        equipment?: any;
    }>;
}>();

// Colores segun los estados para el item y para accesorios
const statusColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'disponible': return 'bg-green-100 text-green-800 border-green-200';
        case 'prestado': return 'bg-orange-100 text-orange-800 border-orange-200';
        case 'mantenimiento': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'dañado': return 'bg-red-100 text-red-800 border-red-200';
        case 'baja': return 'bg-neutral-200 text-neutral-600 border-neutral-300';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
const statusColorA = (status: string) => {
    switch (status.toLowerCase()) {
        case 'bueno': return 'bg-green-100 text-green-800 border-green-200';;
        case 'dañado': return 'bg-red-100 text-red-800 border-red-200';
        case 'perdido': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};

const activeTab = ref<'equipos' | 'herramientas'>('equipos');
const countEquipos = computed(() => props.items.filter(item => item.equipment !== null).length);
const countHerramientas = computed(() => props.items.filter(item => item.equipment === null).length);

// Para Fecha
const formatDate = (date: string) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Buscador y filtro por estado
// El estado para el texto de búsqueda
const searchQuery = ref('');
// Variables para los filtros seleccionados
const selectedStatus = ref('');
// Listas para llenar los selectores (esto podría venir de la BD también)
const estados = ['Disponible', 'Prestado', 'Mantenimiento', 'Dañado'];
// Estado para el Popover de accesorios
const openAccessoryId = ref<number | null>(null);

// Función para alternar la visibilidad de los accesorios
const toggleAccessories = (id: number) => {
    openAccessoryId.value = openAccessoryId.value === id ? null : id;
};

// Buscador y filtro por estado
const filteredItems = computed(() => {
    // Primero filtramos por pestaña (Equipos vs Herramientas)
    let filtered = props.items.filter(item => {
        if (activeTab.value === 'equipos') {
            return item.equipment !== null;
        } else {
            return item.equipment === null;
        }
    });

    // Filtro por el texto de busqueda (si el usuario escribe algo)
    if (searchQuery.value.trim() !== '') {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item => {
            return (
                item.nombre_item.toLowerCase().includes(query) ||
                item.equipment?.codigo_qr?.toLowerCase().includes(query) ||
                item.equipment?.marca?.toLowerCase().includes(query)
            );
        });
    }

    // Filtro por Estado
    if (selectedStatus.value !== '') {
        filtered = filtered.filter(item => item.estado === selectedStatus.value);
    }

    return filtered;
});

// Cierra el popover si se hace clic fuera del contenedor
const closePopovers = (e: MouseEvent) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.relative.inline-block')) {
        openAccessoryId.value = null;
    }
};

// Activar al entrar y desactivar al salir
onMounted(() => window.addEventListener('click', closePopovers));
onUnmounted(() => window.removeEventListener('click', closePopovers));

// Da de baja
const darDeBaja = (id: number) => {
    const itemActual = props.items.find(i => i.id === id);

    if (itemActual && confirm(`¿Confirmar baja de: ${itemActual.nombre_item}?`)) {
        router.post(items.update.url(id), {
            _method: 'put',
            estado: 'Baja',
            nombre_item: itemActual.nombre_item,
            es_equipo: itemActual.equipment !== null,
        });
    }
};
</script>

<template>
    <Head title="Inventario" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-[-20px] opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition ease-in duration-500"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showSuccess" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <CircleCheck class="h-5 w-5 stroke-green-500"/>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-green-800">{{ successMessage }}</p>
                        </div>
                    </div>

                    <button @click="showSuccess = false" class="text-green-500 hover:text-green-700 transition">
                        <XIcon class="h-5 w-5 stroke-green-500"/>
                    </button>
                </div>
            </transition>
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Gestión de Inventario</h1>
                    <p class="text-sm text-neutral-500">Administre equipos y herramientas del taller</p>

                </div>
                <div class="flex justify-end mt-4" >
                    <Link :href="items.create.url()" class="bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-neutral-800 transition shadow-lg">
                        <Plus class="w-5 h-5"/> Nuevo Item
                    </Link>
                </div>
            </div>
            <!--las pestañas  de equipos y herramientas-->
            <div class="flex p-1 bg-neutral-100 rounded-xl w-fit mb-6 border border-neutral-200">
                <button @click="activeTab = 'equipos'" :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all', activeTab === 'equipos' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <Package class="w-5 h-5"/>Equipos ({{ countEquipos }})
                </button>
                <button @click="activeTab = 'herramientas'" :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all', activeTab === 'herramientas' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <Wrench class="w-5 h-5"/>Herramientas ({{ countHerramientas }})
                </button>
            </div>

            <!--buscador y filtro por estado -->
            <div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <Search class="w-5 h-5 stroke-gray-600"/>
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar..."
                        class="block w-full pl-10 pr-3 py-2 bg-neutral-100 border border-neutral-500 rounded-xl leading-5 text-sm placeholder-neutral-600 focus:outline-none focus:ring-1 focus:ring-black focus:border-black transition duration-150 ease-in-out"
                    />
                    <Button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-black"
                    >
                        <XIcon class="w-4 h-4"/>
                    </Button>
                </div>

                <select v-model="selectedStatus" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600 min-w-[180px]">
                    <option value="">Todos los estados</option>
                    <option v-for="estado in estados" :key="estado" :value="estado">{{ estado }}</option>
                </select>
            </div>

            <div class="relative bg-white border border-neutral-200 rounded-xl shadow-sm overflow-x-auto">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-max border-separate border-spacing-0">
                        <thead class="bg-neutral-200 border-b border-neutral-300 text-xs font-bold uppercase tracking-widest text-neutral-800">
                            <tr>
                                <th v-if="activeTab === 'equipos'" class="p-4">Código QR</th>
                                <th class="p-4">Nombre</th>
                                <th class="p-4">Foto</th>
                                <th class="p-4">Descripción</th>
                                <th class="p-4">Estado</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Ubicación</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Accesorios</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Color</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Marca</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Modelo</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Serie</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Rubro</th>
                                <th v-if="activeTab === 'equipos'" class="p-4 whitespace-nowrap">F. Adquisición</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Observación</th>
                                <th class="p-4 text-right sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-neutral-50 transition-colors group">
                                <td v-if="activeTab === 'equipos'" class="p-4 font-mono text-blue-600">{{ item.equipment?.codigo_qr }}</td>
                                <td class="p-4 font-bold text-black">{{ item.nombre_item }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 shrink-0">
                                            <img v-if="item.foto"
                                                :src="'/storage/' + item.foto"
                                                class="h-12 w-12 rounded-lg object-cover border border-neutral-200 shadow-sm transition-transform duration-300 group-hover:scale-110"
                                                alt="Foto"
                                            />
                                            <div v-else class="h-12 w-12 rounded-lg bg-neutral-100 flex items-center justify-center border border-neutral-200 text-neutral-400">
                                                <Image class="w-6 h-6" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-neutral-500 max-w-xs truncate">{{ item.descripcion_item }}</td>
                                <td class="p-4">
                                    <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', statusColor(item.estado)]">
                                        {{ item.estado }}
                                    </span>
                                </td>
                                <td v-if="activeTab === 'equipos'" class="p-4 text-neutral-600">{{ item.equipment?.ubicacion }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 whitespace-nowrap">
                                    <div class="relative inline-block text-left">
                                        <button
                                            v-if="item.equipment?.accessories?.length > 0"
                                            @click.stop="toggleAccessories(item.id)"
                                            class="flex items-center gap-2 px-3 py-1 bg-white border border-neutral-200 rounded-lg shadow-sm hover:bg-neutral-50 transition active:scale-95"
                                        >
                                            <List class="w-4 h-4 stroke-black"/>
                                            <span class="text-sm font-bold text-black">{{ item.equipment.accessories.length }}</span>
                                        </button>
                                        <span v-else class="text-sm text-neutral-400 italic">Sin accesorios</span>

                                        <div
                                            v-if="openAccessoryId === item.id"
                                            class="absolute right-0 z-100 mt-2 w-72 bg-white border border-neutral-200 rounded-xl shadow-2xl p-4 animate-in fade-in zoom-in duration-200"
                                        >
                                            <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                                <div
                                                    v-for="acc in item.equipment.accessories"
                                                    :key="acc.id"
                                                    class="flex items-center justify-between p-2 bg-neutral-50 border border-neutral-100 rounded-lg"
                                                >
                                                    <span class="text-xs font-bold text-neutral-700">{{ acc.nombre_accesorio }}</span>
                                                    <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', statusColorA(acc.estado_accesorio)]">
                                                        {{ acc.estado_accesorio }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td v-if="activeTab === 'equipos'" class="p-4 text-neutral-600">{{ item.equipment?.color }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 font-semibold">{{ item.equipment?.marca }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ item.equipment?.modelo }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 font-mono">{{ item.equipment?.serie }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ item.equipment?.rubro }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ formatDate(item.equipment?.fecha_adquisicion) }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 italic text-neutral-400 max-w-xs truncate">{{ item.equipment?.observacion_equipo }}</td>
                                <td class="p-4 text-right space-x-3 sticky right-0 bg-white group-hover:bg-neutral-50 border-l border-neutral-100">
                                    <Link :href="items.edit.url(item.id)">
                                        <Button class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-blue-500"><SquarePen class="w-4.5 h-4.5 stroke-blue-500"/></Button>
                                    </Link>
                                    <Button
                                        @click="darDeBaja(item.id)"
                                        class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-red-500"
                                        title="Dar de baja"
                                    >
                                        <Ban class="w-4.5 h-4.5 stroke-red-500"/>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
