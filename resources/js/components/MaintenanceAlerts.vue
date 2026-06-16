<script setup lang="ts">
import { FileDown, AlertTriangle, Image, MessageSquareWarning, ShieldAlert } from 'lucide-vue-next';
import StatusBadge from '@/components/shared/StatusBadge.vue';

defineProps<{
    issues: Array<any>;
}>();

const exportIssuesPdf = () => {
    window.open('/dashboard/reports/issues/pdf', '_blank');
};
</script>

<template>
    <div v-if="issues && issues.length > 0"
         class="rounded-[2.5rem] border border-red-200 bg-red-50/30 overflow-hidden shadow-sm">

        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 px-6 py-4 bg-red-50 border-b border-red-200">
            <div class="flex items-center gap-3">
                <AlertTriangle class="w-5 h-5 text-red-500 shrink-0" />
                <h3 class="text-base font-black text-red-800 uppercase tracking-tight">
                    Alertas y Mantenimientos Próximos
                </h3>
                <span class="bg-red-200 text-red-800 text-[10px] font-black px-2.5 py-0.5 rounded-full">
                    {{ issues.length }} pendiente{{ issues.length !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>

        <!-- Filas -->
        <div class="divide-y divide-red-100/60">
            <div
                v-for="issue in issues"
                :key="issue.id"
                :class="[
                    'flex flex-col md:flex-row md:items-center gap-4 px-6 py-4 transition-colors',
                    issue.es_alerta_mantenimiento
                        ? 'bg-amber-50/20 hover:bg-amber-50/40'
                        : 'hover:bg-red-50/30'
                ]"
            >
                <!-- Foto / Ícono -->
                <div :class="[
                    'h-11 w-11 rounded-xl border overflow-hidden shrink-0',
                    issue.es_alerta_mantenimiento ? 'border-amber-200' : 'border-red-100'
                ]">
                    <img
                        v-if="issue.foto"
                        :src="'/storage/' + issue.foto"
                        class="h-full w-full object-cover"
                        :alt="issue.nombre_item"
                    />
                    <Image v-else class="h-full w-full p-2.5 text-neutral-200" />
                </div>

                <!-- Nombre y QR -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-neutral-800 truncate">
                        {{ issue.nombre_item }}
                    </p>
                    <p :class="[
                        'text-[10px] font-black font-mono uppercase mt-0.5',
                        issue.es_alerta_mantenimiento ? 'text-amber-600' : 'text-red-600'
                    ]">
                        QR: {{ issue.codigo_qr }}
                    </p>
                </div>

                <!-- Badge de estado -->
                <div class="shrink-0 text-right">
                    <span
                        v-if="issue.es_alerta_mantenimiento"
                        class="inline-block bg-amber-100 text-amber-700 border border-amber-200 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full"
                    >
                        Mantenimiento próximo
                    </span>
                    <StatusBadge v-else :status="issue.estado" />

                    <p
                        v-if="issue.es_alerta_mantenimiento && issue.proximo_mantenimiento"
                        class="text-[10px] text-neutral-400 font-medium italic mt-1"
                    >
                        {{ issue.proximo_mantenimiento }}
                    </p>
                </div>

                <!-- Detalle -->
                <div :class="[
                    'flex items-start gap-2 p-2.5 rounded-xl border text-xs italic font-medium md:max-w-xs w-full',
                    issue.es_alerta_mantenimiento
                        ? 'bg-amber-50/50 border-amber-100 text-amber-900/80'
                        : 'bg-red-50/50 border-red-100 text-red-900/70'
                ]">
                    <MessageSquareWarning
                        class="w-3.5 h-3.5 shrink-0 mt-0.5"
                        :class="issue.es_alerta_mantenimiento ? 'text-amber-400' : 'text-red-400'"
                    />
                    <span>
                        {{ issue.es_alerta_mantenimiento
                            ? 'Se acerca la fecha de mantenimiento preventivo. Favor coordinar con el taller.'
                            : (issue.observacion_item || 'Sin detalles adicionales de la incidencia.')
                        }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Empty state (por si acaso) -->
        <div v-if="issues.length === 0" class="flex flex-col items-center justify-center py-12 text-neutral-300">
            <ShieldAlert class="w-12 h-12 mb-3" />
            <p class="text-sm font-medium">No hay alertas pendientes.</p>
        </div>
    </div>
</template>
