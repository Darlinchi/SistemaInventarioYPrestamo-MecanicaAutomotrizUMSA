<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import reportRoutes from '@/routes/reports';
import StatCard from '@/components/shared/StatCard.vue';
import PageHeader from '@/components/PageHeader.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import InventoryReport from './partials/InventoryReport.vue';
import HistoryReport from './partials/HistoryReport.vue';
import IssuesReport from './partials/IssuesReport.vue';
import {
    Package, History, AlertTriangle, FileDown,
    Search, Filter, Download
} from 'lucide-vue-next';

const props = defineProps<{
    totalPrestamos: number;
    activos: number;
    devueltos: number;
    items: Array<any>;
    history: Array<any>;
    issues: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reportes',
        href: reportRoutes.index.url(),
    },
];

// Cálculo dinámico de la tasa
const tasaDevolucion = computed(() => {
    if (props.totalPrestamos === 0) return '0.0%';
    const calculo = (props.devueltos / props.totalPrestamos) * 100;
    return calculo.toFixed(1) + '%';
});

const activeTab = ref('inventario');

const reportTabs = computed(() => [
    { id: 'inventario', label: 'Inventario', icon: Package, count: props.items.length },
    { id: 'historial', label: 'Historial', icon: History, count: props.history.length },
    { id: 'problemas', label: 'Equipos con Problemas', icon: AlertTriangle, count: props.issues.length },
]);

</script>

<template>
    <Head title="Reportes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <PageHeader
                title="Reportes y Consultas"
                description="Genere reportes detallados y analice las estadísticas del sistema"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <StatCard
                    title="Total Préstamos"
                    :value="totalPrestamos"
                    variant="neutral"
                />
                <StatCard
                    title="Activos"
                    :value="activos"
                    variant="blue"
                />
                <StatCard
                    title="Devueltos"
                    :value="devueltos"
                    variant="green"
                />
                <StatCard
                    title="Tasa Devolución"
                    :value="tasaDevolucion"
                    variant="neutral"
                />
            </div>

            <div class="max-w-7xl mx-auto p-6 space-y-6">
                <TabSelector
                    :tabs="reportTabs"
                    v-model:activeTab="activeTab"
                />

                <div class="bg-white rounded-[2.5rem] border border-neutral-200 shadow-sm overflow-hidden min-h-[600px] flex flex-col">
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="opacity-0 translate-y-4"
                        enter-to-class="opacity-100 translate-y-0"
                        mode="out-in"
                    >
                        <div :key="activeTab" class="p-8 flex-1">
                            <InventoryReport v-if="activeTab === 'inventario'" :items="items" />
                            <HistoryReport v-if="activeTab === 'historial'" :history="history" />
                            <IssuesReport v-if="activeTab === 'problemas'" :issues="issues" />
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
