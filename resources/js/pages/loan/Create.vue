<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { ref, computed, watch } from 'vue';
import loanRoutes from '@/routes/loans';
import { ArrowLeft, Save, Loader2, Cog, Package, Search, ClipboardPen, XCircle, User, Calendar, Clock,
    ClipboardCheck, BookMarked
 } from 'lucide-vue-next';

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
    items: [] as number[], // IDs de equipos/herramientas
    fecha_salida: today,
    hora_inicio: now,
    hora_fin_prevista: '',
    fecha_retorno_prevista: '',
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
        // 1. Obtener el estado real
        const estado = item.equipment?.estado_equipo || item.tool?.estado_herramienta;

        // 2. Solo permitir 'Disponible' o 'Nuevo'
        const esValido = estado === 'Disponible' || estado === 'Nuevo';

        // 3. Aplicar filtro de búsqueda por nombre o marca
        const search = searchTerm.value.toLowerCase();
        const coincideBusqueda =
            item.nombre_item.toLowerCase().includes(search) ||
            (item.equipment?.marca || '').toLowerCase().includes(search) ||
            (item.tool?.marca_modelo || '').toLowerCase().includes(search);

        return esValido && coincideBusqueda;
    });
});

// Función para obtener el estado sin importar el tipo
const getItemStatus = (item: any) => {
    if (item.equipment) return item.equipment.estado_equipo;
    if (item.tool) return item.tool.estado_herramienta;
    return 'Desconocido';
};
// Función para saber si está disponible (Opcional: para bloquear selección)
const isAvailable = (item: any) => {
    const status = getItemStatus(item);
    return status === 'Disponible' || status === 'Nuevo';
};

const toggleItemSelection = (id: number) => {
    const index = form.items.indexOf(id);
    if (index > -1) form.items.splice(index, 1);
    else form.items.push(id);
};
function submit() {
    form.post('/dashboard/loans', { // <--- Ruta manual temporal para probar
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

function submit2() {
    form.post('/dashboard/loans', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

</script>

<template>
    <Head title="Nuevo Préstamo" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-4xl mx-auto p-4 w-full">
            <div class="mb-4"> <!--arreglar lo de abajo -->
                <Link :href="loanRoutes.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-4 h-4 mr-1"/> Volver a préstamos
                </Link>
                <button v-if="form.borrower_id || form.subject_id" @click="resetFilters" class="text-[14px] font-black uppercase text-red-500 flex items-center gap-1 hover:underline">
                    <XCircle class="w-4 h-4"/> Limpiar selección
                </button>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                        <h3 class="font-bold text-lg border-b pb-2 flex items-center">
                            <ClipboardPen class="w-5 h-5 mr-2 text-blue-500"/> Información
                        </h3>

                        <!--Logica con el seleccionador -->
                        <div class="grid gap-2">
                            <Label><User class="w-4 h-4 text-neutral-700" /> Responsable</Label>
                            <select v-model="form.borrower_id" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="" disabled>Seleccione un docente/auxiliar</option>
                                <option v-for="b in filteredBorrowers" :key="b.id" :value="b.id">
                                    {{ b.apellidosP }} {{ b.nombresP }}
                                </option>
                            </select>
                            <InputError :message="form.errors.borrower_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label><BookMarked class="w-4 h-4 text-neutral-700" />Materia</Label>
                            <select v-model="form.subject_id" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm disabled:bg-neutral-50">
                                <option value="" disabled>Seleccione la materia</option>
                                <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">
                                    {{ s.sigla }} - {{ s.nombre_materia }}
                                </option>
                            </select>
                            <InputError :message="form.errors.subject_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="fecha_salida"><Calendar class="w-4 h-4 text-orange-400" />Fecha de Préstamo</Label>
                            <Input id="fecha_salida" v-model="form.fecha_salida" type="date":max="today"/>
                            <InputError :message="form.errors.fecha_salida" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="fecha_retorno_prevista"><Calendar class="w-4 h-4 text-orange-400" />Fecha Retorno Prevista</Label>
                            <Input id="fecha_retorno_prevista" v-model="form.fecha_retorno_prevista" type="date"/>
                            <InputError :message="form.errors.fecha_retorno_prevista" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="hora_inicio"><Clock class="w-4 h-4 text-blue-600" />Hora de Inicio</Label>
                            <Input id="hora_inicio" v-model="form.hora_inicio" type="time":max="now"/>
                            <InputError :message="form.errors.hora_inicio" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="hora_fin_prevista"><Clock class="w-4 h-4 text-blue-600" />Hora Fin Prevista</Label>
                            <Input id="hora_fin_prevista" v-model="form.hora_fin_prevista" type="time"/>
                            <InputError :message="form.errors.hora_fin_prevista" />
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm flex-1">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex items-center">
                                <Cog class="w-5 h-5 mr-2 text-orange-500"/> Seleccionar Equipos
                            </h3>
                            <span class="text-xs font-bold bg-blue-100 text-blue-600 px-3 py-1 rounded-full">
                                {{ form.items.length }} seleccionados
                            </span>
                        </div>

                        <div class="relative mb-4">
                            <Search class="absolute left-3 top-3 w-4 h-4 text-neutral-400" />
                            <input v-model="searchTerm" type="text" placeholder="Buscar equipo o herramienta disponible..."
                                class="pl-10 flex h-10 w-full rounded-md border border-input bg-neutral-50 px-3 py-2 text-sm shadow-sm transition-colors focus:bg-white" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                            <div
                                v-for="item in filteredItems" :key="item.id"
                                @click="toggleItemSelection(item.id)"
                                :class="['p-3 border rounded-xl cursor-pointer transition-all flex items-center gap-3',
                                    form.items.includes(item.id) ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-neutral-200 hover:border-neutral-400',
                                    !isAvailable(item) ? 'opacity-50 grayscale cursor-not-allowed' : '']"
                            >
                                <div class="w-10 h-10 rounded-lg bg-neutral-100 flex items-center justify-center overflow-hidden">
                                    <img v-if="item.foto" :src="'/storage/' + item.foto" class="object-cover w-full h-full" />
                                    <Package v-else class="w-5 h-5 text-neutral-400" />
                                </div>
                                <!--<div class="flex-1">
                                    <p class="text-sm font-bold text-black leading-none">{{ item.nombre_item }}</p>
                                    <p class="text-[10px] text-neutral-500 mt-1 uppercase">{{ item.estado }}</p>
                                </div>
                                -->
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-black leading-tight">{{ item.nombre_item }}</p>
                                    <p :class="['text-[10px] font-bold mt-1 uppercase px-2 py-0.5 rounded-full inline-block',
                                        isAvailable(item) ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                                        {{ getItemStatus(item) }}
                                    </p>
                                </div>
                                <div v-if="form.items.includes(item.id)" class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                    <Save class="w-3 h-3 text-white" />
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.items" class="mt-2" />
                    </div>

                    <Button type="submit" class="w-full py-8 text-xl font-black uppercase tracking-tighter shadow-lg shadow-blue-100" :disabled="form.processing || form.items.length === 0">
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-6 w-6 animate-spin" /> Procesando...
                        </template>
                        <template v-else>
                            Finalizar Préstamo
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
