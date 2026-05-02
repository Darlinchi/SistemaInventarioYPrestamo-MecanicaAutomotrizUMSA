<script setup lang="ts">
import {
    Calendar, CalendarCheck2, Clock, ClockAlert, User,
    BookMarked, List, Edit, CheckCircle, Package
} from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

defineProps<{
    loan: any;
    isOpen: boolean;
    loanRoutes: any;
    canReturn: boolean;
    canEdit: boolean;
}>();

defineEmits(['toggleItems', 'return', 'edit']);

</script>

<template>
    <div
        :class="[
            'group border border-blue-100 bg-blue-50/50 rounded-4xl p-6 flex flex-col md:flex-row justify-between items-center transition-all duration-300 shadow-sm hover:shadow-xl hover:border-blue-300 hover:-translate-y-1 mb-6 relative',
            isOpen ? 'z-60' : 'z-10'
        ]"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-12 w-full">
            <div class="flex flex-col justify-center space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-blue-700 uppercase tracking-widest leading-none">
                            <Calendar class="w-4 h-4" /> F. Salida
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ loan.fecha_salida }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-700 uppercase tracking-widest leading-none">
                            <CalendarCheck2 class="w-4 h-4" /> F. Límite
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ loan.fecha_retorno_prevista }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-blue-700 uppercase tracking-widest leading-none">
                            <Clock class="w-4 h-4" /> H. Inicio
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ loan.hora_inicio }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-700 uppercase tracking-widest leading-none">
                            <ClockAlert class="w-4 h-4" /> H. Fin
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ loan.hora_fin_prevista }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="space-y-1">
                    <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                        <User class="w-4 h-4" /> Responsable
                        <span class="px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[11px] font-black text-[#1a3a5a] uppercase">
                            {{
                                loan.borrower.teacher
                                ? 'DOCENTE'
                                : (loan.borrower.assistant ? 'AUXILIAR' : 'ESTUDIANTE')
                            }}
                        </span>
                    </p>
                    <p class="flex flex-col text-[14px] font-bold text-neutral-800 leading-tight mt-1">
                        {{ loan.borrower.apellidos }} {{ loan.borrower.nombres }}
                        <span class="text-[12px] text-neutral-500 font-medium mt-0.5">CI: {{ loan.borrower.cedula_identidad }}</span>
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                        <BookMarked class="w-4 h-4" /> Sigla y Materia
                    </p>
                    <p class="text-sm font-bold text-neutral-800 leading-tight">{{ loan.subject.nombre_materia }}</p>
                    <p class="text-[12px] text-[#1a3a5a] font-black tracking-wider">{{ loan?.subject.sigla }}</p>
                </div>
            </div>

            <div class="flex flex-col justify-center">
                <div class="relative" :style="{ zIndex: isOpen ? '999' : '10' }">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-white px-3 py-1 rounded-full text-[13px] font-bold uppercase border border-blue-200 text-[#1a3a5a]">
                            Items Prestados
                        </span>
                    </div>

                    <button
                        @click.stop="$emit('toggleItems', loan.id)"
                        class="flex items-center gap-2 px-4 py-2.5 bg-white border border-blue-200 rounded-xl shadow-sm hover:border-blue-400 transition-all active:scale-95 w-full md:w-auto"
                    >
                        <List class="w-4 h-4 text-[#1a3a5a]"/>
                        <span class="text-xs font-black text-[#1a3a5a] uppercase tracking-tight">
                            Ver {{ loan.all_items.length }} ítems
                        </span>
                    </button>

                    <div v-if="isOpen"
                        class="absolute left-0 top-full mt-2 w-72 bg-white border border-neutral-200 rounded-2xl p-4 animate-in fade-in zoom-in duration-200 origin-top-left"
                        style="z-index: 9999 !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);"
                    >
                        <p class="text-[11px] font-black uppercase text-[#1a3a5a] mb-3 tracking-widest border-b pb-2">
                            Items en préstamo
                        </p>
                        <div class="max-h-52 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                            <div v-for="item in loan.all_items" :key="item.id"
                                class="flex items-center justify-between p-2.5 bg-neutral-50 border border-neutral-100 rounded-xl">
                                <span class="text-[13px] font-bold text-neutral-800 leading-tight flex-1">
                                    {{ item.nombre_mostrar }}
                                </span>
                                <span :class="[
                                    'ml-2 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border leading-none',
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

        <div class="flex flex-row md:flex-col gap-3 mt-6 md:mt-0 md:ml-8 w-full md:w-auto">

            <Link v-if="canEdit" :href="loanRoutes.edit.url(loan.id)" class="flex-1">
                <button class="w-full flex items-center justify-center gap-2 bg-white border border-neutral-200 px-5 py-2.5 rounded-xl text-xs font-black text-neutral-700 hover:bg-neutral-100 transition shadow-sm uppercase tracking-wider">
                    <Edit class="w-3.5 h-3.5" /> Editar
                </button>
            </Link>

            <button
                v-if="canReturn"
                @click="$emit('return', loan)"
                class="flex-1 flex items-center justify-center gap-2 bg-[#1a3a5a] border border-[#1a3a5a] px-5 py-2.5 rounded-xl text-xs font-black text-white hover:bg-[#122a42] transition shadow-md uppercase tracking-wider active:scale-95"
            >
                <CheckCircle class="w-3.5 h-3.5" /> Devolver
            </button>

        </div>
    </div>
</template>
