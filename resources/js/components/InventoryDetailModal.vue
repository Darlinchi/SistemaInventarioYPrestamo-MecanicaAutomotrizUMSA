<script setup lang="ts">
import {
    XIcon, Image, FileText, List, AlignLeft, Eye,
    Wrench, Calendar, Package
} from 'lucide-vue-next';

import { computed } from 'vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import DetailItem from '@/components/shared/DetailItem.vue';

const props = defineProps<{
    show: boolean;
    item: any;
}>();

const emit = defineEmits(['close', 'generatePdf']);

const ultimoMantenimiento = computed(() => {
    // Intentamos obtener el array de mantenimientos
    const mants = props.item?.maintenances || props.item?.mantenimientos;

    // Si existe y tiene elementos, el controlador ya nos envió el más nuevo en la posición 0
    if (mants && mants.length > 0) {
        return mants[0];
    }
    return null;
});

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
};

const generatePdf = () => {
    // Agregamos el parámetro 'tipo' a la URL
    // props.item.tipo debe ser 'equipo' o 'herramienta' (como lo definiste en el index)
    const url = `/dashboard/items/${props.item.id}/pdf?tipo=${props.item.tipo}`;
    window.open(url, '_blank');
};

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md no-print" @click="$emit('close')"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden animate-in fade-in zoom-in duration-300 flex flex-col border">
            <button @click="$emit('close')" class="absolute top-4 right-4 z-10 p-2 hover:bg-neutral-100 rounded-full transition-colors group no-print">
                <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
            </button>

            <div class="p-8 overflow-y-auto custom-scrollbar">
                <div class="flex flex-col md:flex-row gap-6 items-start mb-8 border-b border-neutral-100">
                    <div class="shrink-0 mx-auto md:mx-0">
                        <template v-if="item?.equipment?.foto_equipo || item?.tool?.foto_herramienta">
                            <img :src="'/storage/' + (item.equipment?.foto_equipo || item.tool?.foto_herramienta)"
                                class="w-40 h-40 rounded-3xl object-cover shadow-xl border-4 border-white ring-1 ring-neutral-200" />
                        </template>

                        <div v-else class="w-40 h-40 rounded-3xl bg-neutral-50 flex items-center justify-center border-2 border-dashed border-neutral-200">
                            <Image class="w-16 h-16 text-neutral-200" />
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="inline-flex items-center px-3 py-1 rounded-lg bg-[#1a3a5a]/10 text-[#1a3a5a] text-[11px] font-bold tracking-widest uppercase mb-1">
                            {{ item?.equipment ? 'Equipo de Taller' : 'Herramienta de Mano' }}
                        </div>

                        <h2 class="text-3xl font-bold text-neutral-900 mb-1">{{ item?.nombre_item }}</h2>
                        <div class="flex flex-wrap items-center gap-2">
                            <StatusBadge :status="item?.equipment?.estado_equipo || item?.tool?.estado_herramienta" />
                        </div>

                        <div class="flex items-center gap-4 py-3 border-y border-neutral-100 mt-1">
                            <div class="flex items-center gap-2">
                                <DetailItem icon="QrCode" label="CÓDIGO QR" :value="item?.codigo_qr" />
                            </div>
                            <div class="w-px h-8 bg-neutral-100"></div>
                            <div class="flex items-center gap-2">
                                <DetailItem icon="Rows3" label="UBICACIÓN" :value="item?.ubicacion_item" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-6">
                        <h4 class="text-sm font-bold text-[#1a3a5a] flex items-center gap-2 border-b border-[#1a3a5a]/10 pb-2">
                            <FileText class="w-4 h-4" /> Especificaciones Técnicas
                        </h4>

                        <div class="grid gap-4">
                            <div v-if="item?.equipment" class="space-y-1">
                                <DetailItem icon="Hash" label="NÚMERO DE SERIE" :value="item.equipment.serie" />
                                <DetailItem icon="Package" label="MARCA / MODELO" :value="`${item.equipment.marca} ${item.equipment.modelo}`" />
                                <DetailItem icon="BookText" label="RUBRO" :value="item.equipment.rubro" />
                                <DetailItem icon="CalendarDays" label="FECHA ADQUISICIÓN" :value="formatDate(item.equipment.fecha_adquisicion)" />
                                <DetailItem icon="PaintBucket" label="COLOR" :value="item.equipment.color" />
                            </div>
                            <div v-else-if="item?.tool" class="space-y-3">
                                <DetailItem icon="Package" label="MARCA / MODELO" :value="item.tool.marca_modelo" />
                                <DetailItem icon="Layers" label="CANT. PIEZAS" :value="item.tool.cantidad_piezas" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div v-if="item?.equipment">

                            <h4 class="text-sm font-bold text-[#1a3a5a] flex items-center gap-2 border-b border-[#1a3a5a]/10 pb-2 mb-4">
                                <List class="w-4 h-4" /> Accesorios del Equipo
                            </h4>

                            <div v-if="item.equipment.accessories?.length" class="space-y-3">
                                <div v-for="acc in item.equipment.accessories" :key="acc.id"
                                    class="flex items-center gap-3 p-2 bg-neutral-50 rounded-2xl border border-neutral-100 hover:bg-white transition-colors">

                                    <div class="shrink-0">
                                        <img v-if="acc.foto_accesorio"
                                            :src="'/storage/' + acc.foto_accesorio"
                                            class="w-12 h-12 rounded-xl object-cover border border-neutral-200 shadow-sm" />
                                        <div v-else class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center border border-neutral-200">
                                            <Image class="w-5 h-5 text-neutral-300" />
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-neutral-800 truncate">{{ acc.nombre_accesorio }}</p>
                                        <p class="text-[10px] text-neutral-400 uppercase font-black tracking-widest">Accesorio</p>
                                    </div>

                                    <div class="pr-2">
                                        <StatusBadge :status="acc.estado_accesorio" />
                                    </div>
                                </div>
                            </div>

                            <div v-else class="p-6 bg-neutral-50 rounded-2xl border-2 border-dashed border-neutral-100 text-center mb-6">
                                <p class="text-[10px] text-neutral-400 font-bold uppercase tracking-widest">Sin accesorios registrados</p>
                            </div>

                            <div class="mt-8">
                                <h4 class="text-sm font-bold text-[#1a3a5a] flex items-center gap-2 border-b border-[#1a3a5a]/10 pb-2 mb-4">
                                    <Wrench class="w-4 h-4" /> Último Mantenimiento
                                </h4>

                                <div v-if="ultimoMantenimiento"
                                    class="p-5 bg-blue-50 border border-blue-200 rounded-3xl shadow-sm relative overflow-hidden animate-in fade-in slide-in-from-bottom-2">

                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-[10px] text-blue-600 uppercase font-black tracking-tighter bg-white px-2 py-0.5 rounded-full border border-blue-100">
                                            {{ ultimoMantenimiento.tipo_mantenimiento }}
                                        </span>
                                        <span class="text-[13px] font-bold text-neutral-800 flex items-center gap-1">
                                            <Calendar class="w-3 h-3" /> {{ formatDate(ultimoMantenimiento.fecha_retorno) }}
                                        </span>
                                    </div>

                                    <p class="text-[13px] font-bold text-neutral-800 mb-1 leading-tight">Actividad:</p>
                                    <p class="text-xs text-neutral-600 bg-white/60 p-3 rounded-xl border border-blue-100 italic leading-relaxed">
                                        "{{ ultimoMantenimiento.actividad || 'Sin descripción de actividad' }}"
                                    </p>

                                    <div v-if="ultimoMantenimiento.estado_final_equipo" class="mt-3 flex items-center gap-2">
                                        <span class="text-[13px] font-semibold text-neutral-800 ">Estado Resultante:</span>
                                        <StatusBadge :status="ultimoMantenimiento.estado_final_equipo" />
                                    </div>
                                </div>

                                <div v-else class="p-8 border-2 border-dashed border-neutral-100 rounded-3xl text-center">
                                    <div class="bg-neutral-50 w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <Wrench class="w-5 h-5 text-neutral-200" />
                                    </div>
                                    <p class="text-[10px] text-neutral-300 font-bold uppercase tracking-widest">Sin mantenimientos previos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-if="item?.descripcion_item" class="p-5 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm">
                        <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                            <AlignLeft class="w-4 h-4" /> Descripción
                        </p>
                        <p class="text-sm text-neutral-700 italic leading-relaxed">"{{ item.descripcion_item }}"</p>
                    </div>
                    <div v-if="item?.observacion_item" class="p-5 bg-neutral-50 border border-neutral-200 rounded-3xl shadow-sm">
                        <p class="flex items-center gap-2 text-[13px] font-black text-neutral-700 uppercase tracking-widest">
                            <Eye class="w-4 h-4" /> Observaciones Técnicas
                        </p>
                        <p class="text-sm text-neutral-700 italic leading-relaxed">"{{ item.observacion_item }}"</p>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-neutral-50 border-t border-neutral-100 flex gap-4 no-print">
                <button @click="$emit('close')" class="flex-1 py-3.5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm">
                    Cerrar
                </button>
                <button @click="generatePdf" class="flex-1 py-3.5 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-900/20 active:scale-95">
                    <FileText class="w-5 h-5" /> Generar Ficha PDF
                </button>
            </div>
        </div>
    </div>
</template>
