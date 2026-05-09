<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { FileInput } from '@/components/ui/file-input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import SearchInput from '@/components/shared/SearchInput.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import loanRoutes from '@/routes/loans';
import { ArrowLeft, Save, Loader2, Cog, Settings, Image, Search, ClipboardPen, XCircle, User, Calendar, Clock,
    ClockAlert, CalendarClock, CalendarCheck2, ClipboardCheck, BookMarked, GraduationCap, UserPen, PenLine,
    FileText } from 'lucide-vue-next';

const props = defineProps<{
    borrowers: Array<any>;
    borrowersBloqueados: number[];
    items: Array<any>;
    subjects: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Préstamos', href: loanRoutes.index.url() },
    { title: 'Registrar Préstamo', href: loanRoutes.create.url() },
];

// Definimos los tipos de pestañas
const activeBorrowerTab = ref('personal'); // 'personal' para Docentes/Auxiliares, 'estudiante' para Estudiantes

const searchTerm = ref('');
const date = new Date();

// Extraemos año, mes y día de la hora LOCAL
const year = date.getFullYear();
const month = String(date.getMonth() + 1).padStart(2, '0'); // Los meses van de 0 a 11
const day = String(date.getDate()).padStart(2, '0');
// Formato final: YYYY-MM-DD
const today = `${year}-${month}-${day}`;
const todayP = new Date().toISOString().split('T')[0];
// Extraer hora y minutos locales
const hour = String(date.getHours()).padStart(2, '0');
const minutes = String(date.getMinutes()).padStart(2, '0');
// Formato final: HH:mm
const now = `${hour}:${minutes}`;

// 1. Definimos las opciones de las pestañas
const loansTabs = [
    { id: 'personal', label: 'Docente / Auxiliar', icon: User },
    { id: 'estudiante', label: 'Estudiante', icon: GraduationCap },
];

// 2. Cambiamos el nombre de la variable para que sea genérico (activeTab)
const activeTab = ref('personal');

// 3. Mantén el watch para actualizar el tipo en el form
watch(activeTab, (newVal) => {
    form.tipo_prestatario = newVal;
});

const form = useForm({
    tipo_prestatario: 'docente', // 'personal' o 'estudiante'
    borrower_id: '',
    cedula_identidad: '',
    nombres: '',
    apellidos: '',
    registro_universitario: '',
    motivo: '',
    archivo_nota: null as File | null,
    subject_id: '',
    items: [] as Array<{ id: number; tipo: string }>,
    fecha_salida: today,
    hora_inicio: now,
    fecha_retorno_prevista: '',
    hora_fin_prevista: '',
});

// Sincronización de pestañas y reseteo[cite: 7]
watch(activeTab, (newVal) => {
    form.tipo_prestatario = newVal === 'personal' ? 'docente' : 'estudiante';
    form.reset('borrower_id', 'cedula_identidad', 'nombres', 'apellidos', 'registro_universitario', 'motivo', 'archivo_nota');
});

// Computed: bloqueo de borrower con reposiciones pendientes
const borrowerSeleccionadoBloqueado = computed(() => {
    if (!form.borrower_id) return false;
    return props.borrowersBloqueados.includes(Number(form.borrower_id));
});
const isBloqueado = (id: number) => props.borrowersBloqueados.includes(Number(id));

// Auto-llenado para Docente/Auxiliar[cite: 7]
watch(() => form.borrower_id, (newId) => {
    if (newId && activeTab.value === 'personal') {
        const b = props.borrowers.find(x => x.id === newId);
        if (b) {
            form.cedula_identidad = b.cedula_identidad;
            form.nombres = b.nombres;
            form.apellidos = b.apellidos;
            form.tipo_prestatario = b.teacher ? 'docente' : 'auxiliar';
        }
    }
});

const getItemStatus = (item: any) => item.estado_mostrar || 'Desconocido';
const isAvailable = (item: any) => ['Disponible', 'Nuevo'].includes(getItemStatus(item));

// Sincroniza el tipo de prestatario con la pestaña activa
watch(activeBorrowerTab, (newTab) => {
    form.tipo_prestatario = newTab;
});

// FUNCIÓN PARA EL RELOJ EN TIEMPO REAL
let timerInterval: any;
const updateCurrentTime = () => {
    const nowLocal = new Date();
    const hh = String(nowLocal.getHours()).padStart(2, '0');
    const mm = String(nowLocal.getMinutes()).padStart(2, '0');
    form.hora_inicio = `${hh}:${mm}`;
};
onMounted(() => {
    timerInterval = setInterval(updateCurrentTime, 30000);
});
onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

// Logica de filtrado cruzado para la seleccion de materia y responsable
// Filtra materias
const filteredSubjects = computed(() => {
    if (!form.borrower_id) return props.subjects; // Si no hay responsable, mostrar todas

    const selected = props.borrowers.find(b => b.id === form.borrower_id);
    return selected?.teacher?.subjects || selected?.assistant?.subjects || [];
});

// Filtra responsables
const filteredBorrowers = computed(() => {
    if (!form.subject_id) return props.borrowers; // Si no hay materia, mostrar todos

    // Filtrar prestatarios que tengan la materia seleccionada
    return props.borrowers.filter(b => {
        const subs = b.teacher?.subjects || b.assistant?.subjects || [];
        return subs.some((s: any) => s.id === form.subject_id);
    });
});

watch(activeBorrowerTab, (newTab) => {
    form.tipo_prestatario = newTab === 'personal' ? 'docente' : 'estudiante';
    form.reset('borrower_id', 'cedula_identidad', 'nombres', 'apellidos', 'registro_universitario', 'archivo_nota');
});

// Limpiar el otro campo si la selección actual lo invalida
watch(() => form.borrower_id, (newId) => {
    if (newId && form.subject_id) {
        const isValid = filteredSubjects.value.some((s: any) => s.id === form.subject_id);
        if (!isValid) form.subject_id = '';
    }
});

watch(() => form.subject_id, (newSubId) => {
    if (newSubId && form.borrower_id) {
        const isValid = filteredBorrowers.value.some((b: any) => b.id === form.borrower_id);
        if (!isValid) form.borrower_id = '';
    }
});

watch(() => form.hora_inicio, (newTime) => {
    if (newTime) {
        const [h, m] = newTime.split(':');
        let calculatedHour = (parseInt(h) + 2);
        if (calculatedHour >= 24) calculatedHour -= 24;
        const endHour = String(calculatedHour).padStart(2, '0');
        form.hora_fin_prevista = `${endHour}:${m}`;
    }
}, { immediate: true }); // 'immediate' hace que calcule incluso al cargar la página

watch(() => form.fecha_salida, (newVal) => {
    if (newVal) {
        form.fecha_retorno_prevista = newVal;
    }
}, { immediate: true });

const resetFilters = () => {
    form.borrower_id = '';
    form.subject_id = '';
}

// 2. Filtrar ítems por el buscador
const filteredItems = computed(() => {
    return props.items.filter(item => {
        const search = searchTerm.value.toLowerCase();
        const nombre = (item.nombre_mostrar || '').toLowerCase();
        const marca = (item.marca || item.marca_modelo || '').toLowerCase();

        return nombre.includes(search) || marca.includes(search);
    });
});

// Función auxiliar para saber si un item está seleccionado (para la clase CSS)
const isSelected = (item: any) => {
    return form.items.some((i: any) => i.id === item.id && i.tipo === item.tipo);
};

const toggleItemSelection = (item: any) => {
    // Si no está disponible, no hacemos nada (evita la selección)
    if (!isAvailable(item)) return;

    const index = form.items.findIndex(i => i.id === item.id && i.tipo === item.tipo);
    if (index > -1) {
        form.items.splice(index, 1);
    } else {
        form.items.push({ id: item.id, tipo: item.tipo });
    }
};

function submit() {
    form.post(loanRoutes.store.url(), { // Usando tu objeto de rutas
        preserveScroll: true,
        onSuccess: () => {
            console.log("¡Éxito!");
            form.reset();
        },
        onError: (errors) => {
            console.log("Errores de validación:", errors);
        }
    });
}

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.archivo_nota = target.files[0];
    }
};
const canSubmit = computed(() => {
    if (borrowerSeleccionadoBloqueado.value) return false; // bloqueado por reposiciones pendientes
    const common = form.items.length > 0 && !!form.subject_id && !!form.fecha_retorno_prevista && !form.processing;
    if (activeTab.value === 'personal') return common && !!form.borrower_id;
    return common && !!form.cedula_identidad && !!form.registro_universitario && !!form.archivo_nota;
});
</script>

<template>
    <Head title="Registrar Préstamo" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-5xl mx-auto p-4 w-full">

            <div class="flex justify-between items-center mb-3">
                <Link :href="loanRoutes.index.url()" class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group">
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/> Volver a préstamos
                </Link>
                <button v-if="form.borrower_id || form.subject_id" @click="resetFilters" class="text-[15px] font-black uppercase text-red-500 flex items-center gap-1 hover:text-red-700 transition-colors">
                    <XCircle class="w-5 h-5"/> Reiniciar Formulario
                </button>
            </div>

            <TabSelector
                :tabs="loansTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-neutral-50 p-6 rounded-3xl border border-neutral-200 space-y-2 shadow-sm">
                        <h3 class="font-bold text-lg border-b border-neutral-200 pb-3 flex items-center text-[#1a3a5a]">
                            <ClipboardPen class="w-5 h-5 mr-2 text-[#1a3a5a]"/> Información
                        </h3>

                        <div class="max-h-[330px] overflow-y-auto pr-2 custom-scrollbar">
                            <div v-if="activeTab === 'personal'" class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                                <div class="grid gap-2 mt-1">
                                    <Label for="borrower_id" class="mb-1 flex items-center gap-2 font-black text-[#1a3a5a]">
                                        <User class="w-4 h-4 text-[#1a3a5a]" /> Responsable
                                    </Label>
                                    <select v-model="form.borrower_id"
                                        :class="[
                                            'flex h-10 w-full rounded-md border bg-white px-3 py-2 text-sm',
                                            borrowerSeleccionadoBloqueado
                                                ? 'border-red-400 ring-1 ring-red-300 focus:border-red-500'
                                                : 'border-input'
                                        ]"
                                    >
                                        <option value="" disabled>Seleccionar un docente/auxiliar</option>
                                        <option
                                            v-for="b in filteredBorrowers"
                                            :key="b.id"
                                            :value="b.id"
                                            :disabled="isBloqueado(b.id)"
                                        >
                                            {{ isBloqueado(b.id) ? '🔒 ' : '' }}{{ b.apellidos }} {{ b.nombres }}{{ isBloqueado(b.id) ? ' — BLOQUEADO' : '' }}
                                        </option>
                                    </select>

                                    <!-- Aviso de bloqueo -->
                                    <div v-if="borrowerSeleccionadoBloqueado"
                                        class="flex items-start gap-2 mt-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                        <span class="text-red-500 text-base shrink-0">⚠️</span>
                                        <div>
                                            <p class="text-xs font-black text-red-700 uppercase tracking-wide">Préstamo bloqueado</p>
                                            <p class="text-xs text-red-600 mt-0.5">
                                                Este responsable tiene reposiciones pendientes. Debe resolverlas antes de realizar un nuevo préstamo.
                                            </p>
                                        </div>
                                    </div>

                                    <InputError :message="form.errors.borrower_id" />
                                </div>
                            </div>

                            <div v-else class="animate-in fade-in slide-in-from-bottom-2 duration-500">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="cedula_identidad" class="flex items-center gap-2 font-black text-[#1a3a5a]">
                                            <PenLine class="w-4 h-4 text-[#1a3a5a]"/> Carnet de Identidad
                                        </Label>
                                        <Input v-model="form.cedula_identidad" placeholder="C.I." />
                                        <InputError :message="form.errors.cedula_identidad" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="registro_universitario" class="flex items-center gap-2 font-black text-[#1a3a5a]">
                                            <PenLine class="w-4 h-4 text-[#1a3a5a]"/> Registro Universitario
                                        </Label>
                                        <Input v-model="form.registro_universitario" placeholder="R.U." />
                                        <InputError :message="form.errors.registro_universitario" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="nombre" class="flex items-center gap-2 font-black text-[#1a3a5a]">
                                            <UserPen class="w-4 h-4 text-[#1a3a5a]"/> Nombre(s)
                                        </Label>
                                        <Input id="nombres" v-model="form.nombres" placeholder="Nombre(s)"/>
                                        <InputError :message="form.errors.nombres" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="apellidos" class="flex items-center gap-2 font-black text-[#1a3a5a]">
                                            <UserPen class="w-4 h-4 text-[#1a3a5a]"/> Apellido(s)
                                        </Label>
                                        <Input id="apellidos" v-model="form.apellidos" placeholder="Apellido(s)" />
                                        <InputError :message="form.errors.apellidos" />
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="motivo" class="flex items-center gap-2 font-black text-[#1a3a5a]">
                                        <PenLine class="w-4 h-4 text-[#1a3a5a]"/> Motivo de solicitud
                                    </Label>
                                    <Input v-model="form.motivo" placeholder="Ej: Proyecto de Grado - Taller II" class="border-blue-200 rounded-xl" />
                                    <InputError :message="form.errors.motivo" />
                                </div>

                                <div class="grid gap-2">
                                    <Label class="mb-1 flex items-center gap-2 font-black text-[#1a3a5a]">
                                        <FileText class="w-4 h-4 text-[#1a3a5a]"/> Autorización de Dirección
                                    </Label>
                                    <div class="flex flex-col md:flex-row items-center gap-6">
                                        <div class="flex-1 space-y-2">
                                            <FileInput type="file" @change="(e: Event) => form.archivo_nota = (e.target as HTMLInputElement).files?.[0] || null"
                                            accept="application/pdf" />
                                            <p class="text-[13px] text-neutral-700 leading-tight">Adjuntar Nota (PDF). Máximo 2MB</p>
                                            <InputError :message="form.errors.archivo_nota" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--Logica con el seleccionador -->
                            <div class="space-y-2 grid gap-2 mt-3">
                                <Label for="subject_id" class="mb-1 flex items-center gap-2 font-black text-[#1a3a5a]">
                                    <BookMarked class="w-4 h-4 text-[#1a3a5a]" />Materia
                                </Label>
                                <select v-model="form.subject_id" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm disabled:bg-neutral-50">
                                    <option value="" disabled>Seleccionar la materia</option>
                                    <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">
                                        {{ s.sigla }} - {{ s.nombre_materia }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.subject_id" />
                            </div>

                            <div class="space-y-2 grid grid-cols-2 gap-4 mt-2">
                                <div class="space-y-2">
                                    <Label for="fecha_salida" class="mb-2 flex items-center gap-2 text-blue-700 tracking-wider font-black">
                                        <Calendar class="w-4 h-4" />
                                        <span>Fecha Salida</span>
                                    </Label>
                                    <Input v-model="form.fecha_salida" type="date" readonly class="rounded-xl border-neutral-200 bg-neutral-100 h-10 px-2 cursor-not-allowed w-full" />
                                    <InputError :message="form.errors.fecha_salida" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="hora_inicio" class="mb-2 flex items-center gap-2 text-blue-700 tracking-wider font-black">
                                        <Clock class="w-4 h-4" />
                                        <span>Hora Inicio</span>
                                    </Label>
                                    <Input v-model="form.hora_inicio" type="time" readonly class="rounded-xl border-neutral-200 bg-neutral-100 h-10 px-2 cursor-not-allowed w-full" />
                                    <InputError :message="form.errors.hora_inicio" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="fecha_retorno_prevista" class="mb-2 flex items-center gap-2 text-orange-700 tracking-wider font-black">
                                        <CalendarClock class="w-4 h-4" />
                                        <span>Fecha Retorno</span>
                                    </Label>
                                    <Input v-model="form.fecha_retorno_prevista" type="date":min="form.fecha_salida" />
                                    <InputError :message="form.errors.fecha_retorno_prevista" />
                                </div>
                                <div class="space-y-2">
                                    <Label for="hora_fin_prevista" class="mb-2 flex items-center gap-2 text-orange-700 tracking-wider font-black">
                                        <ClockAlert class="w-4 h-4" />
                                        <span>Hora Retorno</span>
                                    </Label>
                                    <Input v-model="form.hora_fin_prevista" type="time":min="form.hora_inicio"/>
                                    <InputError :message="form.errors.hora_fin_prevista" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <Button
                        type="submit"
                        :disabled="!canSubmit"
                        class="py-6 text-[20px] font-semibold text-white shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full"
                        :class="!canSubmit">
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-5 w-5 animate-spin" /> Procesando...
                        </template>
                        <template v-else>
                            <Save class="w-5 h-5 mr-2" /> Confirmar Préstamo
                        </template>
                    </Button>
                </div>

                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-neutral-200 shadow-sm min-h-[500px] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex items-center">
                                <Cog class="w-5 h-5 mr-2 text-[#1a3a5a]"/> Seleccionar Equipos
                            </h3>
                            <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[13px] font-black text-[#1a3a5a]">
                                {{ form.items.length }} seleccionados
                            </span>
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                            <SearchInput v-model="searchTerm" placeholder="Buscar equipo o herramienta disponible..." class="flex-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[330px] overflow-y-auto pr-2 custom-scrollbar">
                            <div
                                v-for="item in filteredItems"
                                :key="item.id + (item.tipo === 'equipo' ? 'e' : 't')"
                                @click="toggleItemSelection(item)"
                                :class="['p-3 border rounded-xl transition-all flex items-center gap-3 relative',
                                    isSelected(item) ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-neutral-200 hover:border-neutral-400',
                                    !isAvailable(item) ? 'opacity-75 bg-neutral-50 cursor-not-allowed border-dashed border-orange-200' : 'cursor-pointer']"
                            >
                                <!-- Imagen del ítem -->
                                <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-100">
                                    <img
                                        v-if="item.foto"
                                        :src="'/storage/' + item.foto"
                                        class="object-cover w-full h-full"
                                        :class="!isAvailable(item) ? 'grayscale' : ''"
                                        alt="Foto del item"
                                    />
                                    <Image v-else class="w-6 h-6 text-neutral-300" />
                                </div>

                                <div class="flex-1">
                                    <p class="text-sm font-bold text-black leading-tight">
                                        {{ item.nombre_mostrar }}
                                    </p>

                                    <!-- Etiqueta de tipo -->
                                    <span :class="[
                                        'px-2 py-0.5 rounded-full text-[9px] font-black uppercase border transition-colors',
                                        item.tipo?.toLowerCase() === 'equipo' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100'
                                    ]">
                                        {{ item.tipo }}
                                    </span>

                                    <!-- MENSAJES DE ESTADO NO DISPONIBLE -->
                                    <div v-if="!isAvailable(item)" class="mt-1">
                                        <!-- Caso Mantenimiento (Proviene de la tabla maintenances)[cite: 6] -->
                                        <div v-if="getItemStatus(item) === 'Mantenimiento'" class="mt-1 flex flex-col">
                                            <span class="text-[10px] font-black text-orange-600 uppercase flex items-center gap-1">
                                                <Settings class="w-3 h-3" /> Mantenimiento
                                            </span>
                                            <!-- Se agrega la hora con un guion -->
                                            <span v-if="item.fecha_retorno_estimado" class="text-[11px] text-neutral-700 italic">
                                                Disponible el: {{ item.fecha_retorno_estimado }} - {{ item.hora_fin_estimado }}
                                            </span>
                                        </div>

                                        <!-- Caso Prestado (Proviene de la tabla loans)[cite: 2] -->
                                        <!-- Mensaje para cuando está PRESTADO (Equipos y Herramientas) -->
                                        <!-- Caso Prestado -->
                                        <div v-else-if="getItemStatus(item) === 'Prestado'" class="flex flex-col">
                                            <span class="text-[10px] font-black text-red-600 uppercase flex items-center gap-1">
                                                <ClockAlert class="w-3 h-3" /> Prestado
                                            </span>
                                            <!-- Código actualizado con la hora -->
                                            <span v-if="item.fecha_disponible" class="text-[11px] text-neutral-700 italic">
                                                Disponible: {{ item.fecha_disponible }} - {{ item.hora_fin_prevista }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Indicador de selección -->
                                <div v-if="isSelected(item)" class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                    <Save class="w-3 h-3 text-white" />
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.items"/>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
