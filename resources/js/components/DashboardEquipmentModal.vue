<script setup lang="ts">
import { XIcon, Image, FileText, List, Wrench, Calendar, Hash, Package, BookText, PaintBucket } from 'lucide-vue-next';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import DetailItem from '@/components/shared/DetailItem.vue';

defineProps<{
    show: boolean;
    equipo: any;
}>();

defineEmits(['close']);

const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
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
                        <img v-if="equipo.foto_equipo" :src="'/storage/' + equipo.foto_equipo" class="w-40 h-40 rounded-3xl object-cover shadow-xl border-4 border-white ring-1 ring-neutral-200" />
                        <div v-else class="w-40 h-40 rounded-3xl bg-neutral-50 flex items-center justify-center border-2 border-dashed border-neutral-200">
                            <Image class="w-16 h-16 text-neutral-200" />
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="inline-flex items-center px-3 py-1 rounded-lg bg-[#1a3a5a]/10 text-[#1a3a5a] text-[11px] font-bold tracking-widest uppercase mb-1">
                            Equipo de Taller
                        </div>
                        <h2 class="text-3xl font-bold text-neutral-900 mb-1">{{ equipo.nombre_equipo }}</h2>
                        <div class="flex flex-wrap items-center gap-2">
                            <StatusBadge :status="equipo.estado_equipo" />
                        </div>
                        <div class="flex items-center gap-4 py-3 border-y border-neutral-100 mt-1">
                            <div class="flex items-center gap-2">
                                <DetailItem icon="QrCode" label="CÓDIGO QR" :value="equipo.codigo_qr" />
                            </div>
                            <div class="w-px h-8 bg-neutral-100"></div>
                            <div class="flex items-center gap-2">
                                <DetailItem icon="Rows3" label="UBICACIÓN" :value="equipo.ubicacion_equipo" />
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
                            <div class="space-y-1">
                                <DetailItem icon="Hash" label="NÚMERO DE SERIE" :value="equipo.serie" />
                                <DetailItem icon="Package" label="MARCA / MODELO" :value="`${equipo.marca} ${equipo.modelo}`" />
                                <DetailItem icon="BookText" label="RUBRO" :value="equipo.rubro" />
                                <DetailItem icon="Calendar" label="FECHA ADQUISICIÓN" :value="formatDate(equipo.fecha_adquisicion)" />
                                <DetailItem icon="PaintBucket" label="COLOR" :value="equipo.color" />
                            </div>
                        </div>

                    </div>

                    <div class="space-y-4">
                        <h4 class="text-sm font-black text-[#1a3a5a] uppercase tracking-widest flex items-center gap-2 border-b pb-2">
                            <Wrench class="w-4 h-4" /> Estado y Ubicación
                        </h4>
                        <div class="space-y-3">
                            <DetailItem icon="MapPin" label="UBICACIÓN ACTUAL" :value="equipo.ubicacion_equipo" />
                            <DetailItem icon="PaintBucket" label="COLOR" :value="equipo.color" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-neutral-50 border-t flex gap-4">
                <button @click="$emit('close')" class="flex-1 py-4 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm">
                    Cerrar Vista
                </button>
                <a :href="`/dashboard/items/${equipo.id}/pdf?tipo=equipo`" target="_blank" class="flex-1 py-4 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-900/20">
                    <FileText class="w-5 h-5" /> Imprimir Ficha
                </a>
            </div>
        </div>
    </div>
</template>
