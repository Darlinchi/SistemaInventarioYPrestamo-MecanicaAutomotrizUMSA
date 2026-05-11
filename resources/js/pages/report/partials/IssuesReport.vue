<script setup lang="ts">
import { FileDown, ShieldAlert, AlertTriangle, Image, MessageSquareWarning } from 'lucide-vue-next';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';

const props = defineProps<{
    issues: Array<any>;
}>();

const exportIssuesPdf = () => {
    const url = '/dashboard/reports/issues/pdf';
    window.open(url, '_blank');
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div class="space-y-1">
                <h3 class="text-2xl font-black text-neutral-800 tracking-tighter flex items-center gap-3">
                    <AlertTriangle class="w-7 h-7 text-amber-500" />
                    Atención y Mantenimiento
                </h3>
                <p class="text-sm text-neutral-500 font-medium">
                    Alertas críticas y mantenimientos programados para los próximos 30 días.
                </p>
            </div>

            <button
                v-if="issues.length > 0"
                @click="exportIssuesPdf"
                class="bg-[#1a3a5a] text-white px-6 py-3.5 rounded-2xl font-black text-[11px] uppercase tracking-widest flex items-center gap-2 hover:bg-[#122a42] transition-all shadow-lg active:scale-95"
            >
                <FileDown class="w-4 h-4" /> Exportar Alertas
            </button>
        </div>

        <BaseTable :items="issues" emptyText="No hay alertas pendientes. Todo el inventario está al día.">
            <template #empty-icon>
                 <ShieldAlert class="w-12 h-12 text-emerald-200" />
            </template>

            <TableHeader :columns="[
                'ITEM / CÓDIGO QR',
                'ESTADO / ALERTA',
                'DETALLE DE ATENCIÓN'
            ]" />

            <tbody class="divide-y divide-neutral-100">
                <tr v-for="issue in issues" :key="issue.id"
                    :class="[
                        'group transition-colors',
                        issue.es_alerta_mantenimiento ? 'hover:bg-amber-50/40 bg-amber-50/10' : 'hover:bg-red-50/30'
                    ]">

                    <td class="p-4 pl-8">
                        <div class="flex items-center gap-4">
                            <div :class="['h-12 w-12 rounded-xl border overflow-hidden shrink-0 shadow-sm relative',
                                         issue.es_alerta_mantenimiento ? 'border-amber-200 bg-white' : 'border-red-100 bg-white']">
                                <img v-if="issue.foto" :src="'/storage/' + issue.foto" class="h-full w-full object-cover" />
                                <Image v-else class="h-full w-full p-3 text-neutral-200" />
                            </div>
                            <div>
                                <div class="font-bold text-neutral-800 text-sm leading-tight">{{ issue.nombre_item }}</div>
                                <div class="text-[10px] font-black mt-1 tracking-tighter uppercase font-mono"
                                     :class="issue.es_alerta_mantenimiento ? 'text-amber-600' : 'text-red-600'">
                                    QR: {{ issue.codigo_qr }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-4">
                        <div v-if="issue.es_alerta_mantenimiento" class="flex flex-col gap-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-700 border border-amber-200">
                                Mantenimiento Próximo
                            </span>
                            <span class="text-[10px] font-bold text-neutral-500 italic">
                                Fecha: {{ issue.proximo_mantenimiento }}
                            </span>
                        </div>
                        <StatusBadge v-else :status="issue.estado" />
                    </td>

                    <td class="p-4 pr-8">
                        <div :class="['flex items-start gap-3 p-3 rounded-2xl border',
                                      issue.es_alerta_mantenimiento ? 'bg-amber-50/50 border-amber-100/50' : 'bg-red-50/50 border-red-100/50']">
                            <MessageSquareWarning :class="['w-4 h-4 shrink-0 mt-0.5', issue.es_alerta_mantenimiento ? 'text-amber-400' : 'text-red-400']" />
                            <p :class="['text-xs italic leading-relaxed font-medium', issue.es_alerta_mantenimiento ? 'text-amber-900/80' : 'text-red-900/70']">
                                {{ issue.es_alerta_mantenimiento
                                   ? 'Se acerca la fecha de mantenimiento preventivo. Favor coordinar con el taller.'
                                   : (issue.observacion_item || 'Sin detalles adicionales de la incidencia.')
                                }}
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>
    </div>
</template>
