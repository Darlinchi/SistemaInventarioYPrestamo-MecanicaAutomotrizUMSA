<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import itemRoutes from '@/routes/items';
import equipmentRoutes from '@/routes/equipments'; // Asegúrate que el nombre coincida con tus archivos de rutas
import toolRoutes from '@/routes/tools';
import loanRoutes from '@/routes/loans';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import DashboardStat from '@/components/DashboardStat.vue';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import RecentEquipmentCard from '@/components/RecentEquipmentCard.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    Package, User,
    ClipboardCheck,
    Wrench,
    LayoutDashboard,
    History,
    ChevronRight,
    Search
} from 'lucide-vue-next';

// Recibimos los datos del controlador
const props = defineProps<{
    stats: {
        equipos_total: number;
        prestamos_activos: number;
        mantenimientos_pendientes: number;
    };
    recentLoans: any[];
    recentEquipments: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Panel Principal',
        href: dashboard().url,
    },
];

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6 bg-neutral-50/40 min-h-screen">

            <PageHeader
                title="Resumen del Sistema"
                description="Gestión de inventarios y control de préstamos"
            >
                <template #action>
                    <CreateActionButton
                        :href="loanRoutes.create.url()"
                        :label="`Registrar Préstamo`"
                    />
                </template>
            </PageHeader>

            <!--
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Resumen del Sistema</h1>
                    <p class="text-neutral-500 font-medium">Gestión de inventarios y control de préstamos</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="equipmentRoutes.create.url()" class="bg-black text-white px-5 py-2.5 rounded-2xl font-bold text-sm hover:bg-neutral-800 transition shadow-lg shadow-black/10 flex items-center gap-2">
                        <ClipboardCheck class="w-4 h-4" /> Nuevo Préstamo
                    </Link>
                </div>
            </div>-->

            <div class="grid gap-6 md:grid-cols-3">
                <DashboardStat
                    title="Equipos en Inventario"
                    :value="stats.equipos_total"
                    :icon="Package"
                    colorClass="text-[#1a3a5a] group-hover:bg-[#1a3a5a] group-hover:text-white"
                    description="Total de activos registrados"
                />

                <DashboardStat
                    title="Préstamos Activos"
                    :value="stats.prestamos_activos"
                    :icon="ClipboardCheck"
                    colorClass="text-green-600 group-hover:bg-green-600 group-hover:text-white"
                    description="Equipos fuera del taller"
                />

                <DashboardStat
                    title="Con Problemas"
                    :value="stats.mantenimientos_pendientes"
                    :icon="Wrench"
                    colorClass="text-[#d90000] group-hover:bg-[#d90000] group-hover:text-white"
                    description="Requieren mantenimiento"
                />
            </div>

            <Card class="rounded-[2.5rem] border-none shadow-sm overflow-hidden bg-white">
                <CardHeader class="p-6 border-b border-neutral-100 flex flex-row items-center justify-between space-y-0">
                    <CardTitle class="text-lg font-black uppercase tracking-tight flex items-center gap-2">
                        <History class="w-5 h-5 text-[#1a3a5a]" /> Últimos Préstamos
                    </CardTitle>
                    <Link
                        :href="loanRoutes.index.url()"
                        class="text-xs font-black text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition uppercase tracking-wider"
                    >
                        Ver historial completo
                    </Link>
                </CardHeader>

                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-neutral-50/50 text-[10px] font-black text-neutral-400 uppercase tracking-[0.2em]">
                                    <th class="p-4 pl-8">Solicitante</th>
                                    <th class="p-4">Materia / Unidad</th>
                                    <th class="p-4">Fecha Salida</th>
                                    <th class="p-4 text-center">Estado</th>
                                    <th class="p-4 pr-8 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr v-for="loan in recentLoans" :key="loan.id" class="group hover:bg-neutral-50/50 transition-colors">
                                    <td class="p-4 pl-8">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-neutral-100 flex items-center justify-center text-[10px] font-bold text-neutral-500 uppercase">
                                                <template v-if="loan.borrower?.nombresP">
                                                    {{ loan.borrower.apellidosP[0] }}{{ loan.borrower.nombresP[0] }}
                                                </template>
                                                <User v-else class="w-4 h-4 text-neutral-300" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-neutral-800">
                                                    {{ loan.borrower?.apellidosP }} {{ loan.borrower?.nombresP }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-sm text-neutral-600 font-medium">
                                        {{ loan.subject?.nombre_materia || 'Uso General' }}
                                    </td>
                                    <td class="p-4 text-sm text-neutral-600">
                                        {{ loan.fecha_salida }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span :class="[
                                            'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border',
                                            loan.estado_prestamo === 'Activo'
                                                ? 'bg-green-50 text-green-700 border-green-100'
                                                : 'bg-neutral-50 text-neutral-500 border-neutral-100'
                                        ]">
                                            {{ loan.estado_prestamo }}
                                        </span>
                                    </td>
                                    <td class="p-4 pr-8 text-right">
                                        <Link :href="loanRoutes.index.url()" class="text-neutral-300 group-hover:text-blue-600 transition-colors">
                                            <ChevronRight class="w-5 h-5 inline" />
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <div class="flex flex-col gap-6">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-lg font-black text-neutral-800 uppercase tracking-tight flex items-center gap-2">
                        <Package class="w-5 h-5 text-[#1a3a5a]" />
                        Equipos Agregados Recientemente
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <RecentEquipmentCard
                        v-for="equipo in recentEquipments"
                        :key="equipo.id"
                        :equipo="equipo"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
