<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { List, UserCog, UserCheck, UserCheck2 } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import borrower from '@/routes/borrowers';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    borrowers: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Solicitantes',
        href: borrower.index.url(), // Usa la función de tu archivo de rutas
    },
];

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
const getSubjects = (borrower: any) => {
    if (borrower.teacher) return borrower.teacher.subjects || [];
    if (borrower.assistant) return borrower.assistant.subjects || [];
    // Los estudiantes podrían tener materias mediante inscripciones si lo escalas luego
    return borrower.student?.subjects || [];
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

</script>

<template>
    <Head title="Solicitantes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <PageHeader
                description="Información sobre los docentes, auxiliares y estudiantes que realizan préstamos de equipos y herramientas del taller"
            />

            <TabSelector
                :tabs="borrowerTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <!-- Uso de BaseTable -->
            <BaseTable :items="filteredUsers" :emptyText="`No se encontraron ${activeTab}`">
                <TableHeader :columns="[
                    'CÉDULA',
                    ...(activeTab !== 'docentes' ? ['R.U.'] : []), // R.U. para auxiliares y estudiantes
                    'NOMBRE (S)',
                    'APELLIDOS',
                    'MATERIAS'
                ]" />

                <tbody class="divide-y divide-neutral-100 text-sm">
                    <tr v-for="borrower in filteredUsers" :key="borrower.id" class="hover:bg-neutral-50/50 transition-colors group">

                        <td class="p-4 pl-8 text-neutral-700 font-medium">
                            {{ borrower.cedula_identidad }}
                        </td>

                        <td v-if="activeTab !== 'docentes'" class="p-4">
                            <span class="font-mono text-blue-600 bg-blue-50 px-2 py-1 rounded-md text-xs border border-blue-100">
                                {{ borrower.assistant?.registro_universitario || borrower.student?.registro_universitario || 'N/A' }}
                            </span>
                        </td>

                        <td class="p-4 font-bold text-[#1a3a5a]">
                            {{ borrower.nombres }}
                        </td>

                        <td class="p-4 font-bold text-[#1a3a5a]">
                            {{ borrower.apellidos }}
                        </td>

                        <td class="p-4 relative">
                            <div v-if="getSubjects(borrower).length > 0">
                                <button
                                    @click.stop="toggleSubjects(borrower.id)"
                                    class="flex items-center gap-2 px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-xl hover:bg-white transition-all shadow-sm active:scale-95 group/btn"
                                >
                                    <List class="w-4 h-4 text-[#1a3a5a] group-hover/btn:scale-110 transition-transform"/>
                                    <span class="font-black text-[#1a3a5a]">{{ getSubjects(borrower).length }}</span>
                                </button>

                                <div v-if="openSubjectId === borrower.id"
                                    class="absolute left-0 z-50 mt-2 w-72 bg-white border border-neutral-200 rounded-2xl shadow-xl p-4 animate-in fade-in zoom-in-95 duration-200">
                                    <p class="text-[11px] uppercase text-neutral-400 font-black mb-3 tracking-widest border-b pb-2">
                                        Materias / Proyectos
                                    </p>
                                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1 custom-scrollbar">
                                        <div v-for="sub in getSubjects(borrower)" :key="sub.id"
                                            class="p-3 bg-neutral-50 rounded-xl border border-neutral-100 hover:border-blue-200 transition-colors">
                                            <p class="text-xs font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md uppercase tracking-tighter w-fit mb-1">
                                                {{ sub.sigla }}
                                            </p>
                                            <p class="text-[12px] font-bold text-neutral-800 leading-tight">
                                                {{ sub.nombre_materia }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span v-else class="text-neutral-400 italic text-xs px-2">Uso General / Taller</span>
                        </td>
                    </tr>
                </tbody>
            </BaseTable>
        </div>
    </AppLayout>
</template>
