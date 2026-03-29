<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { List, UserCog, UserCheck, UserCheck2 } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import borrower from '@/routes/borrowers';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    borrowers: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Usuarios',
        href: borrower.index.url(), // Usa la función de tu archivo de rutas
    },
];

const activeTab = ref<'docentes' | 'auxiliares'>('docentes');
const openSubjectId = ref<number | null>(null);

// Contadores corregidos
const countDocentes = computed(() => props.borrowers.filter(b => b.teacher !== null).length);
const countAuxiliares = computed(() => props.borrowers.filter(b => b.assistant !== null).length);

const borrowerTabs = computed(() => [
    { id: 'docentes', label: 'Docentes', count: countDocentes.value, icon: 'UserCog' },
    { id: 'auxiliares', label: 'Auxiliares', count: countAuxiliares.value, icon: 'UserCheck' }
]);

const filteredUsers = computed(() => {
    return props.borrowers.filter(borrower => {
        return activeTab.value === 'docentes' ? borrower.teacher !== null : borrower.assistant !== null;
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

</script>

<template>
    <Head title="Usuarios" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <PageHeader
                title="Prestamístas"
                description="Información sobre los docentes y auxiliares que realizan préstamos de equipos y herramientas del taller"
            />

            <TabSelector
                :tabs="borrowerTabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = val"
            />

            <div class="relative bg-white border border-neutral-200 rounded-xl shadow-sm overflow-x-auto">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="bg-neutral-200 text-xs font-bold uppercase text-neutral-800">
                            <tr>
                                <!--<th class="p-4 w-20 text-center mx-auto">Índice</th> -->

                                <th class="p-4 text-center mx-auto">Cédula</th>
                                <th v-if="activeTab === 'auxiliares'" class="p-4">R.U.</th>
                                <th class="p-4">Nombre (s)</th>
                                <th class="p-4">Apellidos</th>
                                <th class="p-4">Materias</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            <tr v-for="borrower in filteredUsers" :key="borrower.id" class="hover:bg-neutral-50 transition-colors">
                                <!-- <td class="p-4 text-center mx-auto">{{ borrower.id }}</td>-->

                                <td class="p-4 text-center mx-auto">{{ borrower.cedula_identidad }}</td>
                                <td v-if="activeTab === 'auxiliares'" class="p-4 font-mono text-blue-600">
                                    {{ borrower.assistant?.registro_universitario }}
                                </td>
                                <td class="p-4 font-bold">{{ borrower.nombresP }}</td>
                                <td class="p-4 font-bold"> {{ borrower.apellidosP }}</td>
                                <td class="p-4 relative inline-block">
                                    <div v-if="getSubjects(borrower).length > 0">
                                        <button
                                            @click.stop="toggleSubjects(borrower.id)"
                                            class="flex items-center gap-2 px-3 py-1 bg-white border border-neutral-200 rounded-lg hover:bg-neutral-100 transition"
                                        >
                                            <List class="w-4 h-4"/>
                                            <span class="font-bold">{{ getSubjects(borrower).length }}</span>
                                        </button>

                                        <div v-if="openSubjectId === borrower.id"
                                            class="absolute left-0 z-50 mt-2 w-64 bg-white border border-neutral-200 rounded-xl shadow-xl p-3">
                                            <p class="text-[10px] uppercase text-neutral-400 font-bold mb-2">Materias Asignadas</p>
                                            <div class="space-y-1 max-h-48 overflow-y-auto">
                                                <div v-for="sub in getSubjects(borrower)" :key="sub.id"
                                                    class="p-2 bg-neutral-50 rounded-lg border border-neutral-100">
                                                    <p class="text-xs font-black text-black">{{ sub.sigla }}</p>
                                                    <p class="text-[11px] text-neutral-600">{{ sub.nombre_materia }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span v-else class="text-neutral-400 italic text-xs">Sin materias</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
