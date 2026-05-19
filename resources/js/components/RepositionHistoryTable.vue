<script setup lang="ts">
import BaseTable from '@/components/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import TableAction from '@/components/table/TableAction.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import { Wrench, RefreshCw, UserCheck, AlertTriangle, CheckCircle, XCircle, Eye } from 'lucide-vue-next';

defineProps<{ repositions: any[] }>();
const emit = defineEmits(['view']);


// ── Helpers visuales ─────────────────────────────────────────────
const iconoTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return Wrench;
    if (tipo === 'Reemplazo')  return RefreshCw;
    return UserCheck;
};

const labelTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'Reparación';
    if (tipo === 'Reemplazo')  return 'Reemplazo';
    if (tipo === 'Desbloqueo') return 'Desbloqueo';
    return '—';
};

const colorTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'bg-purple-50 text-purple-700 border-purple-200';
    if (tipo === 'Reemplazo')  return 'bg-blue-50 text-blue-700 border-blue-200';
    return 'bg-amber-50 text-amber-700 border-amber-200';
};

const colorTipoOrigen = (tipo: string) => {
    if (tipo === 'Equipo')      return 'bg-red-50 text-red-700 border-red-100';
    if (tipo === 'Herramienta') return 'bg-blue-50 text-blue-700 border-blue-100';
    return 'bg-emerald-50 text-emerald-700 border-emerald-100';
};
</script>

<template>
    <BaseTable :items="repositions" emptyText="No hay reposiciones en el historial">
        <TableHeader :columns="[
            'ÍTEM',
            'RESPONSABLE',
            'TIPO',
            'ESTADO',
            'F. REGISTRO',
            'ACCIONES',
        ]" />

        <tbody class="divide-y divide-neutral-100">
            <tr
                v-for="rep in repositions"
                :key="rep.id"
                class="hover:bg-neutral-50/50 group transition-colors"
                :class="rep.estado === 'Incumplida' ? 'bg-red-50/30' : ''"
            >
                <!-- Ítem -->
                <td class="p-4 pl-8">
                    <div class="flex flex-col gap-1">
                        <span class="text-sm font-bold text-neutral-800 leading-tight">
                            {{ rep.nombre_origen }}
                        </span>
                        <p v-if="rep.equipo_padre"
                            class="text-[11px] text-neutral-400 italic">
                            Acc. de: <span class="font-semibold text-neutral-600">{{ rep.equipo_padre }}</span>
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span :class="['px-1.5 py-0.5 rounded text-[9px] font-black uppercase border', colorTipoOrigen(rep.tipo_origen)]">
                                {{ rep.tipo_origen }}
                            </span>
                            <StatusBadge :status="rep.estado_dano" />
                        </div>
                    </div>
                </td>

                <!-- Responsable -->
                <td class="p-4">
                    <div class="text-sm font-semibold text-neutral-700 leading-tight">
                        {{ rep.borrower_nombre }}
                    </div>
                    <div class="text-xs text-neutral-400 mt-0.5">CI: {{ rep.borrower_ci }}</div>
                    <!-- Registrado por -->
                    <div class="text-xs text-emerald-700 font-bold mt-1 flex items-center gap-1">
                        <UserCheck class="w-3 h-3"/>
                        {{ rep.registrado_por }}
                        <span class="text-neutral-400 font-normal">· {{ rep.registrado_username }}</span>
                    </div>
                </td>

                <!-- Tipo de reposición -->
                <td class="p-4">
                    <span v-if="rep.tipo_reposicion"
                        :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-black uppercase border', colorTipo(rep.tipo_reposicion)]">
                        <component :is="iconoTipo(rep.tipo_reposicion)" class="w-3.5 h-3.5"/>
                        {{ labelTipo(rep.tipo_reposicion) }}
                    </span>
                    <span v-else class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-black text-neutral-400 border border-neutral-200 bg-neutral-50">
                        <AlertTriangle class="w-3 h-3"/> Sin definir
                    </span>
                </td>

                <!-- Estado -->
                <td class="p-4">
                    <span :class="[
                        'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-black uppercase border',
                        rep.estado === 'Cumplida'
                            ? 'bg-green-50 text-green-700 border-green-200'
                            : 'bg-red-50 text-red-700 border-red-200'
                    ]">
                        <CheckCircle v-if="rep.estado === 'Cumplida'" class="w-3.5 h-3.5"/>
                        <XCircle v-else class="w-3.5 h-3.5"/>
                        {{ rep.estado }}
                    </span>
                    <!-- Ítem nuevo si fue Reemplazo -->
                    <p v-if="rep.nombre_nuevo_item"
                        class="text-[11px] text-green-700 font-medium mt-1 flex items-center gap-1">
                        <RefreshCw class="w-3 h-3"/> {{ rep.nombre_nuevo_item }}
                    </p>
                </td>

                <!-- Fechas -->
                <td class="p-4">
                    <div class="space-y-1">
                        <div class="text-xs text-neutral-400 font-medium">
                            <span class="font-bold text-neutral-700">{{ rep.created_at }}</span>
                        </div><!--
                        <div v-if="rep.fecha_limite" class="text-xs text-neutral-400 font-medium">
                            Límite:
                            <span class="font-bold text-neutral-700">{{ rep.fecha_limite }}</span>
                        </div>
                        <div v-if="rep.fecha_cumplimiento" class="text-xs text-neutral-400 font-medium">
                            Resuelto:
                            <span class="font-bold text-green-700">{{ rep.fecha_cumplimiento }}</span>
                        </div>-->
                    </div>
                </td>

                <!-- Registrado por
                <td class="p-4">
                    <span class="text-sm font-semibold text-neutral-700">
                        {{ rep.registrado_por }}
                    </span>
                </td>-->

                <!-- Observación
                <td class="p-4 max-w-48">
                    <p class="text-xs text-neutral-500 italic leading-relaxed line-clamp-2">
                        {{ rep.observacion || '—' }}
                    </p>
                </td> -->

                <!-- Acciones -->
                <td class="p-4 pr-8 text-right">
                    <div class="flex justify-end gap-2">
                        <TableAction :icon="Eye" variant="view" title="Ver detalle" @click="emit('view', rep)" />
                    </div>
                </td>
            </tr>
        </tbody>
    </BaseTable>
</template>
