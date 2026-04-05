<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Image } from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import itemRoutes from '@/routes/items';

defineProps<{
    equipo: {
        id: number;
        nombre_equipo: string;
        marca?: string;
        serie?: string;
        foto_equipo?: string;
        estado_equipo: string;
    }
}>();

</script>

<template>
    <Card class="rounded-4xl border border-neutral-200/60 p-4 shadow-sm hover:shadow-xl transition-all group overflow-hidden bg-white">
        <CardContent class="p-0 flex flex-col h-full">
            <div class="aspect-square rounded-3xl bg-neutral-100 mb-4 overflow-hidden relative">
                <img v-if="equipo.foto_equipo" :src="'/storage/' + equipo.foto_equipo"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                <div v-else class="w-full h-full flex items-center justify-center bg-neutral-50">
                    <Image class="w-12 h-12 text-neutral-200" />
                </div>

                <div class="absolute top-3 right-3">
                    <StatusBadge :status="equipo.estado_equipo" class="text-[11px]"/>
                </div>
            </div>

            <div class="px-2 grow">
                <p class="text-[10px] font-black text-[#1a3a5a] uppercase tracking-[0.15em] mb-1">
                    {{ equipo.marca || 'S/M' }}
                </p>
                <h3 class="font-bold text-neutral-900 truncate text-base leading-tight">
                    {{ equipo.nombre_equipo }}
                </h3>
                <p class="text-xs text-neutral-400 font-medium mt-1">
                    S/N: {{ equipo.serie || 'No registrado' }}
                </p>
            </div>

            <Link :href="itemRoutes.index.url()"
                  class="mt-5 w-full py-3 bg-neutral-50 group-hover:bg-[#1a3a5a] group-hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                Ver Detalles
            </Link>
            <!--<Link :href="route('equipments.show', equipo.id)"
                  class="mt-5 w-full py-3 bg-neutral-50 group-hover:bg-[#1a3a5a] group-hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                Ver Detalles
            </Link>-->
        </CardContent>
    </Card>
</template>
