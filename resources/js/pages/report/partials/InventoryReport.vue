<script setup lang="ts">
import { ref, computed } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import { FileDown, Image, Package, ChevronDown, Layers, Activity, Tag } from 'lucide-vue-next';

const props = defineProps<{
    items: any[]
}>();

// ── 1. Inicializa las variables con el valor EXACTO que usará el select ──
const filterCategory = ref('');
const filterStatus   = ref('');
const filterRubro    = ref('');

const categoryOptions = ['Equipo', 'Herramienta'];

const statusOptions = computed(() => {
    return [...new Set(props.items.map(i => i.estado).filter(Boolean))].sort();
});

const rubroOptions = computed(() => {
    const rubros = props.items
        .filter(i => i.tipo === 'equipo' && i.rubro && i.rubro !== 'General')
        .map(i => i.rubro);

    return [...new Set(rubros)].sort();
});

// ── 3. Sincroniza la lógica del filtrado en pantalla con los nuevos nombres ──
const filtered = computed(() => {
    return props.items.filter(item => {
        const matchCategory = filterCategory.value === '' ||
            item.tipo.toLowerCase() === filterCategory.value.toLowerCase();

        const matchStatus = filterStatus.value === '' ||
            item.estado === filterStatus.value;

        const matchRubro = filterRubro.value === '' ||
            (item.rubro && item.rubro === filterRubro.value);

        return matchCategory && matchStatus && matchRubro;
    });
});

// ── Envío de Parámetros Combinados al Backend ────────────────────────
const exportInventoryPdf = () => {
    const params = new URLSearchParams({
        category: filterCategory.value,
        status: filterStatus.value,
        rubro: filterRubro.value
    });

    const url = `/dashboard/reports/inventory/pdf?${params.toString()}`;
    window.open(url, '_blank');
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div class="space-y-4 flex-1 w-full">
                <h3 class="text-2xl font-black text-neutral-800 tracking-tighter flex items-center gap-3">
                    <Package class="w-7 h-7 text-[#1a3a5a]" />
                    Reporte de Inventario Técnico
                </h3>

                <PageHeader
                    description="Filtre y genere reportes detallados del inventario de activos del taller."
                >
                    <template #action>
                        <CreateActionButton
                            type="button"
                            label="Exportar PDF"
                            iconType="download"
                            @click="exportInventoryPdf"
                        />
                    </template>
                </PageHeader>

                <!-- Contenedor de Filtros Reutilizando tus Componentes de UI -->
                <div class="flex flex-col md:flex-row items-center gap-3 mb-4 w-full">

                    <SelectFilter
                        v-model="filterCategory"
                        label="Tipos"
                        :options="categoryOptions"
                        icon="Layers"
                    />

                    <SelectFilter
                        v-model="filterStatus"
                        label="Estados"
                        :options="statusOptions"
                    />

                    <SelectFilter
                        v-show="filterCategory !== 'Herramienta'"
                        v-model="filterRubro"
                        label="Rubros"
                        :options="rubroOptions"
                        icon="Tag"
                    />

                    <ClearFiltersButton
                        @clear="() => { filterCategory = ''; filterStatus = ''; filterRubro = ''; }"
                    />

                </div>
            </div>
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
                                    <span v-if="item.rubro" class="text-neutral-400 font-medium lowercase"> · {{ item.rubro }}</span>
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
                        <span class="text-[12px] font-black uppercase tracking-widest text-neutral-600">
                            {{ item.tipo }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </BaseTable>

        <div class="flex items-center gap-2 px-2">
            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
            <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest">
                Total de registros filtrados: {{ filtered.length }}
            </p>
        </div>
    </div>
</template>
