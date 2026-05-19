<script setup lang="ts">
import { Eye, FileText, Building2, Wrench, Clock } from 'lucide-vue-next';
import BaseTable from '@/components/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import TableAction from '@/components/table/TableAction.vue';

defineProps<{
    maintenances: any[];
}>();

const emit = defineEmits(['view', 'generateReport']);
</script>

<template>
    <BaseTable :items="maintenances" emptyText="No se encontraron registros de mantenimiento">
        <TableHeader :columns="['F. RETORNO', 'EMP. ENCARGADA', 'EQUIPO', 'REGISTRADO POR', 'ACTIVIDAD', 'ACCIONES']" />

        <tbody class="divide-y divide-neutral-100">
            <tr v-for="maint in maintenances" :key="maint.id" class="hover:bg-neutral-50/50 group transition-colors text-sm">

                <td class="p-4">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-[14px] font-bold text-green-700">
                            {{ maint.fecha_retorno }}
                        </div>
                    </div>
                </td>

                <td class="p-4">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-neutral-700">
                            {{ maint.companies[0]?.nombre_empresa || 'S/E' }}
                        </span>
                    </div>
                </td>

                <td class="p-4">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-neutral-800">{{ maint.equipment?.nombre_equipo }}</span>
                    </div>
                </td>

                <!-- Registrado por -->
                <td class="p-4">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[13px] font-bold text-emerald-700">
                            {{ maint.user?.name ?? '—' }}
                        </span>
                        <span class="text-[11px] text-neutral-400 font-medium">
                            {{ maint.user?.username ?? '' }}
                        </span>
                    </div>
                </td>

                <td class="p-4">
                    <p class="italic text-neutral-500 max-w-[200px] truncate text-sm" :title="maint.actividad">
                        {{ maint.actividad }}
                    </p>
                </td>

                <td class="p-4 pr-8 text-right">
                    <div class="flex justify-end gap-2">
                        <TableAction
                            :icon="Eye"
                            variant="view"
                            title="Ver detalles"
                            @click="$emit('view', maint)"
                        />
                        <TableAction
                            :icon="FileText"
                            variant="edit"
                            title="Generar Reporte PDF"
                            @click="$emit('generateReport', maint.id)"
                        />
                        <a
                            :href="`/dashboard/maintenances/equipment/${maint.equipment.id}/history-pdf`"
                            target="_blank"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-black uppercase
                                bg-[#1a3a5a] text-white border border-[#1a3a5a] hover:bg-[#122a42]
                                transition-all shadow-sm active:scale-95"
                            title="Ver historial PDF del equipo"
                        >
                            <FileText class="w-3.5 h-3.5"/>
                            Historial
                        </a>
                    </div>
                </td>
            </tr>
        </tbody>
    </BaseTable>
</template>
