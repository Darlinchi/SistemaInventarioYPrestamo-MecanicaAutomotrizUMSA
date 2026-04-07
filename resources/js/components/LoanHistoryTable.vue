<script setup lang="ts">
import BaseTable from '@/components/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import TableAction from '@/components/table/TableAction.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import { List, Eye, Package, FileText } from 'lucide-vue-next';

const props = defineProps<{
    loans: any[];
    openLoanId: number | null;
}>();

const emit = defineEmits(['view', 'generateReport', 'toggleItems']);

</script>

<template>
    <BaseTable :items="loans" emptyText="No hay registros en el historial de devoluciones">
        <TableHeader :columns="['FECHA SALIDA', 'RESPONSABLE', 'MATERIA', 'SIGLA', 'ÍTEMS PRESTADOS', 'ACCIONES']" />

        <tbody class="divide-y divide-neutral-100">
            <tr v-for="loan in loans" :key="loan.id" class="hover:bg-neutral-50/50 group transition-colors">

                <td class="p-4 pl-8 font-bold text-[#1a3a5a] text-sm">
                    {{ loan.fecha_salida }}
                </td>

                <td class="p-4">
                    <div class="text-sm font-semibold text-neutral-700">
                        {{ loan.borrower.apellidosP }} {{ loan.borrower.nombresP }}
                    </div>
                </td>

                <td class="p-4">
                    <div class="text-xs font-bold text-neutral-600 uppercase">
                        {{ loan.subject.nombre_materia }}
                    </div>
                </td>

                <td class="p-4">
                    <div class="text-[14px] text-[#1a3a5a] font-bold tracking-tighter">
                        {{ loan.subject.sigla }}
                    </div>
                </td>

                <td class="p-4">
                    <div class="relative">
                        <button
                            @click.stop="$emit('toggleItems', loan.id)"
                            class="flex items-center gap-2 px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-xl hover:bg-white transition-all shadow-sm active:scale-95"
                        >
                            <List class="w-4 h-4 text-[#1a3a5a]"/>
                            <span class="text-[13px] font-bold text-neutral-700">Ver {{ loan.all_items?.length }} ítems</span>
                        </button>

                        <div v-if="openLoanId === loan.id"
                            class="absolute left-0 z-50 mt-2 w-72 bg-white border border-neutral-200 rounded-2xl shadow-xl p-4 animate-in fade-in zoom-in-95 duration-200">

                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-neutral-100">
                                <p class="text-[11px] font-black text-[#1a3a5a] tracking-widest uppercase">Estado al devolver</p>
                                <Package class="w-4 h-4 text-neutral-400" />
                            </div>

                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1 custom-scrollbar">
                                <div v-for="item in loan.all_items" :key="item.id"
                                    class="flex flex-col p-2 bg-neutral-50 rounded-lg border border-neutral-100">

                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-[13px] font-bold text-neutral-800 leading-tight flex-1">
                                            {{ item.nombre_mostrar }}
                                        </span>
                                        <StatusBadge :status="item.estado_devolucion" />
                                    </div>

                                    <span :class="[
                                        'w-fit px-2 py-0.5 rounded-lg text-[9px] font-black uppercase border',
                                        item.es_equipo ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100'
                                    ]">
                                        {{ item.es_equipo ? 'EQUIPO' : 'HERRAMIENTA' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>

                <td class="p-4 pr-8 text-right">
                    <div class="flex justify-end gap-2">
                        <TableAction
                            :icon="Eye"
                            variant="view"
                            title="Ver detalles"
                            @click="$emit('view', loan)"
                        />
                        <TableAction
                            :icon="FileText"
                            variant="edit"
                            title="Generar Reporte PDF"
                            @click="$emit('generateReport', loan.id)"
                        />
                    </div>
                </td>
            </tr>
        </tbody>
    </BaseTable>
</template>
