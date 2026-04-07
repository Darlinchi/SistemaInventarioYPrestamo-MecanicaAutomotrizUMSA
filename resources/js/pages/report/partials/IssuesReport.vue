<script setup lang="ts">
import { FileDown, ShieldAlert, AlertTriangle, Image, MessageSquareWarning } from 'lucide-vue-next';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';

const props = defineProps<{
    issues: Array<any>;
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div class="space-y-1">
                <h3 class="text-2xl font-black text-neutral-800 tracking-tighter flex items-center gap-3">
                    <AlertTriangle class="w-7 h-7 text-red-500" />
                    Equipos Dañados o Extraviados
                </h3>
                <p class="text-sm text-neutral-500 font-medium">Listado de activos que requieren atención o reposición inmediata</p>
            </div>

            <button
                v-if="issues.length > 0"
                class="bg-[#1a3a5a] text-white px-6 py-3.5 rounded-2xl font-black text-[11px] uppercase tracking-widest flex items-center gap-2 hover:bg-[#1a3a5a] transition-all shadow-lg shadow-red-900/10 active:scale-95 whitespace-nowrap"
            >
                <FileDown class="w-4 h-4" /> Exportar Incidencias
            </button>
        </div>

        <BaseTable :items="issues" emptyText="No hay equipos con problemas reportados. Todo el inventario está en buen estado.">

            <template #empty-icon>
                 <ShieldAlert class="w-12 h-12 text-emerald-200" />
            </template>

            <TableHeader :columns="[
                'ITEM / CÓDIGO QR',
                'ESTADO ACTUAL',
                'OBSERVACIÓN TÉCNICA'
            ]" />

            <tbody class="divide-y divide-neutral-100">
                <tr v-for="issue in issues" :key="issue.id" class="hover:bg-red-50/30 group transition-colors">

                    <td class="p-4 pl-8">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl border border-red-100 overflow-hidden bg-neutral-50 shrink-0 shadow-sm relative">
                                <img v-if="issue.foto" :src="'/storage/' + issue.foto" class="h-full w-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" />
                                <Image v-else class="h-full w-full p-3 text-neutral-200" />
                                <div class="absolute inset-0 bg-red-500/5 group-hover:bg-transparent"></div>
                            </div>
                            <div>
                                <div class="font-bold text-neutral-800 leading-tight text-sm">
                                    {{ issue.nombre_item }}
                                </div>
                                <div class="text-[11px] font-black text-red-600 mt-1 tracking-tighter uppercase font-mono">
                                    QR: {{ issue.codigo_qr }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-4">
                        <StatusBadge :status="issue.estado" />
                    </td>

                    <td class="p-4 pr-8">
                        <div class="flex items-start gap-3 bg-red-50/50 p-3 rounded-2xl border border-red-100/50">
                            <MessageSquareWarning class="w-4 h-4 text-red-400 shrink-0 mt-0.5" />
                            <p class="text-xs text-red-900/70 italic leading-relaxed font-medium">
                                {{ issue.observacion_item || 'No se registraron detalles específicos sobre la falla o pérdida.' }}
                            </p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <div v-if="issues.length > 0" class="flex items-center gap-3 px-4 py-3 bg-red-50/30 rounded-2xl border border-red-100">
            <div class="w-2 h-2 rounded-full bg-red-500 animate-ping"></div>
            <p class="text-[11px] font-black text-red-700 uppercase tracking-widest">
                Atención crítica: Se requiere gestión para {{ issues.length }} ítems fuera de servicio
            </p>
        </div>
    </div>
</template>
