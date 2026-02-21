<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Plus, Search, Edit, CheckCircle, Package, XIcon, List, Loader2, Clock, History, Calendar,
    User, SquarePen, Eraser, ClipboardPen, BookMarked } from 'lucide-vue-next';
import loanRoutes from '@/routes/loans';

interface Item {
    id: number;
    nombre_item: string;
    codigo_qr?: string;
    es_equipo: boolean;
    pivot?: { // Datos de la tabl item_loan
        estado_devolucion: string;
    }
}

interface Loan {
    id: number;
    fecha_salida: string;
    estado_prestamo: string;
    hora_inicio: string;
    hora_fin?: string;
    observacion?: string;
    borrower: any;
    subject: any;
    items: Item[];
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
const countActivos = computed(() => props.loans.filter(b => b.estado_prestamo == "Activo").length);
// La cantidad de prestamos devueltos hay para el historial
const countHistorial = computed(() => props.loans.filter(b => b.estado_prestamo == "Devuelto").length);

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

// Activar al entrar y desactivar al salir
onMounted(() => window.addEventListener('click', closePopovers));
onUnmounted(() => window.removeEventListener('click', closePopovers));

// Buscador y filtrado
// La pestaña activa (Activos vs Historial)
// El texto escrito en 'searchQuery'
const filteredLoans = computed(() => {
    // Se filtra por la pestaña seleccionada
    let filtered = props.loans.filter(loan => {
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
            const coincideItem = loan.items.some(item =>
                item.nombre_item.toLowerCase().includes(query) ||
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
            const date = new Date(loan.fecha_salida);
            // date.getMonth() devuelve 0-11, por eso sumamos 1
            return (date.getMonth() + 1).toString() === filterMonth.value;
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
    selectedLoan.value = loan;
    returnForm.items = loan.items.map((i: any) => ({
        id: i.id,
        nombre_item: i.nombre_item,
        estado_devolucion: 'Disponible',
        // Mapeo de los accesorios del equipo al formulario
        accessories: i.accessories ? i.accessories.map((acc: any) => ({
            id: acc.id,
            nombre_accesorio: acc.nombre_accesorio,
            estado_accesorio: acc.estado_accesorio
        })) : []
    }));
    // Inicializamos el formulario con la fecha y hora actual al abrir
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

</script>

<template>
    <Head title="Gestión de Préstamos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Gestión de Préstamos</h1>
                    <p class="text-sm text-neutral-500">Administre préstamos y devoluciones de equipos y herramientas del taller</p>
                </div>
                <Link :href="loanRoutes.create.url()" class="bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-neutral-800 transition shadow-lg">
                    <Plus class="w-5 h-5"/> Nuevo Préstamo
                </Link>
            </div>

            <div class="flex p-1 bg-neutral-100 rounded-xl w-fit mb-6 border border-neutral-200">
                <button @click="activeTab = 'activos'"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    activeTab === 'activos' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <ClipboardPen class="w-4.5 h-4.5 "/>Préstamos Activos ({{ countActivos }})
                </button>
                <button @click="activeTab = 'historial'"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    activeTab === 'historial' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <History class="w-4.5 h-4.5 "/>Historial de Devoluciones ({{ countHistorial }})
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="relative w-full md:w-80">
                    <Search class="absolute left-3 top-3 w-5 h-5 text-neutral-400" />

                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por item, materia o usuario..."
                        class="w-full pl-10 pr-12 py-3 bg-neutral-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-black transition"
                    />

                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
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

            <div class="space-y-4">
                <div v-if="filteredLoans.length === 0" class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
                    <Package class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium">No se encontraron préstamos con esos criterios.</p>
                </div>
                <div v-if="activeTab === 'activos'" class="space-y-4">
                    <div v-for="loan in filteredLoans" :key="loan.id"
                        class="group border border-blue-100 bg-blue-50/50 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center transition-all duration-300 shadow-sm hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-12 w-full">
                            <div class="col-span-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="bg-white px-3 py-1 rounded-full text-[12px] font-bold uppercase border border-blue-200">
                                        Items Prestados
                                    </span>
                                </div>

                                <div class="relative">
                                    <button
                                        @click.stop="toggleItems(loan.id)"
                                        class="flex items-center gap-2 px-3 py-1.5 bg-white border border-neutral-200 rounded-lg shadow-sm hover:bg-neutral-50 transition active:scale-95"
                                    >
                                        <List class="w-4 h-4 text-black"/>
                                        <span class="text-xs font-bold text-black">Ver {{ loan.items.length }} ítems</span>
                                    </button>

                                    <div
                                        v-if="openLoanId === loan.id"
                                        class="absolute left-0 z-50 mt-2 w-80 bg-white border border-neutral-200 rounded-xl shadow-2xl p-4 animate-in fade-in zoom-in duration-200"
                                    >
                                        <p class="text-[10px] font-black uppercase text-neutral-400 mb-3 tracking-widest">Equipos en este préstamo</p>
                                        <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                            <div
                                                v-for="item in loan.items"
                                                :key="item.id"
                                                class="flex items-center justify-between p-3 bg-neutral-50 border border-neutral-100 rounded-lg hover:border-blue-200 transition-colors"
                                            >
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-neutral-800">{{ item.nombre_item }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="mb-4">
                                    <p class="flex items-center gap-1 text-xs text-neutral-700 uppercase font-bold mb-1">
                                        <User class="w-4 h-4 text-green-600" />
                                        <span>Usuario</span>
                                    </p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan.borrower.nombresP }} {{ loan.borrower.apellidosP }}</p>
                                    <p class="text-xs text-neutral-500 italic">
                                        {{ loan.borrower.teacher ? 'Docente' : 'Auxiliar' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="flex items-center gap-1 text-xs text-neutral-700 uppercase font-bold mb-1">
                                        <Clock class="w-4 h-4 text-neutral-800" />
                                        <span>Hora de inicio</span>
                                    </p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan.hora_inicio }}</p>
                                </div>
                            </div>

                            <div>
                                <div class="mb-4">
                                    <p class="flex items-center gap-1 text-xs text-neutral-700 uppercase font-bold mb-1">
                                        <Calendar class="w-4 h-4 text-neutral-800" />
                                        <span>Fecha</span>
                                    </p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan.fecha_salida }}</p>
                                </div>
                                <div>
                                    <p class="flex items-center gap-1 text-xs text-neutral-700 uppercase font-bold mb-1">
                                        <BookMarked class="w-4 h-4 text-neutral-800" />
                                        <span>Materia y Sigla</span>
                                    </p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan.subject.nombre_materia }} - {{ loan.subject.sigla }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 ml-6">
                            <Link :href="loanRoutes.edit.url(loan.id)">
                                <button class="flex items-center gap-2 bg-white border border-neutral-200 px-4 py-2 rounded-lg text-xs font-bold hover:bg-neutral-50 transition shadow-sm">
                                    <Edit class="w-4 h-4" /> Editar
                                </button>
                            </Link>
                            <button @click="openReturnModal(loan)" class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-neutral-800 transition shadow-sm">
                                <CheckCircle class="w-4 h-4" /> Devolver
                            </button>
                        </div>
                    </div>
                </div>

                <!--HOSTORIAL DE DEVOLUCION-->
                <div v-if="activeTab === 'historial'"
                    class="relative bg-white border border-neutral-200 rounded-xl shadow-sm overflow-x-auto">
                    <table class="w-full text-left min-w-max border-separate border-spacing-0">
                        <thead class="bg-neutral-200 border-b border-neutral-300 text-xs font-bold uppercase tracking-widest text-neutral-800">
                            <tr>
                                <th class="p-4">Fecha</th>
                                <th class="p-4">C. Identidad</th>
                                <th class="p-4">Responsable</th>
                                <th class="p-4">Materia</th>
                                <th class="p-4">Hora Inicio</th>
                                <th class="p-4">Hora Fin</th>
                                <th class="p-4">Items Prestados</th>
                                <th class="p-4">Observación</th>
                                <th class="p-4 text-right sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            <tr v-for="loan in filteredLoans" :key="loan.id" class="hover:bg-neutral-50 transition-colors group">
                                <td class="p-4 font-bold text-black">{{ loan.fecha_salida }}</td>
                                <td class="p-4 text-neutral-900">{{ loan.borrower.cedula_identidad }} </td>
                                <td class="p-4 text-neutral-900">{{ loan.borrower.nombresP }} {{ loan.borrower.apellidosP }}</td>
                                <td class="p-4 text-neutral-900">{{ loan.subject.nombre_materia }} - {{ loan.subject.sigla }} </td>
                                <td class="p-4 text-neutral-900">{{ loan.hora_inicio }} </td>
                                <td class="p-4 text-neutral-900">{{ loan.hora_fin }} </td>
                                <td class="p-4 text-neutral-900">
                                    <div class="flex justify-end relative">
                                        <button @click.stop="toggleItems(loan.id)"
                                                class="flex items-center gap-2 px-4 py-2 bg-neutral-100 border border-neutral-200 rounded-xl text-xs font-bold hover:bg-neutral-200 transition">
                                            <List class="w-4 h-4" /> Detalle de Ítems
                                        </button>

                                        <div v-if="openLoanId === loan.id"
                                            class="absolute right-0 top-full z-50 mt-2 w-72 bg-white border border-neutral-200 rounded-xl shadow-2xl p-4 animate-in fade-in zoom-in duration-200">
                                            <p class="text-[10px] font-black uppercase text-neutral-400 mb-3 tracking-widest text-center">Estado al devolver</p>
                                            <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                                <div v-for="item in loan.items" :key="item.id"
                                                    class="flex items-center justify-between p-2.5 bg-neutral-50 border border-neutral-100 rounded-lg">
                                                    <span class="text-xs font-bold text-neutral-800">{{ item.nombre_item }}</span>
                                                    <!--<span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border">
                                                        {{ item.equipment ? 'Equipo' : 'Herramienta' }}
                                                    </span>
                                                    -->

                                                    <span :class="['px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border',
                                                        item.pivot?.estado_devolucion === 'Disponible' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200']">
                                                        {{ item.pivot?.estado_devolucion }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 italic text-neutral-400 max-w-xs truncate">{{ loan.observacion }} </td>
                                <td class="p-4 text-right space-x-3 sticky right-0 bg-white group-hover:bg-neutral-50 border-l border-neutral-100">
                                    <Link >
                                        <Button title="Generar Reporte" class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-blue-500"><SquarePen class="w-4.5 h-4.5 stroke-blue-500"/></Button>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="isReturnModalOpen" class="fixed inset-0 z-200 flex items-center justify-center bg-black/40 backdrop-blur-md p-6 lg:p-12">
                <div class="bg-white w-full max-w-2xl rounded-4xl  shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in zoom-in duration-300">

                    <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white">
                        <div>
                            <h2 class="text-2xl font-black uppercase tracking-tighter text-neutral-900">Registrar Devolución</h2>
                            <p class="text-xs text-neutral-800 font-medium">Verifique los datos y el estado de los equipos recibidos</p>
                        </div>
                        <button @click="isReturnModalOpen = false" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                            <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                        </button>
                    </div>

                    <div class="p-8 overflow-y-auto custom-scrollbar space-y-8 flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-2xl border border-neutral-200">
                            <div class="space-y-1">
                                <p class="text-[13px] font-black text-neutral-700 uppercase tracking-widest">Responsable</p>
                                <p class="text-base font-bold text-neutral-900">{{ selectedLoan?.borrower.nombresP }} {{ selectedLoan?.borrower.apellidosP }}</p>
                                <span class="inline-block px-2 py-0.5 rounded-md bg-blue-100 text-[13px] font-bold text-blue-700 uppercase tracking-tighter">
                                    {{ selectedLoan?.borrower.teacher ? 'Docente' : 'Auxiliar' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[13px] font-black text-neutral-700 uppercase tracking-widest">Materia Asignada</p>
                                <p class="text-base font-bold text-neutral-900">{{ selectedLoan?.subject.nombre_materia }}</p>
                                <p class="text-[14px]  text-neutral-500 font-mono">{{ selectedLoan?.subject.sigla }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-orange-50/50 rounded-2xl border border-orange-100 mt-4">
                            <div class="space-y-2 border-r border-orange-100 pr-4">
                                <p class="text-[11px] font-black text-orange-600 uppercase tracking-widest">Planificado (Tesis/Proyecto)</p>
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm font-bold text-neutral-800 flex items-center gap-2">
                                        <Calendar class="w-4 h-4" /> Limite: {{ selectedLoan?.fecha_retorno_prevista }}
                                    </p>
                                    <p class="text-sm font-bold text-neutral-800 flex items-center gap-2">
                                        <Clock class="w-4 h-4" /> Hora: {{ selectedLoan?.hora_fin_prevista }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2 pl-2">
                                <p class="text-[11px] font-black text-blue-600 uppercase tracking-widest">Registro Real de Entrada</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <Label class="text-[10px] uppercase font-bold text-neutral-500">Fecha Retorno</Label>
                                        <Input type="date" v-model="returnForm.fecha_retorno" class="h-8 text-xs rounded-lg" />
                                    </div>
                                    <div>
                                        <Label class="text-[10px] uppercase font-bold text-neutral-500">Hora Entrada</Label>
                                        <Input type="time" v-model="returnForm.hora_fin" class="h-8 text-xs rounded-lg" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-blue-50/50 rounded-2xl border border-blue-100 mt-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Calendar class="w-4 h-4 text-orange-600" />
                                </div>
                                <div>
                                    <p class="text-[13px] font-black text-orange-400 uppercase tracking-widest leading-none mb-1">Fecha</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ selectedLoan.fecha_salida }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 border-l border-blue-100 pl-4">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Clock class="w-4 h-4 text-blue-600" />
                                </div>
                                <div>
                                    <p class="text-[13px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Salida</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ selectedLoan?.hora_inicio }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 border-l border-blue-100 pl-4">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <History class="w-4 h-4 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-[13px] font-black text-green-400 uppercase tracking-widest leading-none mb-1">Retorno</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ currentTime }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <p class="text-[13px] font-black text-neutral-800 uppercase tracking-widest">Revisión Detallada</p>

                            <div v-for="(item, index) in returnForm.items" :key="item.id" class="border border-neutral-200 rounded-3xl overflow-hidden shadow-sm">
                                <div class="flex items-center justify-between p-5 bg-white">
                                    <div class="flex flex-col">
                                        <span class="text-[14px]  font-bold text-neutral-900">{{ item.nombre_item }}</span>
                                        <span v-if="item.accessories?.length" class="text-[10px] text-blue-600 font-black uppercase mt-0.5">
                                            Contiene {{ item.accessories.length }} accesorios
                                        </span>
                                    </div>
                                    <select v-model="returnForm.items[index].estado_devolucion"
                                        class="text-[14px]  font-bold rounded-xl border-neutral-200 bg-neutral-50 focus:ring-black focus:border-black transition-all">
                                        <option value="Disponible">Disponible</option>
                                        <option value="Dañado">Dañado</option>
                                        <option value="Extraviado">Extraviado</option>
                                        <option value="Baja">Baja</option>
                                    </select>
                                </div>

                                <div v-if="item.accessories?.length" class="bg-neutral-50 p-5 border-t border-neutral-100 space-y-4">
                                    <p class="text-[12px] font-black text-neutral-800 uppercase tracking-widest">Estado de los Accesorios:</p>
                                    <div v-for="(acc, accIndex) in item.accessories" :key="acc.id" class="flex items-center justify-between bg-white p-3 rounded-xl border border-neutral-100">
                                        <span class="text-[14px]  font-semibold text-neutral-600">↳ {{ acc.nombre_accesorio }}</span>
                                        <select v-model="returnForm.items[index].accessories[accIndex].estado_accesorio"
                                            class="text-[14px] py-1 px-2 border-neutral-100 rounded-lg bg-neutral-50 font-bold focus:ring-black">
                                            <option value="Bueno">Bueno</option>
                                            <option value="Dañado">Dañado</option>
                                            <option value="Extraviado">Extraviado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <Label class="text-[13px] font-black uppercase text-neutral-800 tracking-widest">Observación de recepción final</Label>
                            <Textarea v-model="returnForm.observacion"
                                placeholder="Escriba aquí notas adicionales sobre la devolución..."
                                class="bg-neutral-50 border-neutral-200 text-sm rounded-2xl min-h-[100px] focus:bg-white transition-all" />
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-white border-t border-neutral-100 flex gap-4">
                        <Button variant="outline" class="flex-1 py-7 rounded-2xl font-bold uppercase tracking-widest text-xs" @click="isReturnModalOpen = false">
                            Cerrar
                        </Button>
                        <Button class="flex-1 bg-black text-white py-7 rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl hover:shadow-neutral-200 transition-all active:scale-[0.98]"
                            @click="processReturn" :disabled="returnForm.processing">
                            <Loader2 v-if="returnForm.processing" class="mr-2 animate-spin w-4 h-4 text-white"/>
                            Confirmar Devolución
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
