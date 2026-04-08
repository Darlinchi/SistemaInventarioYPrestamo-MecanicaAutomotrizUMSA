<script setup lang="ts">
import {
    XIcon, ClipboardPen, Cog, Building2, CalendarClock, CalendarCheck2, ClockAlert, History, CalendarCog,
    Calendar, Clock, Package, Wrench, AlignLeft, Loader2, Image
} from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    show: boolean;
    maint: any;
    form: any;
}>();

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-100 flex items-center justify-center bg-black/40 backdrop-blur-md p-6 lg:p-12">
        <div class="bg-white w-full max-w-3xl rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh] animate-in zoom-in duration-300">

            <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-[#1a3a5a] rounded-xl shadow-lg">
                        <ClipboardPen class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tighter text-neutral-900 leading-none">Finalizar Mantenimiento</h2>
                        <p class="text-neutral-700 text-sm font-medium mt-1">Recepción de equipo reparado</p>

                    </div>
                </div>
                <button @click="$emit('close')" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                    <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                </button>
            </div>

            <div class="p-8 pt-2 overflow-y-auto custom-scrollbar space-y-6 flex-1 bg-neutral-50/30">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <Cog class="w-4 h-4" /> Equipo en Reparación
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
                            <Building2 class="w-4 h-4" /> Empresa Encargada
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">{{ maint?.companies[0]?.nombre_empresa }}</p>
                        <p class="text-[16px] text-[#1a3a5a] font-mono tracking-tighter">{{ maint?.companies[0]?.telefono || 'S/N' }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <CalendarClock class="w-4 h-4" /> Cronología del mantenimiento
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-2xl border border-neutral-200 mt-4">
                        <div class="flex flex-col gap-3">
                            <span class="text-[13px] font-bold text-blue-700 uppercase tracking-tighter">Registro de Salida</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Calendar class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Fecha Salida</p>
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
                            <span class="text-[13px] font-bold text-orange-700 uppercase tracking-tighter">Retorno (Programado)</span>
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
                                    <CalendarCheck2 class="w-4 h-4 text-green-700" />
                                </div>
                                <div>
                                    <Label for="fecha_retorno" class="text-[11px] font-black text-green-700 uppercase tracking-widest leading-none mb-1">Fecha Retorno</Label>
                                    <Input type="date" v-model="form.fecha_retorno" class="h-8 text-xs rounded-lg" />
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <History class="w-4 h-4 text-green-600" />
                                </div>
                                <div>
                                    <Label for="hora_fin" class="text-[11px] font-black text-green-700 uppercase tracking-widest leading-none mb-1">Hora Retorno</Label>
                                    <Input type="time" v-model="form.hora_fin" class="h-8 text-xs rounded-lg" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-2xl border border-neutral-200 mt-4">

                    <div class="space-y-2">
                        <Label class="text-[12px] font-black uppercase text-neutral-800 tracking-widest flex items-center gap-2 px-1">
                            <Wrench class="w-4 h-4" /> Tipo de Trabajo
                        </Label>
                        <div class="py-3.5 px-5 bg-neutral-100/80 rounded-2xl border border-neutral-200 text-sm font-bold text-[#1a3a5a] flex items-center gap-2 shadow-inner">
                            <div class="w-2 h-2 rounded-full bg-[#1a3a5a] animate-pulse"></div>
                            {{ maint?.tipo_mantenimiento }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[12px] font-black uppercase text-neutral-800 tracking-widest flex items-center gap-2 px-1">
                            <Package class="w-4 h-4" /> Estado tras revisión
                        </Label>
                        <select v-model="form.estado_equipo"
                            class="w-full text-sm font-bold rounded-2xl border-neutral-200 bg-white shadow-sm focus:ring-4 focus:ring-[#1a3a5a]/5 transition-all py-3 px-4 outline-none border-2 focus:border-[#1a3a5a] appearance-none cursor-pointer">
                            <option value="Reparado">Reparado (Disponible)</option>
                            <option value="Dañado">Dañado (No Reparado)</option>
                            <option value="Baja">Dar de Baja</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-[12px] font-black uppercase text-violet-950 tracking-widest flex items-center gap-2 px-1">
                            <CalendarCog class="w-4 h-4" />Prox. Mantenimiento
                        </Label>
                        <div class="relative group">
                            <Input
                                type="date"
                                v-model="form.fecha_proximo_mantenimiento"
                                class="w-full text-sm font-bold rounded-2xl border-2 border-violet-100 bg-white focus:ring-4 focus:ring-violet-500/5 transition-all py-6 px-4 outline-none focus:border-violet-600 calendar-tight"
                            />
                        </div>
                        <InputError :message="form.errors.fecha_proximo_mantenimiento" />
                    </div>
                </div>

                <div class="space-y-2 px-2">
                    <Label class="text-[13px] font-black uppercase text-neutral-700 tracking-widest">
                        <AlignLeft class="w-4 h-4 inline mr-1" /> Informe de actividad / Reparaciones
                    </Label>
                    <Textarea v-model="form.observacion"
                        placeholder="Detalle las reparaciones realizadas por la empresa..."
                        class="bg-white border-neutral-200 text-sm rounded-3xl min-h-[100px] focus:ring-[#1a3a5a]/10" />
                </div>
            </div>

            <div class="p-8 bg-neutral-50 border-t border-neutral-100 flex gap-4 no-print">
                <button @click="$emit('close')" class="flex-1 py-3.5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm">
                    Cerrar
                </button>
                <button @click="$emit('confirm')" :disabled="form.processing" class="flex-1 py-3.5 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-900/20 active:scale-95">
                    <Loader2 v-if="form.processing" class="mr-2 animate-spin w-4 h-4 text-white"/>
                    Confirmar Finalización
                </button>
            </div>
        </div>
    </div>
</template>
