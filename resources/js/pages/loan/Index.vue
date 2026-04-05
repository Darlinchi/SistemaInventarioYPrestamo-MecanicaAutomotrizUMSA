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
        foto_equipo: i.foto_equipo,
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

const imprimirReporte = () => {
    window.print();
};

</script>

<template>
    <Head title="Gestión de Préstamos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                title="Gestión de Préstamos"
                description="Administre préstamos y devoluciones de equipos y herramientas del taller"
            >
                <template #action>
                    <CreateActionButton
                        :href="loanRoutes.create.url()"
                        :label="`Registrar Préstamo`"
                    />
                </template>
            </PageHeader>

            <!--
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Gestión de Préstamos</h1>
                    <p class="text-sm text-neutral-500">Administre préstamos y devoluciones de equipos y herramientas del taller</p>
                </div>
                <Link :href="loanRoutes.create.url()" class="bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-neutral-800 transition shadow-lg">
                    <Plus class="w-5 h-5"/> Nuevo Préstamo
                </Link>
            </div>-->

            <TabSelector
                :tabs="loansTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <!--<div class="flex p-1 bg-neutral-100 rounded-xl w-fit mb-6 border border-neutral-200">
                <button @click="activeTab = 'activos'"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    activeTab === 'activos' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <NotebookPen  class="w-4.5 h-4.5 "/>Préstamos Activos ({{ countActivos }})
                </button>
                <button @click="activeTab = 'historial'"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    activeTab === 'historial' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <NotebookText class="w-4.5 h-4.5 "/>Historial de Devoluciones ({{ countHistorial }})
                </button>
            </div>
            -->

            <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                <SearchInput v-model="searchQuery" placeholder="Buscar por item, materia o usuario..." class="flex-1" />
                <SelectFilter v-model="selectedSubject" label="Materias" :options="uniqueSubjects" option-value="sigla" option-label="nombre" icon="BookText" class="md:w-[165px]" />
                <SelectFilter v-model="selectedBorrowerCI" label="Usuarios" :options="uniqueBorrowerCI" option-value="cedula_identidad" option-label="apellidosP" icon="User" class="md:w-40" />
                <SelectFilter v-model="filterMonth" label="Meses" :options="months" option-value="id" option-label="name" icon="CalendarDays" class="md:w-[150px]" />
                <DateFilter v-model="filterDate" label="Fecha específica" />
                <ClearFiltersButton @clear="() => { filterDate=''; filterMonth=''; selectedBorrowerCI=''; selectedSubject=''; searchQuery='' }" />
            </div>

            <!--<div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="relative w-full md:w-80">
                    <Search class="absolute left-3 top-3 w-5 h-5 text-neutral-400" />
                    <input v-model="searchQuery" type="text" placeholder="Buscar por item, materia o usuario..."
                        class="pl-10 flex h-10 w-full rounded-md border border-input bg-neutral-50 px-3 py-2 text-sm shadow-sm transition-colors focus:bg-white"
                    />

                    <button v-if="searchQuery" @click="searchQuery = ''"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-red-500 transition-colors"
                    >
                        <XIcon class="w-5 h-5"/>
                    </button>
                </div>

                <select v-model="selectedSubject" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600 md:w-28">
                    <option value="">Materias</option>
                    <option v-for="sub in uniqueSubjects" :key="sub.sigla" :value="sub.sigla">
                        {{ sub.sigla }} - {{ sub.nombre }}
                    </option>
                </select>

                <select v-model="selectedBorrowerCI" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600 md:w-28">
                    <option value="">Usuarios</option>
                    <option v-for="sub in uniqueBorrowerCI" :key="sub.cedula_identidad" :value="sub.cedula_identidad">
                        {{ sub.cedula_identidad }} - {{ sub.apellidosP }}
                    </option>
                </select>

                <select v-model="filterMonth" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600 md:w-24">
                    <option value="">Meses</option>
                    <option v-for="month in months" :key="month.id" :value="month.id.toString()">
                        {{ month.name }}
                    </option>
                </select>

                <div class="space-y-2">
                    <Input type="date" title="Fecha Específica" v-model="filterDate" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600" />
                </div>

                <Button @click="() => { filterDate=''; filterMonth=''; filterDay=''; selectedBorrowerCI=''; selectedSubject=''; searchQuery='' }" title="Limpiar Filtros" variant="outline" class="rounded-xl border-dashed border-neutral-300 hover:bg-red-50 hover:text-red-600 transition-colors">
                    <Eraser class="w-4 h-4 mr-2"/> Limpiar Filtros
                </Button>
            </div>
            -->


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
                        @toggleItems="toggleItems"
                        @return="openReturnModal"
                    />
                </div>

                <!--<div v-if="activeTab === 'activos' && filteredLoans.length > 0" class="space-y-4">
                    <div v-for="loan in filteredLoans" :key="loan.id"
                        class="group border border-blue-100 bg-blue-50/50 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center transition-all duration-300 shadow-sm hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-12 w-full">
                            <div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <p class="flex items-center gap-2 text-[13px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">
                                            <Calendar class="w-4 h-4 text-blue-600" />
                                            <span>F. Salida</span>
                                        </p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loan.fecha_salida }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-500 uppercase tracking-widest leading-none mb-1">
                                            <CalendarCheck2 class="w-4 h-4 text-orange-600" />
                                            <span>F. Limite</span>
                                        </p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loan.fecha_retorno_prevista }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="flex items-center gap-2 text-[13px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">
                                            <Clock class="w-4 h-4 text-blue-600" />
                                            <span>Hora Inicio</span>
                                        </p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loan.hora_inicio }}</p>
                                    </div>
                                    <div>
                                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-500 uppercase tracking-widest leading-none mb-1">
                                            <ClockAlert class="w-4 h-4 text-orange-600" />
                                            <span>Hora Fin</span>
                                        </p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loan.hora_fin_prevista }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="mb-4">
                                    <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                                        <User class="w-4 h-4 text-neutral-800" />
                                        <span>Responsable</span>
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-blue-100 text-[11px] font-bold text-blue-700 uppercase tracking-tighter">
                                            {{ loan.borrower.teacher ? 'Docente' : 'Auxiliar' }}
                                        </span>
                                    </p>
                                    <p class="flex items-center gap-2 text-[14px] font-bold text-neutral-800">{{ loan.borrower.nombresP }} {{ loan.borrower.apellidosP }}
                                        <span class="text-[13px] px-2 py-0.5 bg-neutral-100 rounded-md font-bold text-neutral-700">
                                            CI: {{ loan.borrower.cedula_identidad }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                                        <BookMarked class="w-4 h-4 text-neutral-800" />
                                        <span>Sigla y Materia</span>
                                    </p>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-neutral-900 leading-tight">{{ loan.subject.nombre_materia }}</span>
                                        <span class="text-[14px] font-mono text-blue-600">{{ loan.subject.sigla }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="bg-white px-3 py-1 rounded-full text-[13px] font-bold uppercase border border-blue-200">
                                        Items Prestados
                                    </span>
                                </div>

                                <div class="relative">
                                    <button
                                        @click.stop="toggleItems(loan.id)"
                                        class="flex items-center gap-2 px-3 py-1.5 bg-white border border-neutral-200 rounded-lg shadow-sm hover:bg-neutral-50 transition active:scale-95">
                                        <List class="w-4 h-4 text-black"/>
                                        <span class="text-[13px] font-bold text-black">Ver detalle de {{ loan.all_items.length }} ítems</span>
                                    </button>

                                    <div
                                        v-if="openLoanId === loan.id"
                                        class="absolute left-0 z-50 mt-2 w-80 bg-white border border-neutral-200 rounded-xl shadow-2xl p-4 animate-in fade-in zoom-in duration-200"
                                    >
                                        <p class="text-[11px] font-black uppercase text-neutral-800 mb-3 tracking-widest">Items en este préstamo</p>
                                        <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                            <div
                                                v-for="item in loan.all_items"
                                                :key="item.id"
                                                class="flex items-center justify-between p-1 bg-neutral-50 border border-neutral-100 rounded-lg hover:border-blue-200 transition-colors"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[13px] font-bold text-neutral-800">{{ item.nombre_mostrar }}</span>
                                                    <span :class="[
                                                        'px-2 py-0.5 rounded-full text-[9px] font-black uppercase border leading-none',
                                                        item.es_equipo ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                                                    ]">
                                                        {{ item.tipo_personalizado }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 ml-6">
                            <Link :href="loanRoutes.edit.url(loan.id)">
                                <button class="flex items-center gap-2 bg-white border border-neutral-200 px-4 py-2 rounded-lg text-[13px] font-bold hover:bg-neutral-200 transition shadow-sm">
                                    <Edit class="w-4 h-4" /> Editar
                                </button>
                            </Link>
                            <button @click="openReturnModal(loan)" class="flex items-center gap-2 bg-blue-600 border border-neutral-200 px-4 py-2 rounded-lg text-[13px] font-bold hover:bg-blue-700 transition text-white shadow-m">
                                <CheckCircle class="w-4 h-4" /> Devolver
                            </button>
                        </div>
                    </div>
                </div>
                -->


                <!--HOSTORIAL DE DEVOLUCION-->
                <!--<div v-if="activeTab === 'historial' && filteredLoans.length > 0"
                    class="relative bg-white border border-neutral-200 rounded-xl shadow-sm overflow-x-auto">
                    <table class="w-full text-left min-w-max border-separate border-spacing-0">
                        <thead class="bg-neutral-200 border-b border-neutral-300 text-xs font-bold uppercase tracking-widest text-neutral-800">
                            <tr>
                                <th class="p-4">Fecha Salida</th>
                                <th class="p-4">Responsable</th>
                                <th class="p-4">Materia - Sigla</th>
                                <th class="p-4">Items Prestados</th>
                                <th class="p-4 text-right sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            <tr v-for="loan in filteredLoans" :key="loan.id" class="hover:bg-neutral-50 transition-colors group">
                                <td class="p-4 font-bold text-black">{{ loan.fecha_salida }}</td>
                                <td class="p-4 text-neutral-900">{{ loan.borrower.apellidosP }} {{ loan.borrower.nombresP }} </td>
                                <td class="p-4 text-neutral-900">{{ loan.subject.nombre_materia }} - {{ loan.subject.sigla }} </td>
                                <td class="p-4 text-neutral-900">{{ loan.hora_fin }} </td>
                                <td class="p-4 text-neutral-900">
                                    <div class="flex justify-end relative">
                                        <button @click.stop="toggleItems(loan.id)"
                                                class="flex items-center gap-2 px-4 py-2 bg-neutral-100 border border-neutral-200 rounded-xl text-xs font-bold hover:bg-neutral-200 transition">
                                            <List class="w-4 h-4" /> Detalle de Ítems
                                        </button>

                                        <div v-if="openLoanId === loan.id"
                                            class="absolute right-0 top-full z-50 mt-2 w-80 bg-white border border-neutral-200 rounded-2xl shadow-2xl p-4 animate-in fade-in slide-in-from-top-2 duration-200">

                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-neutral-100">
                                                <p class="text-[11px] font-black uppercase text-neutral-700 tracking-widest">Estado al devolver</p>
                                                <Package class="w-3 h-3 text-neutral-400" />
                                            </div>

                                            <div class="max-h-64 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                                <div v-for="item in loan.all_items" :key="item.id"
                                                    class="flex items-center justify-between p-3 bg-neutral-50 border border-neutral-100 rounded-xl hover:bg-white transition-colors">

                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-[11px] font-bold text-neutral-800 leading-tight">
                                                            {{ item.nombre_mostrar }}
                                                        </span>
                                                        <span class="text-[9px] font-medium text-neutral-500 uppercase tracking-tighter">
                                                            {{ item.es_equipo ? 'Equipo' : 'Herramienta' }}
                                                        </span>
                                                    </div>

                                                    <span :class="[
                                                        'px-2 py-1 rounded-lg text-[9px] font-black uppercase border shadow-sm',
                                                        statusColor(item.estado_devolucion)
                                                    ]">
                                                        {{ item.estado_devolucion }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-right space-x-3 sticky right-0 bg-white group-hover:bg-neutral-50 border-l border-neutral-100">
                                    <Button
                                        @click="openViewInformacion(loan)"
                                        class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-red-500"
                                        title="Información"
                                    >
                                        <Eye class="w-4.5 h-4.5 stroke-green-500"/>
                                    </Button>
                                    <Link >
                                        <Button title="Generar Reporte" class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-blue-500"><SquarePen class="w-4.5 h-4.5 stroke-blue-500"/></Button>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                -->

                <div v-if="activeTab === 'historial' && filteredLoans.length > 0">
                    <LoanHistoryTable
                        :loans="filteredLoans"
                        :openLoanId="openLoanId"
                        @view="openViewInformacion"
                        @toggleItems="toggleItems"
                        @generateReport=""
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

        <!--<div v-if="isReturnModalOpen" class="fixed inset-0 z-200 flex items-center justify-center bg-black/40 backdrop-blur-md p-6 lg:p-12">
            <div class="bg-white w-full max-w-3xl rounded-4xl  shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in zoom-in duration-300">

                <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-blue-600 rounded-lg">
                                <NotebookPen class="w-6 h-6 text-white" />
                            </div>
                            <h2 class="text-2xl font-black uppercase tracking-tighter text-neutral-900">Registrar Devolución</h2>
                        </div>
                        <p class="text-neutral-700 text-sm font-medium">Verifique el estado de los items recibidos</p>
                    </div>
                    <button @click="isReturnModalOpen = false" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                        <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                    </button>
                </div>

                <div class="p-8 pt-2 overflow-y-auto custom-scrollbar space-y-3 flex-1">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-2xl border border-neutral-200">
                        <div class="space-y-1">
                            <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                                <User class="w-4 h-4" /> <span>Responsable</span>
                            </p>
                            <p class="text-base font-bold text-neutral-900">{{ selectedLoan?.borrower.nombresP }} {{ selectedLoan?.borrower.apellidosP }}</p>
                            <span class="inline-block px-2 py-0.5 rounded-md bg-blue-100 text-[13px] font-bold text-blue-700 uppercase tracking-tighter">
                                {{ selectedLoan?.borrower.teacher ? 'Docente' : 'Auxiliar' }}
                            </span>
                            <span class="text-[13px] px-2 py-0.5 bg-neutral-100 rounded-md font-bold text-neutral-800">
                                CI: {{ selectedLoan?.borrower.cedula_identidad }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                                <BookMarked class="w-4 h-4 text-neutral-700" /> <span>Materia Asignada</span>
                            </p>
                            <p class="text-base font-bold text-neutral-900">{{ selectedLoan?.subject.nombre_materia }}</p>
                            <p class="text-[14px] text-blue-600 font-mono">{{ selectedLoan?.subject.sigla }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                            <CalendarClock class="w-4 h-4" /> Fecha y horario del préstamo
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-2xl border border-neutral-200 mt-4">
                            <div class="flex flex-col gap-3">
                                <span class="text-[13px] font-bold text-blue-600 uppercase tracking-tighter">Registro de Salida</span>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Calendar class="w-4 h-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">Fecha Salida</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ selectedLoan?.fecha_salida }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Clock class="w-4 h-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">Hora Inicio</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ selectedLoan?.hora_inicio }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                                <span class="text-[13px] font-bold text-orange-600 uppercase tracking-tighter">Retorno (Opcional)</span>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Calendar class="w-4 h-4 text-orange-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-orange-500 uppercase tracking-widest leading-none mb-1">Fecha Limite</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ selectedLoan?.fecha_retorno_prevista }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Clock class="w-4 h-4 text-orange-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-orange-500 uppercase tracking-widest leading-none mb-1">Hora Fin</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ selectedLoan?.hora_fin_prevista }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                                <span class="text-[13px] font-bold text-green-600 uppercase tracking-tighter">Retorno Real</span>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Calendar class="w-4 h-4 text-green-600" />
                                    </div>
                                    <div>
                                        <Label for="fecha_retorno" class="text-[11px] font-black text-green-500 uppercase tracking-widest leading-none mb-1">Fecha Retorno</Label>
                                        <Input type="date" v-model="returnForm.fecha_retorno" :min="selectedLoan?.fecha_salida" class="h-8 text-xs rounded-lg" />
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Clock class="w-4 h-4 text-green-600" />
                                    </div>
                                    <div>
                                        <Label for="hora_fin" class="text-[11px] font-black text-green-500 uppercase tracking-widest leading-none mb-1">Hora Entrada</Label>
                                        <Input type="time" v-model="returnForm.hora_fin" class="h-8 text-xs rounded-lg" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-3">
                        <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                            <Package class="w-4 h-4" /> Revisión Detallada de Equipos y Herramientas
                        </h3>

                        <div v-for="(item, index) in returnForm.items" :key="item.id" class="border border-neutral-200 bg-neutral-50 rounded-3xl overflow-hidden shadow-sm">
                            <div class="flex items-center justify-between p-4 bg-white">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-100 shadow-inner">
                                        <img v-if="item.foto" :src="'/storage/' + item.foto" class="object-cover w-full h-full" />
                                        <Package v-else class="w-6 h-6 text-neutral-300" />
                                    </div>

                                    <div class="flex flex-col min-w-0">
                                        <span class="text-[14px] font-bold text-neutral-900 truncate leading-tight">{{ item.nombre_mostrar }}</span>
                                        <span :class="[
                                            'w-fit px-2 py-0.5 rounded-full text-[10px] font-black uppercase border mt-1',
                                            item.es_equipo ? 'bg-red-50 text-red-600 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100'
                                        ]">
                                            {{ item.es_equipo ? 'Equipo' : 'Herramienta' }}
                                        </span>
                                    </div>
                                </div>

                                <select v-model="returnForm.items[index].estado_devolucion"
                                    class="text-[13px] font-bold rounded-xl border-neutral-200 bg-neutral-50 focus:ring-black focus:border-black transition-all py-1.5 px-3">
                                    <option value="Disponible">Disponible</option>
                                    <option value="Dañado">Dañado</option>
                                    <option value="Extraviado">Extraviado</option>
                                    <option value="Incompleto">Incompleto</option>
                                    <option value="Baja">Baja</option>
                                </select>
                            </div>

                            <div v-if="item.accessories?.length" class="bg-neutral-50 p-4 border-t border-neutral-100 space-y-2">
                                <p class="text-[11px] font-black text-neutral-800 uppercase tracking-widest flex items-center gap-2 mb-1">
                                    <div class="w-1.5 h-1.5 bg-blue-400 rounded-full"></div>
                                    Accesorios del Equipo
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div v-for="(acc, accIndex) in item.accessories" :key="acc.id"
                                        class="flex items-center justify-between bg-white p-2.5 rounded-xl border border-neutral-200/60 shadow-sm">
                                        <span class="text-[13px] font-semibold text-neutral-800 flex items-center gap-1">
                                            <CornerDownRight class="w-4 h-4 text-blue-400"/> {{ acc.nombre_accesorio }}
                                        </span>
                                        <select v-model="returnForm.items[index].accessories[accIndex].estado_accesorio"
                                            class="text-[13px] py-1 px-2 border-neutral-100 rounded-lg bg-neutral-50 font-bold focus:ring-black outline-none">
                                            <option value="Bueno">Bueno</option>
                                            <option value="Dañado">Dañado</option>
                                            <option value="Extraviado">Extraviado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <Label for="observacion" class="text-[13px] font-black uppercase text-amber-500 tracking-widest">
                            <AlignLeft class="w-4 h-4 text-amber-500"/> Observación de recepción final
                        </Label>
                        <Textarea v-model="returnForm.observacion"
                            placeholder="Escriba aquí notas adicionales sobre la devolución..."
                            class="bg-amber-50 border-amber-200 text-sm rounded-2xl min-h-[100px] focus:bg-white transition-all" />
                    </div>
                </div>

                <div class="px-8 py-4 bg-white border-t border-neutral-100 flex gap-4">
                    <Button variant="outline" class="flex-1 py-7 rounded-2xl font-bold uppercase tracking-widest text-[15px]" @click="isReturnModalOpen = false">
                        Cerrar
                    </Button>
                    <Button class="flex-1 bg-black text-white py-7 rounded-2xl font-black uppercase tracking-widest text-[15px] shadow-xl hover:shadow-neutral-200 transition-all active:scale-[0.98]"
                        @click="processReturn" :disabled="returnForm.processing">
                        <Loader2 v-if="returnForm.processing" class="mr-2 animate-spin w-4 h-4 text-white"/>
                        Confirmar Devolución
                    </Button>
                </div>
            </div>
        </div>
        -->

        <LoanDetailModal
            :show="viewInformacion"
            :loan="loanInformacion"
            @close="closeViewInformacion"
        />

        <!--<div v-if="viewInformacion" class="fixed inset-0 z-100 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md no-print" @click="closeViewInformacion"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-300 flex flex-col">

                <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-blue-600 rounded-lg">
                                <NotebookText class="w-6 h-6 text-white" />
                            </div>
                            <h2 class="text-2xl font-black text-neutral-800 flex items-center gap-3">Resumen de Devolución</h2>
                        </div>

                        <p class="text-neutral-700 text-sm font-medium">Comprobante de recepción de equipos y herramientas - Taller de Mecánica</p>
                    </div>
                    <button @click="closeViewInformacion" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                        <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                    </button>
                </div>

                <div class="p-8 pt-2 overflow-y-auto custom-scrollbar  space-y-3 flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-white rounded-2xl border border-neutral-200 shadow-sm">
                            <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                                <User class="w-4 h-4" /> <span>Responsable</span>
                            </p>
                            <p class="text-base font-bold text-neutral-900">{{ loanInformacion?.borrower.nombresP }} {{ loanInformacion?.borrower.apellidosP }}</p>
                            <span class="inline-block px-2 py-0.5 rounded-md bg-blue-100 text-[13px] font-bold text-blue-700 uppercase tracking-tighter">
                                {{ loanInformacion?.borrower.teacher ? 'Docente' : 'Auxiliar' }}
                            </span>
                            <span class="text-xs px-2 py-0.5 bg-neutral-100 rounded-md font-bold text-neutral-600">
                                CI: {{ loanInformacion?.borrower.cedula_identidad }}
                            </span>
                        </div>

                        <div class="p-4 bg-white rounded-2xl border border-neutral-200 shadow-sm">
                            <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                                <BookMarked class="w-4 h-4 text-neutral-700" /> <span>Materia Asignada</span>
                            </p>
                            <p class="text-base font-bold text-neutral-900">{{ loanInformacion?.subject.nombre_materia }}</p>
                            <p class="text-[14px]  text-blue-500 font-mono">{{ loanInformacion?.subject.sigla }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                            <CalendarClock class="w-4 h-4" /> Fecha y horario del préstamo
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-2xl border border-neutral-200 mt-4">
                            <div class="flex flex-col gap-3">
                                <span class="text-[13px] font-bold text-blue-600 uppercase tracking-tighter">Registro de Salida</span>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Calendar class="w-4 h-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">Fecha Salida</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loanInformacion?.fecha_salida }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Clock class="w-4 h-4 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-blue-500 uppercase tracking-widest leading-none mb-1">Hora Inicio</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loanInformacion?.hora_inicio }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                                <span class="text-[13px] font-bold text-orange-600 uppercase tracking-tighter">Retorno (Opcional)</span>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Calendar class="w-4 h-4 text-orange-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-orange-500 uppercase tracking-widest leading-none mb-1">Fecha Limite</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loanInformacion?.fecha_retorno_prevista }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Clock class="w-4 h-4 text-orange-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-orange-500 uppercase tracking-widest leading-none mb-1">Hora Fin</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loanInformacion?.hora_fin_prevista }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                                <span class="text-[13px] font-bold text-green-600 uppercase tracking-tighter">Retorno Real</span>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Calendar class="w-4 h-4 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-green-500 uppercase tracking-widest leading-none mb-1">Fecha Retorno</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loanInformacion?.fecha_retorno }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white rounded-lg shadow-sm">
                                        <Clock class="w-4 h-4 text-green-600" />
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-green-500 uppercase tracking-widest leading-none mb-1">Hora Entrada</p>
                                        <p class="text-sm font-bold text-neutral-800">{{ loanInformacion?.hora_fin }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 mb-2">
                        <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                            <Package class="w-4 h-4" /> Estado Final de Equipos y Herramientas
                        </h3>

                        <div v-for="item in loanInformacion?.all_items" :key="item.id"
                            class="border border-neutral-200 rounded-2xl overflow-hidden shadow-sm bg-white mb-3 transition-all hover:border-neutral-300">

                            <div class="flex items-center justify-between p-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-neutral-50 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0 shadow-inner">
                                        <img v-if="item.foto" :src="'/storage/' + item.foto" class="object-cover w-full h-full" />
                                        <Image v-else class="w-7 h-7 text-neutral-300" />
                                    </div>

                                    <div class="flex flex-col gap-1">
                                        <p class="text-[15px] font-bold text-neutral-900 leading-tight">{{ item.nombre_mostrar }}</p>
                                        <span :class="[
                                            'w-fit px-2 py-0.5 rounded-full text-[10px] font-black uppercase border leading-none',
                                            item.es_equipo ? 'bg-red-50 text-red-600 border-red-100' : 'bg-blue-50 text-blue-600 border-blue-100'
                                        ]">
                                            {{ item.es_equipo ? 'Equipo' : 'Herramienta' }}
                                        </span>
                                        <span v-if="item.codigo_qr" class="text-[12px] font-mono text-blue-400">
                                            #{{ item.codigo_qr }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-1">
                                    <span :class="['px-3 py-1 rounded-lg text-[11px] font-black uppercase border shadow-sm', statusColor(item.estado_devolucion)]">
                                        {{ item.estado_devolucion }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="item.accessories && item.accessories.length > 0" class="bg-neutral-50/50 p-4 border-t border-neutral-100">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="h-px flex-1 bg-neutral-200"></div>
                                     <p class="text-[11px] font-black text-neutral-800 uppercase tracking-widest mb-2 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span> Accesorios del Equipo
                                    </p>
                                    <div class="h-px flex-1 bg-neutral-200"></div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div v-for="acc in item.accessories" :key="acc.id"

                                        class="flex items-center justify-between bg-white px-3 py-1.5 rounded-lg border border-neutral-200/60 shadow-sm">
                                        <span class="text-[13px] font-medium text-neutral-800 flex items-center gap-1">
                                            <CornerDownRight class="w-4 h-4 text-blue-400"/>  {{ acc.nombre_accesorio }}
                                        </span>
                                        <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', statusColorA(acc.estado_accesorio?? '')]">
                                            {{ acc.estado_accesorio }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                        <h3 class="text-[13px] font-black uppercase tracking-widest text-amber-600 flex items-center gap-2">
                            <AlignLeft class="w-4 h-4 text-amber-600"/>  Observación Final
                        </h3>
                        <p class="text-sm text-neutral-700 italic">{{ loanInformacion?.observacion || 'Sin observaciones.' }}</p>
                    </div>
                </div>

                <div class="p-6 bg-white border-t border-neutral-100 flex gap-4 no-print">
                    <Button variant="outline" class="flex-1 py-7 rounded-2xl font-bold uppercase tracking-widest text-[15px]" @click="closeViewInformacion">
                        Cerrar
                    </Button>
                    <Button class="flex-1 py-7 bg-black text-white rounded-2xl font-bold gap-2 uppercase tracking-widest text-[15px]" @click="imprimirReporte">
                        <Printer class="w-5 h-5" /> Imprimir
                    </Button>
                </div>
            </div>
        </div>
        -->

    </AppLayout>
</template>
