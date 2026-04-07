<script setup lang="ts">
import { Image, List, Rows3, Eye, SquarePen, Ban, AlignLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import BaseTable from '@/components/ui/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import TableAction from '@/components/table/TableAction.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import equipmentRoutes from '@/routes/equipments';
import toolRoutes from '@/routes/tools';

// Definimos las propiedades que recibe del Index
const props = defineProps<{
    items: any[];
    activeTab: 'equipos' | 'herramientas' | 'bajas'; // <--- Ahora sí coincide
    openAccessoryId: number | null;
}>();

const emit = defineEmits(['view', 'edit', 'baja', 'toggleAccessories']);

const canEdit = (item: any) => {
    const estado = item.estado_equipo || item.estado_herramienta;
    // No se puede editar si está en Mantenimiento o Préstamo
    const estadosBloqueados = ['Mantenimiento', 'Prestado'];
    return !estadosBloqueados.includes(estado);
};

</script>

<template>
    <BaseTable :items="items" :emptyText="`No se encontraron ${activeTab}`">
        <TableHeader :columns="[
            'ITEM / INFORMACIÓN',
            'ESTADO',
            'UBICACIÓN',
            activeTab === 'bajas'
                ? 'MOTIVO DE BAJA'
                : (activeTab === 'equipos' ? 'ACCESORIOS' : 'MARCA / MODELO'),
            'ACCIONES'
        ]" />

        <tbody class="divide-y divide-neutral-100">
            <tr v-for="item in items" :key="`${item.tipo}-${item.id}`" class="hover:bg-neutral-50/50 group transition-colors">

                <td class="p-4 pl-8">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl border border-neutral-200 overflow-hidden bg-neutral-50 shrink-0 shadow-sm flex items-center justify-center">
                            <img
                                v-if="item.foto_equipo || item.foto_herramienta || item.foto"
                                :src="'/storage/' + (item.foto_equipo || item.foto_herramienta || item.foto)"
                                class="h-full w-full object-cover transition-transform hover:scale-110 duration-300"
                                alt="Imagen del item"
                            />

                            <Image v-else class="h-full w-full p-3 text-neutral-300" />
                        </div>
                        <div>
                            <div class="font-bold text-[#1a3a5a] leading-tight">
                                {{ item.nombre_equipo || item.nombre_herramienta }}
                            </div>
                            <div class="text-[10px] font-black text-blue-500 mt-1 tracking-tighter uppercase">
                                COD: {{ item.codigo_qr || "Sin código"}}
                            </div>
                        </div>
                    </div>
                </td>

                <td class="p-4">
                    <StatusBadge :status="item.estado_equipo || item.estado_herramienta" />
                </td>

                <td class="p-4">
                    <div class="flex items-center gap-2 text-sm font-semibold text-neutral-600">
                        <Rows3 class="w-3.5 h-3.5 text-neutral-400" />
                        {{ item.ubicacion_equipo || item.ubicacion_herramienta }}
                    </div>
                </td>

                <td class="p-4">
                    <div v-if="activeTab === 'bajas'" class="max-w-xs">
                        <div class="flex items-start gap-2">
                            <AlignLeft class="w-3.5 h-3.5 text-red-400 mt-1 shrink-0" />
                            <p class="text-[12px] font-medium text-neutral-600 italic leading-snug line-clamp-2" :title="item.observacion_equipo || item.observacion_herramienta">
                                {{ item.observacion_equipo || item.observacion_herramienta || 'Sin motivo registrado' }}
                            </p>
                        </div>
                    </div>

                    <div v-else-if="activeTab === 'equipos'" class="relative">
                        <button v-if="item.accessories?.length > 0"
                            @click.stop="$emit('toggleAccessories', item.id)"
                            class="flex items-center gap-2 px-3 py-1.5 bg-neutral-50 border border-neutral-200 rounded-xl hover:bg-white transition-all shadow-sm active:scale-95"
                        >
                            <List class="w-4 h-4 text-[#1a3a5a]"/>
                            <span class="text-xs font-bold text-neutral-700">{{ item.accessories.length }}</span>
                        </button>
                        <span v-else class="text-[10px] font-medium text-neutral-400 italic">Sin accesorios</span>

                        <div v-if="openAccessoryId === item.id" class="absolute left-0 z-50 mt-2 w-64 bg-white border border-neutral-200 rounded-2xl shadow-xl p-4 animate-in fade-in zoom-in-95 duration-200">
                            <p class="text-[11px] font-black text-[#1a3a5a] mb-3 tracking-widest border-b pb-2">LISTA DE ACCESORIOS</p>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
                                <div v-for="acc in item.accessories" :key="acc.id" class="flex items-center justify-between p-2 bg-neutral-50 rounded-lg border border-neutral-100">
                                    <span class="text-[13px] font-semibold text-neutral-700">{{ acc.nombre_accesorio }}</span>
                                    <StatusBadge :status="acc.estado_accesorio" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-[12px] font-bold text-neutral-600 uppercase tracking-tight">
                        {{ item.marca_modelo || '-' }}
                    </div>
                </td>

                <td class="p-4 pr-8 text-right">
                    <div class="flex justify-end gap-2">
                        <TableAction :icon="Eye" variant="view" title="Ver detalles" @click="$emit('view', item)" />
                        <template v-if="activeTab !== 'bajas'">
                            <template v-if="canEdit(item)">
                                <Link :href="item.tipo === 'equipo' ? equipmentRoutes.edit.url(item.id) : toolRoutes.edit.url(item.id)">
                                    <TableAction :icon="SquarePen" variant="edit" title="Editar" />
                                </Link>
                            </template>

                            <template v-else>
                                <div title="No se puede editar: El ítem está en mantenimiento o préstamo">
                                    <TableAction
                                        :icon="SquarePen"
                                        variant="edit"
                                        class="opacity-30 cursor-not-allowed grayscale"
                                        @click.prevent
                                    />
                                </div>
                            </template>

                            <template v-if="canEdit(item)">
                                <TableAction :icon="Ban" variant="delete" title="Dar de baja" @click="$emit('baja', item)" />
                            </template>
                            <template v-else>
                                <div title="No se puede editar: El ítem está en mantenimiento o préstamo">
                                    <TableAction :icon="Ban" variant="delete" title="Dar de baja"
                                        class="opacity-30 cursor-not-allowed grayscale"
                                        @click.prevent />
                                </div>
                            </template>
                        </template>
                    </div>
                </td>
            </tr>
        </tbody>
    </BaseTable>
</template>
