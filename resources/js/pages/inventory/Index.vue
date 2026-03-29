<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import {
    Package, Wrench, SquarePen, Ban, Search, List, Image, Plus, CircleCheck, XIcon, Eye, Hash, BookText,
    QrCode, CalendarDays, Rows3, AlignLeft, PaintBucket, FileText, Layers
} from 'lucide-vue-next';
import AlertNotification from '@/components/AlertNotification.vue';
import PageHeader from '@/components/PageHeader.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import InventoryTable from '@/components/InventoryTable.vue';
import DataTable from '@/components/DataTable.vue';
import InventoryDetailModal from '@/components/InventoryDetailModal.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';
import { Button } from '@/components/ui/button';
import itemRoutes from '@/routes/items';
import equipmentRoutes from '@/routes/equipments';
import toolRoutes from '@/routes/tools';

// --- INTERFACES ---
interface Accessory {
    id: number;
    nombre_accesorio: string;
    estado_accesorio: string;
}

interface Item {
    id: number;
    codigo_qr: string;
    foto: string | null;
    // Campos de Equipo
    nombre_equipo?: string;
    ubicacion_equipo?: string;
    estado_equipo?: string;
    marca?: string;
    modelo?: string;
    accessories?: Accessory[];
    // Campos de Herramienta
    nombre_herramienta?: string;
    ubicacion_herramienta?: string;
    estado_herramienta?: string;
    marca_modelo?: string;
    cantidad_piezas?: BigInteger;
    descripcion_herramienta?: string;
    // Identificador para saber qué es
    tipo: 'equipo' | 'herramienta';
}

const props = defineProps<{
    items: Item[];
    estados_equipo: string[];
    estados_herramienta: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{
    title: 'Inventario',
    href: itemRoutes.index.url()
}];

// --- NOTIFICACIONES FLASH ---
const page = usePage();
const flashSuccess = computed(() => (page.props.flash as any)?.success);

// --- LÓGICA DE TABLA ---
const activeTab = ref<'equipos' | 'herramientas'>('equipos');
const searchQuery = ref('');
const selectedStatus = ref('');
const openAccessoryId = ref<number | null>(null);

// Limpiamos el filtro de estado cada vez que cambiamos de pestaña (Opcional, pero recomendado)
watch(activeTab, () => {
    selectedStatus.value = '';
});
// Dependiendo de la pestaña, pasamos los estados correspondientes
const currentOptions = computed(() => {
    return activeTab.value === 'equipos'
        ? props.estados_equipo
        : props.estados_herramienta;
});
// Contadores basados en el campo 'tipo'
const countEquipos = computed(() => props.items.filter(i => i.tipo === 'equipo').length);
const countHerramientas = computed(() => props.items.filter(i => i.tipo === 'herramienta').length);

// En tu <script setup> de Index.vue
const inventoryTabs = computed(() => [
    { id: 'equipos', label: 'Equipos', count: countEquipos.value, icon: 'Package' },
    { id: 'herramientas', label: 'Herramientas', count: countHerramientas.value, icon: 'Wrench' }
]);

// --- FILTRADO INTELIGENTE ---
const filteredItems = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    const currentStatus = selectedStatus.value;

    return props.items.filter(item => {
        // 1. Filtro por Pestaña
        if (activeTab.value === 'equipos' && item.tipo !== 'equipo') return false;
        if (activeTab.value === 'herramientas' && item.tipo !== 'herramienta') return false;

        // 2. Filtro por Estado (Simplificado)
        if (currentStatus) {
            const estadoItem = item.tipo === 'equipo' ? item.estado_equipo : item.estado_herramienta;
            if (estadoItem !== currentStatus) return false;
        }

        // 3. Buscador
        if (!query) return true;
        const nombre = (item.nombre_equipo || item.nombre_herramienta || '').toLowerCase();
        const qr = (item.codigo_qr || '').toLowerCase();
        const marca = (item.marca || item.marca_modelo || '').toLowerCase();

        return nombre.includes(query) || qr.includes(query) || marca.includes(query);
    });
});

// --- ACCIONES ---
const toggleAccessories = (id: number) => {
    openAccessoryId.value = openAccessoryId.value === id ? null : id;
};

// Lógica de ejecución (Conectada al botón "Sí, dar de baja")
const executeBaja = () => {
    const item = itemToBaja.value;
    if (!item) return;

    const url = item.tipo === 'equipo'
        ? equipmentRoutes.update.url(item.id)
        : toolRoutes.update.url(item.id);

    const payload: any = {
        _method: 'PUT',
        solo_estado: true,
    };

    if (item.tipo === 'equipo') {
        payload.estado_equipo = 'Baja';
    } else {
        payload.estado_herramienta = 'Baja';
    }

    router.post(url, payload, {
        preserveScroll: true,
        onSuccess: () => {
            closeConfirmBaja(); // Cerramos el modal tras el éxito
        },
        onError: () => {
            alert("Error al procesar la baja");
        }
    });
};

// Estados para el Modal
const isConfirmingBaja = ref(false);
const itemToBaja = ref<Item | null>(null);

// Función para abrir el modal
const openConfirmBaja = (item: Item) => {
    itemToBaja.value = item;
    isConfirmingBaja.value = true;
};

// Función para cerrar el modal
const closeConfirmBaja = () => {
    isConfirmingBaja.value = false;
    itemToBaja.value = null;
};

// Estados del Modal
const viewInformacion = ref(false);
const itemInformacion = ref<any>(null);

// Función para abrir el modal y preparar los datos
const openViewInformacion = (item: any) => {
    // Aquí normalizamos los datos para que el modal los entienda fácilmente
    itemInformacion.value = {
        ...item,
        nombre_item: item.tipo === 'equipo' ? item.nombre_equipo : item.nombre_herramienta,
        ubicacion_item: item.tipo === 'equipo' ? item.ubicacion_equipo : item.ubicacion_herramienta,
        descripcion_item: item.tipo === 'equipo' ? item.descripcion_equipo : item.descripcion_herramienta,
        observacion_item: item.tipo === 'equipo' ? item.observacion_equipo : item.observacion_herramienta,
        // Agregamos el objeto específico para los templates v-if
        equipment: item.tipo === 'equipo' ? item : null,
        tool: item.tipo === 'herramienta' ? item : null,
    };
    viewInformacion.value = true;
};

const closeViewInformacion = () => {
    viewInformacion.value = false;
    itemInformacion.value = null;
};

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Si tienes lógica de mantenimientos, puedes computar el último aquí
const ultimoMantenimiento = computed(() => {
    if (itemInformacion.value?.equipment?.mantenimientos?.length > 0) {
        return itemInformacion.value.equipment.mantenimientos[0];
    }
    return null;
});

</script>

<template>
    <Head title="Inventario" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                title="Gestión de Inventario"
                description="Administre equipos y herramientas del taller"
            >
                <template #action>
                    <CreateActionButton
                        type="button" :href="activeTab === 'equipos' ? equipmentRoutes.create.url() : toolRoutes.create.url()"
                        :label="`Agregar ${activeTab === 'equipos' ? 'Equipo' : 'Herramienta'}`"
                    />
                </template>
            </PageHeader>

            <TabSelector
                :tabs="inventoryTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <div class="flex flex-col md:flex-row items-center gap-3 mb-4 w-full">
                <SearchInput v-model="searchQuery" placeholder="Buscar por nombre, marca o QR..." />
                <SelectFilter v-model="selectedStatus" label="Estados" :options="currentOptions" />
                <ClearFiltersButton @clear="() => { selectedStatus=''; searchQuery='' }" />
            </div>
            <InventoryTable
                :items="filteredItems"
                :activeTab="activeTab"
                :openAccessoryId="openAccessoryId"
                @view="openViewInformacion"
                @baja="openConfirmBaja"
                @toggleAccessories="toggleAccessories"
            />

            <!--<div class="space-y-4">
                <div v-if="filteredItems.length === 0" class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
                    <Package class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium">No se encontraron items con esos criterios.</p>
                </div>

                <div class="relative bg-white border border-neutral-200 rounded-xl shadow-sm overflow-x-auto">
                    <div v-if="(activeTab === 'equipos' || activeTab === 'herramientas') && filteredItems.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left min-w-max border-separate border-spacing-0">
                            <thead class="bg-neutral-200 border-b border-neutral-300 text-xs font-bold uppercase tracking-widest text-neutral-800 sticky top-0 z-10">
                                <tr>
                                    <th class="p-4">Item / Información</th>
                                    <th class="p-4">Estado</th>
                                    <th class="p-4">Ubicación</th>
                                    <th v-if="activeTab === 'equipos'" class="p-4">Accesorios</th>
                                    <th v-else class="p-4">Marca / Modelo</th>
                                    <th class="p-4 text-center sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-neutral-50/50 group transition-colors">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 rounded-lg border overflow-hidden bg-neutral-100 shrink-0">
                                                <img v-if="item.foto" :src="'/storage/' + item.foto" class="h-full w-full object-cover" />
                                                <Image v-else class="h-full w-full p-3 text-neutral-300" />
                                            </div>
                                            <div>
                                                <div class="font-bold text-black">{{ item.nombre_equipo || item.nombre_herramienta }}</div>
                                                <div class="text-[10px] font-mono text-neutral-400">{{ item.codigo_qr }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', statusColor(item.estado_equipo || item.estado_herramienta)]">
                                            {{ item.estado_equipo || item.estado_herramienta }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-neutral-600">
                                        <div class="flex items-center gap-1">
                                            <Rows3 class="w-3 h-3" /> {{ item.ubicacion_equipo || item.ubicacion_herramienta }}
                                        </div>
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <div v-if="activeTab === 'equipos'" class="relative inline-block text-left">
                                            <button v-if="item.accessories && item.accessories.length > 0"
                                                @click.stop="toggleAccessories(item.id)"
                                                class="flex items-center gap-2 px-3 py-1 bg-white border border-neutral-200 rounded-lg shadow-sm hover:bg-neutral-50 transition active:scale-95" >
                                                <List class="w-4 h-4 stroke-black"/>
                                                <span class="text-sm font-bold text-black">{{ item.accessories.length }}</span>
                                            </button>
                                            <span v-else class="text-sm text-neutral-400 italic">Sin accesorios</span>
                                            <div v-if="openAccessoryId === item.id"
                                                class="absolute right-0 z-100 mt-2 w-72 bg-white border border-neutral-200 rounded-xl shadow-2xl p-4 animate-in fade-in zoom-in duration-200" >
                                                <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                                    <div v-for="acc in item.accessories"
                                                        :key="acc.id"
                                                        class="flex items-center justify-between p-2 bg-neutral-50 border border-neutral-100 rounded-lg" >
                                                        <span class="text-xs font-bold text-neutral-700">{{ acc.nombre_accesorio }}</span>
                                                        <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', statusColor(acc.estado_accesorio)]">
                                                            {{ acc.estado_accesorio }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="text-sm text-neutral-600">
                                            {{ item.marca_modelo || '-' }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <Button
                                            @click="openViewInformacion(item)"
                                            class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-red-500"
                                            title="Información"
                                        >
                                            <Eye class="w-4.5 h-4.5 stroke-green-500"/>
                                        </Button>
                                        <Link :href="item.tipo === 'equipo' ? equipmentRoutes.edit.url(item.id) : toolRoutes.edit.url(item.id)">
                                            <button class="p-2 border rounded-lg hover:bg-blue-50 text-blue-600"><SquarePen class="w-4 h-4"/></button>
                                        </Link>
                                        <Button
                                            @click="openConfirmBaja(item)"
                                            class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 text-red-500"
                                            title="Dar de baja"
                                        >
                                            <Ban class="w-4 h-4 stroke-red-500"/>
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>-->

            <!--
            <div v-if="isConfirmingBaja" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm" @click="closeConfirmBaja"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-in fade-in zoom-in duration-200">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto bg-red-100 rounded-full mb-4">
                        <Ban class="w-8 h-8 text-red-600" />
                    </div>

                    <h3 class="text-xl font-black text-center text-neutral-900 uppercase tracking-tighter">¿Confirmar Baja?</h3>

                    <p class="mt-3 text-sm text-center text-neutral-500 leading-relaxed">
                        Estás a punto de dar de baja el ítem: <br>
                        <span class="font-bold text-red-600 text-base">
                            {{ itemToBaja?.nombre_equipo || itemToBaja?.nombre_herramienta }}
                        </span>
                    </p>

                    <div class="mt-8 flex gap-3">
                        <button
                            @click="closeConfirmBaja"
                            class="flex-1 px-4 py-3 border border-neutral-200 text-neutral-600 rounded-xl font-bold text-sm hover:bg-neutral-100 transition-colors"
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
            -->

            <ConfirmDialog
                :show="isConfirmingBaja"
                variant="danger"
                title="¿Confirmar Baja?"
                message="Estás a punto de dar de baja el siguiente ítem del sistema:"
                :item-name="itemToBaja?.nombre_equipo || itemToBaja?.nombre_herramienta"
                confirm-label="Sí, dar de baja"
                @close="closeConfirmBaja"
                @confirm="executeBaja"
            />

            <!--
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
                                        <h2 class="text-2xl font-black text-neutral-800 flex items-center gap-3">
                                            {{ itemInformacion?.nombre_item }}
                                        </h2>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="px-2 py-0.5 rounded-md bg-black text-white text-[10px] font-black uppercase tracking-widest">
                                            {{ itemInformacion?.equipment ? 'Equipo' : 'Herramienta' }}
                                        </span>
                                        <span :class="['px-3 py-1 rounded-full text-[10px] font-black uppercase border shadow-sm', statusColor(itemInformacion?.equipment?.estado_equipo || itemInformacion?.tool?.estado_herramienta)]">
                                            {{ itemInformacion?.equipment?.estado_equipo || itemInformacion?.tool?.estado_herramienta }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-4 py-3 border-y border-neutral-100 mt-4">
                                        <div class="flex items-center gap-2">
                                            <QrCode class="w-5 h-5 text-blue-600" />
                                            <div>
                                                <p class="text-[10px] text-neutral-700 uppercase font-black tracking-widest">Código QR</p>
                                                <p class="text-sm font-mono font-black text-neutral-800">{{ itemInformacion?.codigo_qr }}</p>
                                            </div>
                                        </div>
                                        <div class="w-px h-8 bg-neutral-100"></div>
                                        <div class="flex items-center gap-2">
                                            <Rows3 class="w-5 h-5 text-green-600" />
                                            <div>
                                                <p class="text-[10px] text-neutral-700 uppercase font-black tracking-widest">Ubicación</p>
                                                <p class="text-sm font-black text-neutral-800">{{ itemInformacion?.ubicacion_item }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <template v-if="itemInformacion?.equipment">
                                    <div class="space-y-4">
                                        <h4 class="text-sm font-black text-neutral-900 uppercase tracking-widest flex items-center gap-2">
                                            <FileText class="w-4 h-4" /> Ficha Técnica del Equipo
                                        </h4>
                                        <div class="grid grid-cols-1 gap-2">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-blue-50 rounded-lg"><Hash class="w-4 h-4 text-neutral-900"/></div>
                                                <div>
                                                    <p class="text-[12px] text-neutral-700 uppercase font-bold">Serie</p>
                                                    <p class="text-sm font-mono font-bold text-neutral-800">{{ itemInformacion.equipment.serie || 'N/A' }}</p>
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
                                                <div class="p-2 bg-green-50 rounded-lg"><BookText class="w-4 h-4 text-neutral-900" /></div>
                                                <div>
                                                    <p class="text-[12px] text-neutral-700 uppercase font-bold">Rubro</p>
                                                    <p class="text-sm font-bold text-neutral-800">{{ itemInformacion?.equipment?.rubro }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-green-50 rounded-lg"><CalendarDays class="w-4 h-4 text-neutral-900"/></div>
                                                <div>
                                                    <p class="text-[12px] text-neutral-700 uppercase font-bold">Fecha de Adquisición</p>
                                                    <p class="text-sm font-bold text-neutral-800">{{ formatDate(itemInformacion?.equipment?.fecha_adquisicion) }}</p>
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

                                    <div class="space-y-4">
                                        <h4 class="text-sm font-black text-neutral-900 uppercase tracking-widest flex items-center gap-2">
                                            <List class="w-4 h-4" /> Accesorios Incluidos
                                        </h4>
                                        <div v-if="itemInformacion.equipment.accessories?.length" class="grid gap-2">
                                            <div v-for="acc in itemInformacion.equipment.accessories" :key="acc.id" class="flex items-center justify-between p-2.5 bg-white border border-neutral-100 rounded-xl shadow-sm">
                                                <span class="text-xs font-bold text-neutral-700">{{ acc.nombre_accesorio }}</span>
                                                <span :class="['px-2 py-0.5 rounded-lg text-[9px] font-black uppercase border', statusColorA(acc.estado_accesorio)]">
                                                    {{ acc.estado_accesorio }}
                                                </span>
                                            </div>
                                        </div>
                                        <div v-else class="p-8 bg-neutral-50 rounded-2xl border-2 border-dashed border-neutral-200 text-center">
                                            <p class="text-xs text-neutral-400 font-bold uppercase">Sin accesorios registrados</p>
                                        </div>

                                        <div v-if="ultimoMantenimiento" class="p-4 bg-blue-50 border border-blue-200 rounded-2xl">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-[12px] text-blue-600 uppercase font-bold">Último mantenimiento</span>
                                                <span class="text-sm font-bold text-neutral-800">{{ formatDate(ultimoMantenimiento.fecha_mantenimiento) }}</span>
                                            </div>
                                            <p class="text-xs text-neutral-600 bg-white/50 p-2 rounded-lg border border-blue-100 italic">
                                                "{{ ultimoMantenimiento.actividad }}"
                                            </p>
                                        </div>
                                        <div v-else class="p-4 border-2 border-dashed border-neutral-100 rounded-2xl text-center">
                                            <p class="text-xs text-neutral-400 font-bold uppercase">No hay registros previos</p>
                                        </div>
                                    </div>
                                </template>

                                <template v-else-if="itemInformacion?.tool">
                                    <div class="space-y-4">
                                        <h4 class="text-sm font-black text-neutral-900 uppercase tracking-widest flex items-center gap-2">
                                            <Wrench class="w-4 h-4" /> Detalle Herramienta
                                        </h4>
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-blue-50 rounded-lg"><Package class="w-4 h-4 text-neutral-900"/></div>
                                            <div>
                                                <p class="text-[12px] text-neutral-700 uppercase font-bold">Marca / Modelo Específico</p>
                                                <p class="text-sm font-bold text-neutral-800">{{ itemInformacion.tool.marca_modelo || 'Sin especificar' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-blue-50 rounded-lg"><Layers class="w-4 h-4 text-neutral-900"/></div>
                                            <div>
                                                <p class="text-[12px] text-neutral-700 uppercase font-bold">Cantidad de piezas</p>
                                                <p class="text-sm font-bold text-neutral-800">{{ itemInformacion.tool.cantidad_piezas || 'Sin especificar' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-if="itemInformacion?.descripcion_item" class="mt-3 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                                    <p class="text-[14px] text-neutral-700 uppercase font-bold flex items-center gap-2 mb-1">
                                        <AlignLeft class="w-4 h-4 stroke-indigo-900" />
                                        <span>Descripción</span>
                                    </p>
                                    <p class="text-sm leading-relaxed text-neutral-800 italic font-medium">"{{ itemInformacion?.descripcion_item }}"</p>
                                </div>
                                <div v-if="itemInformacion?.observacion_item" class="mt-3 p-4 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <p class="text-[14px] text-amber-600 font-black uppercase mb-2 flex items-center gap-2">
                                        <Eye class="w-3 h-3" /> Observaciones
                                    </p>
                                    <p class="text-sm text-amber-900 italic font-medium">"{{ itemInformacion.observacion_item }}"</p>
                                </div>
                            </div>
                            <div class="p-6 bg-neutral-50/80 border-t border-neutral-100 shrink-0 flex gap-3 no-print">
                                <button @click="closeViewInformacion" class="flex-1 py-3 bg-white border border-neutral-200 text-neutral-600 rounded-xl font-bold text-sm hover:bg-neutral-100 transition-all">
                                    Cerrar
                                </button>
                                <button @click="" class="flex-1 py-3 bg-black text-white rounded-xl font-bold text-sm hover:bg-neutral-800 transition-all flex items-center justify-center gap-2 shadow-lg active:scale-95">
                                    <FileText class="w-4 h-4" /> Generar Ficha PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->

            <InventoryDetailModal
                :show="viewInformacion"
                :item="itemInformacion"
                @close="closeViewInformacion"
                @generate-pdf=""
            />
        </div>
    </AppLayout>
</template>
