<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import AlertNotification from '@/components/AlertNotification.vue';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import DateFilter from '@/components/shared/DateFilter.vue';
import LoanHistoryTable from '@/components/LoanHistoryTable.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import LoanActiveCard from '@/components/LoanActiveCard.vue';
import ReturnLoanModal from '@/components/ReturnLoanModal.vue';
import LoanDetailModal from '@/components/LoanDetailModal.vue';
import { Package } from 'lucide-vue-next';
import loanRoutes from '@/routes/loans';

interface Loan {
    id: number;
    fecha_salida: string;
    fecha_retorno?: string;
    fecha_retorno_prevista?: string ;
    estado_prestamo: string;
    hora_inicio: string;
    hora_fin?: string;
    hora_fin_prevista?: string;
    observacion?: string;
    borrower: any;
    subject: any;
    all_items: any[];
}

// Recibe la lista de prestamos desde el controlador de Laravel
const props = defineProps<{
    loans: Loan[];
}>();

const can = (permission: string) =>
    (page.props.auth.user?.permissions ?? []).includes(permission);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Préstamos',
        href: loanRoutes.index.url()
    },
];

// --- NOTIFICACIONES FLASH ---
const page = usePage();
const flashSuccess = computed(() => (page.props.flash as any)?.success);

// --- ESTADO Y BUSQUEDA ---
// Define si vemos la pestaña de prestamos "activos" o el "historial" (devueltos)
const activeTab = ref<'activos' | 'historial'>('activos');
// Variable reactiva que guardara el texto que el usuario escribe en el buscador
const searchQuery = ref('');
// Variables para los filtros seleccionados
const selectedName = ref('');
// preguntar si desea el filtrado por ci, nombres y apellidos
const selectedBorrowerCI = ref('');
const selectedSubject = ref('');
const filterDate = ref(''); // Para fecha exacta (YYYY-MM-DD)
const filterMonth = ref(''); // Para el mes (1-12)
const filterDay = ref(''); // Para el día de la semana (0-6)

// LÓGICA DEL POPOVER DE ITEMS
const openLoanId = ref<number | null>(null);
const toggleItems = (id: number) => {
    openLoanId.value = openLoanId.value === id ? null : id;
};

// La cantidad de prestamos activos hay para mostrar el numero en la pestaña
const countActivos = computed(() => props.loans.filter((b: Loan) => b.estado_prestamo == "Activo").length);
const countHistorial = computed(() => props.loans.filter((b: Loan) => b.estado_prestamo == "Devuelto").length);

// En tu <script setup> de Index.vue
const loansTabs = computed(() => [
    { id: 'activos', label: 'Préstamos Activos', count: countActivos.value, icon: 'NotebookPen' },
    { id: 'historial', label: 'Préstamos Devueltos', count: countHistorial.value, icon: 'NotebookText' }
]);

// Cierra el popover si se hace clic fuera del contenedor
const closePopovers = (e: MouseEvent) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.relative.inline-block')) {
        openLoanId.value = null;
    }
};

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

// Buscador y filtrado
// La pestaña activa (Activos vs Historial)
// El texto escrito en 'searchQuery'
const filteredLoans = computed(() => {
    // Se filtra por la pestaña seleccionada
    let filtered = props.loans.filter((loan: Loan) => {
        return activeTab.value === 'activos'
            ? loan.estado_prestamo === 'Activo'
            : loan.estado_prestamo === 'Devuelto';
    });

    // Si el usuario escribio algo en el buscador, filtramos esos resultados
    if (searchQuery.value.trim() !== '') {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(loan => {
            // Busca en el nombre completo del prestatario (Docente/Auxiliar)
            const nombreUsuario = `${loan.borrower.nombresP} ${loan.borrower.apellidosP}`.toLowerCase();
            // Busca en la cedula de identidad del prestatario (Docente/Auxiliar)
            const cedulaUsuario = `${loan.borrower.cedula_identidad}`;
            // Busca en los nombres de todos los items incluidos en este prestamo
            // 'some' para ver si al menos uno coincide
            const coincideItem = (loan.all_items || []).some(item =>
                item.nombre_mostrar.toLowerCase().includes(query) ||
                item.codigo_qr?.toLowerCase().includes(query)
            );
            // Busca en la sigla o nombre de la materia
            const coincideMateria = loan.subject.nombre_materia.toLowerCase().includes(query) ||
                                  loan.subject.sigla.toLowerCase().includes(query);
            // Si coincide en cualquiera de estos campos, el prestamo se muestra
            return nombreUsuario.includes(query) || cedulaUsuario.includes(query) || coincideItem || coincideMateria;
        });
    }

    // Filtro por responsable
    if (selectedName.value !== '') {
        filtered = filtered.filter(loan => loan.borrower.apellidosP === selectedName.value);
    }
    if (selectedBorrowerCI.value !== '') {
        filtered = filtered.filter(loan => loan.borrower.cedula_identidad === selectedBorrowerCI.value);
    }
    // Filtro por materia
    if (selectedSubject.value !== '') {
        filtered = filtered.filter(loan => loan.subject.sigla === selectedSubject.value);
    }
    // Filtro por fecha
    if (filterDate.value !== '') {
        filtered = filtered.filter(loan => loan.fecha_salida === filterDate.value);
    }
    if (filterMonth.value !== '') {
        filtered = filtered.filter(loan => {
            const month = new Date(loan.fecha_salida + 'T00:00:00').getMonth() + 1;
            return month.toString() === filterMonth.value;
        });
    }
    if (filterDay.value !== '') {
        filtered = filtered.filter(loan => {
            const day = loan.fecha_salida.split('-')[2]; // Extrae el DD de YYYY-MM-DD
            return day === filterDay.value.padStart(2, '0');
        });
    }

    return filtered;
});

// Activar al entrar y desactivar al salir
onMounted(() => window.addEventListener('click', closePopovers));
onUnmounted(() => window.removeEventListener('click', closePopovers));

const uniqueSubjects = computed(() => {
    const subjectsMap = new Map();
    props.loans.forEach(loan => {
        if (loan.subject) {
            subjectsMap.set(loan.subject.sigla, loan.subject.nombre_materia);
        }
    });
    // Retornamos un array de objetos { sigla, nombre }
    return Array.from(subjectsMap.entries()).map(([sigla, nombre]) => ({ sigla, nombre }));
});

const uniqueBorrowerCI = computed(() => {
    const borrowersMap = new Map();
    props.loans.forEach(loan => {
        if (loan.borrower) {
            borrowersMap.set(loan.borrower.cedula_identidad, loan.borrower.apellidosP);
        }
    });
    // Retornamos un array de objetos { sigla, nombre }
    return Array.from(borrowersMap.entries()).map(([cedula_identidad, apellidosP]) => ({ cedula_identidad, apellidosP }));
});

// Modal para registrar la devolucion del prestamo
const isReturnModalOpen = ref(false);
const selectedLoan = ref<any>(null);

const returnForm = useForm({
    items: [] as any[],
    observacion: '',
    fecha_retorno: new Date().toISOString().split('T')[0], // Por defecto hoy
    hora_fin: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }),
});

// Funcion para abrir el modal (preguntar si le gustaria si un accesorio esta mal el equipo completo marcarse como dañado)
const openReturnModal = (loan: any) => {
    if (!loan) return;
    selectedLoan.value = loan;

    // Usamos el accessor polimórfico que definiste en el modelo Loan.php
    const itemsToProcess = loan.all_items || [];

    // CORRECCIÓN: Usar itemsToProcess.map
    returnForm.items = itemsToProcess.map((i: any) => ({
        id: i.id,
        nombre_mostrar: i.nombre_mostrar,
        foto_equipo: i.foto_equipo || i.foto_herramienta || i.foto,
        foto_herramienta: i.foto_herramienta || i.foto_equipo || i.foto,
        foto: i.foto || i.foto_equipo || i.foto_herramienta,
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
    returnForm.post(`/dashboard/loans/${selectedLoan.value.id}/return`, {
        preserveScroll: true,
        onSuccess: () => {
            isReturnModalOpen.value = false;
            selectedLoan.value = null;
            returnForm.reset();
        }
    });
};

const currentTime = ref(new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));

// Hora de devolucion
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

// Para visualizar toda la informacion y hacer reporte
const viewInformacion = ref(false);
const loanInformacion = ref<Loan | null>(null);

// Abre el modal y guarda el item seleccionado
const openViewInformacion = (loan: Loan) => {
    loanInformacion.value = loan;
    viewInformacion.value = true;
};

// Cierra el modal y limpia el estado
const closeViewInformacion = () => {
    viewInformacion.value = false;
    loanInformacion.value = null;
};

// Define la función para evitar el error de "window" en el template
const handleGenerateReport = (id: number | string) => {
    // Usamos la cadena de texto exacta de tu ruta de Wayfinder
    const url = `/dashboard/loans/${id}/report`;
    window.open(url, '_blank');
};

</script>

<template>
    <Head title="Gestión de Préstamos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                description="Administre préstamos y devoluciones de equipos y herramientas del taller"
            >
                <template #action>
                    <CreateActionButton
                        v-if="can('prestamos.crear')"
                        :href="loanRoutes.create.url()"
                        :label="`Registrar Préstamo`"
                    />
                </template>
            </PageHeader>

            <TabSelector
                :tabs="loansTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                <SearchInput v-model="searchQuery" placeholder="Buscar por item, materia o usuario..." class="flex-1" />
                <SelectFilter v-model="selectedSubject" label="Materias" :options="uniqueSubjects" option-value="sigla" option-label="nombre" icon="BookText" class="md:w-[165px]" />
                <SelectFilter v-model="selectedBorrowerCI" label="Usuarios" :options="uniqueBorrowerCI" option-value="cedula_identidad" option-label="apellidosP" icon="User" class="md:w-40" />
                <SelectFilter v-model="filterMonth" label="Meses" :options="months" option-value="id" option-label="name" icon="CalendarDays" class="md:w-[150px]" />
                <DateFilter v-model="filterDate" label="Fecha específica" />
                <ClearFiltersButton @clear="() => { filterDate=''; filterMonth=''; selectedBorrowerCI=''; selectedSubject=''; searchQuery='' }" />
            </div>

            <div class="space-y-4">
                <div v-if="filteredLoans.length === 0" class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
                    <Package class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium">No se encontraron préstamos con esos criterios.</p>
                </div>

                <div v-if="activeTab === 'activos' && filteredLoans.length > 0" class="space-y-4">
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

                <!--HOSTORIAL DE DEVOLUCION-->
                <div v-if="activeTab === 'historial' && filteredLoans.length > 0">
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

        <ReturnLoanModal
            :show="isReturnModalOpen"
            :loan="selectedLoan"
            :form="returnForm"
            @close="isReturnModalOpen = false"
            @confirm="processReturn"
        />

        <LoanDetailModal
            :show="viewInformacion"
            :loan="loanInformacion"
            @close="closeViewInformacion"
        />

    </AppLayout>
</template>
