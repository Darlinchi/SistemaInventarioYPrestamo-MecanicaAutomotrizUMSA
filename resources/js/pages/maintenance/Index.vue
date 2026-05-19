<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import maintenancesRoutes from '@/routes/maintenances';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import DateFilter from '@/components/shared/DateFilter.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import MaintenanceTable from '@/components/MaintenanceTable.vue';
import MaintenanceActiveCard from '@/components/MaintenanceActiveCard.vue';
import FinishMaintenanceModal from '@/components/FinishMaintenanceModal.vue';
import MaintenanceDetailModal from '@/components/MaintenanceDetailModal.vue';
import { Package } from 'lucide-vue-next';

interface Maintenance {
    id: number;
    fecha_mantenimiento: string;
    fecha_proximo_mantenimiento?: string;
    fecha_retorno?: string;
    fecha_retorno_estimado?: string ;
    estado_mantenimiento: string;
    tipo_mantenimiento: string;
    hora_inicio: string;
    hora_fin?: string;
    hora_fin_estimado?: string;
    actividad?: string;
    equipment: any;
    estado_final_equipo: string;
    company?: any;   // Añade el signo ? para que sea opcional
    companies?: any[];
}

const props = defineProps<{
    maintenances: Array<any>; // Recibidos del controlador
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mantenimientos',
        href: maintenancesRoutes.index.url(),
    },
];

// --- ESTADO Y BUSQUEDA ---
const activeTab = ref<'proceso' | 'completados'>('proceso');
const searchQuery = ref('');
const selectedCompany = ref('');
const filterDate = ref(''); // Para fecha exacta (YYYY-MM-DD)
const filterMonth = ref('');

// --- CONTADORES ---
const countEnProceso = computed(() =>
    props.maintenances.filter(m => m.estado_mantenimiento === 'En Proceso').length
);
const countCompletado = computed(() =>
    props.maintenances.filter(m => m.estado_mantenimiento === 'Completado').length
);
// En tu <script setup> de Index.vue
const maintenancesTabs = computed(() => [
    { id: 'proceso', label: 'En Proceso', count: countEnProceso.value, icon: 'ClipboardPen' },
    { id: 'completados', label: 'Completados', count: countCompletado.value, icon: 'ClipboardList' }
]);

// Meses para el filtro por meses
const months = [
    { id: 1, name: 'Enero' },
    { id: 2, name: 'Febrero' },
    { id: 3, name: 'Marzo' },
    { id: 4, name: 'Abril' },
    { id: 5, name: 'Mayo' },
    { id: 6, name: 'Junio' },
    { id: 7, name: 'Julio' },
    { id: 8, name: 'Agosto' },
    { id: 9, name: 'Septiembre' },
    { id: 10, name: 'Octubre' },
    { id: 11, name: 'Noviembre' },
    { id: 12, name: 'Diciembre' }
];

// 3. Filtrado unificado por Pestaña y Buscador
const filteredMaintenances = computed(() => {
    // Mapeo de tus términos simples a los de la Base de Datos
    const estadoMapa = {
        proceso: 'En Proceso',
        completados: 'Completado'
    };

    const estadoBusqueda = estadoMapa[activeTab.value];
    // 1. Filtro por pestaña activa
    let filtered = (props.maintenances || []).filter(maint =>
        maint.estado_mantenimiento === estadoBusqueda
    );

    // 2. Filtro por buscador (Empresa, Equipo, Serie o QR)
    if (searchQuery.value.trim() !== '') {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(maint => {
            const nombreEmpresa = maint.companies[0]?.nombre_empresa?.toLowerCase() || '';
            const nombreEquipo = maint.equipment.nombre_equipo?.toLowerCase() || '';
            const serieEquipo = maint.equipment.serie?.toLowerCase() || '';
            const codigoQr = maint.equipment.codigo_qr?.toLowerCase() || '';

            return nombreEmpresa.includes(query) ||
                   nombreEquipo.includes(query) ||
                   serieEquipo.includes(query) ||
                   codigoQr.includes(query);
        });
    }

    // Filtro por empresa
    if (selectedCompany.value !== '') {
        filtered = filtered.filter(maint => maint.companies[0]?.nombre_empresa === selectedCompany.value);
    }
    // Filtro por fecha
    if (filterDate.value !== '') {
        // Importante: Asegúrate de que maint.fecha_mantenimiento venga como 'YYYY-MM-DD' de la DB
        filtered = filtered.filter(maint => maint.fecha_mantenimiento === filterDate.value);
    }
    if (filterMonth.value !== '') {
        filtered = filtered.filter(maint => {
            const partes = maint.fecha_mantenimiento.split('-');
            const mesMantenimiento = partes[1];
            return parseInt(mesMantenimiento).toString() === filterMonth.value;
        });
    }

    return filtered;
});

const uniqueCompanies = computed(() => {
    const companiesMap = new Map();
    (props.maintenances || []).forEach(maint => {
        // Accedemos a la primera empresa de la relación (o recorre si son varias)
        const company = maint.companies[0];
        if (company) {
            // Usamos el ID como llave para que no se repitan
            companiesMap.set(company.id, company.nombre_empresa);
        }
    });
    // Retornamos array de objetos { id, nombre }
    return Array.from(companiesMap.entries()).map(([id, nombre]) => ({ id, nombre }));
});

// Actualizar hora del modal en tiempo real
let timer: any;
onMounted(() => {
    timer = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }, 10000);
});
onUnmounted(() => clearInterval(timer));

const isReturnModalOpen = ref(false);
const selectedMaint = ref<any>(null);
// Esto es solo para mostrar en el modal (puedes dejarlo como está o ponerlo en 24h)
const currentTime = ref(new Date().toLocaleTimeString('es-BO', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false // Cambiar a false ayuda a evitar confusiones
}));
// --- FORMULARIO DE FINALIZACIÓN ---
const returnForm = useForm({
    fecha_proximo_mantenimiento: new Date().toISOString().split('T')[0],
    fecha_retorno: new Date().toISOString().split('T')[0],
    hora_fin: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }),
    estado_equipo: 'Reparado',
    observacion: '',
});

// Observador para calcular automáticamente 1 año después
watch(() => returnForm.fecha_retorno, (newDate) => {
    if (newDate) {
        const date = new Date(newDate);
        // Sumamos un año
        date.setFullYear(date.getFullYear() + 1);

        // Formateamos a YYYY-MM-DD para el input date
        const nextYear = date.toISOString().split('T')[0];

        returnForm.fecha_proximo_mantenimiento = nextYear;
    }
});

// Funcion para abrir el modal (preguntar si le gustaria si un accesorio esta mal el equipo completo marcarse como dañado)
// --- FUNCIONES DEL MODAL ---
const openReturnModal = (maint: any) => {
    selectedMaint.value = maint;
    const now = new Date();
    const today = now.toISOString().split('T')[0];

    // Calculamos el año siguiente para el valor inicial
    const nextYearDate = new Date();
    nextYearDate.setFullYear(nextYearDate.getFullYear() + 1);
    const nextYear = nextYearDate.toISOString().split('T')[0];

    returnForm.fecha_retorno = today;
    returnForm.fecha_proximo_mantenimiento = nextYear;
    returnForm.hora_fin = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    returnForm.estado_equipo = 'Reparado';
    returnForm.observacion = '';
    isReturnModalOpen.value = true;
};

const processReturn = () => {
    // Generar la hora exacta
    const ahora = new Date();
    const horaFormateada = ahora.toLocaleTimeString('es-BO', {
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    returnForm.hora_fin = horaFormateada;

    returnForm.put(maintenancesRoutes.update.url(selectedMaint.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isReturnModalOpen.value = false;
            selectedMaint.value = null;
            returnForm.reset();
        },
        onError: (errors) => {
            console.error("Errores de validación del servidor:", errors);
        }
    });
};

// Para visualizar toda la informacion y hacer reporte
const viewInformacion = ref(false);
const maintenanceInformacion = ref<Maintenance | null>(null);

// Abre el modal y guarda el item seleccionado
const openViewInformacion = (maintenance: Maintenance) => {
    maintenanceInformacion.value = maintenance;
    viewInformacion.value = true;
};

// Cierra el modal y limpia el estado
const closeViewInformacion = () => {
    viewInformacion.value = false;
    maintenanceInformacion.value = null;
};

const handleGenerateReport = (id: number) => {
    window.open(`/dashboard/maintenances/${id}/report`, '_blank');
};

</script>

<template>
    <Head title="Mantenimientos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <PageHeader
                description="Gestione el mantenimiento de equipos"
            >
                <template #action>
                    <CreateActionButton
                        type="button" :href="maintenancesRoutes.create.url()"
                        :label="`Registrar Actividad de Mantenimiento`"
                    />
                </template>
            </PageHeader>

            <TabSelector
                :tabs="maintenancesTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                <SearchInput v-model="searchQuery" placeholder="Buscar por empresa, equipo o serie..." class="md:w-[360px]" />
                <SelectFilter v-model="selectedCompany" label="Empresas" :options="uniqueCompanies" option-value="sigla" option-label="nombre" icon="Building2" class="md:w-[210px]" />
                <SelectFilter v-model="filterMonth" label="Meses" :options="months" option-value="id" option-label="name" icon="CalendarDays" class="md:w-[180px]" />
                <DateFilter v-model="filterDate" label="Fecha específica" class="md:w-[180px]" />
                <ClearFiltersButton @clear="() => { filterDate=''; filterMonth=''; selectedCompany=''; searchQuery='' }" />
            </div>

            <div class="space-y-4">
                <div v-if="filteredMaintenances.length === 0" class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
                    <Package class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium">No se encontraron registros en esta sección.</p>
                </div>

                <div v-if="activeTab === 'proceso' && filteredMaintenances.length > 0" class="space-y-4">
                    <MaintenanceActiveCard
                        v-for="maint in filteredMaintenances"
                        :key="maint.id"
                        :maint="maint"
                        @complete="openReturnModal"
                    />
                </div>

                <!--HOSTORIAL DE MANTENIMIENTOS COMPLETADOS-->

                <div v-if="activeTab === 'completados' && filteredMaintenances.length > 0">
                    <MaintenanceTable
                        :maintenances="filteredMaintenances"
                        @view="openViewInformacion"
                        @generateReport="handleGenerateReport"
                    />
                </div>
            </div>
        </div>

        <FinishMaintenanceModal
            :show="isReturnModalOpen"
            :maint="selectedMaint"
            :form="returnForm"
            @close="isReturnModalOpen = false"
            @confirm="processReturn"
        />

        <MaintenanceDetailModal
            :show="viewInformacion"
            :maint="maintenanceInformacion"
            @close="closeViewInformacion"
        />

    </AppLayout>
</template>
