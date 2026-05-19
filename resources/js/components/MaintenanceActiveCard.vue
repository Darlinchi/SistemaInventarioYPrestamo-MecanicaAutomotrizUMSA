<script setup lang="ts">
import {
    Package, Building2, Wrench, Calendar, UserCog, 
    CalendarCheck2, Clock, ClockAlert, CheckCircle
} from 'lucide-vue-next';

defineProps<{
    maint: any;
}>();

defineEmits(['complete']);

</script>

<template>
    <div class="group border border-blue-100 bg-blue-50/50 rounded-4xl p-6 flex flex-col md:flex-row justify-between items-center transition-all duration-300 shadow-sm hover:shadow-xl hover:border-blue-300 hover:-translate-y-1 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full items-center">

            <div class="col-span-1">
                <div class="flex gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white border border-neutral-100 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform overflow-hidden">
                        <img
                            v-if="maint.equipment.foto_equipo"
                            :src="'/storage/' + maint.equipment.foto_equipo"
                            class="w-full h-full object-cover"
                            alt="Foto del equipo"
                        />
                        <Package v-else class="w-8 h-8 text-red-500" />
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                                <Package class="w-4 h-4 text-[#1a3a5a]"/>
                                <span>Equipo</span>
                            </p>
                            <p class="flex items-center gap-2 text-[14px] font-bold text-neutral-800 leading-tight">
                                {{ maint.equipment.nombre_equipo }}
                            </p>
                        </div>

                        <div class="space-y-1">
                            <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                                <UserCog class="w-4 h-4 text-[#1a3a5a]"/>
                                <span>Registrado por:</span>
                            </p>
                            <p class="flex items-center gap-2 text-[14px] font-bold text-neutral-800 leading-tight">
                                {{ maint.user.name }}
                            </p>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="space-y-4">
                <div class="space-y-1">
                    <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                        <Building2 class="w-4 h-4 text-[#1a3a5a]"/>
                        <span>Empresa Encargada</span>
                    </p>
                    <p class="flex items-center gap-2 text-[14px] font-bold text-neutral-800 leading-tight">
                        {{ maint.companies[0]?.nombre_empresa || 'Empresa No Registrada' }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                        <Wrench class="w-4 h-4 text-[#1a3a5a]" />
                        <span>Tipo de Mantenimiento</span>
                    </p>
                    <p class="flex items-center gap-2 text-[14px] font-bold text-neutral-800 leading-tight">
                        {{ maint.tipo_mantenimiento }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-blue-700 uppercase tracking-widest leading-none">
                            <Calendar class="w-4 h-4" /> F. Salida
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ maint.fecha_mantenimiento }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-blue-700 uppercase tracking-widest leading-none">
                            <Clock class="w-4 h-4" /> H. Inicio
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ maint.hora_inicio }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-700 uppercase tracking-widest leading-none">
                            <CalendarCheck2 class="w-4 h-4" /> F. Límite
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ maint.fecha_retorno_estimado }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-700 uppercase tracking-widest leading-none">
                            <ClockAlert class="w-3.5 h-3.5" /> H. Fin
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ maint.hora_fin_estimado }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-2 mt-6 md:mt-0 md:ml-8 w-full md:w-auto">
            <button
                @click="$emit('complete', maint)"
                class="flex items-center justify-center gap-2 bg-[#1a3a5a] border border-[#1a3a5a] px-6 py-3 rounded-xl text-xs font-black text-white hover:bg-[#122a42] transition shadow-md uppercase tracking-wider active:scale-95 whitespace-nowrap"
            >
                <CheckCircle class="w-4 h-4"/> Completar
            </button>
        </div>
    </div>
</template>
