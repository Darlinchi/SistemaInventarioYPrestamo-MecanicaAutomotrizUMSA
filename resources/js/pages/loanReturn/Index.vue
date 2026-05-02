<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AlertNotification from '@/components/AlertNotification.vue';
import PageHeader from '@/components/PageHeader.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import DateFilter from '@/components/shared/DateFilter.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import LoanHistoryTable from '@/components/LoanHistoryTable.vue';
import LoanDetailModal from '@/components/LoanDetailModal.vue';
import { Package } from 'lucide-vue-next';
import loanRoutes from '@/routes/loans';

interface Loan {
    id: number;
    fecha_salida: string;
    fecha_retorno?: string;
    fecha_retorno_prevista?: string;
    estado_prestamo: string;
    hora_inicio: string;
    hora_fin?: string;
    hora_fin_prevista?: string;
    observacion?: string;
    borrower: any;
    subject: any;
    all_items: any[];
}

const props = withDefaults(defineProps<{
    loans?: Loan[];
}>(), {
    loans: () => []
});

const page = usePage();
const can = (permission: string) =>
    (page.props.auth.user?.permissions ?? []).includes(permission);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Préstamos', href: loanRoutes.index.url() },
    { title: 'Devoluciones', href: '#' },
];

// --- NOTIFICACIONES FLASH ---
const flashSuccess = computed(() => (page.props.flash as any)?.success);

// --- FILTROS ---
const searchQuery = ref('');
const selectedBorrowerCI = ref('');
const selectedSubject = ref('');
const filterDate = ref('');
const filterMonth = ref('');

// LÓGICA DEL POPOVER DE ITEMS
const openLoanId = ref<number | null>(null);
const toggleItems = (id: number) => {
    openLoanId.value = openLoanId.value === id ? null : id;
};

const closePopovers = (e: MouseEvent) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.relative.inline-block')) {
        openLoanId.value = null;
    }
};

onMounted(() => window.addEventListener('click', closePopovers));
onUnmounted(() => window.removeEventListener('click', closePopovers));

// Meses para el filtro
const months = [
    { id: 1, name: 'Enero' }, { id: 2, name: 'Febrero' }, { id: 3, name: 'Marzo' },
    { id: 4, name: 'Abril' }, { id: 5, name: 'Mayo' }, { id: 6, name: 'Junio' },
    { id: 7, name: 'Julio' }, { id: 8, name: 'Agosto' }, { id: 9, name: 'Septiembre' },
    { id: 10, name: 'Octubre' }, { id: 11, name: 'Noviembre' }, { id: 12, name: 'Diciembre' }
];

// Solo préstamos devueltos
const filteredLoans = computed(() => {
    let filtered = props.loans.filter((loan: Loan) => loan.estado_prestamo === 'Devuelto');

    if (searchQuery.value.trim() !== '') {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(loan => {
            const nombreUsuario = `${loan.borrower.nombres} ${loan.borrower.apellidos}`.toLowerCase();
            const cedulaUsuario = `${loan.borrower.cedula_identidad}`;
            const coincideItem = (loan.all_items || []).some(item =>
                item.nombre_mostrar.toLowerCase().includes(query) ||
                item.codigo_qr?.toLowerCase().includes(query)
            );
            const coincideMateria =
                loan.subject.nombre_materia.toLowerCase().includes(query) ||
                loan.subject.sigla.toLowerCase().includes(query);
            return nombreUsuario.includes(query) || cedulaUsuario.includes(query) || coincideItem || coincideMateria;
        });
    }

    if (selectedBorrowerCI.value !== '') {
        filtered = filtered.filter(loan => loan.borrower.cedula_identidad === selectedBorrowerCI.value);
    }
    if (selectedSubject.value !== '') {
        filtered = filtered.filter(loan => loan.subject.sigla === selectedSubject.value);
    }
    if (filterDate.value !== '') {
        // Para devoluciones filtramos por fecha_retorno si existe, sino por fecha_salida
        filtered = filtered.filter(loan =>
            (loan.fecha_retorno ?? loan.fecha_salida) === filterDate.value
        );
    }
    if (filterMonth.value !== '') {
        filtered = filtered.filter(loan => {
            const fechaRef = loan.fecha_retorno ?? loan.fecha_salida;
            const month = new Date(fechaRef + 'T00:00:00').getMonth() + 1;
            return month.toString() === filterMonth.value;
        });
    }

    return filtered;
});

const uniqueSubjects = computed(() => {
    const subjectsMap = new Map();
    props.loans
        .filter(l => l.estado_prestamo === 'Devuelto')
        .forEach(loan => {
            if (loan.subject) subjectsMap.set(loan.subject.sigla, loan.subject.nombre_materia);
        });
    return Array.from(subjectsMap.entries()).map(([sigla, nombre]) => ({ sigla, nombre }));
});

const uniqueBorrowerCI = computed(() => {
    const borrowersMap = new Map();
    props.loans
        .filter(l => l.estado_prestamo === 'Devuelto')
        .forEach(loan => {
            if (loan.borrower) borrowersMap.set(loan.borrower.cedula_identidad, loan.borrower.apellidos);
        });
    return Array.from(borrowersMap.entries()).map(([cedula_identidad, apellidos]) => ({ cedula_identidad, apellidos }));
});

// --- MODAL DE DETALLE ---
const viewInformacion = ref(false);
const loanInformacion = ref<Loan | null>(null);

const openViewInformacion = (loan: Loan) => {
    loanInformacion.value = loan;
    viewInformacion.value = true;
};

const closeViewInformacion = () => {
    viewInformacion.value = false;
    loanInformacion.value = null;
};

const handleGenerateReport = (id: number | string) => {
    const url = `/dashboard/loans/${id}/report`;
    window.open(url, '_blank');
};

</script>

<template>
    <Head title="Devoluciones" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                description="Historial de devoluciones de equipos y herramientas del taller"
            />

            <!-- FILTROS -->
            <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                <SearchInput v-model="searchQuery" placeholder="Buscar por item, materia o usuario..." class="flex-1" />
                <SelectFilter v-model="selectedSubject" label="Materias" :options="uniqueSubjects" option-value="sigla" option-label="nombre" icon="BookText" class="md:w-[165px]" />
                <SelectFilter v-model="selectedBorrowerCI" label="Usuarios" :options="uniqueBorrowerCI" option-value="cedula_identidad" option-label="apellidos" icon="User" class="md:w-40" />
                <SelectFilter v-model="filterMonth" label="Meses" :options="months" option-value="id" option-label="name" icon="CalendarDays" class="md:w-[150px]" />
                <DateFilter v-model="filterDate" label="Fecha específica" />
                <ClearFiltersButton @clear="() => { filterDate=''; filterMonth=''; selectedBorrowerCI=''; selectedSubject=''; searchQuery='' }" />
            </div>

            <!-- LISTADO -->
            <div class="space-y-4">
                <div v-if="filteredLoans.length === 0" class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
                    <Package class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium">No se encontraron devoluciones con esos criterios.</p>
                </div>

                <div v-else>
                    <LoanHistoryTable
                        :loans="filteredLoans"
                        :openLoanId="openLoanId"
                        :can-edit="can('prestamos.editar')"
                        :can-delete="can('prestamos.eliminar')"
                        @view="openViewInformacion"
                        @toggleItems="toggleItems"
                        @generateReport="handleGenerateReport"
                    />
                </div>
            </div>
        </div>

        <LoanDetailModal
            :show="viewInformacion"
            :loan="loanInformacion"
            @close="closeViewInformacion"
        />
    </AppLayout>
</template>
