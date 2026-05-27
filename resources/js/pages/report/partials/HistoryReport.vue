<script setup lang="ts">
import { ref, computed } from 'vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import PageHeader from '@/components/PageHeader.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import {
    History, Calendar, CalendarCheck, List, User, Search
} from 'lucide-vue-next';

const props = defineProps<{
    history: Array<any>
}>();

// ─── VARIABLES DE ESTADO PARA FILTROS ─────────────────────────────────
const searchQuery   = ref('');
const selectedState = ref('Todos'); // 'Todos', 'Completado', 'Activo'
const startDate     = ref('');
const endDate       = ref('');

const openLoanId = ref<number | null>(null);

const toggleItems = (id: number) => {
    openLoanId.value = openLoanId.value === id ? null : id;
};

// Formateador de fechas seguro para la interfaz
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

// ─── FILTRADO REACTIVO EN PANTALLA ────────────────────────────────────
const filteredHistory = computed(() => {
    return props.history.filter(log => {
        // 1. Filtro por Responsable (Buscador de texto)
        const responsableCompleto = `
            ${log.borrower?.apellidoPaterno ?? ''}
            ${log.borrower?.apellidoMaterno ?? ''}
            ${log.borrower?.nombres ?? ''}
        `.toLowerCase();
        const matchSearch = responsableCompleto.includes(searchQuery.value.toLowerCase().trim());

        // 2. Filtro por Estado (Completado con retorno / Activo en curso)
        const esCompletado = log.fecha_retorno !== null;
        const matchState = selectedState.value === 'Todos' ||
            (selectedState.value === 'Completado' && esCompletado) ||
            (selectedState.value === 'Activo' && !esCompletado);

        // 3. Filtro por Rango de Fechas (Fecha de Salida)
        if (!log.fecha_salida) return false;
        const itemDate = log.fecha_salida.split(' ')[0]; // Extrae YYYY-MM-DD
        const matchStart = !startDate.value || itemDate >= startDate.value;
        const matchEnd = !endDate.value || itemDate <= endDate.value;

        return matchSearch && matchState && matchStart && matchEnd;
    });
});

// ─── EXPORTACIÓN PARAMETRIZADA AL BACKEND ─────────────────────────────
const exportHistoryPdf = () => {
    const params = new URLSearchParams({
        search: searchQuery.value,
        state: selectedState.value,
        start: startDate.value,
        end: endDate.value
    });

    const url = `/dashboard/reports/history/pdf?${params.toString()}`;
    window.open(url, '_blank');
};
</script>

<template>
    <div class="space-y-6">

        <PageHeader description="Registro cronológico y auditoría de préstamos y devoluciones en los talleres.">
            <template #action>
                <CreateActionButton
                    type="button"
                    label="Exportar Historial"
                    iconType="download"
                    @click="exportHistoryPdf"
                />
            </template>
        </PageHeader>

        <!-- Barra de Herramientas de Filtros Reutilizando tus Componentes Globales -->
        <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">

            <!-- 1. Buscador Avanzado Reutilizable -->
            <SearchInput
                v-model="searchQuery"
                placeholder="Buscar responsable o detalles..."
                class="flex-1 w-full md:w-[300px]"
            />

            <!-- 2. Filtro de Estado Estilizado -->
            <SelectFilter
                v-model="selectedState"
                label="Estados"
                :options="['Todos', 'Activo', 'Completado']" class="md:w-[165px]"
            />

            <!-- 3. Rango de Fechas: Desde -->
            <div class="flex items-center gap-2 bg-white border border-neutral-200 rounded-2xl px-4 py-2.5 shadow-sm group hover:border-blue-400 transition-all w-full md:w-auto">
                <span class="text-[10px] font-black text-neutral-400 uppercase tracking-wider shrink-0">Desde:</span>
                <input
                    v-model="startDate"
                    type="date"
                    class="bg-transparent text-xs font-bold text-neutral-700 outline-none cursor-pointer w-full"
                />
            </div>

            <!-- 4. Rango de Fechas: Hasta -->
            <div class="flex items-center gap-2 bg-white border border-neutral-200 rounded-2xl px-4 py-2.5 shadow-sm group hover:border-blue-400 transition-all w-full md:w-auto">
                <span class="text-[10px] font-black text-neutral-400 uppercase tracking-wider shrink-0">Hasta:</span>
                <input
                    v-model="endDate"
                    type="date"
                    class="bg-transparent text-xs font-bold text-neutral-700 outline-none cursor-pointer w-full"
                />
            </div>

            <!-- 5. Botón Reutilizable para Limpiar Filtros a su Estado Base -->
            <ClearFiltersButton
                @clear="() => { searchQuery = ''; selectedState = 'Todos'; startDate = ''; endDate = ''; }"
            />
        </div>

        <div v-for="log in filteredHistory" :key="log.id"
            class="p-7 bg-white border border-neutral-100 rounded-[2.5rem] shadow-sm flex flex-col gap-6 relative group hover:border-blue-200 transition-all border-l-4 border-l-[#1a3a5a]">

            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-start gap-4 min-w-60 w-full md:w-auto">
                    <div class="p-3.5 bg-neutral-50 rounded-2xl text-[#1a3a5a] group-hover:bg-blue-50 transition-colors">
                        <History class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-[12px] font-black text-neutral-500 uppercase tracking-widest leading-none">Responsable</p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">
                            {{ log.borrower?.teacher?.titulo }} {{ log.borrower?.apellidoPaterno }} {{ log.borrower?.apellidoMaterno }} {{ log.borrower?.nombres }}
                        </p>
                        <span class="inline-block text-[11px] px-2 py-0.5 bg-[#1a3a5a]/10 text-[#1a3a5a] rounded-lg font-black uppercase tracking-tighter">
                            {{
                                log.borrower?.teacher
                                ? 'DOCENTE'
                                : (log.borrower?.assistant ? 'AUXILIAR' : 'ESTUDIANTE')
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
                            <p class="text-sm font-extrabold text-neutral-800">{{ formatSafeDate(log.fecha_retorno_prevista) }}</p>
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
                            class="absolute right-0 z-50 mt-2 w-72 bg-white border border-neutral-200 rounded-2xl shadow-2xl p-4">
                            <p class="text-[11px] font-black uppercase text-[#1a3a5a] mb-3 tracking-widest border-b pb-2">Detalle del préstamo</p>
                            <div class="max-h-52 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                <div v-for="item in log.items_prestados" :key="item.id"
                                    class="flex items-center justify-between p-2.5 bg-neutral-50 border border-neutral-100 rounded-xl">
                                    <div class="flex flex-col">
                                        <span class="text-[12px] font-bold text-neutral-800 leading-tight">{{ item.nombre_mostrar }}</span>
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
                    : 'bg-[#1a3a5a] text-white border-[#1a3a5a]'
            ]">
                {{ log.fecha_retorno ? 'Completado' : 'Activo' }}
            </span>
        </div>
    </div>
</template>
