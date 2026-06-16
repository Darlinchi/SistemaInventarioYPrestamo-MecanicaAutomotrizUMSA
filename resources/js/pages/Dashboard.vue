<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { ref } from 'vue';
import DashboardEquipmentModal from '@/components/DashboardEquipmentModal.vue';
import itemRoutes from '@/routes/items';
import equipmentRoutes from '@/routes/equipments';
import toolRoutes from '@/routes/tools';
import loanRoutes from '@/routes/loans';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import StatCard from '@/components/shared/StatCard.vue';
import PageHeader from '@/components/PageHeader.vue';
import MaintenanceAlerts from '@/components/MaintenanceAlerts.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import RecentEquipmentCard from '@/components/RecentEquipmentCard.vue';
import ReturnLoanModal from '@/components/ReturnLoanModal.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Package, User, ClipboardCheck, Wrench, Edit, CheckCircle, History,
    ChevronRight, Search
} from 'lucide-vue-next';

// Recibimos los datos del controlador
const props = defineProps<{
    stats: {
        equipos_total: number;
        prestamos_activos: number;
        equipos_con_problemas: number;
    };
    recentLoans: any[];
    recentEquipments: any[];
    auth_user: { id: number; name: string; username: string };
    issues: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Panel Principal',
        href: dashboard().url,
    },
];

const page = usePage();
const can = (permission: string) =>
    (page.props.auth.user?.permissions ?? []).includes(permission);

// 3. Variables para el estado del modal
const isModalOpen = ref(false);
const selectedEquipo = ref(null);

// 4. Función que recibe los datos de la tarjeta
const handleVerDetalle = (equipo: any) => {
    selectedEquipo.value = equipo;
    isModalOpen.value = true;
};

// --- LÓGICA DE DEVOLUCIÓN ---
const isReturnModalOpen = ref(false);
const selectedLoan = ref<any>(null);

const returnForm = useForm({
    items: [] as any[],
    observacion: '',
    fecha_retorno: new Date().toISOString().split('T')[0], // Por defecto hoy
    hora_fin: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }),
});

const openReturnModal = (loan: any) => {
    if (!loan) return;
    selectedLoan.value = loan;

    // Usamos el accessor polimórfico que definiste en el modelo Loan.php
    const itemsToProcess = loan.all_items || [];

    // CORRECCIÓN: Usar itemsToProcess.map
    returnForm.items = itemsToProcess.map((i: any) => ({
        id: i.id,
        nombre_mostrar: i.nombre_mostrar,
        foto: i.foto_equipo ?? i.foto_herramienta ?? i.foto ?? null,
        // El 'type' debe ser exacto para el controlador
        type: i.es_equipo ? 'App\\Models\\Equipment' : 'App\\Models\\Tool',
        es_equipo: i.es_equipo,
        estado_devolucion: 'Disponible',
        accessories: (i.accessories || []).map((acc: any) => ({
            id: acc.id,
            nombre_accesorio: acc.nombre_accesorio,
            foto_accesorio: acc.foto_accesorio,
            estado_accesorio: 'Bueno'
        }))
    }));

    const now = new Date();
    returnForm.fecha_retorno = now.toISOString().split('T')[0];
    returnForm.hora_fin = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    isReturnModalOpen.value = true;
};

const processReturn = () => {
    returnForm.post(loanRoutes.return.url(selectedLoan.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            isReturnModalOpen.value = false;
            selectedLoan.value = null;
            returnForm.reset();
        }
    });
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6 bg-neutral-50/40 min-h-screen">

            <PageHeader
                description="Gestión de inventarios y control de préstamos"
            >
                <template #action>
                    <CreateActionButton
                        v-if="can('prestamos.crear')"
                        :href="loanRoutes.create.url()"
                        :label="`Registrar Préstamo`"
                    />
                </template>
            </PageHeader>

            <div class="grid gap-6 md:grid-cols-3">
                <StatCard
                    title="Equipos y Herramientas en Inventario"
                    :value="stats.equipos_total"
                    :icon="Package"
                    colorClass="text-[#1a3a5a] group-hover:bg-[#1a3a5a] group-hover:text-white"
                    description="Total de activos registrados"
                />
                <StatCard
                    title="Préstamos Activos"
                    :value="stats.prestamos_activos"
                    :icon="ClipboardCheck"
                    colorClass="text-green-600 group-hover:bg-green-600 group-hover:text-white"
                    description="Equipos fuera del taller"
                />
                <StatCard
                    title="Estado Crítico"
                    :value="stats.equipos_con_problemas"
                    :icon="Wrench"
                    colorClass="text-[#d90000] group-hover:bg-[#d90000] group-hover:text-white"
                    description="Dañados, extraviados o en taller"
                />
            </div>

            <MaintenanceAlerts :issues="issues" />

            <Card class="rounded-[2.5rem] border-none shadow-sm overflow-hidden bg-white">
                <CardHeader class="p-6 border-b border-neutral-100 flex flex-row items-center justify-between space-y-0">
                    <CardTitle class="text-lg font-black uppercase tracking-tight flex items-center gap-2">
                        <History class="w-5 h-5 text-[#1a3a5a]" />
                        Préstamos Vigentes en Taller
                    </CardTitle>
                    <Link
                        :href="loanRoutes.index.url()"
                        class="text-xs font-black text-[#1a3a5a] hover:bg-blue-50 px-3 py-1.5 rounded-full transition uppercase tracking-wider"
                    >
                        Ver historial completo
                    </Link>
                </CardHeader>

                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-neutral-50/50 text-[11px] font-black text-[#1a3a5a] uppercase tracking-[0.2em]">
                                    <th class="p-4 pl-8">Responsable</th>
                                    <th class="p-4">Materia</th>
                                    <th class="p-4">Fecha Salida</th>
                                    <th class="p-4 text-center">Estado</th>
                                    <th class="p-4 pr-8 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100">
                                <tr v-for="loan in recentLoans" :key="loan.id" class="group hover:bg-neutral-50/50 transition-colors">
                                    <td class="p-4 pl-8">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-[#1a3a5a] flex items-center justify-center text-[10px] font-bold text-white uppercase">
                                                <template v-if="loan.borrower?.nombres">
                                                    {{ loan.borrower.apellidoPaterno[0] }}{{ loan.borrower.nombres[0] }}
                                                </template>
                                                <User v-else class="w-4 h-4 text-neutral-300" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-neutral-800">
                                                    {{ loan.borrower?.teacher?.titulo }} {{ loan.borrower?.apellidoPaterno }} {{ loan.borrower?.apellidoMaterno }} {{ loan.borrower?.nombres }}
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
                                        <StatusBadge :status="loan.estado_prestamo" class="text-[11px]" />
                                    </td>
                                    <td class="p-4 pr-8 text-right flex items-center justify-end gap-3">
                                        <Link :href="loanRoutes.index.url()" class="text-neutral-300 hover:text-[#1a3a5a] transition-colors">
                                            <ChevronRight class="w-5 h-5" />
                                        </Link>

                                        <Link
                                            v-if="can('prestamos.editar')"
                                            :href="`/dashboard/loans/${loan.id}/edit`"
                                            class="text-neutral-300 hover:text-blue-600 transition-colors"
                                            title="Editar Préstamo"
                                        >
                                            <Edit class="w-5 h-5" />
                                        </Link>

                                        <button
                                            v-if="can('prestamos.devolver') && loan.estado_prestamo === 'Activo'"
                                            @click="openReturnModal(loan)"
                                            class="text-neutral-300 hover:text-green-600 transition-colors"
                                            title="Registrar Devolución"
                                        >
                                            <CheckCircle class="w-5 h-5" />
                                        </button>
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
                        @ver-detalle="handleVerDetalle"
                    />
                </div>
                <DashboardEquipmentModal
                    :show="isModalOpen"
                    :equipo="selectedEquipo"
                    @close="isModalOpen = false"
                />
                <ReturnLoanModal
                    v-if="selectedLoan"
                    :show="isReturnModalOpen"
                    :loan="selectedLoan"
                    :form="returnForm"
                    :auth-user="auth_user"
                    @close="isReturnModalOpen = false"
                    @confirm="processReturn"
                />
            </div>
        </div>
    </AppLayout>
</template>
