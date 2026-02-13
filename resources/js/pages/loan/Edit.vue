<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ref, computed } from 'vue';
import loans from '@/routes/loans';
import { ArrowLeft, Loader2, Package, Search, CheckCircle, User, Save, Trash2, Plus, Calendar, Clock,
    ClipboardCheck, BookMarked
 } from 'lucide-vue-next';

const props = defineProps<{
    loan: any;
    borrowers: any[];
    items: any[]; // Todos los items: disponibles + los que ya tiene el préstamo
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Editar Préstamo',
        href: loans.edit.url(props.loan.id),
    },
];

const searchTerm = ref('');

const form = useForm({
    borrower_id: props.loan.borrower_id,
    subject_id: props.loan.subject_id,
    items: props.loan.items.map((i: any) => i.id) as number[],
});

// Filtra los ítems para el buscador (Solo mostramos los que NO están seleccionados todavía)
const availableItemsForSearch = computed(() => {
    return props.items.filter(item =>
        !form.items.includes(item.id) &&
        item.nombre_item.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

// Obtener los objetos completos de los ítems seleccionados para mostrarlos arriba
const selectedItemsList = computed(() => {
    return props.items.filter(item => form.items.includes(item.id));
});

const toggleItemSelection = (id: number) => {
    const index = form.items.indexOf(id);
    if (index > -1) form.items.splice(index, 1);
    else form.items.push(id);
};

const submit = () => {
    form.put(loans.update.url(props.loan.id), {
        preserveScroll: true,
    });
};

</script>

<template>
    <Head title="Editar Préstamo" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-5xl mx-auto p-4 w-full">
            <div class="mb-4">
                <Link :href="loans.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-4 h-4 mr-1"/> Volver a préstamos
                </Link>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-neutral-50 p-6 rounded-2xl border border-neutral-200 space-y-6">
                        <h3 class="font-bold text-lg border-b pb-2 flex items-center">
                            <ClipboardCheck class="w-5 h-5 mr-2 text-blue-500"/> Datos del Préstamo
                        </h3>

                        <div class="space-y-1">
                            <p class="flex items-center gap-1 text-[12px] font-black text-neutral-700 uppercase tracking-widest">
                                <User class="w-4 h-4 text-neutral-700" />
                                <span>Responsable</span>
                            </p>
                            <p class="text-sm font-bold text-neutral-800">{{ loan.borrower.nombresP }} {{ loan.borrower.apellidosP }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="flex items-center gap-1 text-[12px] font-black text-neutral-700 uppercase tracking-widest">
                                <BookMarked class="w-4 h-4 text-neutral-700" />
                                <span>Materia</span>
                            </p>
                            <p class="text-xs font-black text-blue-600">{{ loan.subject.sigla }}</p>
                            <p class="text-sm font-bold text-neutral-800">{{ loan.subject.nombre_materia }}</p>
                        </div>

                        <div class="pt-4 border-t border-neutral-200 grid grid-cols-2 gap-4">
                            <div>
                                <p class="flex items-center gap-1 text-[12px] font-black text-orange-400 uppercase tracking-widest">
                                    <Calendar class="w-4 h-4 text-orange-400" />
                                    <span>Fecha</span>
                                </p>
                                <p class="text-xs font-medium">{{ loan.fecha_prestamo }}</p>
                            </div>
                            <div>
                                <p class="flex items-center gap-1 text-[12px] font-black text-blue-600 uppercase tracking-widest">
                                    <Clock class="w-4 h-4 text-blue-600" />
                                    <span>Salida</span>
                                </p>
                                <p class="text-xs font-medium">{{ loan.hora_inicio }}</p>
                            </div>
                        </div>
                    </div>

                    <Button type="submit" class="w-full py-7 text-lg font-black uppercase tracking-widest" :disabled="form.processing || form.items.length === 0">
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-5 w-5 animate-spin" /> Guardando...
                        </template>
                        <template v-else>
                            <Save class="w-5 h-5 mr-2"/> Actualizar
                        </template>
                    </Button>
                </div>

                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-neutral-900 flex items-center">
                                <CheckCircle class="w-5 h-5 mr-2 text-green-500"/> Equipos en el Préstamo
                            </h3>
                            <span class="text-[10px] font-black bg-green-100 text-green-700 px-3 py-1 rounded-full uppercase">
                                {{ form.items.length }} Items
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <div v-for="item in selectedItemsList" :key="item.id"
                                class="flex items-center justify-between p-3 bg-blue-50/50 border border-blue-100 rounded-xl group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-white flex items-center justify-center border border-blue-100">
                                        <Package class="w-4 h-4 text-blue-500" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-neutral-800">{{ item.nombre_item }}</p>
                                    </div>
                                </div>
                                <button type="button" @click="toggleItemSelection(item.id)" class="text-neutral-400 hover:text-red-500 p-2">
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
                                @click="toggleItemSelection(item.id)"
                                class="p-3 border border-neutral-100 rounded-xl cursor-pointer hover:border-blue-300 hover:bg-blue-50/30 transition-all flex items-center gap-3"
                            >
                                <div class="w-10 h-10 rounded-lg bg-neutral-100 flex items-center justify-center overflow-hidden border">
                                    <img v-if="item.foto" :src="'/storage/' + item.foto" class="object-cover w-full h-full" />
                                    <Package v-else class="w-5 h-5 text-neutral-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-[15px] font-bold text-neutral-800 leading-tight">{{ item.nombre_item }}</p>
                                </div>
                                <Plus class="w-4 h-4 text-neutral-300" />
                            </div>
                        </div>
                        <p v-if="availableItemsForSearch.length === 0 && searchTerm" class="text-center py-4 text-xs text-neutral-400">No se encontraron coincidencias</p>
                    </div>

                </div>
            </form>
        </div>
    </AppLayout>
</template>
