<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import AlertNotification from '@/components/AlertNotification.vue';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import DateFilter from '@/components/shared/DateFilter.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import LoanActiveCard from '@/components/LoanActiveCard.vue';
import ReturnLoanModal from '@/components/ReturnLoanModal.vue';
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

const props = defineProps<{
    loans: Loan[];
    auth_user: { id: number; name: string; username: string };
}>();

const page = usePage();
const can = (permission: string) =>
    (page.props.auth.user?.permissions ?? []).includes(permission);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Préstamos', href: loanRoutes.index.url() },
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

// Solo préstamos activos
const filteredLoans = computed(() => {
    let filtered = props.loans.filter((loan: Loan) => loan.estado_prestamo === 'Activo');

    if (searchQuery.value.trim() !== '') {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(loan => {

            const nombreUsuario = `${loan.borrower.nombres} ${loan.borrower.apellidoPaterno} ${loan.borrower.apellidoMaterno ?? ''}`.toLowerCase();
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
        filtered = filtered.filter(loan => loan.fecha_salida === filterDate.value);
    }
    if (filterMonth.value !== '') {
        filtered = filtered.filter(loan => {
            const month = new Date(loan.fecha_salida + 'T00:00:00').getMonth() + 1;
            return month.toString() === filterMonth.value;
        });
    }

    return filtered;
});

const uniqueSubjects = computed(() => {
    const subjectsMap = new Map();
    props.loans
        .filter(l => l.estado_prestamo === 'Activo')
        .forEach(loan => {
            if (loan.subject) subjectsMap.set(loan.subject.sigla, loan.subject.nombre_materia);
        });
    return Array.from(subjectsMap.entries()).map(([sigla, nombre]) => ({ sigla, nombre }));
});

const uniqueBorrowerCI = computed(() => {
    const borrowersMap = new Map();
    props.loans
        .filter(l => l.estado_prestamo === 'Activo')
        .forEach(loan => {
            if (loan.borrower) borrowersMap.set(
                loan.borrower.cedula_identidad,
                `${loan.borrower.apellidoPaterno ?? ''} ${loan.borrower.apellidoMaterno ?? ''}`.trim()
            );
        });
    return Array.from(borrowersMap.entries()).map(([cedula_identidad, apellidos]) => ({ cedula_identidad, apellidos }));
});

// --- MODAL DE DEVOLUCIÓN ---
const isReturnModalOpen = ref(false);
const selectedLoan = ref<any>(null);

const returnForm = useForm({
    loan_id: null as number | null,
    items: [] as any[],
    observacion: '',
    fecha_retorno: new Date().toISOString().split('T')[0],
    hora_fin: '',
    acuerdos: [] as any[],   // acuerdos de reposición del paso 2 del modal
});

const openReturnModal = (loan: any) => {
    if (!loan) return;
    selectedLoan.value = loan;
    returnForm.loan_id = loan.id;
    returnForm.observacion = '';

    returnForm.items = (loan.all_items || []).map((i: any) => ({
        id: i.id,
        nombre_mostrar: i.nombre_mostrar,
        foto: i.foto_equipo ?? i.foto_herramienta ?? i.foto ?? null,
        tipo: i.es_equipo ? 'equipo' : 'herramienta',
        es_equipo: i.es_equipo,
        estado_devolucion: 'Disponible',
        accessories: (i.accessories || []).map((acc: any) => ({
            id: acc.id,
            nombre_accesorio: acc.nombre_accesorio,
            estado_accesorio: 'Bueno'
        }))
    }));

    const now = new Date();
    returnForm.fecha_retorno = now.toISOString().split('T')[0];
    returnForm.hora_fin = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    isReturnModalOpen.value = true;
};

const processReturn = (acuerdos: any[]) => {
    // Inyectamos los acuerdos emitidos por el modal antes del POST
    returnForm.acuerdos = acuerdos;
    returnForm.post('/dashboard/loan-returns', {
        preserveScroll: true,
        onSuccess: () => {
            isReturnModalOpen.value = false;
            selectedLoan.value = null;
            returnForm.reset();
        },
        onError: (err) => {
            console.log("Error detallado:", err);
        }
    });
};

const currentTime = ref(new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
let timer: any;
watch(isReturnModalOpen, (isOpen) => {
    if (isOpen) {
        timer = setInterval(() => {
            currentTime.value = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }, 60000);
    } else {
        clearInterval(timer);
    }
});

</script>

<template>
    <Head title="Préstamos Activos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                description="Administre los préstamos activos de equipos y herramientas del taller"
            >
                <template #action>
                    <CreateActionButton
                        v-if="can('prestamos.crear')"
                        :href="loanRoutes.create.url()"
                        :label="`Registrar Préstamo`"
                    />
                </template>
            </PageHeader>

            <!-- FILTROS -->
            <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                <SearchInput v-model="searchQuery" placeholder="Buscar por item, materia o usuario..." class="flex-1" />
                <SelectFilter v-model="selectedSubject" label="Materias" :options="uniqueSubjects" option-value="sigla" option-label="nombre" combined icon="BookText" class="md:w-[165px]" />
                <SelectFilter v-model="selectedBorrowerCI" label="Usuarios" :options="uniqueBorrowerCI" option-value="cedula_identidad" option-label="apellidos" combined icon="User" class="md:w-40" />
                <SelectFilter v-model="filterMonth" label="Meses" :options="months" option-value="id" option-label="name" combined icon="CalendarDays" class="md:w-[150px]" />
                <DateFilter v-model="filterDate" label="Fecha específica" />
                <ClearFiltersButton @clear="() => { filterDate=''; filterMonth=''; selectedBorrowerCI=''; selectedSubject=''; searchQuery='' }" />
            </div>

            <!-- LISTADO -->
            <div class="space-y-4">
                <div v-if="filteredLoans.length === 0" class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
                    <Package class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium">No se encontraron préstamos activos con esos criterios.</p>
                </div>

                <div v-else class="space-y-4">
                    <LoanActiveCard
                        v-for="loan in filteredLoans"
                        :key="loan.id"
                        :loan="loan"
                        :isOpen="openLoanId === loan.id"
                        :loanRoutes="loanRoutes"
                        :can-return="can('prestamos.devolver')"
                        :can-edit="can('prestamos.editar')"
                        @toggleItems="toggleItems"
                        @return="openReturnModal"
                    />
                </div>
            </div>
        </div>

        <ReturnLoanModal
            :show="isReturnModalOpen"
            :loan="selectedLoan"
            :form="returnForm"
            :auth-user="auth_user"
            @close="isReturnModalOpen = false"
            @confirm="processReturn"
        />
    </AppLayout>
</template>
