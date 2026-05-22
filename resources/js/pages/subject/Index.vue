<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { router } from '@inertiajs/vue3';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BookOpen, FileSpreadsheet, Upload, CheckCircle2, AlertCircle, TriangleAlert, Hash, Calendar,
    Power, PowerOff, Check, X, Loader2
 } from 'lucide-vue-next';
import AlertNotification from '@/components/AlertNotification.vue';
import PageHeader from '@/components/PageHeader.vue';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';

const props = defineProps<{
    subjects: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Materias',
        href: '/dashboard/subjects',
    },
];

// ── Flash messages ────────────────────────────────────────────────
const page = usePage();
const flashSuccess = computed(() => (page.props.flash as any)?.success);

// ── Modal de importación Excel ────────────────────────────────────
const modalImport = ref(false);
const archivoNombre = ref<string | null>(null);

const importForm = useForm({
    archivo: null as File | null,
});

const onFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    importForm.archivo = file;
    archivoNombre.value = file?.name ?? null;
};

const abrirImport = () => {
    archivoNombre.value = null;
    importForm.reset();
    importForm.clearErrors();
    modalImport.value = true;
};

const cerrarImport = () => {
    modalImport.value = false;
};

const toggleSubjectStatus = (id: number) => {
    // Usamos router.post o router.put según tu preferencia
    router.post(`/dashboard/subjects/${id}/toggle`, {}, {
        preserveScroll: true,
    });
};

const submitImport = () => {
    if (!importForm.archivo || importForm.processing) return;
    importForm.post('/dashboard/subjects/import',{
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => cerrarImport(),
    });
};

const columnasEjemplo = ['sigla', 'nombre_materia', 'semestre*', 'estado', 'pensum'];
</script>

<template>
    <Head title="Materias" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                title="Catálogo de Materias"
                description="Listado oficial de materias y proyectos de la carrera para la asignación de responsables."
            >
                <template #action>
                    <button
                        @click="abrirImport"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-neutral-200 text-[#1a3a5a] rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-neutral-50 transition-all shadow-sm active:scale-95"
                    >
                        <FileSpreadsheet class="w-4 h-4 text-[#1a3a5a]"/>
                        Importar Materias
                    </button>
                </template>
            </PageHeader>

            <BaseTable :items="subjects" emptyText="No hay materias registradas en el catálogo.">
                <TableHeader :columns="['SIGLA', 'NOMBRE DE LA MATERIA', 'SEMESTRE', 'PENSUM', 'ACTIVO', 'ACCIONES']" />

                <tbody class="divide-y divide-neutral-100 text-sm">
                    <tr v-for="subject in subjects" :key="subject.id" class="hover:bg-neutral-50/50 transition-colors">

                        <td class="p-4 font-bold text-[#1a3a5a]">
                            {{ subject.sigla }}
                        </td>

                        <td class="p-4 font-bold text-[#1a3a5a]">
                            {{ subject.nombre_materia }}
                        </td>

                        <td class="p-4">
                            <div v-if="subject.semestre" class="flex items-center gap-2 text-neutral-500">
                                <span class="font-medium">{{ subject.semestre }}° Semestre</span>
                            </div>
                            <span v-else class="text-neutral-300 italic text-xs">No asignado</span>
                        </td>

                        <td class="p-4 font-bold text-[#1a3a5a]">
                            {{ subject.pensum }}
                        </td>

                        <td class="p-4">
                            <span :class="[
                                'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase border',
                                subject.activo
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                    : 'bg-red-50 text-red-700 border-red-100'
                            ]">
                                <Check v-if="subject.activo" class="w-3 h-3"/>
                                <X v-else class="w-3 h-3"/>
                                {{ subject.activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>

                        <td class="p-4">
                            <button
                                @click="toggleSubjectStatus(subject.id)"
                                :title="subject.activo ? 'Deshabilitar materia' : 'Habilitar materia'"
                                :class="[
                                    'p-2 rounded-xl border transition-all active:scale-90',
                                    subject.activo
                                        ? 'bg-white border-neutral-200 text-red-500 hover:bg-red-50 hover:border-red-200'
                                        : 'bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700'
                                ]"
                            >
                                <Power v-if="subject.activo" class="w-4 h-4" />
                                <PowerOff v-else class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </BaseTable>
        </div>

        <Teleport to="body">
            <div v-if="modalImport" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md p-4" @click.self="cerrarImport">
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in duration-200">

                    <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-[#1a3a5a]">
                                <BookOpen class="w-5 h-5 text-white"/>
                            </div>
                            <div>
                                <h2 class="text-sm font-black text-neutral-800 uppercase tracking-wider">Importar Materias</h2>
                                <p class="text-xs text-neutral-400 mt-0.5">Catálogo de materias (Excel/CSV)</p>
                            </div>
                        </div>
                        <button @click="cerrarImport" class="p-1.5 rounded-full hover:bg-neutral-100 transition text-neutral-400 hover:text-neutral-700">
                            <X class="w-5 h-5"/>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-5">
                        <div class="p-4 bg-neutral-50 rounded-2xl border border-neutral-200 space-y-2">
                            <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest">Columnas requeridas</p>
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                <span v-for="col in columnasEjemplo" :key="col" :class="['px-2 py-0.5 rounded-md text-[11px] font-mono font-bold border', col.endsWith('*') ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-white text-[#1a3a5a] border-neutral-200']">
                                    {{ col }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-2">Seleccionar archivo</label>
                            <label :class="['flex flex-col items-center justify-center gap-3 w-full py-10 border-2 border-dashed rounded-2xl cursor-pointer transition-all', archivoNombre ? 'border-green-300 bg-green-50' : 'border-neutral-200 bg-neutral-50 hover:border-[#1a3a5a]/50']">
                                <Upload class="w-9 h-9 text-neutral-300"/>
                                <p class="text-sm font-bold text-neutral-500">{{ archivoNombre ?? 'Haz clic para seleccionar' }}</p>
                                <input type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="onFileChange" />
                            </label>
                            <div v-if="importForm.errors.archivo" class="flex items-start gap-2 mt-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <AlertCircle class="w-4 h-4 text-red-500 shrink-0 mt-0.5"/>
                                <p class="text-xs text-red-600">{{ importForm.errors.archivo }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4 w-full pt-4">
                        <button
                            type="button"
                            @click="cerrarImport"
                            class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-500 rounded-2xl font-semibold text-[18px] hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            @click="submitImport"
                            :disabled="!importForm.archivo || importForm.processing"
                            class="flex items-center justify-center h-14 bg-[#1a3a5a] text-white rounded-2xl font-semibold text-[18px] shadow-lg shadow-blue-900/20 hover:bg-[#122a42] transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none w-full"
                        >
                            <template v-if="importForm.processing">
                                <Loader2 class="w-5 h-5 animate-spin mr-2" />
                                Importando...
                            </template>

                            <template v-else>
                                <Upload class="w-5 h-5 mr-2" />
                                Importar
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
