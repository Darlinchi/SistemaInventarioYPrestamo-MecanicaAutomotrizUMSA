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
import { Plus, Wrench, Building2, Calendar, Clock, Search, Package, CheckCircle, SquarePen, XIcon, History,
    Loader2, Eraser, ClipboardCheck, FileClock, FileCheck } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mantenimientos',
        href: maintenancesRoutes.index.url(),
    },
];

const props = defineProps<{
    maintenances: Array<any>; // Recibidos del controlador
}>();

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
            const nombreEquipo = maint.equipment.item.nombre_item.toLowerCase();
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
            const date = new Date(maint.fecha_mantenimientoS);
            // date.getMonth() devuelve 0-11, por eso sumamos 1
            return (date.getMonth() + 1).toString() === filterMonth.value;
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
    hora_fin: '',
    estado_equipo: 'Disponible',
    observacion: '',
});

// Funcion para abrir el modal (preguntar si le gustaria si un accesorio esta mal el equipo completo marcarse como dañado)
// --- FUNCIONES DEL MODAL ---
const openReturnModal = (maint: any) => {
    selectedMaint.value = maint;
    returnForm.hora_fin = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    returnForm.estado_equipo = 'Disponible';
    returnForm.observacion = '';
    isReturnModalOpen.value = true;
};

const processReturn = () => {
    // Obtenemos la hora actual en formato 24h (HH:mm:ss)
    const date = new Date();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    // Asignamos el formato que MySQL acepta: "15:38:00"
    returnForm.hora_fin = `${hours}:${minutes}:${seconds}`;

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

</script>

<template>
    <Head title="Mantenimientos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Control de Mantenimiento</h1>
                    <p class="text-sm text-neutral-500">Gestione el mantenimiento de equipos </p>
                </div>
                <Link :href="maintenancesRoutes.create.url()" class="bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-neutral-800 transition shadow-lg">
                    <Plus class="w-5 h-5"/> Nueva Actividad de Mantenimiento
                </Link>
            </div>

            <div class="flex p-1 bg-neutral-100 rounded-xl w-fit mb-6 border border-neutral-200">
                <button @click="activeTab = 'proceso'"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    activeTab === 'proceso' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <FileClock class="w-4.5 h-4.5 "/>En Proceso ({{ countEnProceso }})
                </button>
                <button @click="activeTab = 'completados'"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    activeTab === 'completados' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <FileCheck class="w-4.5 h-4.5 "/>Completados ({{ countCompletado }})
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-4 mb-6">
                <div class="relative w-full md:w-80">
                    <Search class="absolute left-3 top-3 w-5 h-5 text-neutral-400" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por empresa, equipo o serie..."
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

                <select v-model="selectedCompany" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600 md:w-45">
                    <option value="">Todas las Empresas</option>
                    <option v-for="company in uniqueCompanies" :key="company.id" :value="company.nombre">
                        {{ company.nombre }}
                    </option>
                </select>

                <select v-model="filterMonth" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600 md:w-40">
                    <option value="">Todos los Meses</option>
                    <option v-for="month in months" :key="month.id" :value="month.id.toString()">
                        {{ month.name }}
                    </option>
                </select>

                <div class="space-y-2">
                    <Input type="date" title="Fecha Específica" v-model="filterDate" class="bg-neutral-100 border border-neutral-500 rounded-xl text-sm py-2 px-4 focus:ring-2 focus:ring-black cursor-pointer text-neutral-600" />
                </div>

                <Button @click="() => { filterDate=''; filterMonth=''; selectedCompany=''; searchQuery='' }" title="Limpiar Filtros" variant="outline" class="rounded-xl border-dashed border-neutral-300 hover:bg-red-50 hover:text-red-600 transition-colors">
                    <Eraser class="w-4 h-4 mr-2"/> Limpiar Filtros
                </Button>
            </div>

            <div class="space-y-4">
                <div v-if="filteredMaintenances.length === 0" class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
                    <Package class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium">No se encontraron registros en esta sección.</p>
                </div>

                <div v-if="activeTab === 'proceso'" class="space-y-4" >
                    <div v-for="maint in filteredMaintenances" :key="maint.id"
                    class="group border border-blue-100 bg-blue-50/50 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center transition-all duration-300 shadow-sm hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full items-center">
                            <div class="col-span-1">
                                <div class="flex gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-white border border-neutral-100 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 transition-transform overflow-hidden">
                                        <img
                                            v-if="maint.equipment.item.foto"
                                            :src="'/storage/' + maint.equipment.item.foto"
                                            class="w-full h-full object-cover"
                                            alt="Foto del equipo"
                                        />
                                        <Wrench
                                            v-else
                                            :class="['w-7 h-7', activeTab === 'proceso' ? 'text-blue-500' : 'text-neutral-400']"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs text-neutral-800 uppercase font-bold mb-1">Equipo</p>
                                        <p class="text-base font-bold text-black truncate">{{ maint.equipment.item.nombre_item }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span v-if="maint.equipment.codigo_qr" class="text-[13px] bg-neutral-100 text-blue-600 px-2 py-0.5 rounded font-mono font-bold uppercase">QR: {{ maint.equipment.codigo_qr }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white/50 p-3 rounded-2xl border border-transparent group-hover:border-neutral-100 transition-colors">
                                <p class="flex items-center gap-1 text-xs text-neutral-700 uppercase font-bold mb-1">
                                    <Building2 class="w-4 h-4 text-green-600"/>
                                    <span>Empresa Encargada</span>
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <p class="text-sm font-extrabold text-neutral-800">{{ maint.companies[0]?.nombre_empresa || 'Empresa No Registrada' }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <p class="text-xs text-neutral-700 uppercase font-bold mb-1">Fecha y Hora de Inicio</p>
                                <div class="flex items-center gap-4">
                                    <span class="flex items-center gap-1.5 text-xs font-bold text-neutral-700">
                                        <Calendar class="w-4 h-4 text-neutral-800" /> {{ maint.fecha_mantenimiento }}
                                    </span>
                                    <span class="flex items-center gap-1.5 text-xs font-bold text-neutral-700">
                                        <Clock class="w-4 h-4 text-neutral-800" /> {{ maint.hora_inicio }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 ml-6">
                            <button @click="openReturnModal(maint)" class="flex items-center gap-2 bg-blue-600 border border-neutral-200 px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition text-white shadow-m">
                                <CheckCircle class="w-4 h-4"/> Completar
                            </button>
                        </div>
                    </div>
                </div>

                <!--HOSTORIAL DE MANTENIMIENTOS COMPLETADOS-->
                <div v-if="activeTab === 'completados'"
                    class="relative bg-white border border-neutral-200 rounded-xl shadow-sm overflow-x-auto">
                    <table class="w-full text-left min-w-max border-separate border-spacing-0">
                        <thead class="bg-neutral-200 border-b border-neutral-300 text-xs font-bold uppercase tracking-widest text-neutral-800">
                            <tr>
                                <th class="p-4">Fecha</th>
                                <th class="p-4">Emp. Encargada</th>
                                <th class="p-4">Equipo</th>
                                <th class="p-4">Hora Inicio</th>
                                <th class="p-4">Hora Fin</th>
                                <th class="p-4">Actividad</th>
                                <th class="p-4 text-right sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            <tr v-for="maint in filteredMaintenances" :key="maint.id" class="hover:bg-neutral-50 transition-colors group">
                                <td class="p-4 font-bold text-black">{{ maint.fecha_mantenimiento }}</td>
                                <td class="p-4 text-neutral-900">{{ maint.companies[0]?.nombre_empresa }}</td>
                                <td class="p-4 text-neutral-900">{{ maint.equipment.item.nombre_item }}</td>
                                <td class="p-4 text-neutral-900">{{ maint.hora_inicio }} </td>
                                <td class="p-4 text-neutral-900">{{ maint.hora_fin }} </td>
                                <td class="p-4 italic text-neutral-400 max-w-xs truncate">{{ maint.actividad }} </td>
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

            <div v-if="isReturnModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md p-6">
                <div class="bg-white w-full max-w-2xl rounded-4xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in zoom-in duration-300">

                    <div class="px-8 py-6 border border-neutral-100 flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-black uppercase tracking-tighter text-neutral-900">Finalizar Mantenimiento</h2>
                        </div>
                        <button @click="isReturnModalOpen = false" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                            <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500"/>
                        </button>
                    </div>

                    <div class="p-8 overflow-y-auto space-y-6 flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-2xl border border-neutral-200">
                            <div>
                                <p class="text-[11px] font-black text-neutral-700 uppercase tracking-widest">Equipo en Reparación</p>
                                <p class="text-base font-bold">{{ selectedMaint?.equipment.item.nombre_item }}</p>
                                <p class="text-xs text-blue-500">Código QR: {{ selectedMaint?.equipment.item.codigo_qr }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-neutral-700 uppercase tracking-widest">Taller Responsable</p>
                                <p class="text-base font-bold">{{ selectedMaint?.companies[0]?.nombre_empresa }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <div class="flex items-center gap-3">
                                <Calendar class="w-4 h-4 text-orange-600" />
                                <div>
                                    <p class="text-[11px] font-black text-orange-400 uppercase">Fecha</p>
                                    <p class="text-sm font-bold">{{ selectedMaint?.fecha_mantenimiento }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 border-l border-blue-100 pl-4">
                                <Clock class="w-4 h-4 text-blue-600" />
                                <div>
                                    <p class="text-[11px] font-black text-blue-400 uppercase">Inicio</p>
                                    <p class="text-sm font-bold">{{ selectedMaint?.hora_inicio }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 border-l border-blue-100 pl-4">
                                <History class="w-4 h-4 text-green-600" />
                                <div>
                                    <p class="text-[11px] font-black text-green-400 uppercase">Finalización</p>
                                    <p class="text-sm font-bold">{{ currentTime }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <Label class="text-[11px] font-black uppercase text-neutral-800">Estado del Equipo</Label>
                            <select v-model="returnForm.estado_equipo"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 text-sm font-bold focus:ring-black">
                                <option value="Disponible">Disponible (Reparado)</option>
                                <option value="Dañado">No Reparado (Dañado)</option>
                                <option value="Baja">Dar de Baja</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <Label class="text-[11px] font-black uppercase text-neutral-800">Actividad Desarrollada</Label>
                            <Textarea v-model="returnForm.observacion" placeholder="Detalle las reparaciones realizadas o el motivo del cambio de estado..." class="bg-neutral-50 rounded-2xl min-h-[100px]" />
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-white border-t border-neutral-100 flex gap-4">
                        <Button variant="outline" class="flex-1 py-6 rounded-2xl font-bold" @click="isReturnModalOpen = false">Cancelar</Button>
                        <Button class="flex-1 bg-black text-white py-6 rounded-2xl font-black shadow-xl" @click="processReturn" :disabled="returnForm.processing">
                            <Loader2 v-if="returnForm.processing" class="mr-2 animate-spin w-4 h-4"/> Confirmar Finalización
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
