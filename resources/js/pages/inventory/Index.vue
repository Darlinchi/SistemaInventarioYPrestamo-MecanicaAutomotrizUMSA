<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Package, Wrench, SquarePen, Ban, Search, List, Image, Plus, CircleCheck, XIcon, Eye, FileText, QrCode,
    Calendar, Hash, AlignLeft, PaintBucket, Rows3, BookText, CalendarDays } from 'lucide-vue-next';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import items from '@/routes/items';
import { router } from '@inertiajs/vue3';

// TABLA DE EQUIPOS Y HERRAMIENTAS
// Definición de interfaces
interface Accessory {
    id: number;
    nombre_accesorio: string;
    estado_accesorio: string;
}
interface Equipment {
    codigo_qr?: string;
    ubicacion?: string;
    marca?: string;
    modelo?: string;
    serie?: string;
    rubro?: string;
    fecha_adquisicion?: string;
    observacion_equipo?: string;
    color?: string;
    accessories?: Accessory[];
}
interface Item {
    id: number;
    nombre_item: string;
    foto: string;
    descripcion_item: string;
    estado: string;
    equipment?: Equipment | null;
}
const props = defineProps<{
    items: Item[];
    estados: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventario', href: items.index.url(), },
];

// MANEJO DE NOTIFICACIONES (FLASH)
const page = usePage();
const showSuccess = ref(false);
const successMessage = ref('');

// Funcion para el mensaje flash
watch( () => page.props.flash, (nextFlash) => {
        // Usamos una aserción de tipo simple o el encadenamiento opcional
        const success = (nextFlash as any)?.success;
        if (success) {
            successMessage.value = success;
            showSuccess.value = true;
            // Limpiamos el mensaje después de 5 segundos
            setTimeout(() => {
                showSuccess.value = false;
            }, 5000);
        }
    },
    { deep: true, immediate: true }
);

// LÓGICA DE TABLA Y FILTROS
const activeTab = ref<'equipos' | 'herramientas'>('equipos');
// El estado para el texto de búsqueda
const searchQuery = ref('');
// Variables para los filtros seleccionados
const selectedStatus = ref('');
// Estado para el Popover de accesorios
const openAccessoryId = ref<number | null>(null);
const countEquipos = computed(() => props.items.filter(item => item.equipment !== null).length);
const countHerramientas = computed(() => props.items.filter(item => item.equipment === null).length);

// Buscador y filtro por estado
const filteredItems = computed(() => {
    // Filtra todo para mejor rendimiento
    return props.items.filter(item => {

        // FILTRO 1: Pestaña (Equipos / Herramientas)
        const isEquipment = item.equipment !== null;
        const matchesTab = activeTab.value === 'equipos' ? isEquipment : !isEquipment;
        if (!matchesTab) return false;

        // FILTRO 2: Estado (Select)
        const matchesStatus = selectedStatus.value === '' || item.estado === selectedStatus.value;
        if (!matchesStatus) return false;

        // FILTRO 3: Búsqueda (Texto)
        const query = searchQuery.value.toLowerCase().trim();
        if (query === '') return true;

        // Buscamos de forma segura usando encadenamiento opcional (?.)
        return (
            item.nombre_item.toLowerCase().includes(query) ||
            item.equipment?.codigo_qr?.toLowerCase().includes(query) ||
            item.equipment?.marca?.toLowerCase().includes(query) ||
            item.equipment?.modelo?.toLowerCase().includes(query)
        );
    });
});

// FUNCIONES DE UTILIDAD
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

// Dar baja
const isConfirmingBaja = ref(false);
const itemToBaja = ref<Item | null>(null);

// Abre el modal y guarda el item seleccionado
const openConfirmBaja = (item: Item) => {
    itemToBaja.value = item;
    isConfirmingBaja.value = true;
};

// Cierra el modal y limpia el estado
const closeConfirmBaja = () => {
    isConfirmingBaja.value = false;
    itemToBaja.value = null;
};
const executeBaja = () => {
    if (!itemToBaja.value) return;

    router.put(items.update.url(itemToBaja.value.id), {
        nombre_item: itemToBaja.value.nombre_item,
        descripcion_item: itemToBaja.value.descripcion_item,
        estado: 'Baja',
        es_equipo: itemToBaja.value.equipment !== null,
        // Datos del equipo con encadenamiento opcional seguro
        codigo_qr: itemToBaja.value.equipment?.codigo_qr,
        marca: itemToBaja.value.equipment?.marca,
        modelo: itemToBaja.value.equipment?.modelo,
        serie: itemToBaja.value.equipment?.serie,
        ubicacion: itemToBaja.value.equipment?.ubicacion,
        color: itemToBaja.value.equipment?.color,
        rubro: itemToBaja.value.equipment?.rubro,
        fecha_adquisicion: itemToBaja.value.equipment?.fecha_adquisicion,
        observacion_equipo: itemToBaja.value.equipment?.observacion_equipo,
    }, {
        preserveScroll: true,
        onSuccess: () => closeConfirmBaja(),
    });
};

// Para visualizar toda la informacion y hacer reporte
const viewInformacion = ref(false);
const itemInformacion = ref<Item | null>(null);

// Abre el modal y guarda el item seleccionado
const openViewInformacion = (item: Item) => {
    itemInformacion.value = item;
    viewInformacion.value = true;
};

// Cierra el modal y limpia el estado
const closeViewInformacion = () => {
    viewInformacion.value = false;
    itemInformacion.value = null;
};

const imprimirFicha = () => {
    const originalTitle = document.title;
    // Cambiamos el nombre del archivo para que al guardar el PDF sea profesional
    document.title = `Ficha_Tecnica_${itemInformacion.value?.nombre_item || 'Item'}`;
    window.print();
    document.title = originalTitle;
};

// Para Fecha
const formatDate = (date?: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Función para alternar la visibilidad de los accesorios
const toggleAccessories = (id: number) => {
    openAccessoryId.value = openAccessoryId.value === id ? null : id;
};

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
                        <Plus class="w-5 h-5"/> Agregar Item
                    </Link>
                </div>
            </div>

            <!--las pestañas  de equipos y herramientas-->
            <div class="flex p-1.5 bg-neutral-100 rounded-xl w-fit mb-6 border border-neutral-200 shadow-inner">
                <button
                    @click="activeTab = 'equipos'"
                    :class="[
                        'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-200',
                        activeTab === 'equipos'
                            ? 'bg-white text-black shadow-md scale-[1.02]'
                            : 'text-neutral-500 hover:text-black hover:bg-neutral-200/50'
                    ]"
                >
                    <Package :class="['w-5 h-5', activeTab === 'equipos' ? 'text-red-600' : 'text-neutral-400']"/>
                    <span>Equipos ({{ countEquipos }})</span>
                </button>

                <button
                    @click="activeTab = 'herramientas'"
                    :class="[
                        'flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-200',
                        activeTab === 'herramientas'
                            ? 'bg-white text-black shadow-md scale-[1.02]'
                            : 'text-neutral-500 hover:text-black hover:bg-neutral-200/50'
                    ]"
                >
                    <Wrench :class="['w-5 h-5', activeTab === 'herramientas' ? 'text-blue-600' : 'text-neutral-400']"/>
                    <span>Herramientas ({{ countHerramientas }})</span>
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
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-black"
                    >
                        <XIcon class="w-4 h-4"/>
                    </button>
                </div>

                <select v-model="selectedStatus" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600 min-w-[180px]">
                    <option value="">Todos los estados</option>
                    <option v-for="estado in props.estados" :key="estado" :value="estado">{{ estado }}</option>
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
                                <th v-if="activeTab === 'herramientas'" class="p-4">Descripción</th>
                                <th class="p-4">Estado</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Ubicación</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Accesorios</th>
                                <!--
                                <th v-if="activeTab === 'equipos'" class="p-4">Color</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Marca</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Modelo</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Serie</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Rubro</th>
                                <th v-if="activeTab === 'equipos'" class="p-4 whitespace-nowrap">F. Adquisición</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Observación</th>
                                -->

                                <th class="p-4 text-center sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
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
                                <td v-if="activeTab === 'herramientas'" class="p-4 text-neutral-500 max-w-xs truncate">{{ item.descripcion_item }}</td>
                                <td class="p-4">
                                    <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', statusColor(item.estado)]">
                                        {{ item.estado }}
                                    </span>
                                </td>
                                <td v-if="activeTab === 'equipos'" class="p-4 text-neutral-600">{{ item.equipment?.ubicacion }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 whitespace-nowrap">
                                    <div class="relative inline-block text-left">
                                        <button
                                            v-if="(item.equipment?.accessories?.length ?? 0) > 0"
                                            @click.stop="toggleAccessories(item.id)"
                                            class="flex items-center gap-2 px-3 py-1 bg-white border border-neutral-200 rounded-lg shadow-sm hover:bg-neutral-50 transition active:scale-95"
                                        >
                                            <List class="w-4 h-4 stroke-black"/>
                                            <span class="text-sm font-bold text-black">{{ item.equipment?.accessories?.length }}</span>
                                        </button>
                                        <span v-else class="text-sm text-neutral-400 italic">Sin accesorios</span>

                                        <div
                                            v-if="openAccessoryId === item.id"
                                            class="absolute right-0 z-100 mt-2 w-72 bg-white border border-neutral-200 rounded-xl shadow-2xl p-4 animate-in fade-in zoom-in duration-200"
                                        >
                                            <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                                <div
                                                    v-for="acc in item.equipment?.accessories"
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
                                <!--
                                <td v-if="activeTab === 'equipos'" class="p-4 text-neutral-600">{{ item.equipment?.color }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 font-semibold">{{ item.equipment?.marca }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ item.equipment?.modelo }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 font-mono">{{ item.equipment?.serie }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ item.equipment?.rubro }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ formatDate(item.equipment?.fecha_adquisicion ?? '') }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 italic text-neutral-400 max-w-xs truncate">{{ item.equipment?.observacion_equipo }}</td>
                                -->

                                <td class="p-4 text-right space-x-3 sticky right-0 bg-white group-hover:bg-neutral-50 border-l border-neutral-100">
                                    <Button
                                        @click="openViewInformacion(item)"
                                        class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-red-500"
                                        title="Información"
                                    >
                                        <Eye class="w-4.5 h-4.5 stroke-green-500"/>
                                    </Button>
                                    <Link :href="items.edit.url(item.id)">
                                        <Button class="p-2 bg-white border border-neutral-200 rounded-lg group shadow-sm transition hover:bg-blue-50 text-blue-500"><SquarePen class="w-4.5 h-4.5 stroke-blue-500"/></Button>
                                    </Link>
                                    <Button
                                        @click="openConfirmBaja(item)"
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

        <div v-if="isConfirmingBaja" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="closeConfirmBaja"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-center w-14 h-14 mx-auto bg-red-100 rounded-full mb-4">
                    <Ban class="w-8 h-8 text-red-600" />
                </div>

                <h3 class="text-xl font-black text-center text-neutral-900 uppercase tracking-tighter">¿Confirmar Baja?</h3>
                <p class="mt-3 text-sm text-center text-neutral-500 leading-relaxed">
                    Estás a punto de dar de baja el ítem: <br>
                    <span class="font-bold text-blue-700 text-base">{{ itemToBaja?.nombre_item }}</span>
                </p>

                <div class="mt-8 flex gap-3">
                    <button
                        @click="closeConfirmBaja"
                        class="flex-1 px-4 py-3 border border-neutral-200 text-neutral-600 rounded-xl font-bold text-sm hover:bg-neutral-200 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="executeBaja"
                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-200 active:scale-95"
                    >
                        Sí, dar de baja
                    </button>
                </div>
            </div>
        </div>

        <div v-if="viewInformacion" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md no-print" @click="closeViewInformacion"></div>

            <div class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-300 flex flex-col">

                <button @click="closeViewInformacion" class="absolute top-4 right-4 z-10 p-2 bg-neutral-100 hover:bg-neutral-200 rounded-full transition-colors no-print">
                    <XIcon class="w-5 h-5 text-neutral-700" />
                </button>

                <div class="p-8 overflow-y-auto">

                    <div class="no-print">
                        <div class="flex flex-col md:flex-row gap-6 items-start mb-8">
                            <div class="shrink-0">
                                <img v-if="itemInformacion?.foto" :src="'/storage/' + itemInformacion.foto" class="w-32 h-32 rounded-2xl object-cover shadow-md border border-neutral-100" />
                                <div v-else class="w-32 h-32 rounded-2xl bg-neutral-100 flex items-center justify-center border border-dashed border-neutral-300">
                                    <Image class="w-12 h-12 text-neutral-300" />
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h2 class="text-3xl font-black text-neutral-900 uppercase tracking-tighter leading-none">
                                        {{ itemInformacion?.nombre_item }}
                                    </h2>
                                </div>
                                <p class="text-neutral-500 text-sm leading-relaxed ">
                                    {{ itemInformacion?.descripcion_item }}
                                </p>
                                <span :class="['px-3 py-1 rounded-full text-[10px] font-black uppercase border', statusColor(itemInformacion?.estado ?? '')]">
                                    {{ itemInformacion?.estado }}
                                </span>
                                <div class="flex items-center gap-3 mt-2.5">
                                    <div class="p-2 bg-white rounded-lg shadow-sm border border-neutral-100">
                                        <QrCode class="w-5 h-5 text-neutral-900" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-neutral-700 uppercase font-black tracking-widest">Código QR</p>
                                        <p class="text-sm font-mono font-bold text-blue-600">
                                            {{ itemInformacion?.equipment?.codigo_qr || 'SIN CÓDIGO' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div v-if="itemInformacion?.equipment" class="space-y-4">
                                <h4 class="text-[14px] font-black text-neutral-700 uppercase tracking-widest border-b border-neutral-100 pb-2">Ficha Técnica</h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-50 rounded-lg"><Hash class="w-4 h-4 text-neutral-900"/></div>
                                        <div>
                                            <p class="text-[12px] text-neutral-700 uppercase font-bold">Serie</p>
                                            <p class="text-sm font-mono font-bold text-neutral-800">{{ itemInformacion.equipment.serie }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-50 rounded-lg"><Package class="w-4 h-4 text-neutral-900"/></div>
                                        <div>
                                            <p class="text-[12px] text-neutral-700 uppercase font-bold">Marca / Modelo</p>
                                            <p class="text-sm font-bold text-neutral-800">{{ itemInformacion.equipment.marca }} - {{ itemInformacion.equipment.modelo }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-green-50 rounded-lg"><Rows3 class="w-4 h-4 text-neutral-900"/></div>
                                        <div>
                                            <p class="text-[12px] text-neutral-700 uppercase font-bold">Ubicación</p>
                                            <p class="text-sm font-bold text-neutral-800">{{ itemInformacion.equipment.ubicacion }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-green-50 rounded-lg"><BookText class="w-4 h-4 text-neutral-900" /></div>
                                        <div>
                                            <p class="text-[12px] text-neutral-700 uppercase font-bold">Rubro</p>
                                            <p class="text-sm font-bold text-neutral-800">{{ itemInformacion.equipment.rubro }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-green-50 rounded-lg"><CalendarDays class="w-4 h-4 text-neutral-900"/></div>
                                        <div>
                                            <p class="text-[12px] text-neutral-700 uppercase font-bold">Fecha de Adquisición</p>
                                            <p class="text-sm font-bold text-neutral-800">{{ formatDate(itemInformacion.equipment.fecha_adquisicion) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-[14px] font-black text-neutral-700 uppercase tracking-widest border-b border-neutral-100 pb-2 flex items-center gap-2">
                                    <List class="w-4 h-4 stroke-neutral-800" />
                                    <span>Accesorios</span>
                                </h4>
                                <div v-if="itemInformacion?.equipment?.accessories?.length" class="space-y-3 mt-2">
                                    <div v-for="acc in itemInformacion.equipment.accessories" :key="acc.id" class="flex items-center justify-between p-2 bg-neutral-50 rounded-xl border border-neutral-100">
                                        <span class="text-sm font-bold text-neutral-700">{{ acc.nombre_accesorio }}</span>
                                        <span :class="['px-2.5 py-1 rounded-lg text-[10px] font-black uppercase border', statusColorA(acc.estado_accesorio)]">
                                            {{ acc.estado_accesorio }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-green-50 rounded-lg"><PaintBucket class="w-4 h-4 text-neutral-900" /></div>
                                    <div>
                                        <p class="text-[12px] text-neutral-700 uppercase font-bold">Color</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ itemInformacion?.equipment?.color }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div v-if="itemInformacion?.descripcion_item" class="mt-3 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                                <p class="text-[14px] text-neutral-700 uppercase font-bold flex items-center gap-2 mb-1">
                                    <AlignLeft class="w-4 h-4 stroke-indigo-900" />
                                    <span>Descripción</span>
                                </p>
                                <p class="text-sm text-neutral-800 italic">"{{ itemInformacion?.descripcion_item }}"</p>
                            </div>

                            <div v-if="itemInformacion?.equipment?.observacion_equipo" class="mt-3 p-4 bg-orange-50/50 border border-orange-100 rounded-xl">
                                <p class="text-[14px] text-neutral-700 uppercase font-bold flex items-center gap-2 mb-1">
                                    <AlignLeft class="w-4 h-4 stroke-orange-600" />
                                    <span>Observación</span>
                                </p>
                                <p class="text-sm text-neutral-800 italic">"{{ itemInformacion.equipment.observacion_equipo }}"</p>
                            </div>
                        </div>
                    </div>

                    <div class="print-only hidden">
                        <div class="border-2 border-neutral-800 rounded-lg overflow-hidden">
                            <div class="flex border-b-2 border-neutral-800">
                                <div class="w-1/5 p-2 border-r-2 border-neutral-800 flex items-center justify-center bg-white">
                                    <img src="/images/logo-carrera.png" class="max-h-20 object-contain" alt="Logo UMSA" />
                                </div>
                                <div class="w-3/5 p-4 text-center flex flex-col justify-center border-r-2 border-neutral-800">
                                    <h2 class="text-sm font-black uppercase leading-tight">Universidad Mayor de San Andrés</h2>
                                    <h3 class="text-[11px] font-bold uppercase">Facultad de Tecnología - Mecánica Automotriz</h3>
                                    <p class="text-[10px] mt-1 font-black py-1 uppercase bg-neutral-100">Ficha Técnica de Equipos y Herramientas</p>
                                    <div class="flex justify-center gap-4 text-[10px] font-bold uppercase mt-1">
                                        <span>{{ itemInformacion?.nombre_item }}</span>
                                    </div>
                                    <div class="flex justify-center gap-4 mt-1 text-[9px] font-bold uppercase">
                                        <span>Código: {{ itemInformacion?.equipment?.codigo_qr || 'N/A' }}</span>
                                        <span>Fecha: {{ formatDate(new Date().toISOString()) }}</span>
                                    </div>
                                </div>
                                <div class="w-1/5 p-1 flex items-center justify-center bg-white">
                                    <img v-if="itemInformacion?.foto" :src="'/storage/' + itemInformacion.foto" class="max-h-20 w-full object-cover rounded-sm border border-neutral-200" />
                                    <div v-else class="text-[8px] text-neutral-400 text-center">SIN FOTO</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 border-b-2 border-neutral-800 bg-neutral-50">
                                <div class="p-2 border-r-2 border-neutral-800">
                                    <p class="text-[9px] font-black uppercase text-neutral-800">Área / Ubicación:</p>
                                    <p class="text-xs font-bold">{{ itemInformacion?.equipment?.ubicacion || 'ALMACÉN' }}</p>
                                </div>
                                <div class="p-2">
                                    <p class="text-[9px] font-black uppercase text-neutral-800">Estado Técnico:</p>
                                    <p class="text-xs font-bold uppercase">{{ itemInformacion?.estado }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 border-b-2 border-neutral-800">
                                <div class="p-2 border-r-2 border-neutral-800">
                                    <p class="text-[9px] font-black uppercase text-neutral-800">Marca:</p>
                                    <p class="text-xs font-bold">{{ itemInformacion?.equipment?.marca || '-' }}</p>
                                </div>
                                <div class="p-2 border-r-2 border-neutral-800">
                                    <p class="text-[9px] font-black uppercase text-neutral-800">Modelo/Tipo:</p>
                                    <p class="text-xs font-bold">{{ itemInformacion?.equipment?.modelo || '-' }}</p>
                                </div>
                                <div class="p-2">
                                    <p class="text-[9px] font-black uppercase text-neutral-800">Serie:</p>
                                    <p class="text-xs font-mono font-bold">{{ itemInformacion?.equipment?.serie || '-' }}</p>
                                </div>
                            </div>

                            <div class="p-2 bg-neutral-50 border-b-2 border-neutral-800">
                                <p class="text-[9px] font-black uppercase text-neutral-800 mb-1">Otros accesorios que dispone el equipo:</p>
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div v-for="acc in itemInformacion?.equipment?.accessories" :key="acc.id" class="text-[10px] flex justify-between border-b border-neutral-200">
                                        <span>• {{ acc.nombre_accesorio }}</span>
                                        <span class="italic text-neutral-900 text-[9px]">{{ acc.estado_accesorio }}</span>
                                    </div>
                                    <div v-if="!itemInformacion?.equipment?.accessories?.length" class="text-[10px] text-neutral-400">Ninguno</div>
                                </div>
                            </div>

                            <div class="p-2 min-h-20 border-b-2 border-neutral-800">
                                <p class="text-[9px] font-black uppercase text-neutral-800">Descripción:</p>
                                <p class="text-[10px] italic leading-tight mt-1">
                                    {{ itemInformacion?.descripcion_item || 'Sin descripción.' }}
                                </p>
                            </div>

                            <div class="p-2 min-h-20">
                                <p class="text-[9px] font-black uppercase text-neutral-800">Observaciones / Especificaciones de Seguridad:</p>
                                <p class="text-[10px] italic leading-tight mt-1">
                                    {{ itemInformacion?.equipment?.observacion_equipo || 'Sin observaciones adicionales.' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-16 flex justify-around text-center">
                            <div class="border-t border-neutral-800 w-56 pt-2">
                                <p class="text-[10px] font-bold uppercase">Entregado por</p>
                                <p class="text-[8px] text-neutral-500 mt-1">Nombre y Firma</p>
                            </div>
                            <div class="border-t border-neutral-800 w-56 pt-2">
                                <p class="text-[10px] font-bold uppercase">Recibido Conforme</p>
                                <p class="text-[8px] text-neutral-500 mt-1">Nombre y Firma</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-neutral-50/80 border-t border-neutral-100 shrink-0 flex gap-3 no-print">
                    <button @click="closeViewInformacion" class="flex-1 py-3 bg-white border border-neutral-200 text-neutral-600 rounded-xl font-bold text-sm hover:bg-neutral-100 transition-all">
                        Cerrar
                    </button>
                    <button @click="imprimirFicha" class="flex-1 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-neutral-800 transition-all flex items-center justify-center gap-2 shadow-lg active:scale-95">
                        <FileText class="w-4 h-4" /> Generar Ficha PDF
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
