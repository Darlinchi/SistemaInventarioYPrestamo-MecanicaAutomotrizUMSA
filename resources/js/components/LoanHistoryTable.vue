<script setup lang="ts">
import BaseTable from '@/components/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import TableAction from '@/components/table/TableAction.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import { List, Eye, Package, FileText, AlertTriangle } from 'lucide-vue-next';

const props = defineProps<{
    loans: any[];
    openLoanId: number | null;
    canEdit?: boolean;
    canDelete?: boolean;
    permitirReposicion?: boolean;
}>();

const emit = defineEmits(['view', 'generateReport', 'toggleItems', 'createReposition']);

const estadoDevolucion = (item: any): string => item.estado_devolucion ?? 'Disponible';

// Detectar si un préstamo tiene ítems con problema para mostrar el botón de reposición
const tieneProblemas = (loan: any): boolean => {
    const estadosBad = ['Dañado', 'Extraviado', 'Incompleto', 'Baja'];
    return (loan.all_items || []).some((item: any) =>
        estadosBad.includes(item.estado_devolucion) ||
        (item.accessories || []).some((acc: any) => ['Dañado', 'Extraviado'].includes(acc.estado_accesorio))
    );
};
</script>

<template>
    <BaseTable :items="loans" emptyText="No hay registros en el historial de devoluciones">
        <TableHeader :columns="['F. RETORNO', 'ENCARGADO', 'RESPONSABLE', 'MATERIA', 'SIGLA', 'ÍTEMS', 'ACCIONES']" />

        <tbody class="divide-y divide-neutral-100">
            <tr v-for="loan in loans" :key="loan.id" class="hover:bg-neutral-50/50 group transition-colors">

                <!-- Fecha retorno real -->
                <td class="p-4 pl-8 font-bold text-[#1a3a5a] text-sm">
                    {{ loan.fecha_retorno ?? loan.fecha_salida }}
                </td>

                <td class="p-4">
                    <div class="text-sm font-semibold text-neutral-700">
                        {{ loan.user?.name }}
                    </div>
                    <div class="text-xs text-neutral-600 mt-0.5">CI: {{ loan.borrower?.cedula_identidad }}</div>
                </td>

                <td class="p-4">
                    <div class="text-sm font-semibold text-neutral-700">
                        {{ loan.borrower?.teacher?.titulo }} {{ loan.borrower?.apellidoPaterno }} {{ loan.borrower?.apellidoMaterno }}
                        {{ loan.borrower?.nombres  }}
                    </div>
                    <div class="text-xs text-neutral-600 mt-0.5">CI: {{ loan.borrower?.cedula_identidad }}</div>
                </td>

                <td class="p-4">
                    <div class="text-xs font-bold text-neutral-600 uppercase">{{ loan.subject?.nombre_materia }}</div>
                </td>

                <td class="p-4">
                    <div class="text-[14px] text-[#1a3a5a] font-bold tracking-tighter">{{ loan.subject?.sigla }}</div>
                </td>

                <!-- Ítems con popover -->
                <td class="p-4">
                    <div class="relative inline-block">
                        <button
                            @click.stop="$emit('toggleItems', loan.id)"
                            class="flex items-center gap-2 px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-xl hover:bg-white transition-all shadow-sm active:scale-95"
                        >
                            <List class="w-4 h-4 text-[#1a3a5a]"/>
                            <span class="text-[13px] font-bold text-neutral-700">
                                Ver {{ loan.all_items?.length ?? 0 }} ítems
                            </span>
                        </button>

                        <!-- Popover ítems -->
                        <div v-if="openLoanId === loan.id && loan.all_items?.length"
                            class="absolute left-0 z-50 mt-2 w-80 bg-white border border-neutral-200 rounded-2xl shadow-xl p-4 animate-in fade-in zoom-in-95 duration-200">

                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-neutral-100">
                                <p class="text-[11px] font-black text-[#1a3a5a] tracking-widest uppercase">Estado al devolver</p>
                                <Package class="w-4 h-4 text-neutral-400" />
                            </div>

                            <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                <div v-for="item in loan.all_items" :key="item.id"
                                    class="flex flex-col p-2 bg-neutral-50 rounded-lg border border-neutral-100">

                                    <div class="flex justify-between items-start mb-1 gap-2">
                                        <span class="text-[13px] font-bold text-neutral-800 leading-tight flex-1">
                                            {{ item.nombre_mostrar }}
                                        </span>
                                        <StatusBadge :status="estadoDevolucion(item)" />
                                    </div>

                                    <span :class="[
                                        'w-fit px-2 py-0.5 rounded-lg text-[9px] font-black uppercase border mb-1',
                                        item.es_equipo ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100'
                                    ]">{{ item.es_equipo ? 'EQUIPO' : 'HERRAMIENTA' }}</span>

                                    <!-- Accesorios (estado histórico) -->
                                    <div v-if="item.accessories?.length" class="mt-1 pl-2 border-l-2 border-neutral-200 space-y-1">
                                        <div v-for="acc in item.accessories" :key="acc.id"
                                            class="flex items-center justify-between gap-2">
                                            <span class="text-[11px] text-neutral-500 truncate">↳ {{ acc.nombre_accesorio }}</span>
                                            <StatusBadge :status="acc.estado_accesorio ?? 'Bueno'" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Popover vacío -->
                        <div v-else-if="openLoanId === loan.id && !loan.all_items?.length"
                            class="absolute left-0 z-50 mt-2 w-64 bg-white border border-neutral-200 rounded-2xl shadow-xl p-4">
                            <p class="text-xs text-neutral-400 text-center">Sin ítems registrados</p>
                        </div>
                    </div>
                </td>

                <!-- Acciones -->
                <td class="p-4 pr-8 text-right">
                    <div class="flex justify-end gap-2">
                        <TableAction :icon="Eye" variant="view" title="Ver detalles" @click="$emit('view', loan)" />
                        <TableAction :icon="FileText" variant="edit" title="Generar Reporte PDF" @click="$emit('generateReport', loan.id)" />
                    </div>
                </td>
            </tr>
        </tbody>
    </BaseTable>
</template>
