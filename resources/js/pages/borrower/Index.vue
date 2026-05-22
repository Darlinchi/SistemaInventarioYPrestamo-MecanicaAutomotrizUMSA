<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import {
    List, UserCog, UserCheck, FileSpreadsheet, Upload, X, SquarePen,
    CheckCircle2, AlertCircle, TriangleAlert, Plus, GraduationCap, Phone,
    CalendarCheck, Power, PowerOff, Check, Loader2,
    CalendarX
} from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import AlertNotification from '@/components/AlertNotification.vue';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import borrowerRoutes from '@/routes/borrowers';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    borrowers: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Responsables', href: borrowerRoutes.index.url() },
];

const page = usePage();
// --- PERMISOS ---
// Verifica que 'usuarios.crear' exista en tu base de datos.
// Si quieres que aparezca siempre para probar, cambia esto a: return true;
const can = (permission: string) => {
    const userPermissions = (page.props.auth.user as any)?.permissions ?? [];
    return userPermissions.includes(permission);
};

const activeTab = ref<'docentes' | 'auxiliares' | 'estudiantes'>('docentes');
const openSubjectId = ref<number | null>(null);

// Contadores corregidos
const countDocentes = computed(() => props.borrowers.filter(b => b.teacher !== null).length);
const countAuxiliares = computed(() => props.borrowers.filter(b => b.assistant !== null).length);
const countEstudiantes = computed(() => props.borrowers.filter(b => b.teacher === null && b.assistant === null).length);

const borrowerTabs = computed(() => [
    { id: 'docentes', label: 'Docentes', count: countDocentes.value, icon: 'UserCog' },
    { id: 'auxiliares', label: 'Auxiliares', count: countAuxiliares.value, icon: 'UserCheck' },
    { id: 'estudiantes', label: 'Estudiantes', count: countEstudiantes.value, icon: 'GraduationCap' }
]);

const filteredUsers = computed(() => {
    return props.borrowers.filter(b => {
        if (activeTab.value === 'docentes') return b.teacher !== null;
        if (activeTab.value === 'auxiliares') return b.assistant !== null;
        return b.teacher === null && b.assistant === null; // Caso estudiantes
    });
});

// Función para abrir/cerrar el dropdown de materias
const toggleSubjects = (id: number) => {
    openSubjectId.value = openSubjectId.value === id ? null : id;
};

// Función auxiliar para obtener las materias del usuario actual
// Reemplaza la función getSubjects completa
const getSubjects = (borrower: any) => {
    if (borrower.teacher) {
        return borrower.teacher.subjects || [];
    }
    if (borrower.assistant) {
        // Para auxiliares las materias vienen dentro de subjectTeachers[].subject
        return (borrower.assistant.subject_teachers || [])
            .map((st: any) => st.subject)
            .filter(Boolean);
    }
    return [];
};

// Cierra el popover si se hace clic fuera del contenedor
const closePopovers = (e: MouseEvent) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.relative.inline-block')) {
        openSubjectId.value = null;
    }
};

// Activar al entrar y desactivar al salir
onMounted(() => window.addEventListener('click', closePopovers));
onUnmounted(() => window.removeEventListener('click', closePopovers));

// ── Flash messages ────────────────────────────────────────────────
const flashSuccess = computed(() => (page.props.flash as any)?.success);
const flashWarning = computed(() => (page.props.flash as any)?.warning);

// ── Modal de importación Excel ────────────────────────────────────
const modalImport   = ref(false);
const tipoImport    = ref<'docente' | 'auxiliar'>('docente');
const archivoNombre = ref<string | null>(null);

const importForm = useForm({
    tipo:    'docente' as 'docente' | 'auxiliar',
    archivo: null as File | null,
});

const onFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    importForm.archivo  = file;
    archivoNombre.value = file?.name ?? null;
};

const abrirImport = (tipo: 'docente' | 'auxiliar') => {
    tipoImport.value    = tipo;
    archivoNombre.value = null;
    importForm.reset();
    importForm.clearErrors();
    modalImport.value   = true;
};

const cerrarImport = () => {
    modalImport.value   = false;
    archivoNombre.value = null;
    importForm.reset();
    importForm.clearErrors();
};

const submitImport = () => {
    if (!importForm.archivo || importForm.processing) return;
    importForm.tipo = tipoImport.value;
    importForm.post('/dashboard/borrowers/import', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => cerrarImport(),
    });
};

// Columnas según tipo
const columnasEjemplo = computed(() =>
    tipoImport.value === 'docente'
        ? ['cedula_identidad','nombres', 'apellido_paterno', 'apellido_materno', 'celular', 'titulo',  'categoria', 'materia_sigla', 'paralelo']
        : ['cedula_identidad', 'celular', 'nombres', 'apellido_paterno', 'apellido_materno', 'categoria', 'materia_sigla', 'docente_ci', 'fecha_inicio*', 'fecha_fin*']
);

const formatDate = (dateString: string | null) => {
    if (!dateString) return '---';
    // Si viene el formato largo "2026-05-10T04:00...", tomamos solo los primeros 10 caracteres
    return dateString.split('T')[0];
};

const toggleBorrowerStatus = (id: number) => {
    router.post(`/dashboard/borrowers/${id}/toggle`, {}, {
        preserveScroll: true,
        preserveState: false,  // ← fuerza recarga de props para mostrar el cambio
    });
};
</script>

<template>
    <Head title="Solicitantes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />
            <div v-if="flashWarning" class="mb-4 flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 font-medium">
                <TriangleAlert class="w-4 h-4 shrink-0"/> {{ flashWarning }}
            </div>

            <PageHeader description="Gestione los responsables autorizados para realizar préstamos.">
                <template #action>
                    <div class="flex items-center gap-3">
                        <button
                            v-if="activeTab === 'docentes'"
                            @click="abrirImport('docente')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-neutral-200 text-[#1a3a5a] rounded-xl text-sm font-bold uppercase tracking-wider hover:bg-neutral-50 transition-all shadow-sm active:scale-95"
                        >
                            <FileSpreadsheet class="w-4 h-4 text-[#1a3a5a]"/>
                            Importar Docentes
                        </button>

                        <button
                            v-if="activeTab === 'auxiliares'"
                            @click="abrirImport('auxiliar')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-emerald-200 text-emerald-700 rounded-xl text-sm font-bold uppercase tracking-wider hover:bg-emerald-50 transition-all shadow-sm active:scale-95"
                        >
                            <FileSpreadsheet class="w-4 h-4 text-emerald-600"/>
                            Importar Auxiliares
                        </button>

                        <CreateActionButton
                            v-if="activeTab !== 'estudiantes' && (can('usuarios.crear') || true)"
                            :href="borrowerRoutes.create.url()"
                            label="Registrar Responsable"
                        />
                    </div>
                </template>
            </PageHeader>

            <TabSelector
                :tabs="borrowerTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <!-- Uso de BaseTable -->
            <BaseTable :items="filteredUsers" :emptyText="`No se encontraron ${activeTab}`">
                <TableHeader :columns="[
                    ...(activeTab === 'docentes' || activeTab === 'auxiliares' ? ['CAT.'] : []),
                    'CÉDULA',
                    // R.U. se muestra para Auxiliares y Estudiantes
                    ...(activeTab === 'estudiantes' ? ['R.U.'] : []),

                    'NOMBRE COMPLETO',
                    'CELULAR',

                    // Materias solo para Docentes y Auxiliares (Los estudiantes suelen ser uso general)
                    ...(activeTab === 'docentes' || activeTab === 'auxiliares' ? ['MATERIAS'] : []),

                    // Periodo solo para Auxiliares
                    ...(activeTab === 'auxiliares' ? ['FECHA INICIO', 'FECHA FIN'] : []),

                    'ESTADO',

                    'ACCIONES'
                ]" />

                <tbody class="divide-y divide-neutral-100 text-sm">
                    <tr v-for="b in filteredUsers" :key="b.id" class="hover:bg-neutral-50/50 transition-colors group">

                        <td v-if="activeTab === 'auxiliares' || activeTab === 'docentes'" class="p-4 font-bold text-[#1a3a5a]">
                            {{ b.teacher?.categoria || b.assistant?.categoria }}
                        </td>

                        <td class="p-2 pl-8 text-neutral-700 font-medium">
                            {{ b.cedula_identidad }}
                        </td>

                        <td v-if="activeTab === 'estudiantes'" class="p-2">
                            <span class="font-mono text-blue-600 bg-blue-50 px-2 py-1 rounded-md text-xs border border-blue-100 font-bold">
                                {{ b.student?.registro_universitario || 'N/A' }}
                            </span>
                        </td>

                        <td class="p-2 font-bold text-[#1a3a5a]">
                            {{ b.teacher?.titulo }} {{ b.apellidoPaterno }} {{ b.apellidoMaterno }} {{ b.nombres }}
                        </td>

                        <td class="p-2 pl-8 text-neutral-700 font-medium">
                            {{ b.celular || 'Sin número' }}
                        </td>

                        <td v-if="activeTab === 'docentes' || activeTab === 'auxiliares'" class="p-2 relative">
                            <div v-if="getSubjects(b).length > 0">
                                <button @click.stop="toggleSubjects(b.id)"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-xl hover:bg-white transition-all shadow-sm">
                                    <List class="w-4 h-4 text-[#1a3a5a]"/>
                                    <span class="font-black text-[#1a3a5a]">{{ getSubjects(b).length }}</span>
                                </button>

                                <div v-if="openSubjectId === b.id"
                                    class="absolute left-0 z-50 mt-2 w-72 bg-white border border-neutral-200 rounded-2xl shadow-xl p-4 animate-in fade-in zoom-in-95 duration-200">
                                    <p class="text-[11px] uppercase text-neutral-400 font-black mb-3 tracking-widest border-b pb-2">Asignaciones</p>
                                    <div class="space-y-2 max-h-56 overflow-y-auto custom-scrollbar">
                                        <div v-for="sub in getSubjects(b)" :key="sub.id" class="p-2 bg-neutral-50 rounded-lg border border-neutral-100">
                                            <p class="text-[10px] font-black text-blue-700 uppercase">{{ sub.sigla }}</p>
                                            <p class="text-xs font-bold text-neutral-800 leading-tight">{{ sub.nombre_materia }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span v-else class="text-neutral-400 italic text-xs">Uso General</span>
                        </td>

                        <template v-if="activeTab === 'auxiliares'">
                            <td class="p-2 pl-8 font-bold text-[#1a3a5a] text-sm">
                                {{ formatDate(b.assistant?.fecha_inicio) }}
                            </td>
                            <td class="p-2 pl-8 font-bold text-[#1a3a5a] text-sm">
                                {{ formatDate(b.assistant?.fecha_fin) }}
                            </td>
                        </template>

                        <td class="p-2">
                            <span :class="[
                                'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase border',
                                b.activo
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                    : 'bg-red-50 text-red-700 border-red-100'
                            ]">
                                <Check v-if="b.activo" class="w-3 h-3"/>
                                <X v-else class="w-3 h-3"/>
                                {{ b.activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>

                        <td class="p-2 flex items-center gap-2">
                            <button
                                @click="toggleBorrowerStatus(b.id)"
                                :title="b.activo ? 'Deshabilitar responsable' : 'Habilitar responsable'"
                                :class="[
                                    'p-2 rounded-xl border transition-all active:scale-90',
                                    b.activo
                                        ? 'bg-white border-neutral-200 text-red-500 hover:bg-red-50 hover:border-red-200'
                                        : 'bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700'
                                ]"
                            >
                                <Power v-if="b.activo" class="w-4 h-4" />
                                <PowerOff v-else class="w-4 h-4" />
                            </button>

                            <Link :href="borrowerRoutes.edit.url(b.id)" v-if="activeTab === 'auxiliares' || activeTab === 'docentes'"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg inline-block transition-colors">
                                <SquarePen class="w-5 h-5"/>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </BaseTable>
        </div>

        <!-- ── Modal Importar Excel ─────────────────────────────────────── -->
        <Teleport to="body">
            <div
                v-if="modalImport"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md p-4"
                @click.self="cerrarImport"
            >
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in zoom-in duration-200">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <div :class="[
                                'p-2.5 rounded-xl',
                                tipoImport === 'docente' ? 'bg-[#1a3a5a]' : 'bg-emerald-600'
                            ]">
                                <FileSpreadsheet class="w-5 h-5 text-white"/>
                            </div>
                            <div>
                                <h2 class="text-sm font-black text-neutral-800 uppercase tracking-wider">
                                    Importar {{ tipoImport === 'docente' ? 'Docentes' : 'Auxiliares' }}
                                </h2>
                                <p class="text-xs text-neutral-400 mt-0.5">
                                    Archivo Excel .xlsx, .xls o .csv
                                </p>
                            </div>
                        </div>
                        <button @click="cerrarImport"
                            class="p-1.5 rounded-full hover:bg-neutral-100 transition text-neutral-400 hover:text-neutral-700">
                            <X class="w-5 h-5"/>
                        </button>
                    </div>

                    <!-- Cuerpo -->
                    <div class="px-6 py-5 space-y-5">

                        <!-- Columnas requeridas -->
                        <div class="p-4 bg-neutral-50 rounded-2xl border border-neutral-200 space-y-2">
                            <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest">
                                Columnas requeridas en el Excel
                            </p>
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                <span
                                    v-for="col in columnasEjemplo"
                                    :key="col"
                                    :class="[
                                        'px-2 py-0.5 rounded-md text-[11px] font-mono font-bold border',
                                        col.endsWith('*')
                                            ? 'bg-amber-50 text-amber-700 border-amber-200'
                                            : 'bg-white text-[#1a3a5a] border-neutral-200'
                                    ]"
                                >
                                    {{ col }}
                                </span>
                            </div>
                            <p class="text-[10px] text-neutral-400 italic mt-1">
                                * columnas opcionales · La primera fila debe ser el encabezado
                            </p>
                        </div>

                        <!-- Zona de upload -->
                        <div>
                            <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-2">
                                Seleccionar archivo
                            </label>
                            <label :class="[
                                'flex flex-col items-center justify-center gap-3 w-full py-10 border-2 border-dashed rounded-2xl cursor-pointer transition-all',
                                archivoNombre
                                    ? 'border-green-300 bg-green-50'
                                    : 'border-neutral-200 bg-neutral-50 hover:border-[#1a3a5a]/50 hover:bg-blue-50/20'
                            ]">
                                <CheckCircle2 v-if="archivoNombre" class="w-9 h-9 text-green-500"/>
                                <Upload v-else class="w-9 h-9 text-neutral-300"/>
                                <div class="text-center">
                                    <p class="text-sm font-bold" :class="archivoNombre ? 'text-green-700' : 'text-neutral-500'">
                                        {{ archivoNombre ?? 'Haz clic para seleccionar' }}
                                    </p>
                                    <p v-if="!archivoNombre" class="text-xs text-neutral-400 mt-0.5">
                                        xlsx, xls o csv — máx. 5 MB
                                    </p>
                                </div>
                                <input
                                    type="file"
                                    accept=".xlsx,.xls,.csv"
                                    class="hidden"
                                    @change="onFileChange"
                                />
                            </label>

                            <!-- Error -->
                            <div v-if="importForm.errors.archivo"
                                class="flex items-start gap-2 mt-2 p-3 bg-red-50 border border-red-200 rounded-xl">
                                <AlertCircle class="w-4 h-4 text-red-500 shrink-0 mt-0.5"/>
                                <p class="text-xs text-red-600 leading-relaxed">{{ importForm.errors.archivo }}</p>
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
                            :class="[
                                'flex items-center justify-center h-14 text-white rounded-2xl font-semibold text-[18px] shadow-lg active:scale-95 transition-all w-full disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none',
                                tipoImport === 'docente'
                                    ? 'bg-[#1a3a5a] shadow-blue-900/20 hover:bg-[#122a42]'
                                    : 'bg-emerald-600 shadow-emerald-900/20 hover:bg-emerald-700'
                            ]"
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
