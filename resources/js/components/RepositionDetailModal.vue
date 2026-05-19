<script setup lang="ts">
import StatusBadge from '@/components/shared/StatusBadge.vue';
import {
    XIcon, RefreshCcw, User, Calendar, CalendarCheck2,
    Wrench, RefreshCw, UserCheck, AlertTriangle,
    CheckCircle, XCircle, AlignLeft, Package2, ClockAlert
} from 'lucide-vue-next';

defineProps<{
    show: boolean;
    rep: any;
}>();

defineEmits(['close']);

const iconoTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return Wrench;
    if (tipo === 'Reemplazo')  return RefreshCw;
    return UserCheck;
};
const labelTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'Reparación';
    if (tipo === 'Reemplazo')  return 'Reemplazo';
    if (tipo === 'Desbloqueo') return 'Desbloqueo';
    return 'Sin definir';
};
const descTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'El ítem fue enviado a reparar';
    if (tipo === 'Reemplazo')  return 'Se entregó un ítem equivalente';
    if (tipo === 'Desbloqueo') return 'Se realizó una compensación económica';
    return 'Tipo aún no definido';
};
const colorTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'bg-purple-50 text-purple-700 border-purple-200';
    if (tipo === 'Reemplazo')  return 'bg-blue-50 text-blue-700 border-blue-200';
    if (tipo === 'Desbloqueo') return 'bg-amber-50 text-amber-700 border-amber-200';
    return 'bg-neutral-100 text-neutral-500 border-neutral-200';
};
const colorTipoOrigen = (tipo: string) => {
    if (tipo === 'Equipo')      return 'bg-red-50 text-red-700 border-red-100';
    if (tipo === 'Herramienta') return 'bg-blue-50 text-blue-700 border-blue-100';
    return 'bg-emerald-50 text-emerald-700 border-emerald-100';
};
const estaVencida = (rep: any) =>
    rep?.estado === 'Pendiente' && !!rep?.fecha_limite &&
    new Date(rep.fecha_limite) < new Date();
</script>

<template>
    <div v-if="show && rep" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md" @click="$emit('close')"></div>

        <div class="relative bg-white rounded-4xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-300 flex flex-col">

            <!-- Header -->
            <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-[#1a3a5a] rounded-xl shadow-lg">
                        <RefreshCcw class="w-6 h-6 text-white"/>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-neutral-800 uppercase tracking-tighter leading-none">
                            Detalle de Reposición
                        </h2>
                        <p class="text-neutral-500 text-sm font-medium mt-1">
                            Registrado el {{ rep.created_at }} · por {{ rep.registrado_por }}
                            <span class="text-neutral-400">({{ rep.registrado_username }})</span>
                        </p>
                    </div>
                </div>
                <button @click="$emit('close')"
                    class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                    <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                </button>
            </div>

            <!-- Cuerpo -->
            <div class="p-8 pt-5 overflow-y-auto custom-scrollbar space-y-6 flex-1 bg-neutral-50/30">

                <!-- Ítem -->
                <div class="p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <Package2 class="w-4 h-4"/> Ítem que requiere reposición
                    </h3>
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2">
                            <p class="text-lg font-black text-neutral-900 leading-tight">{{ rep.nombre_origen }}</p>
                            <p v-if="rep.equipo_padre" class="text-sm text-neutral-500 italic">
                                Accesorio de: <span class="font-bold text-neutral-700">{{ rep.equipo_padre }}</span>
                            </p>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span :class="['px-2.5 py-1 rounded-lg text-[11px] font-black uppercase border', colorTipoOrigen(rep.tipo_origen)]">
                                    {{ rep.tipo_origen }}
                                </span>
                                <StatusBadge :status="rep.estado_dano"/>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <span :class="[
                                'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[12px] font-black uppercase border',
                                rep.estado === 'Cumplida'   ? 'bg-green-50 text-green-700 border-green-200' :
                                rep.estado === 'Incumplida' ? 'bg-red-50 text-red-700 border-red-200' :
                                                              'bg-amber-50 text-amber-700 border-amber-200'
                            ]">
                                <CheckCircle v-if="rep.estado === 'Cumplida'" class="w-4 h-4"/>
                                <XCircle v-else-if="rep.estado === 'Incumplida'" class="w-4 h-4"/>
                                <AlertTriangle v-else class="w-4 h-4"/>
                                {{ rep.estado }}
                            </span>
                            <span v-if="estaVencida(rep)"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black uppercase bg-red-50 text-red-600 border border-red-100">
                                <AlertTriangle class="w-3.5 h-3.5"/> Plazo vencido
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Responsable + Registrado por -->
                <div class="p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <!-- Responsable de la reposición (prestatario) -->
                        <div class="space-y-2">
                            <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                                <User class="w-4 h-4"/> Responsable
                            </p>
                            <p class="text-base font-bold text-neutral-900 leading-tight">{{ rep.borrower_nombre }}</p>
                            <span class="inline-block px-2 py-0.5 rounded-lg bg-neutral-100 text-[13px] font-black text-neutral-700 border border-neutral-200">
                                CI: {{ rep.borrower_ci }}
                            </span>
                        </div>

                        <!-- Encargado que registró -->
                        <div class="space-y-2 border-l border-neutral-100 pl-4">
                            <p class="flex items-center gap-2 text-[13px] font-black text-emerald-700 uppercase tracking-widest">
                                <UserCheck class="w-4 h-4"/> Registrado por
                            </p>
                            <p class="text-base font-bold text-neutral-900 leading-tight">{{ rep.registrado_por }}</p>
                            <span class="inline-block px-2 py-0.5 rounded-lg bg-emerald-50 text-[11px] font-black text-emerald-700 border border-emerald-100">
                                {{ rep.registrado_username }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Acuerdo -->
                <div class="p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm space-y-3">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <Wrench class="w-4 h-4"/> Acuerdo de reposición
                    </h3>
                    <div v-if="rep.tipo_reposicion" class="flex items-center gap-3 flex-wrap">
                        <span :class="['inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black uppercase border', colorTipo(rep.tipo_reposicion)]">
                            <component :is="iconoTipo(rep.tipo_reposicion)" class="w-4 h-4"/>
                            {{ labelTipo(rep.tipo_reposicion) }}
                        </span>
                        <p class="text-sm text-neutral-500 italic">{{ descTipo(rep.tipo_reposicion) }}</p>
                    </div>
                    <div v-else class="flex items-center gap-2 text-neutral-400">
                        <AlertTriangle class="w-4 h-4"/>
                        <p class="text-sm italic">Sin acuerdo definido aún</p>
                    </div>
                    <div v-if="rep.nombre_nuevo_item"
                        class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                        <RefreshCw class="w-4 h-4 text-green-600 shrink-0"/>
                        <div>
                            <p class="text-[11px] font-black text-green-700 uppercase tracking-widest">Ítem de reemplazo registrado</p>
                            <p class="text-sm font-bold text-green-800">{{ rep.nombre_nuevo_item }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cronología — igual a LoanDetailModal -->
                <div class="space-y-3">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <Calendar class="w-4 h-4"/> Cronología
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-3xl border border-neutral-200">
                        <div class="flex flex-col gap-3">
                            <span class="text-[13px] font-bold text-blue-700 uppercase tracking-tighter">Registro</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <Calendar class="w-4 h-4 text-blue-700"/>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Fecha</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ rep.created_at }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-orange-700 uppercase tracking-tighter">Plazo</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <ClockAlert class="w-4 h-4 text-orange-700"/>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Fecha límite</p>
                                    <p :class="['text-sm font-bold', estaVencida(rep) ? 'text-red-600' : 'text-neutral-800']">
                                        {{ rep.fecha_limite ?? 'Sin límite' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-green-600 uppercase tracking-tighter">Resolución</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <CalendarCheck2 class="w-4 h-4 text-green-600"/>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-green-600 uppercase tracking-widest leading-none mb-1">Fecha resolución</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ rep.fecha_cumplimiento ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observación -->
                <div class="p-5 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm space-y-2">
                    <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                        <AlignLeft class="w-4 h-4"/> Observación
                    </p>
                    <p class="text-sm text-neutral-700 italic leading-relaxed">
                        {{ rep.observacion || 'Sin observaciones registradas.' }}
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 bg-neutral-50 border-t border-neutral-100">
                <button @click="$emit('close')"
                    class="w-full py-3.5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</template>
