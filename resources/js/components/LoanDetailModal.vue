<script setup lang="ts">
import StatusBadge from '@/components/shared/StatusBadge.vue';
import {
    XIcon, NotebookText, User, BookMarked, CalendarClock,
    Calendar, Clock, Package, Image, CornerDownRight,
    AlignLeft, Printer
} from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    loan: any;
}>();

const emit = defineEmits(['close']);

const imprimirReporte = () => window.print();

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md no-print" @click="$emit('close')"></div>

        <div class="relative bg-white rounded-4xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-300 flex flex-col">

            <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-[#1a3a5a] rounded-xl shadow-lg">
                        <NotebookText class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-neutral-800 uppercase tracking-tighter leading-none">Resumen de Devolución</h2>
                        <p class="text-neutral-700 text-sm font-medium mt-1">Comprobante de recepción - Taller de Mecánica</p>
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
                            <User class="w-4 h-4" /> Responsable
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">
                            {{ loan?.borrower.nombresP }} {{ loan?.borrower.apellidosP }}
                        </p>
                        <div class="flex gap-2">
                            <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[13px] font-black text-[#1a3a5a]">
                                {{ loan?.borrower.teacher ? 'DOCENTE' : 'AUXILIAR' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-lg bg-neutral-100 text-[13px] font-black text-neutral-700 border border-neutral-200">
                                CI: {{ loan?.borrower.cedula_identidad }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 border-l border-neutral-100 pl-4 font-black">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <BookMarked class="w-4 h-4" /> Materia Asignada
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">{{ loan?.subject.nombre_materia }}</p>
                        <p class="text-[15px] text-[#1a3a5a] font-mono tracking-tighter">{{ loan?.subject.sigla }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <CalendarClock class="w-4 h-4" /> Cronología del préstamo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-3xl border border-neutral-200 mt-4">
                        <div class="flex flex-col gap-3">
                            <span class="text-[13px] font-bold text-blue-700 uppercase tracking-tighter">Registro de Salida</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Calendar class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Fecha Salida</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_salida }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Clock class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Hora Inicio</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_inicio }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-orange-700 uppercase tracking-tighter">Retorno (Previsto)</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Calendar class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Fecha Limite</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_retorno_prevista }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Clock class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Hora Fin</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_fin_prevista }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-green-600 uppercase tracking-tighter">Retorno Real</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Calendar class="w-4 h-4 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-green-500 uppercase tracking-widest leading-none mb-1">Fecha Retorno</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_retorno }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Clock class="w-4 h-4 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-green-500 uppercase tracking-widest leading-none mb-1">Hora Entrada</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_fin }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mb-2">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <Package class="w-4 h-4" /> Estado Final de Equipos y Herramientas
                    </h3>

                    <div v-for="item in loan?.all_items" :key="item.id"
                        class="border border-neutral-200 rounded-2xl overflow-hidden shadow-sm bg-white mb-3 transition-all hover:border-neutral-300">

                        <div class="flex items-center justify-between p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl bg-neutral-50 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0 shadow-inner">
                                    <img v-if="item.foto" :src="'/storage/' + item.foto" class="object-cover w-full h-full" />
                                    <Image v-else class="w-7 h-7 text-neutral-300" />
                                </div>

                                <div class="flex flex-col gap-1">
                                    <p class="text-[15px] font-bold text-neutral-900 leading-tight">{{ item.nombre_mostrar }}</p>
                                    <span :class="[
                                        'w-fit px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border',
                                        item.es_equipo ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100'
                                    ]">
                                        {{ item.es_equipo ? 'EQUIPO' : 'HERRAMIENTA' }}
                                    </span>
                                    <span v-if="item.codigo_qr" class="text-[12px] font-mono text-blue-400">
                                        #{{ item.codigo_qr }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                <StatusBadge :status="item.estado_devolucion" />
                            </div>
                        </div>

                        <div v-if="item.accessories && item.accessories.length > 0" class="bg-neutral-50/50 p-4 border-t border-neutral-100">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="h-px flex-1 bg-neutral-200"></div>
                                    <p class="text-[11px] font-black text-neutral-800 uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span> Accesorios del Equipo
                                </p>
                                <div class="h-px flex-1 bg-neutral-200"></div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div v-for="acc in item.accessories" :key="acc.id"

                                    class="flex items-center justify-between bg-white px-3 py-1.5 rounded-lg border border-neutral-200/60 shadow-sm">
                                    <span class="text-[13px] font-medium text-neutral-800 flex items-center gap-1">
                                        <CornerDownRight class="w-4 h-4 text-blue-400"/>  {{ acc.nombre_accesorio }}
                                    </span>
                                    <StatusBadge :status="acc.estado_accesorio" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm space-y-2">
                    <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                        <AlignLeft class="w-4 h-4" /> Notas de recepción final
                    </p>
                    <p class="text-sm text-neutral-700 italic leading-relaxed">
                        {{ loan?.observacion || 'El préstamo fue devuelto sin observaciones adicionales registradas.' }}
                    </p>
                </div>
            </div>

            <div class="p-8 bg-neutral-50 border-t border-neutral-100 flex gap-4 no-print">
                <button @click="$emit('close')" class="flex-1 py-3.5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm">
                    Cerrar
                </button>
                <button @click="imprimirReporte" class="flex-1 py-3.5 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-900/20 active:scale-95">
                    <Printer class="w-4 h-4 mr-2" />
                    Imprimir Comprobante
                </button>
            </div>
        </div>
    </div>
</template>
