<script setup lang="ts">
import itemRoutes from '@/routes/items';
import { Link } from '@inertiajs/vue3';
import { Image } from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';

defineProps<{
    equipo: {
        id: number;
        nombre_equipo: string;
        marca?: string;
        serie?: string;
        foto?: string;
        estado_equipo: string;
    }
}>();

</script>

<template>
    <Card class="rounded-4xl border border-neutral-200/60 p-4 shadow-sm hover:shadow-xl transition-all group overflow-hidden bg-white">
        <CardContent class="p-0 flex flex-col h-full">
            <div class="aspect-square rounded-3xl bg-neutral-100 mb-4 overflow-hidden relative">
                <img v-if="equipo.foto" :src="'/storage/' + equipo.foto"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                <div v-else class="w-full h-full flex items-center justify-center bg-neutral-50">
                    <Image class="w-12 h-12 text-neutral-200" />
                </div>

                <div class="absolute top-3 right-3">
                    <span :class="[
                        'px-3 py-1 rounded-xl text-[10px] font-black uppercase shadow-lg border',
                        equipo.estado_equipo === 'Disponible'
                            ? 'bg-green-500 text-white border-green-400'
                            : 'bg-orange-500 text-white border-orange-400'
                    ]">
                        {{ equipo.estado_equipo }}
                    </span>
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
