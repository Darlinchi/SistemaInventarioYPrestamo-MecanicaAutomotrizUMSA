<script setup lang="ts">
import { ref } from 'vue';
import {
    History, FileDown, Package, ClipboardCheck,
    List, Calendar, CalendarCheck
} from 'lucide-vue-next';

const props = defineProps<{
    history: Array<any>
}>();

// Control de dropdown de ítems
const openLoanId = ref<number | null>(null);

const toggleItems = (id: number) => {
    openLoanId.value = openLoanId.value === id ? null : id;
};

// FUNCIÓN PARA FORMATEAR FECHAS SEGURA
const formatSafeDate = (dateString: string) => {
    if (!dateString) return 'Pendiente';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return 'No registrada';
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const exportHistoryPdf = () => {
    // Abrimos el reporte en una pestaña nueva
    const url = '/dashboard/reports/history/pdf';
    window.open(url, '_blank');
};

</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center px-2">
            <div class="space-y-1">
                <h3 class="text-2xl font-black text-neutral-800 tracking-tighter flex items-center gap-3">
                    <History class="w-7 h-7 text-[#1a3a5a]" />
                    Historial de Movimientos
                </h3>
                <p class="text-sm text-neutral-500 font-medium">Registro cronológico de préstamos y devoluciones</p>
            </div>
            <button
                @click="exportHistoryPdf"
                class="bg-[#1a3a5a] text-white px-6 py-3 rounded-2xl font-black text-[11px] uppercase tracking-widest flex items-center gap-2 hover:bg-[#122a42] transition-all shadow-lg shadow-blue-900/10 active:scale-95"
            >
                <FileDown class="w-4 h-4" /> Exportar Historial
            </button>
        </div>

        <div v-for="log in history" :key="log.id"
            class="p-7 bg-white border border-neutral-100 rounded-[2.5rem] shadow-sm flex flex-col gap-6 relative group hover:border-blue-200 transition-all border-l-4 border-l-[#1a3a5a]">

            <div class="flex flex-col md:flex-row justify-between items-center ">
                <div class="flex items-start gap-4 min-w-60 w-full md:w-auto">
                    <div class="p-3.5 bg-neutral-50 rounded-2xl text-[#1a3a5a] group-hover:bg-blue-50 transition-colors">
                        <History class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-[12px] font-black text-neutral-500 uppercase tracking-widest leading-none">Responsable</p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">
                            {{ log.borrower.apellidos }} {{ log.borrower.nombres }}
                        </p>
                        <span class="inline-block text-[11px] px-2 py-0.5 bg-[#1a3a5a]/10 text-[#1a3a5a] rounded-lg font-black uppercase tracking-tighter">
                            {{
                                log.borrower.teacher
                                ? 'DOCENTE'
                                : (log.borrower.assistant ? 'AUXILIAR' : 'ESTUDIANTE')
                            }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-1 flex-col md:flex-row items-center justify-around w-full gap-6 border-l border-neutral-100 pl-6">

                    <div class="text-center md:text-left">
                        <p class="text-[13px] font-black text-blue-700 uppercase tracking-widest mb-1 flex items-center justify-center md:justify-start gap-1">
                             <Calendar class="w-4 h-4"/> Fecha Salida
                        </p>
                        <p class="text-sm font-extrabold text-neutral-800">{{ formatSafeDate(log.fecha_salida) }}</p>
                    </div>

                    <div class="text-center md:text-left">
                        <template v-if="log.fecha_retorno">
                            <p class="text-[13px] font-black text-green-700 uppercase tracking-widest mb-1 flex items-center justify-center md:justify-start gap-1">
                                <CalendarCheck class="w-4 h-4"/> Fecha Devolución
                            </p>
                            <p class="text-sm font-extrabold text-neutral-800">{{ formatSafeDate(log.fecha_retorno) }}</p>
                        </template>
                        <template v-else>
                            <p class="text-[13px] font-black text-orange-700 uppercase tracking-widest mb-1 flex items-center justify-center md:justify-start gap-1">
                                <CalendarCheck class="w-4 h-4"/> Retorno Previsto
                            </p>
                            <p class="text-sm font-extrabold text-neutral-800 animate-pulse">{{ formatSafeDate(log.fecha_retorno_prevista) }}</p>
                        </template>
                    </div>

                    <div class="relative">
                        <button
                            @click.stop="toggleItems(log.id)"
                            :class="[
                                'flex items-center gap-2 px-5 py-2.5 rounded-xl border transition-all active:scale-95 shadow-sm',
                                openLoanId === log.id
                                    ? 'bg-[#1a3a5a] text-white border-[#1a3a5a]'
                                    : 'bg-white border-blue-200 text-[#1a3a5a] hover:border-blue-400'
                            ]"
                        >
                            <List class="w-4 h-4"/>
                            <span class="text-xs font-black uppercase tracking-tight">
                                {{ log.items_prestados?.length || 0 }} Ítems
                            </span>
                        </button>

                        <div v-if="openLoanId === log.id"
                            class="absolute right-0 z-50 mt-2 w-72 bg-white border border-neutral-200 rounded-2xl shadow-2xl p-4 animate-in fade-in zoom-in duration-200">
                            <p class="text-[11px] font-black uppercase text-[#1a3a5a] mb-3 tracking-widest border-b pb-2">Detalle del préstamo</p>
                            <div class="max-h-52 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                <div v-for="item in log.items_prestados" :key="item.id"
                                    class="flex items-center justify-between p-2.5 bg-neutral-50 border border-neutral-100 rounded-xl">
                                    <div class="flex flex-col">
                                        <span class="text-[12px] font-bold text-neutral-800 leading-tight">{{ item.nombre_mostrar }}</span>
                                        <!-- Mostramos el estado histórico si existe[cite: 11] -->
                                        <span v-if="item.estado_devolucion" class="text-[9px] font-black text-[#1a3a5a] uppercase">
                                            Regresó: {{ item.estado_devolucion }}
                                        </span>
                                    </div>
                                    <span :class="[
                                        'ml-2 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border',
                                        item.es_equipo ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100'
                                    ]">
                                        {{ item.es_equipo ? 'EQUIPO' : 'HERRAMIENTA' }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <span :class="[
                'absolute top-6 right-6 px-4 py-1.5 rounded-full text-[9px] font-black uppercase border shadow-sm transition-all',
                log.fecha_retorno
                    ? 'bg-neutral-50 text-neutral-400 border-neutral-100'
                    : 'bg-[#1a3a5a] text-white border-[#1a3a5a] shadow-blue-900/20'
            ]">
                {{ log.fecha_retorno ? 'Completado' : 'Activo' }}
            </span>
        </div>
    </div>
</template>
