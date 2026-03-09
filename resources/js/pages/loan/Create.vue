<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import loanRoutes from '@/routes/loans';
import { ArrowLeft, Save, Loader2, Cog, Package, Search, ClipboardPen, XCircle, User, Calendar, Clock,
    ClockAlert, CalendarClock, CalendarCheck2, ClipboardCheck, BookMarked } from 'lucide-vue-next';

const props = defineProps<{
    borrowers: Array<any>;
    items: Array<any>;
    subjects: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nuevo Préstamo',
        href: loanRoutes.create.url(),
    },
];

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

const form = useForm({
    borrower_id: '',
    subject_id: '',
    items: [] as Array<{ id: number; tipo: string }>,
    fecha_salida: today,
    hora_inicio: now,
    hora_fin_prevista: '',
    fecha_retorno_prevista: '',
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

        // Si pasa de 24, vuelve a empezar (ej. 23 + 2 = 01)
        if (calculatedHour >= 24) calculatedHour -= 24;

        const endHour = String(calculatedHour).padStart(2, '0');

        // Asignamos directamente al form
        form.hora_fin_prevista = `${endHour}:${m}`;
        console.log("Hora fin calculada:", form.hora_fin_prevista); // Mira tu consola (F12) para probar
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
        // 1. El estado ya viene mapeado como 'estado_mostrar' desde el controlador
        const estado = item.estado_mostrar;

        // 2. Solo permitir 'Disponible' o 'Nuevo'
        const esValido = estado === 'Disponible' || estado === 'Nuevo';

        // 3. Aplicar filtro de búsqueda
        const search = searchTerm.value.toLowerCase();

        // Usamos 'nombre_mostrar' y buscamos también en la marca si existe
        const nombre = (item.nombre_mostrar || '').toLowerCase();
        const marca = (item.marca || item.marca_modelo || '').toLowerCase();

        const coincideBusqueda =
            nombre.toLowerCase().includes(search) ||
            (marca || '').toLowerCase().includes(search);

        return esValido && coincideBusqueda;
    });
});

// Función para obtener el estado sin importar el tipo
const getItemStatus = (item: any) => item.estado_mostrar || 'Desconocido';

// Función para saber si está disponible (Opcional: para bloquear selección)
const isAvailable = (item: any) => {
    const status = getItemStatus(item);
    return status === 'Disponible' || status === 'Nuevo';
};

// Función auxiliar para saber si un item está seleccionado (para la clase CSS)
const isSelected = (item: any) => {
    return form.items.some((i: any) => i.id === item.id && i.tipo === item.tipo);
};

const toggleItemSelection = (item: any) => {
    // Buscamos si ya está en el array
    const index = form.items.findIndex(i => i.id === item.id && i.tipo === item.tipo);

    if (index > -1) {
        // Si existe, lo quitamos
        form.items.splice(index, 1);
    } else {
        // Si no existe, agregamos el objeto completo
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

</script>

<template>
    <Head title="Nuevo Préstamo" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-5xl mx-auto p-4 w-full">
            <div class="flex justify-between items-center mb-6">
                <Link :href="loanRoutes.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black transition-colors">
                    <ArrowLeft class="w-4 h-4 mr-1"/> Volver a préstamos
                </Link>
                <button v-if="form.borrower_id || form.subject_id" @click="resetFilters" class="text-[12px] font-black uppercase text-red-500 flex items-center gap-1 hover:text-red-700 transition-colors">
                    <XCircle class="w-4 h-4"/> Limpiar selección
                </button>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-neutral-50 p-6 rounded-3xl border border-neutral-200 space-y-2 shadow-sm">
                        <h3 class="font-bold text-lg border-b border-neutral-200 pb-3 flex items-center text-neutral-800">
                            <ClipboardPen class="w-5 h-5 mr-2 text-blue-500"/> Información
                        </h3>

                        <!--Logica con el seleccionador -->
                        <div class="grid gap-2">
                            <Label for="borrower_id" class="text-[13px] font-black uppercase text-neutral-800 tracking-wider flex items-center gap-1"><User class="w-4 h-4 text-neutral-700" /> Responsable</Label>
                            <select v-model="form.borrower_id"
                            class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="" disabled>Seleccione un docente/auxiliar</option>
                                <option v-for="b in filteredBorrowers" :key="b.id" :value="b.id">
                                    {{ b.apellidosP }} {{ b.nombresP }}
                                </option>
                            </select>
                            <InputError :message="form.errors.borrower_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="subject_id" class="text-[13px] font-black uppercase text-neutral-800 tracking-wider flex items-center gap-1"><BookMarked class="w-4 h-4 text-neutral-700" />Materia</Label>
                            <select v-model="form.subject_id" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm disabled:bg-neutral-50">
                                <option value="" disabled>Seleccione la materia</option>
                                <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">
                                    {{ s.sigla }} - {{ s.nombre_materia }}
                                </option>
                            </select>
                            <InputError :message="form.errors.subject_id" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 ">
                            <div class="space-y-2">
                                <Label for="fecha_salida" class="flex items-center gap-1 text-[13px] font-black uppercase text-orange-500 tracking-wider">
                                    <Calendar class="w-3.5 h-3.5" />
                                    <span>Fecha Salida</span>
                                </Label>
                                <Input v-model="form.fecha_salida" type="date" readonly class="rounded-xl border-neutral-200 bg-neutral-100 h-10 text-xs px-2 cursor-not-allowed w-full" />
                                <InputError :message="form.errors.fecha_salida" />
                            </div>
                            <div class="space-y-2">
                                <Label for="hora_inicio" class="flex items-center gap-1.5 text-[13px] font-black uppercase text-blue-500 tracking-wider">
                                    <Clock class="w-3.5 h-3.5" />
                                    <span>Hora Inicio</span>
                                </Label>
                                <Input v-model="form.hora_inicio" type="time" readonly class="rounded-xl border-neutral-200 bg-neutral-100 h-10 text-xs px-2 cursor-not-allowed w-full" />
                                <InputError :message="form.errors.hora_inicio" />
                            </div>
                        </div>

                        <h3 class="text-[13px] font-black uppercase text-neutral-800 tracking-wider flex items-center gap-1">
                            <CalendarClock class="w-4 h-4 text-neutral-700" /> Retorno (Opcional)
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="fecha_retorno_prevista" class="flex items-center gap-1 text-[13px] font-black uppercase text-orange-500 tracking-wider">
                                    <CalendarCheck2 class="w-3.5 h-3.5" />
                                    <span>F. Retorno</span>
                                </Label>
                                <Input v-model="form.fecha_retorno_prevista" type="date":min="form.fecha_salida" class="rounded-xl border-neutral-300 h-10 text-xs" />
                                <InputError :message="form.errors.fecha_retorno_prevista" />
                            </div>
                            <div class="space-y-2">
                                <Label for="hora_fin_prevista" class="flex items-center gap-1.5 text-[13px] font-black uppercase text-blue-500 tracking-wider">
                                    <ClockAlert class="w-3.5 h-3.5" />
                                    <span>H. Retorno</span>
                                </Label>
                                <Input v-model="form.hora_fin_prevista" type="time":min="form.hora_inicio" class="rounded-xl border-neutral-300 h-10 text-xs" />
                                <InputError :message="form.errors.hora_fin_prevista" />
                            </div>
                        </div>
                    </div>
                    <Button type="submit"
                        class="w-full py-6 text-xl font-black uppercase tracking-widest shadow-xl shadow-blue-100 transition-all active:scale-[0.98]"
                        :disabled="form.processing || form.items.length === 0">
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-5 w-5 animate-spin" /> Procesando...
                        </template>
                        <template v-else>
                            Finalizar Préstamo
                        </template>
                    </Button>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-neutral-200 shadow-sm min-h-[500px] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex items-center">
                                <Cog class="w-5 h-5 mr-2 text-orange-500"/> Seleccionar Equipos
                            </h3>
                            <span class="text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">
                                {{ form.items.length }} seleccionados
                            </span>
                        </div>

                        <div class="relative mb-4">
                            <Search class="absolute left-3 top-3 w-5 h-5 text-neutral-400" />
                            <input v-model="searchTerm" type="text" placeholder="Buscar equipo o herramienta disponible..."
                                class="pl-10 flex h-10 w-full rounded-md border border-input bg-neutral-50 px-3 py-2 text-sm shadow-sm transition-colors focus:bg-white" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            <div
                                v-for="item in filteredItems" :key="item.id + (item.equipment ? 'e' : 't')"
                                @click="toggleItemSelection(item)"
                                :class="['p-3 border rounded-xl cursor-pointer transition-all flex items-center gap-3',
                                    isSelected(item) ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-neutral-200 hover:border-neutral-400',
                                    !isAvailable(item) ? 'opacity-50 grayscale cursor-not-allowed' : '']"
                            >
                                <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-100 shadow-inner">
                                    <img v-if="item.foto" :src="'/storage/' + item.foto" class="object-cover w-full h-full" />
                                    <Package v-else class="w-6 h-6 text-neutral-300" />
                                </div>

                                <div class="flex-1">
                                    <p class="text-sm font-bold text-black leading-tight">
                                        {{ item.nombre_mostrar }}
                                    </p>
                                    <span :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-black uppercase border leading-none',
                                        item.equipment ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                                    ]">
                                        {{ item.tipo }}
                                    </span>
                                </div>

                                <div v-if="isSelected(item)" class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                    <Save class="w-3 h-3 text-white" />
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.items" class="mt-2" />
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
