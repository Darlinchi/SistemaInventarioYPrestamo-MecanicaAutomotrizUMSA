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
        <TableHeader :columns="['F. INICIO', 'F. RETORNO', 'EMP. ENCARGADA', 'EQUIPO', 'ACTIVIDAD', 'ACCIONES']" />

        <tbody class="divide-y divide-neutral-100">
            <tr v-for="maint in maintenances" :key="maint.id" class="hover:bg-neutral-50/50 group transition-colors text-sm">

                <td class="p-4">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-1.5 text-[14px] font-bold text-blue-700">
                            {{ maint.fecha_mantenimiento }}
                        </div>
                    </div>
                </td>

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
                    </div>
                </td>
            </tr>
        </tbody>
    </BaseTable>
</template>
