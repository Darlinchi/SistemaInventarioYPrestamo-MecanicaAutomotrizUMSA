<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ref, computed } from 'vue';
import loanRoutes from '@/routes/loans';
import { ArrowLeft, Loader2, Package, Search, CheckCircle, User, Save, Trash2, Plus, Calendar, Clock,
    ClockAlert, CalendarCheck2, ClipboardCheck, BookMarked, Image, Wrench
 } from 'lucide-vue-next';

const props = defineProps<{
    loan: any;
    borrowers: any[];
    items: any[]; // Todos los items: disponibles + los que ya tiene el préstamo
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Préstamos', href: loanRoutes.index.url() },
    { title: 'Editar Préstamo', href: loanRoutes.create.url() },
];

const searchTerm = ref('');

interface SelectedItem {
    id: number;
    type: string;
}

// En Edit.vue
const form = useForm({
    borrower_id: props.loan?.borrower_id || '',
    subject_id: props.loan?.subject_id || '',
    selected_items: (props.loan?.all_items || []).map((i: any) => ({
        id: i.id,
        // Usamos el namespace completo que espera tu controlador en el método update
        type: i.es_equipo ? 'App\\Models\\Equipment' : 'App\\Models\\Tool'
    })),
});

// 2. Protege la búsqueda de items disponibles
const availableItemsForSearch = computed(() => {
    const items = props.items || []; // Si props.items es undefined, usa un array vacío
    return items.filter((item: any) =>
        !isSelected(item.id, item.es_equipo) &&
        (item.nombre_mostrar || '').toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

// 2. Función isSelected: Debe comparar con la misma lógica del formulario
const isSelected = (id: number, esEquipo: boolean) => {
    const type = esEquipo ? 'App\\Models\\Equipment' : 'App\\Models\\Tool';
    return form.selected_items.some((i: SelectedItem) => i.id === id && i.type === type);
};

// 3. selectedItemsList: Ahora filtrará correctamente los items de props.items
const selectedItemsList = computed(() => {
    const allItems = props.items || [];
    return allItems.filter((item: any) => isSelected(item.id, item.es_equipo));
});


// Agrega estas funciones dentro de tu script setup actual
const getItemStatus = (item: any) => item.estado_mostrar || 'Desconocido';

const isAvailable = (item: any) => {
    const status = getItemStatus(item);
    // En edición, un item es "disponible" si está disponible/nuevo O si ya pertenece a este préstamo
    return status === 'Disponible' || status === 'Nuevo' || isSelected(item.id, item.es_equipo);
};

// Actualiza el toggle para que no permita seleccionar items bloqueados
const toggleItemSelection = (item: any) => {
    if (!item || !isAvailable(item)) return; // BLOQUEO

    const type = item.es_equipo ? 'App\\Models\\Equipment' : 'App\\Models\\Tool';
    const index = form.selected_items.findIndex((i: SelectedItem) =>
        i.id === item.id && i.type === type
    );

    if (index > -1) {
        form.selected_items.splice(index, 1);
    } else {
        form.selected_items.push({ id: item.id, type: type });
    }
};


const submit = () => {
    form.put(loanRoutes.update.url(props.loan.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log("Préstamo actualizado con éxito");
        },
        onError: (errors) => {
            console.log("Errores detectados:", errors);
        }
    });
};

const canSubmit = computed(() => {
    return form.borrower_id && form.subject_id && form.selected_items.length > 0 && !form.processing;
});

</script>

<template>
    <Head title="Editar Préstamo" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-5xl mx-auto p-4 w-full">
            <div class="mb-4">
                <Link :href="loanRoutes.index.url()" class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group">
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/> Volver a préstamos
                </Link>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-neutral-50 p-6 rounded-3xl border border-neutral-200 space-y-6 shadow-sm">
                        <h3 class="font-bold text-lg border-b pb-2 flex items-center text-[#1a3a5a]">
                            <ClipboardCheck class="w-5 h-5 mr-2 text-[#1a3a5a]"/> Datos del Préstamo
                        </h3>

                        <div class="space-y-1">
                            <p class="flex items-center gap-1 text-[14px] font-black text-[#1a3a5a] tracking-wider">
                                <User class="w-4 h-4 text-[#1a3a5a]" />
                                <span>Responsable</span>
                            </p>
                            <p class="text-sm font-bold text-neutral-800">{{ loan.borrower.nombres }} {{ loan.borrower.apellidos }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="flex items-center gap-1 text-[14px] font-black text-[#1a3a5a] tracking-wider">
                                <BookMarked class="w-4 h-4 text-[#1a3a5a]" />
                                <span>Materia</span>
                            </p>
                            <p class="text-sm font-bold text-neutral-800">{{ loan.subject.nombre_materia }}</p>
                            <p class="text-xs font-black text-[#1a3a5a]">{{ loan.subject.sigla }}</p>
                        </div>

                        <div class="pt-4 border-t border-neutral-200 grid grid-cols-2 gap-4">
                            <div>
                                <p class="flex items-center gap-1 text-[14px] font-black text-orange-400 tracking-wider">
                                    <Calendar class="w-4 h-4 text-orange-400" />
                                    <span>Fecha Salida</span>
                                </p>
                                <p class="text-[13px] font-medium mt-1.5">{{ loan.fecha_salida }}</p>
                            </div>
                            <div>
                                <p class="flex items-center gap-1 text-[14px] font-black text-blue-600 tracking-wider">
                                    <Clock class="w-4 h-4 text-blue-600" />
                                    <span>Hora Salida</span>
                                </p>
                                <p class="text-[13px] font-medium mt-1.5">{{ loan.hora_inicio }}</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-neutral-200 grid grid-cols-2 gap-4">
                            <div>
                                <p class="flex items-center gap-1 text-[14px] font-black text-orange-400 tracking-wider">
                                    <CalendarCheck2 class="w-4 h-4 text-orange-400" />
                                    <span>F. Retorno</span>
                                </p>
                                <p class="text-[13px] font-medium mt-1.5">{{ loan.fecha_retorno_prevista }}</p>
                            </div>
                            <div>
                                <p class="flex items-center gap-1 text-[14px] font-black text-blue-600 tracking-wider">
                                    <ClockAlert class="w-4 h-4 text-blue-600" />
                                    <span>H. Retorno</span>
                                </p>
                                <p class="text-[13px] font-medium mt-1.5">{{ loan.hora_fin_prevista }}</p>
                            </div>
                        </div>
                    </div>

                    <Button type="submit"
                    class="w-full py-6 text-[20px] font-semibold text-white shadow-lg shadow-blue-900/20 transition-all active:scale-95"
                    :disabled="!canSubmit">
                    <!--:disabled="form.processing || form.selected_items.length === 0">-->
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-5 w-5 animate-spin" /> Guardando...
                        </template>
                        <template v-else>
                            <Save class="w-5 h-5 mr-2"/> Actualizar
                        </template>
                    </Button>
                </div>

                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-neutral-900 flex items-center">
                                <CheckCircle class="w-5 h-5 mr-2 text-[#1a3a5a]"/> Equipos y Herramientas en el Préstamo
                            </h3>
                            <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[13px] font-black text-[#1a3a5a]">
                                {{ form.selected_items.length }} seleccionados
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div v-for="item in selectedItemsList" :key="item.id"
                                class="flex items-center justify-between p-3 bg-blue-50/50 border border-blue-100 rounded-xl group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-white flex items-center justify-center border border-blue-100 overflow-hidden">
                                        <img
                                            v-if="item.foto_equipo || item.foto_herramienta || item.foto"
                                            :src="'/storage/' + (item.foto_equipo || item.foto_herramienta || item.foto)"
                                            class="object-cover w-full h-full"
                                        />
                                        <Image v-else class="w-4 h-4 text-blue-500" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[14px] font-bold text-neutral-800 leading-tight">{{ item.nombre_mostrar }}</p>
                                        <span :class="[
                                            'px-2 py-0.5 rounded-full text-[10px] font-black uppercase border leading-none',
                                            item.es_equipo ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                                        ]">
                                            {{ item.es_equipo ? 'Equipo' : 'Herramienta' }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="toggleItemSelection(item)" class="text-neutral-400 hover:text-red-500 p-2">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            <p v-if="selectedItemsList.length === 0" class="text-center py-4 text-xs text-neutral-400 italic">No hay equipos asignados</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
                        <h3 class="font-bold text-neutral-900 mb-4 flex items-center">
                            <Plus class="w-5 h-5 mr-2 text-blue-500"/> Agregar más equipos
                        </h3>

                        <div class="relative mb-4">
                            <Search class="absolute left-3 top-3 w-4 h-4 text-neutral-400" />
                            <input v-model="searchTerm" type="text" placeholder="Buscar en el inventario disponible..."
                                class="pl-10 flex h-10 w-full rounded-md border border-input bg-neutral-50 px-3 py-2 text-sm focus:bg-white transition-all" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <div
    v-for="item in availableItemsForSearch" :key="item.id"
    @click="toggleItemSelection(item)"
    :class="[
        'p-3 border rounded-xl transition-all flex items-center gap-3',
        !isAvailable(item) ? 'opacity-70 bg-neutral-50 cursor-not-allowed border-dashed' : 'cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 border-neutral-100'
    ]"
>
    <div class="w-10 h-10 rounded-lg bg-neutral-100 flex items-center justify-center overflow-hidden border">
        <img
            v-if="item.foto"
            :src="'/storage/' + item.foto"
            class="object-cover w-full h-full"
            :class="!isAvailable(item) ? 'grayscale' : ''"
        />
        <Image v-else class="w-5 h-5 text-neutral-400" />
    </div>
    <div class="flex-1">
        <p class="text-[15px] font-bold text-neutral-800 leading-tight">{{ item.nombre_mostrar }}</p>

        <span :class="[
            'px-2 py-0.5 rounded-full text-[10px] font-black uppercase border leading-none',
            item.es_equipo ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200'
        ]">
            {{ item.tipo }}
        </span>

        <!-- MENSAJES DE ESTADO (Igual que en Create.vue) -->
        <div v-if="!isAvailable(item)" class="mt-1">
            <div v-if="getItemStatus(item) === 'Mantenimiento'" class="flex flex-col">
                <span class="text-[10px] font-black text-orange-600 uppercase flex items-center gap-1">
                    <Wrench class="w-3 h-3" /> En Mantenimiento
                </span>
                <span v-if="item.fecha_retorno_estimado" class="text-[11px] text-neutral-700 italic">
                    Disponible el: {{ item.fecha_retorno_estimado }}
                </span>
            </div>
            <div v-else-if="getItemStatus(item) === 'Prestado'" class="flex flex-col">
                <span class="text-[10px] font-black text-red-600 uppercase flex items-center gap-1">
                    <ClockAlert class="w-3 h-3" /> Prestado
                </span>
                <span v-if="item.fecha_disponible" class="text-[11px] text-neutral-700 italic">
                    Disponible: {{ item.fecha_disponible }}
                </span>
            </div>
        </div>
    </div>
    <Plus v-if="isAvailable(item)" class="w-4 h-4 text-neutral-300" />
</div>
                        </div>
                        <p v-if="availableItemsForSearch.length === 0 && searchTerm" class="text-center py-4 text-xs text-neutral-400">No se encontraron coincidencias</p>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
