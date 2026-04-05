<script setup lang="ts">
import StatusBadge from '@/components/shared/StatusBadge.vue';
import {
    XIcon, ClipboardList, Package, Building2, Wrench, CalendarCheck, ClockAlert, History,
    CalendarClock, Calendar, Clock, AlignLeft, Printer, Image
} from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    maint: any;
}>();

const emit = defineEmits(['close']);

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md no-print" @click="$emit('close')"></div>

        <div class="relative bg-white rounded-[2.5rem] shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-300 flex flex-col">

            <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-[#1a3a5a] rounded-xl shadow-lg">
                        <ClipboardList class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-neutral-800 uppercase tracking-tighter leading-none">Detalle del Mantenimiento</h2>
                        <p class="text-neutral-700 text-sm font-medium mt-1">Hoja de servicio técnico - Taller de Mecánica</p>
                    </div>
                </div>
                <button @click="$emit('close')" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group no-print">
                    <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                </button>
            </div>

            <div class="p-8 pt-2 overflow-y-auto custom-scrollbar space-y-6 flex-1 bg-neutral-50/30">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <Cog class="w-4 h-4" /> Información del Equipo
                        </p>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-neutral-50 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0 shadow-inner">
                                <img v-if="maint?.equipment.foto_equipo" :src="'/storage/' + maint.equipment.foto_equipo" class="w-full h-full object-cover" />
                                <Image v-else class="w-7 h-7 text-neutral-200" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-base font-bold text-neutral-900">{{ maint?.equipment.nombre_equipo }}</p>
                                <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[13px] font-black text-[#1a3a5a]">
                                    Código QR: {{ maint?.equipment.codigo_qr || 'N/A' }}
                                </span>
                                <StatusBadge :status="maint?.estado_final_equipo || 'En Revisión'" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1 border-l border-neutral-100 pl-4 font-black">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <Building2 class="w-4 h-4" /> Empresa Responsable
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">{{ maint?.company?.nombre_empresa || maint?.companies?.[0]?.nombre_empresa || 'Empresa Externa' }}</p>
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <Wrench class="w-4 h-4" /> Tipo de Trabajo
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">Mantenimiento {{ maint?.tipo_mantenimiento }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <CalendarClock class="w-4 h-4" /> Registro de Tiempos
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-2xl border border-neutral-200 mt-4">
                        <div class="flex flex-col gap-3">
                            <span class="text-[13px] font-bold text-blue-700 uppercase tracking-tighter">Registro de Salida</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Calendar class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Fecha Mantenimiento</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ maint?.fecha_mantenimiento }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Clock class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Hora Inicio</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ maint?.hora_inicio }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-orange-700 uppercase tracking-tighter">Retorno (Opcional)</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <CalendarClock class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Fecha Limite</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ maint?.fecha_retorno_estimado }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <ClockAlert class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Hora Fin</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ maint?.hora_fin_estimado }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-green-700 uppercase tracking-tighter">Retorno Real</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <CalendarCheck class="w-4 h-4 text-green-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-green-700 uppercase tracking-widest leading-none mb-1">Fecha Retorno</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ maint?.fecha_retorno }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <History class="w-4 h-4 text-green-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-green-700 uppercase tracking-widest leading-none mb-1">Hora Entrada</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ maint?.hora_fin }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm space-y-2">
                    <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                        <AlignLeft class="w-4 h-4" /> Informe Técnico de Actividad
                    </p>
                    <p class="text-sm text-neutral-700 italic leading-relaxed">
                        {{ maint?.actividad || 'No se registraron detalles técnicos de la actividad realizada.' }}
                    </p>
                </div>
            </div>

            <div class="p-8 bg-neutral-50 border-t border-neutral-100 flex gap-4 no-print">
                <button @click="$emit('close')" class="flex-1 py-3.5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm">
                    Cerrar
                </button>
                <button @click="" class="flex-1 py-3.5 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-900/20 active:scale-95">
                    <Printer class="w-4 h-4 mr-2" />
                    Imprimir Comprobante
                </button>
            </div>
        </div>
    </div>
</template>
