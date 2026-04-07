<script setup lang="ts">
import { ref, computed } from 'vue';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import { FileDown, Image, Rows3, Package, ChevronDown, Layers } from 'lucide-vue-next';

const props = defineProps<{
    items: any[]
}>();

const filterCategory = ref('Todas');

const filtered = computed(() => {
    if (filterCategory.value === 'Todas') return props.items;
    // Comparamos el tipo (equipo/herramienta) con el valor seleccionado
    return props.items.filter(i =>
        i.tipo.toLowerCase() === filterCategory.value.toLowerCase()
    );
});

</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div class="space-y-4 flex-1 w-full">
                <h3 class="text-2xl font-black text-neutral-800 tracking-tighter flex items-center gap-3">
                    <Package class="w-7 h-7 text-[#1a3a5a]" />
                    Reporte de Inventario
                </h3>

                <div class="relative w-full md:w-[280px] group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none z-10">
                        <Layers class="h-5 w-5 text-neutral-400" />
                    </div>

                    <select
                        v-model="filterCategory"
                        class="appearance-none w-full bg-white border border-neutral-200 rounded-2xl py-3 pl-12 pr-10 text-sm font-bold text-neutral-700 cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm outline-none"
                    >
                        <option value="Todas">Tipo (Todos)</option>
                        <option value="Equipo">Equipos</option>
                        <option value="Herramienta">Herramientas</option>
                    </select>

                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <ChevronDown class="h-4 w-4 text-neutral-400" />
                    </div>
                </div>
            </div>

            <button class="bg-[#1a3a5a] text-white px-6 py-3.5 rounded-2xl font-black text-[11px] uppercase tracking-widest flex items-center gap-2 hover:bg-[#122a42] transition-all shadow-lg shadow-blue-900/10 active:scale-95 whitespace-nowrap">
                <FileDown class="w-4 h-4" /> Exportar CSV
            </button>
        </div>

        <BaseTable :items="filtered" emptyText="No se encontraron ítems con los filtros seleccionados">
            <TableHeader :columns="[
                'ITEM / INFORMACIÓN',
                'ESTADO',
                'UBICACIÓN',
                'TIPO'
            ]" />

            <tbody class="divide-y divide-neutral-100">
                <tr v-for="item in filtered" :key="item.id" class="hover:bg-neutral-50/50 group transition-colors">

                    <td class="p-4 pl-8">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl border border-neutral-200 overflow-hidden bg-neutral-50 shrink-0 shadow-sm">
                                <img v-if="item.foto" :src="'/storage/' + item.foto" class="h-full w-full object-cover" />
                                <Image v-else class="h-full w-full p-3 text-neutral-300" />
                            </div>
                            <div>
                                <div class="font-bold text-[#1a3a5a] leading-tight text-sm">
                                    {{ item.nombre_item }}
                                </div>
                                <div class="text-[10px] font-black text-blue-500 mt-1 tracking-tighter uppercase">
                                    QR: {{ item.codigo_qr || "N/A"}}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-4">
                        <StatusBadge :status="item.estado" />
                    </td>

                    <td class="p-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-neutral-600">
                            {{ item.ubicacion_item }}
                        </div>
                    </td>

                    <td class="p-4 pr-8">
                        <div class="flex items-center gap-2">
                            <span class="text-[12px] font-black uppercase tracking-widest text-neutral-600">
                                {{ item.tipo }}
                            </span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <div class="flex items-center gap-2 px-2">
            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
            <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest">
                Total de registros: {{ filtered.length }}
            </p>
        </div>
    </div>
</template>
