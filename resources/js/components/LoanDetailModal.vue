<script setup lang="ts">
import StatusBadge from '@/components/shared/StatusBadge.vue';
import {
    XIcon, NotebookText, User, BookMarked, CalendarClock, ClockAlert, CalendarCheck, History,
    Calendar, Clock, Package, Image, CornerDownRight, FileText, AlignLeft, UserCog
} from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    loan: any;
}>();

const emit = defineEmits(['close']);

const handleGenerateReport = (id: number | string) => {
    window.open(`/dashboard/loans/${id}/report`, '_blank');
};

// Tipo de prestatario legible
const tipoPrestatario = (loan: any): string => {
    if (loan?.borrower?.teacher)   return 'DOCENTE';
    if (loan?.borrower?.assistant) return 'AUXILIAR';
    return 'ESTUDIANTE';
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md no-print" @click="$emit('close')"></div>

        <div class="relative bg-white rounded-4xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-300 flex flex-col">

            <!-- Header -->
            <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-[#1a3a5a] rounded-xl shadow-lg">
                        <NotebookText class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-neutral-800 uppercase tracking-tighter leading-none">Resumen de Devolución</h2>
                        <p class="text-neutral-700 text-sm font-medium mt-1">Comprobante de recepción — Taller de Mecánica</p>
                    </div>
                </div>
                <button @click="$emit('close')" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group no-print">
                    <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                </button>
            </div>


            <div class="p-8 pt-4 overflow-y-auto custom-scrollbar space-y-6 flex-1 bg-neutral-50/30">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm">
                    <!-- Encargado que entregó -->
                    <div class="space-y-2 border-l border-neutral-100 pl-4 flex flex-col justify-center">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                            <UserCog class="w-4 h-4" /> Entregado por
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">{{ loan?.user?.name ?? '—' }}</p>
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-[11px] font-black text-[#1a3a5a] border border-emerald-100 w-fit">
                            {{ loan?.user?.username ?? '' }}
                        </span>
                    </div>

                    <!-- Quién recibió la devolución -->
                    <div v-if="loan?.return_user" class="space-y-2 border-l border-neutral-100 pl-4 flex flex-col justify-center">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                            <UserCog class="w-4 h-4" /> Recibido por
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">{{ loan?.return_user?.name ?? '—' }}</p>
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-[11px] font-black text-[#1a3a5a] border border-emerald-100 w-fit">
                            {{ loan?.return_user?.username ?? '' }}
                        </span>
                    </div>
                </div>

                <!-- Responsable + Materia -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm">
                    <div class="space-y-2">
                        <!-- SECCIÓN CONDICIONAL: Solo se muestra si es AUXILIAR y existe un docente asignado -->
                        <div v-if="loan.borrower.assistant && loan.docente_asignado" class="mb-4 pb-4 border-b border-neutral-100">
                            <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                                <UserCog class="w-4 h-4" /> Docente Relacionado
                            </p>
                            <p class="text-base font-bold text-neutral-900 leading-tight">
                                {{ loan.docente_asignado }}
                            </p>
                            <p class="text-[10px] font-bold text-neutral-400 uppercase italic">Supervisor de Auxiliatura</p>
                        </div>

                        <!-- RESPONSABLE: Se muestra siempre -->
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <User class="w-4 h-4" /> Responsable del Préstamo
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">
                            {{ loan?.borrower?.teacher?.titulo }} {{ loan?.borrower?.apellidoPaterno }} {{ loan?.borrower?.apellidoMaterno }} {{ loan?.borrower?.nombres }}
                        </p>

                        <div class="flex gap-2 flex-wrap mt-2">
                            <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[13px] font-black text-[#1a3a5a]">
                                {{
                                    loan.borrower.teacher
                                    ? 'DOCENTE'
                                    : (loan.borrower.assistant ? 'AUXILIAR' : 'ESTUDIANTE')
                                }}
                            </span>
                            <span class="px-2 py-0.5 rounded-lg bg-neutral-100 text-[13px] font-black text-neutral-700 border border-neutral-200">
                                CI: {{ loan?.borrower?.cedula_identidad }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2 border-l border-neutral-100 pl-4 flex flex-col justify-center">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                            <BookMarked class="w-4 h-4" /> Materia Asignada
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">{{ loan?.subject?.nombre_materia }}</p>
                        <p class="text-[15px] text-[#1a3a5a] font-mono tracking-tighter">{{ loan?.subject?.sigla }}</p>
                    </div>
                </div>

                <!-- SECCIÓN DE AUTORIZACIÓN (Solo si existe archivo_autorizacion) -->
                <div v-if="loan.archivo_autorizacion" class="p-6 bg-blue-50/40 rounded-3xl border border-blue-100 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white rounded-2xl shadow-sm text-blue-600 border border-blue-50">
                            <FileText class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Autorización de Dirección</p>
                            <p class="text-sm font-bold text-neutral-800 uppercase tracking-tighter italic">
                                "{{ loan.motivo_autorizacion || 'Proyecto de Grado / Práctica' }}"
                            </p>
                        </div>
                    </div>
                    <a
                        :href="'/storage/' + loan.archivo_autorizacion"
                        target="_blank"
                        class="w-full sm:w-auto bg-white text-blue-700 px-5 py-3 rounded-xl font-black text-[11px] uppercase tracking-widest border border-blue-200 hover:bg-blue-50 transition-all shadow-sm flex items-center justify-center gap-2 active:scale-95"
                    >
                        <FileText class="w-4 h-4" /> Ver Nota PDF
                    </a>
                </div>

                <!-- Cronología -->
                <div class="space-y-3">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <CalendarClock class="w-4 h-4" /> Cronología del préstamo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-3xl border border-neutral-200">

                        <!-- Salida -->
                        <div class="flex flex-col gap-3">
                            <span class="text-[13px] font-bold text-blue-700 uppercase tracking-tighter">Salida</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <Calendar class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Fecha</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_salida ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <Clock class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Hora inicio</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_inicio ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Previsto -->
                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-orange-700 uppercase tracking-tighter">Previsto</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <CalendarClock class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Fecha límite</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_retorno_prevista ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <ClockAlert class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Hora fin</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_fin_prevista ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Real -->
                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-green-600 uppercase tracking-tighter">Retorno real</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <CalendarCheck class="w-4 h-4 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-green-600 uppercase tracking-widest leading-none mb-1">Fecha retorno</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_retorno ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm shrink-0">
                                    <History class="w-4 h-4 text-green-600" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-green-600 uppercase tracking-widest leading-none mb-1">Hora entrada</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_fin ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ítems devueltos -->
                <div class="space-y-4">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <Package class="w-4 h-4" /> Estado final de equipos y herramientas
                    </h3>

                    <div v-if="!loan?.all_items?.length" class="text-center py-8 text-neutral-400 text-sm">
                        Sin ítems registrados en esta devolución.
                    </div>

                    <div
                        v-for="item in loan?.all_items"
                        :key="item.id"
                        class="border border-neutral-200 rounded-2xl overflow-hidden shadow-sm bg-white transition-all hover:border-neutral-300"
                    >
                        <!-- Fila principal del ítem -->
                        <div class="flex items-center justify-between p-4 gap-4">
                            <div class="flex items-center gap-4">
                                <!-- Foto -->
                                <div class="w-14 h-14 rounded-xl bg-neutral-50 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0 shadow-inner">
                                    <img v-if="item.foto" :src="'/storage/' + item.foto" class="object-cover w-full h-full" alt="" />
                                    <Image v-else class="w-7 h-7 text-neutral-300" />
                                </div>
                                <!-- Info -->
                                <div class="flex flex-col gap-1">
                                    <p class="text-[15px] font-bold text-neutral-900 leading-tight">{{ item.nombre_mostrar }}</p>
                                    <span :class="[
                                        'w-fit px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border',
                                        item.es_equipo
                                            ? 'bg-red-50 text-red-700 border-red-100'
                                            : 'bg-blue-50 text-blue-700 border-blue-100'
                                    ]">
                                        {{ item.es_equipo ? 'EQUIPO' : 'HERRAMIENTA' }}
                                    </span>
                                    <span v-if="item.codigo_qr" class="text-[12px] font-mono text-blue-400">
                                        #{{ item.codigo_qr }}
                                    </span>
                                </div>
                            </div>
                            <!-- Estado de devolución desde return_details -->
                            <StatusBadge :status="item.estado_devolucion ?? 'Disponible'" />
                        </div>

                        <!-- Accesorios (estado HISTÓRICO desde return_detail_accessories) -->
                        <div
                            v-if="item.accessories?.length"
                            class="bg-neutral-50/50 p-4 border-t border-neutral-100"
                        >
                            <div class="flex items-center gap-2 mb-3">
                                <div class="h-px flex-1 bg-neutral-200"></div>
                                <p class="text-[11px] font-black text-neutral-600 uppercase tracking-widest flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full inline-block"></span>
                                    Accesorios del equipo
                                </p>
                                <div class="h-px flex-1 bg-neutral-200"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div
                                    v-for="acc in item.accessories"
                                    :key="acc.id"
                                    class="flex items-center justify-between bg-white px-3 py-2 rounded-lg border border-neutral-200/60 shadow-sm gap-2"
                                >
                                    <span class="flex items-center gap-2 text-[13px] font-medium text-neutral-800 min-w-0">
                                        <CornerDownRight class="w-4 h-4 text-blue-400 shrink-0"/>
                                        <!-- Foto del accesorio -->
                                        <span class="w-8 h-8 rounded-md bg-neutral-50 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0">
                                            <img v-if="acc.foto_accesorio" :src="'/storage/' + acc.foto_accesorio" class="object-cover w-full h-full" alt="" />
                                            <Image v-else class="w-3.5 h-3.5 text-neutral-300" />
                                        </span>
                                        <span class="truncate">{{ acc.nombre_accesorio }}</span>
                                    </span>
                                    <!-- estado_accesorio viene de return_detail_accessories (histórico) -->
                                    <StatusBadge :status="acc.estado_accesorio ?? 'Bueno'" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="p-5 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm space-y-2">
                    <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                        <AlignLeft class="w-4 h-4" /> Notas de recepción final
                    </p>
                    <p class="text-sm text-neutral-700 italic leading-relaxed">
                        {{ loan?.observacion || 'El préstamo fue devuelto sin observaciones adicionales registradas.' }}
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 bg-neutral-50 border-t border-neutral-100 flex gap-4 no-print">
                <button
                    @click="$emit('close')"
                    class="flex-1 py-3.5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm"
                >
                    Cerrar
                </button>
                <button
                    @click="handleGenerateReport(loan.id)"
                    class="flex-1 py-3.5 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-900/20 active:scale-95"
                >
                    <FileText class="w-4 h-4" />
                    Generar Reporte
                </button>
            </div>
        </div>
    </div>
</template>
