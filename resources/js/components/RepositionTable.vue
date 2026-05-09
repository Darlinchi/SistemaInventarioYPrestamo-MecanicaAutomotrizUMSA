<script setup lang="ts">
import StatusBadge from '@/components/shared/StatusBadge.vue';
import { CheckCircle, XCircle, Clock, Wrench, RefreshCw, DollarSign, Package2 } from 'lucide-vue-next';

interface Reposition {
    id: number;
    nombre_origen: string;
    tipo_origen: string;
    estado_dano: string;
    borrower_nombre: string;
    borrower_ci: string;
    tipo_reposicion: string;
    estado: string;
    fecha_limite: string | null;
    fecha_cumplimiento: string | null;
    observacion: string | null;
    nombre_nuevo_item: string | null;
    registrado_por: string;
    created_at: string;
}

const props = defineProps<{
    repositions: Reposition[];
    canEdit?: boolean;
}>();

const emit = defineEmits(['marcarCumplida', 'marcarIncumplida']);

const iconoTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return Wrench;
    if (tipo === 'Reemplazo')  return RefreshCw;
    return DollarSign;
};

const labelTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'Reparación';
    if (tipo === 'Reemplazo')  return 'Reemplazo';
    return 'Desbloqueo';
};

const colorTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'bg-purple-50 text-purple-700 border-purple-100';
    if (tipo === 'Reemplazo')  return 'bg-blue-50 text-blue-700 border-blue-100';
    return 'bg-amber-50 text-amber-700 border-amber-100';
};

const estaVencida = (rep: Reposition): boolean => {
    if (rep.estado !== 'Pendiente' || !rep.fecha_limite) return false;
    return new Date(rep.fecha_limite) < new Date();
};
</script>

<template>
    <div class="space-y-3">
        <!-- Vacío -->
        <div v-if="!repositions.length"
            class="text-center py-16 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200">
            <Package2 class="w-10 h-10 mx-auto text-neutral-300 mb-3" />
            <p class="text-neutral-500 font-medium text-sm">No hay acuerdos de reposición registrados.</p>
        </div>

        <!-- Cards de reposición -->
        <div
            v-for="rep in repositions"
            :key="rep.id"
            :class="[
                'bg-white rounded-2xl border shadow-sm overflow-hidden transition-all',
                rep.estado === 'Pendiente' && estaVencida(rep)
                    ? 'border-red-200 shadow-red-50'
                    : rep.estado === 'Cumplida'
                        ? 'border-green-200'
                        : rep.estado === 'Incumplida'
                            ? 'border-red-200'
                            : 'border-neutral-200'
            ]"
        >
            <!-- Header de la card -->
            <div class="flex items-center justify-between px-5 py-3 border-b border-neutral-100 bg-neutral-50/50">
                <div class="flex items-center gap-3">
                    <!-- Badge tipo reposición -->
                    <span :class="['flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-black uppercase border', colorTipo(rep.tipo_reposicion)]">
                        <component :is="iconoTipo(rep.tipo_reposicion)" class="w-3 h-3" />
                        {{ labelTipo(rep.tipo_reposicion) }}
                    </span>
                    <!-- Ítem origen -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm font-bold text-neutral-800">{{ rep.nombre_origen }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black uppercase bg-neutral-100 text-neutral-500">
                            {{ rep.tipo_origen }}
                        </span>
                    </div>
                    <!-- Badge estado daño -->
                    <StatusBadge :status="rep.estado_dano" />
                </div>
                <!-- Estado del acuerdo -->
                <div class="flex items-center gap-2">
                    <span v-if="rep.estado === 'Pendiente' && estaVencida(rep)"
                        class="flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-black uppercase bg-red-50 text-red-600 border border-red-100">
                        <Clock class="w-3 h-3" /> Vencida
                    </span>
                    <StatusBadge :status="rep.estado" />
                </div>
            </div>

            <!-- Cuerpo -->
            <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Responsable -->
                <div>
                    <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">Responsable</p>
                    <p class="text-sm font-bold text-neutral-800 leading-tight">{{ rep.borrower_nombre }}</p>
                    <p class="text-xs text-neutral-400 font-mono">CI: {{ rep.borrower_ci }}</p>
                </div>

                <!-- Fechas -->
                <div>
                    <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">Fecha límite</p>
                    <p :class="['text-sm font-bold', estaVencida(rep) ? 'text-red-600' : 'text-neutral-800']">
                        {{ rep.fecha_limite ?? 'Sin límite' }}
                    </p>
                    <p v-if="rep.fecha_cumplimiento" class="text-xs text-green-600 font-medium mt-0.5">
                        Cumplida: {{ rep.fecha_cumplimiento }}
                    </p>
                </div>

                <!-- Nuevo ítem (Reemplazo) -->
                <div>
                    <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">
                        {{ rep.tipo_reposicion === 'Reemplazo' ? 'Ítem nuevo' : 'Registrado por' }}
                    </p>
                    <p class="text-sm font-medium text-neutral-700">
                        {{ rep.tipo_reposicion === 'Reemplazo'
                            ? (rep.nombre_nuevo_item ?? 'Sin asignar')
                            : rep.registrado_por }}
                    </p>
                </div>

                <!-- Observación -->
                <div>
                    <p class="text-[10px] font-black text-neutral-400 uppercase tracking-widest mb-1">Observación</p>
                    <p class="text-xs text-neutral-600 italic leading-relaxed">
                        {{ rep.observacion || 'Sin observación.' }}
                    </p>
                </div>
            </div>

            <!-- Acciones (solo si Pendiente y puede editar) -->
            <div v-if="rep.estado === 'Pendiente' && canEdit"
                class="px-5 py-3 bg-neutral-50 border-t border-neutral-100 flex justify-end gap-2">
                <button
                    @click="$emit('marcarIncumplida', rep)"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-bold text-red-600 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100 transition-all active:scale-95"
                >
                    <XCircle class="w-4 h-4" /> Marcar Incumplida
                </button>
                <button
                    @click="$emit('marcarCumplida', rep)"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-bold text-green-700 bg-green-50 border border-green-100 rounded-xl hover:bg-green-100 transition-all active:scale-95"
                >
                    <CheckCircle class="w-4 h-4" /> Marcar Cumplida
                </button>
            </div>
        </div>
    </div>
</template>
