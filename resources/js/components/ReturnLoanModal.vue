<script setup lang="ts">
import {
    XIcon, NotebookPen, User, BookMarked, CalendarClock, Calendar, CalendarCheck2, ClockAlert, History,
    Clock, Package, CornerDownRight, AlignLeft, Loader2, Image
} from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    show: boolean;
    loan: any;
    form: any; // El form de Inertia que pasamos desde el padre
}>();

const emit = defineEmits(['close', 'confirm']);

const handleAccessoryStatusChange = (itemIndex: number, accIndex: number) => {
    const item = props.form.items[itemIndex];
    const accessory = item.accessories[accIndex];
    const nombreAcc = accessory.nombre_accesorio;

    // 1. Si el accesorio se marca como extraviado -> Equipo Incompleto
    if (accessory.estado_accesorio === 'Extraviado') {
        item.estado_devolucion = 'Incompleto';

        const nota = `[AUTO]: El equipo "${item.nombre_mostrar}" se marca como INCOMPLETO porque el accesorio "${nombreAcc}" fue reportado como EXTRAVIADO. `;
        if (!props.form.observacion.includes(nota)) {
            props.form.observacion += nota;
        }
    }

    // 2. Si el accesorio se marca como dañado -> Equipo Dañado
    else if (accessory.estado_accesorio === 'Dañado') {
        item.estado_devolucion = 'Dañado';

        const nota = `[AUTO]: El equipo "${item.nombre_mostrar}" se marca como DAÑADO porque el accesorio "${nombreAcc}" presenta DAÑOS. `;
        if (!props.form.observacion.includes(nota)) {
            props.form.observacion += nota;
        }
    }
};

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-200 flex items-center justify-center bg-black/40 backdrop-blur-md p-6 lg:p-12">
        <div class="bg-white w-full max-w-3xl rounded-4xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in zoom-in duration-300">

            <div class="px-8 py-6 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-[#1a3a5a] rounded-xl shadow-lg">
                        <NotebookPen class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tighter text-neutral-900 leading-none">Registrar Devolución</h2>
                        <p class="text-neutral-700 text-sm font-medium mt-1">Verifique el estado de los items recibidos</p>
                    </div>
                </div>
                <button @click="$emit('close')" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                    <XIcon class="w-7 h-7 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                </button>
            </div>

            <div class="p-8 pt-2 overflow-y-auto custom-scrollbar space-y-6 flex-1 bg-neutral-50/30">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <User class="w-4 h-4" /> Responsable
                        </p>
                        <p class="text-base font-bold text-neutral-900">{{ loan?.borrower.apellidosP }} {{ loan?.borrower.nombresP }}</p>
                        <div class="flex gap-2">
                            <span class="inline-block px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[13px] font-black text-[#1a3a5a]">
                                {{ loan?.borrower.teacher ? 'DOCENTE' : 'AUXILIAR' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-lg bg-neutral-100 text-[13px] font-black text-neutral-700 border border-neutral-200">
                                CI: {{ loan?.borrower.cedula_identidad }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 border-l border-neutral-100 pl-4 font-black">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <BookMarked class="w-4 h-4" /> Materia Asignada
                        </p>
                        <p class="text-base font-bold text-neutral-900 leading-tight">{{ loan?.subject.nombre_materia }}</p>
                        <p class="text-[15px] text-[#1a3a5a] font-mono tracking-tighter">{{ loan?.subject.sigla }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <CalendarClock class="w-4 h-4" /> Fecha y horario del préstamo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-neutral-50 rounded-3xl border border-neutral-200 mt-4">
                        <div class="flex flex-col gap-3">
                            <span class="text-[13px] font-bold text-blue-700 uppercase tracking-tighter">Registro de Salida</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Calendar class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Fecha Salida</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_salida }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <Clock class="w-4 h-4 text-blue-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-blue-700 uppercase tracking-widest leading-none mb-1">Hora Inicio</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_inicio }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-orange-700 uppercase tracking-tighter">Retorno (Opcional)</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <CalendarClock class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Fecha Limite</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_retorno_prevista }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <ClockAlert class="w-4 h-4 text-orange-700" />
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-orange-700 uppercase tracking-widest leading-none mb-1">Hora Fin</p>
                                    <p class="text-sm font-bold text-neutral-800">{{ loan?.hora_fin_prevista }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 border-l border-neutral-100 pl-4">
                            <span class="text-[13px] font-bold text-green-700 uppercase tracking-tighter">Retorno Real</span>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <CalendarCheck2 class="w-4 h-4 text-green-700" />
                                </div>
                                <div>
                                    <Label for="fecha_retorno" class="text-[11px] font-black text-green-700 uppercase tracking-widest leading-none mb-1">Fecha Retorno</Label>
                                    <Input type="date" v-model="form.fecha_retorno" :min="loan?.fecha_salida" class="h-8 text-xs rounded-lg" />
                                    <InputError :message="form.errors.fecha_retorno" />
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <History class="w-4 h-4 text-green-700" />
                                </div>
                                <div>
                                    <Label for="hora_fin" class="text-[11px] font-black text-green-700 uppercase tracking-widest leading-none mb-1">Hora Entrada</Label>
                                    <Input type="time" v-model="form.hora_fin" class="h-8 text-xs rounded-lg" />
                                    <InputError :message="form.errors.hora_fin" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-3">
                    <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-800 flex items-center gap-2">
                        <Package class="w-4 h-4" /> Revisión Detallada de Equipos y Herramientas
                    </h3>

                    <div v-for="(item, index) in form.items" :key="item.id" class="border border-neutral-200 bg-neutral-50 rounded-3xl overflow-hidden shadow-sm">
                        <div class="flex items-center justify-between p-4 bg-white">
                            <div class="flex items-center gap-4">
                                <div class="w-15 h-15 rounded-xl bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-100 shadow-inner shrink-0">
                                    <img
                                        v-if="item.foto_equipo || item.foto_herramienta || item.foto"
                                        :src="'/storage/' + (item.foto_equipo || item.foto_herramienta || item.foto)"
                                        class="object-cover w-full h-full"
                                    />
                                    <Image v-else class="w-6 h-6 text-neutral-300" />
                                </div>

                                <div class="flex flex-col min-w-0">
                                    <span class="text-[14px] font-bold text-neutral-900 truncate leading-tight">{{ item.nombre_mostrar }}</span>
                                    <span :class="[
                                        'w-fit px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border',
                                        item.es_equipo ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100'
                                    ]">
                                        {{ item.es_equipo ? 'EQUIPO' : 'HERRAMIENTA' }}
                                    </span>
                                </div>
                            </div>

                            <select v-model="form.items[index].estado_devolucion"
                                class="text-[13px] font-bold rounded-xl border-neutral-200 bg-neutral-50 focus:ring-black focus:border-black transition-all py-1.5 px-3">
                                <option value="Disponible">Disponible</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                                <option value="Incompleto">Incompleto</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>

                        <div v-if="item.accessories?.length" class="bg-neutral-50 p-4 border-t border-neutral-100 space-y-2">
                            <p class="text-[11px] font-black text-neutral-800 uppercase tracking-widest flex items-center gap-2 mb-1">
                                <div class="w-1.5 h-1.5 bg-blue-400 rounded-full"></div>
                                Accesorios del Equipo
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div v-for="(acc, accIndex) in item.accessories" :key="acc.id"
                                    class="flex items-center justify-between bg-white p-2.5 rounded-xl border border-neutral-200/60 shadow-sm">
                                    <span class="text-[13px] font-semibold text-neutral-800 flex items-center gap-1">
                                        <CornerDownRight class="w-4 h-4 text-blue-400"/>
                                        <div class="w-15 h-15 rounded-lg bg-neutral-50 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0">
                                            <img
                                                v-if="acc.foto_accesorio"
                                                :src="'/storage/' + acc.foto_accesorio"
                                                class="object-cover w-full h-full"
                                            />
                                            <Image v-else class="w-3.5 h-3.5 text-neutral-300" />
                                        </div>
                                        {{ acc.nombre_accesorio }}
                                    </span>
                                    <select v-model="form.items[index].accessories[accIndex].estado_accesorio"
                                        @change="handleAccessoryStatusChange(Number(index), Number(accIndex))"
                                        class="text-[13px] py-1 px-2 border-neutral-100 rounded-lg bg-neutral-50 font-bold focus:ring-black outline-none">
                                        <option value="Bueno">Bueno</option>
                                        <option value="Dañado">Dañado</option>
                                        <option value="Extraviado">Extraviado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 px-2">
                    <Label class="text-[13px] font-black uppercase text-neutral-700 tracking-widest">
                        <AlignLeft class="w-4 h-4 inline mr-1" /> Notas adicionales de recepción
                    </Label>
                    <Textarea
                        v-model="form.observacion"
                        placeholder="Escriba aquí si hubo algún incidente..."
                        class="bg-white border-neutral-200 text-sm rounded-3xl min-h-[120px] focus:ring-[#1a3a5a]/10 font-medium italic text-neutral-600"
                    />
                </div>
            </div>

            <div class="p-8 bg-neutral-50 border-t border-neutral-100 flex gap-4 no-print">
                <button @click="$emit('close')" class="flex-1 py-3.5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all shadow-sm">
                    Cerrar
                </button>
                <button @click="$emit('confirm')" :disabled="form.processing" class="flex-1 py-3.5 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-3 shadow-lg shadow-blue-900/20 active:scale-95">
                    <Loader2 v-if="form.processing" class="mr-2 animate-spin w-4 h-4 text-white"/>
                    Confirmar Devolución
                </button>
            </div>
        </div>
    </div>
</template>
