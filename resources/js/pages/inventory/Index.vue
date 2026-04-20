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

const page = usePage();
const can = (permission: string) =>
    (page.props.auth.user?.permissions ?? []).includes(permission);

// --- NOTIFICACIONES FLASH ---
const flashSuccess = computed(() => (page.props.flash as any)?.success);

// --- LÓGICA DE TABLA ---
const urlParams = new URLSearchParams(window.location.search);
const initialTab = urlParams.get('tab') as 'equipos' | 'herramientas' | 'bajas';
const activeTab = ref<'equipos' | 'herramientas' | 'bajas'>(initialTab || 'equipos');
const searchQuery = ref('');
const selectedStatus = ref('');
const selectedLocation = ref('');
const openAccessoryId = ref<number | null>(null);

// Helper para saber si un item está de baja
const isBaja = (item: Item) => {
    return item.tipo === 'equipo'
        ? item.estado_equipo === 'Baja'
        : item.estado_herramienta === 'Baja';
};

// Limpiamos el filtro de estado cada vez que cambiamos de pestaña (Opcional, pero recomendado)
watch(activeTab, () => {
    selectedStatus.value = '';
});
// Dependiendo de la pestaña, pasamos los estados correspondientes
const currentOptions = computed(() => {
    if (activeTab.value === 'bajas') return ['Baja']; // Solo opción de baja
    return activeTab.value === 'equipos'
        ? props.estados_equipo.filter(e => e !== 'Baja') // Quitamos 'Baja' de la pestaña normal
        : props.estados_herramienta.filter(e => e !== 'Baja');
});
// Extraemos ubicaciones únicas DEPENDIENDO de la pestaña activa
const locationOptions = computed(() => {
    const itemsFiltradosPorPestaña = props.items.filter(item => {
        if (activeTab.value === 'bajas') return isBaja(item);
        if (activeTab.value === 'equipos') return item.tipo === 'equipo' && !isBaja(item);
        return item.tipo === 'herramienta' && !isBaja(item);
    });
    const locations = itemsFiltradosPorPestaña.map(item =>
        item.tipo === 'equipo' ? item.ubicacion_equipo : item.ubicacion_herramienta
    );
    const unique = [...new Set(locations.filter(l => l && l.trim() !== ''))].sort();

    return unique;
});

// Contadores basados en el campo 'tipo'
const countEquipos = computed(() => props.items.filter(i => i.tipo === 'equipo').length);
const countHerramientas = computed(() => props.items.filter(i => i.tipo === 'herramienta').length);
const countBajas = computed(() => props.items.filter(i => isBaja(i)).length);

// En tu <script setup> de Index.vue
const inventoryTabs = computed(() => [
    { id: 'equipos', label: 'Equipos', count: countEquipos.value, icon: 'Package' },
    { id: 'herramientas', label: 'Herramientas', count: countHerramientas.value, icon: 'Wrench' },
    { id: 'bajas', label: 'Bajas', count: countBajas.value, icon: 'Ban' }
]);

// --- FILTRADO INTELIGENTE ---
const filteredItems = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    const currentStatus = selectedStatus.value;
    const currentLocation = selectedLocation.value;

    return props.items.filter(item => {
        // 1. Filtro por Pestaña
        if (activeTab.value === 'bajas') {
            if (!isBaja(item)) return false;
        } else if (activeTab.value === 'equipos') {
            if (item.tipo !== 'equipo' || isBaja(item)) return false;
        } else if (activeTab.value === 'herramientas') {
            if (item.tipo !== 'herramienta' || isBaja(item)) return false;
        }

        // 2. Filtro por Estado (Simplificado)
        if (currentStatus) {
            const estadoItem = item.tipo === 'equipo' ? item.estado_equipo : item.estado_herramienta;
            if (estadoItem !== currentStatus) return false;
        }

        // 3. Filtro por Ubicación
        if (currentLocation) {
            const ubicacionItem = item.tipo === 'equipo' ? item.ubicacion_equipo : item.ubicacion_herramienta;
            if (ubicacionItem !== currentLocation) return false;
        }

        // 4. Buscador
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
const executeBaja = (motivo: string) => {
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
        payload.observacion_equipo = motivo;
    } else {
        payload.estado_herramienta = 'Baja';
        payload.observacion_herramienta = motivo;
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

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const tabFromUrl = params.get('tab');

    if (tabFromUrl && (tabFromUrl === 'equipos' || tabFromUrl === 'herramientas' || tabFromUrl === 'bajas')) {
        activeTab.value = tabFromUrl as any;
    }
});

</script>

<template>
    <Head title="Inventario" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                description="Administre equipos y herramientas del taller"
            >
                <template #action>
                    <CreateActionButton
                        v-if="activeTab !== 'bajas' && can(activeTab === 'equipos' ? 'equipos.crear' : 'herramientas.crear')"
                        type="button"
                        :href="activeTab === 'equipos'
                            ? equipmentRoutes.create.url() + '?tab=equipos'
                            : toolRoutes.create.url() + '?tab=herramientas'"
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
                <SelectFilter v-model="selectedLocation" label="Ubicaciones" :options="locationOptions" icon="Rows3" />
                <ClearFiltersButton @clear="() => { selectedStatus=''; selectedLocation=''; searchQuery='' }" />
            </div>

            <InventoryTable
                :items="filteredItems"
                :activeTab="activeTab"
                :openAccessoryId="openAccessoryId"
                :can-edit="can('equipos.editar') || can('herramientas.editar')"
                :can-baja="can('equipos.editar') || can('herramientas.editar')"
                @view="openViewInformacion"
                @baja="openConfirmBaja"
                @toggleAccessories="toggleAccessories"
            />

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

            <InventoryDetailModal
                :show="viewInformacion"
                :item="itemInformacion"
                @close="closeViewInformacion"
                @generate-pdf=""
            />

        </div>
    </AppLayout>
</template>
