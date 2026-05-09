<script setup lang="ts">
import { ref, computed } from 'vue';
import {
    XIcon, NotebookPen, User, BookMarked, CalendarClock, Calendar, CalendarCheck2,
    ClockAlert, History, Clock, Package, CornerDownRight, AlignLeft, Loader2, Image,
    AlertTriangle, Wrench, RefreshCw, UserCheck, CheckCircle, ChevronRight
} from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    show: boolean;
    loan: any;
    form: any; // useForm de Inertia pasado desde el padre
}>();

const emit = defineEmits<{ close: []; confirm: [acuerdos: any[]] }>();

// ─── PASOS ────────────────────────────────────────────────────────
type Step = 'devolucion' | 'reposicion';
const currentStep = ref<Step>('devolucion');

const handleClose = () => {
    currentStep.value = 'devolucion';
    currentRepIndex.value = 0;
    acuerdosAcumulados.value = [];
    resetTipoForm();
    emit('close');
};

// ─── PASO 1: lógica original ──────────────────────────────────────
const handleItemStatusChange = (index: number) => {
    const item = props.form.items[index];
    const tipoLabel = item.es_equipo ? 'El equipo' : 'La herramienta';
    if (['Dañado', 'Extraviado', 'Incompleto'].includes(item.estado_devolucion)) {
        const nota = `[AUTO]: ${tipoLabel} "${item.nombre_mostrar}" se reporta como ${item.estado_devolucion.toUpperCase()} al momento de la devolución. `;
        if (!props.form.observacion.includes(nota)) props.form.observacion += nota;
    }
};

const handleAccessoryStatusChange = (itemIndex: number, accIndex: number) => {
    const item = props.form.items[itemIndex];
    const acc  = item.accessories[accIndex];
    if (acc.estado_accesorio === 'Extraviado') {
        item.estado_devolucion = 'Incompleto';
        const nota = `[AUTO]: El equipo "${item.nombre_mostrar}" se marca como INCOMPLETO porque el accesorio "${acc.nombre_accesorio}" fue reportado como EXTRAVIADO. `;
        if (!props.form.observacion.includes(nota)) props.form.observacion += nota;
    } else if (acc.estado_accesorio === 'Dañado') {
        item.estado_devolucion = 'Dañado';
        const nota = `[AUTO]: El equipo "${item.nombre_mostrar}" se marca como DAÑADO porque el accesorio "${acc.nombre_accesorio}" presenta DAÑOS. `;
        if (!props.form.observacion.includes(nota)) props.form.observacion += nota;
    }
};

// ─── DETECCIÓN DE ÍTEMS CON PROBLEMA ─────────────────────────────
interface ItemProblema {
    label: string;
    tipo: 'Equipo' | 'Herramienta' | 'Accesorio';
    estado: string;
    item_index: number;          // posición en form.items[]
    acc_index: number | null;    // posición en accessories[] (solo accesorios)
    originable_type: 'return_detail' | 'return_detail_accessory';
    contexto?: string;
}

const ESTADOS_BAD_ITEM = ['Dañado', 'Extraviado', 'Incompleto', 'Baja'];
const ESTADOS_BAD_ACC  = ['Dañado', 'Extraviado'];

const itemsConProblema = computed((): ItemProblema[] => {
    const result: ItemProblema[] = [];
    (props.form.items || []).forEach((item: any, i: number) => {

        // ── Accesorios con problema ───────────────────────────────
        // Si un accesorio está Dañado o Extraviado → el ACCESORIO necesita reposición.
        // El equipo se marcó automáticamente como Dañado/Incompleto por consecuencia,
        // pero eso NO significa que el equipo en sí necesite una reposición separada.
        const accesConProblema = (item.accessories || [])
            .map((acc: any, j: number) => ({ acc, j }))
            .filter(({ acc }: any) => ESTADOS_BAD_ACC.includes(acc.estado_accesorio));

        accesConProblema.forEach(({ acc, j }: any) => {
            result.push({
                label: acc.nombre_accesorio,
                tipo: 'Accesorio',
                estado: acc.estado_accesorio,
                item_index: i,
                acc_index: j,
                originable_type: 'return_detail_accessory',
                contexto: `Accesorio de: ${item.nombre_mostrar}`,
            });
        });

        // ── Daño directo en el equipo/herramienta ────────────────
        // Solo se agrega el ítem si su estado fue marcado DIRECTAMENTE por el usuario,
        // es decir, cuando NO hay accesorios con problema que lo hayan forzado.
        // Regla: si hay accesorios con problema, el estado del equipo es consecuencia
        // de esos accesorios → no duplicar con reposición del equipo.
        const tieneAccConProblema = accesConProblema.length > 0;
        const estadoDirectoBad    = ESTADOS_BAD_ITEM.includes(item.estado_devolucion);

        if (estadoDirectoBad && !tieneAccConProblema) {
            // El equipo/herramienta está dañado/extraviado directamente → necesita reposición
            result.push({
                label: item.nombre_mostrar,
                tipo: item.es_equipo ? 'Equipo' : 'Herramienta',
                estado: item.estado_devolucion,
                item_index: i,
                acc_index: null,
                originable_type: 'return_detail',
            });
        }
        // Si tieneAccConProblema && estadoDirectoBad → el estado del equipo fue
        // forzado automáticamente por handleAccessoryStatusChange, no es un daño
        // independiente. La reposición va sobre el/los accesorio(s).
    });
    return result;
});

const hayProblemas = computed(() => itemsConProblema.value.length > 0);

// ─── PASO 2: acuerdos acumulados ──────────────────────────────────
// Array que se enviará junto con la devolución en un solo POST
interface AcuerdoAcumulado {
    item_index: number;
    acc_index: number | null;
    originable_type: 'return_detail' | 'return_detail_accessory';
    tipo_reposicion: 'Reparacion' | 'Reemplazo' | 'Desbloqueo';
    fecha_limite: string;
    observacion: string;
}

const acuerdosAcumulados = ref<AcuerdoAcumulado[]>([]);
const currentRepIndex    = ref(0);

// Estado del formulario del ítem actual (sin useForm — se acumula localmente)
const tipoReposicion = ref<'Reparacion' | 'Reemplazo' | 'Desbloqueo' | null>(null);
const fechaLimite    = ref('');
const obsAcuerdo     = ref('');

const resetTipoForm = () => {
    tipoReposicion.value = null;
    fechaLimite.value    = '';
    obsAcuerdo.value     = '';
};

const itemActual = computed(() => itemsConProblema.value[currentRepIndex.value]);
const esUltimo   = computed(() => currentRepIndex.value === itemsConProblema.value.length - 1);
const puedeAcumular = computed(() => tipoReposicion.value !== null);

// Al confirmar desde paso 1
const handleConfirm = () => {
    if (hayProblemas.value) {
        currentStep.value = 'reposicion';
        currentRepIndex.value = 0;
        acuerdosAcumulados.value = [];
        resetTipoForm();
    } else {
        // Sin problemas → confirmar directo sin acuerdos
        emit('confirm', []);
    }
};

// Acumular acuerdo para el ítem actual y avanzar
const acumularYSiguiente = () => {
    if (!tipoReposicion.value) return;
    const item = itemActual.value;
    acuerdosAcumulados.value.push({
        item_index:      item.item_index,
        acc_index:       item.acc_index,
        originable_type: item.originable_type,
        tipo_reposicion: tipoReposicion.value,
        fecha_limite:    fechaLimite.value,
        observacion:     obsAcuerdo.value,
    });
    avanzar();
};

// Saltar ítem sin crear acuerdo
const saltarItem = () => avanzar();

const avanzar = () => {
    if (esUltimo.value) {
        confirmarConAcuerdos();
    } else {
        currentRepIndex.value++;
        resetTipoForm();
    }
};

// Confirmar devolución: emitimos los acuerdos al padre para que él los incluya en el POST.
// No mutamos props.form directamente porque useForm de Inertia no acepta campos nuevos
// asignados desde un componente hijo — los ignoraría silenciosamente.
const confirmarConAcuerdos = () => {
    if (props.form.processing) return;
    emit('confirm', acuerdosAcumulados.value);
};

const omitirTodo = () => {
    if (props.form.processing) return;
    emit('confirm', []);
};

const tiposReposicion = [
    { value: 'Reparacion' as const, label: 'Reparación', desc: 'Ítem enviado a reparar', icon: Wrench,
      color: 'border-purple-200 bg-purple-50 text-purple-700', active: 'border-purple-500 bg-purple-100 ring-2 ring-purple-200' },
    { value: 'Reemplazo' as const, label: 'Reemplazo', desc: 'Entrega ítem equivalente', icon: RefreshCw,
      color: 'border-blue-200 bg-blue-50 text-blue-700', active: 'border-blue-500 bg-blue-100 ring-2 ring-blue-200' },
    { value: 'Desbloqueo' as const, label: 'Desbloqueo', desc: 'Otra compensación', icon: UserCheck,
      color: 'border-amber-200 bg-amber-50 text-amber-700', active: 'border-amber-500 bg-amber-100 ring-2 ring-amber-200' },
];

const colorEstado = (estado: string) => {
    if (['Dañado', 'Extraviado'].includes(estado)) return 'bg-red-50 text-red-700 border-red-100';
    if (['Incompleto'].includes(estado))            return 'bg-amber-50 text-amber-700 border-amber-100';
    return 'bg-neutral-100 text-neutral-600 border-neutral-200';
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-200 flex items-center justify-center bg-black/40 backdrop-blur-md p-6 lg:p-12">
        <div class="bg-white w-full max-w-3xl rounded-4xl shadow-2xl overflow-hidden flex flex-col max-h-[88vh] animate-in zoom-in duration-300">

            <!-- HEADER -->
            <div class="px-8 py-5 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <template v-if="currentStep === 'devolucion'">
                        <div class="p-2.5 bg-[#1a3a5a] rounded-xl shadow-lg">
                            <NotebookPen class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg font-black uppercase tracking-tighter text-neutral-900 leading-none">Registrar Devolución</h2>
                            <p class="text-neutral-500 text-sm mt-0.5">Verifique el estado de los ítems recibidos</p>
                        </div>
                    </template>
                    <template v-else>
                        <div class="p-2.5 bg-amber-500 rounded-xl shadow-lg">
                            <AlertTriangle class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h2 class="text-lg font-black uppercase tracking-tighter text-neutral-900 leading-none">Acuerdo de Reposición</h2>
                            <p class="text-neutral-500 text-sm mt-0.5">
                                Ítem {{ currentRepIndex + 1 }} de {{ itemsConProblema.length }}
                                <span v-if="acuerdosAcumulados.length > 0" class="text-green-600 font-bold">
                                    · {{ acuerdosAcumulados.length }} preparado{{ acuerdosAcumulados.length > 1 ? 's' : '' }}
                                </span>
                            </p>
                        </div>
                    </template>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Progreso por ítem (paso 2) -->
                    <div v-if="currentStep === 'reposicion'" class="flex items-center gap-1">
                        <span v-for="(_, i) in itemsConProblema" :key="i"
                            :class="['h-2 rounded-full transition-all duration-300',
                                i < currentRepIndex ? 'w-4 bg-green-400' :
                                i === currentRepIndex ? 'w-6 bg-amber-500' : 'w-2 bg-neutral-200']" />
                    </div>
                    <!-- Pasos globales -->
                    <div class="flex items-center gap-1">
                        <span :class="['w-2 h-2 rounded-full transition-all', currentStep === 'devolucion' ? 'bg-[#1a3a5a] w-5' : 'bg-neutral-300']"/>
                        <span :class="['w-2 h-2 rounded-full transition-all', currentStep === 'reposicion' ? 'bg-amber-500 w-5' : 'bg-neutral-300']"/>
                    </div>
                    <button @click="handleClose" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                        <XIcon class="w-6 h-6 text-neutral-300 group-hover:text-red-500 transition-colors"/>
                    </button>
                </div>
            </div>

            <!-- ══ PASO 1: DEVOLUCIÓN ══ -->
            <div v-if="currentStep === 'devolucion'" class="p-7 pt-3 overflow-y-auto custom-scrollbar space-y-5 flex-1 bg-neutral-50/30">

                <!-- Responsable + Materia -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-5 bg-neutral-50 rounded-3xl border border-neutral-200 shadow-sm">
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[12px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <User class="w-3.5 h-3.5" /> Responsable
                        </p>
                        <p class="text-sm font-bold text-neutral-900">{{ loan?.borrower?.apellidos }} {{ loan?.borrower?.nombres }}</p>
                        <div class="flex gap-2 flex-wrap">
                            <span class="px-2 py-0.5 rounded-md bg-[#1a3a5a]/10 text-[12px] font-black text-[#1a3a5a]">
                                {{ loan?.borrower?.teacher ? 'DOCENTE' : (loan?.borrower?.assistant ? 'AUXILIAR' : 'ESTUDIANTE') }}
                            </span>
                            <span class="px-2 py-0.5 rounded-lg bg-neutral-100 text-[12px] font-black text-neutral-700 border border-neutral-200">
                                CI: {{ loan?.borrower?.cedula_identidad }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-1 border-l border-neutral-100 pl-4">
                        <p class="flex items-center gap-2 text-[12px] font-black text-[#1a3a5a] uppercase tracking-widest">
                            <BookMarked class="w-3.5 h-3.5" /> Materia Asignada
                        </p>
                        <p class="text-sm font-bold text-neutral-900 leading-tight">{{ loan?.subject?.nombre_materia }}</p>
                        <p class="text-[14px] text-[#1a3a5a] font-mono tracking-tighter">{{ loan?.subject?.sigla }}</p>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="space-y-2">
                    <h3 class="text-[12px] font-black uppercase tracking-widest text-neutral-700 flex items-center gap-2">
                        <CalendarClock class="w-3.5 h-3.5" /> Fechas y horarios
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-neutral-50 rounded-3xl border border-neutral-200">
                        <div class="flex flex-col gap-2.5">
                            <span class="text-[12px] font-bold text-blue-700 uppercase tracking-tighter">Salida</span>
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-white rounded-lg shadow-sm shrink-0"><Calendar class="w-3.5 h-3.5 text-blue-700"/></div>
                                <div><p class="text-[10px] font-black text-blue-700 uppercase tracking-widest leading-none mb-0.5">Fecha</p><p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_salida }}</p></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-white rounded-lg shadow-sm shrink-0"><Clock class="w-3.5 h-3.5 text-blue-700"/></div>
                                <div><p class="text-[10px] font-black text-blue-700 uppercase tracking-widest leading-none mb-0.5">Hora inicio</p><p class="text-sm font-bold text-neutral-800">{{ loan?.hora_inicio }}</p></div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5 border-l border-neutral-100 pl-3">
                            <span class="text-[12px] font-bold text-orange-700 uppercase tracking-tighter">Previsto</span>
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-white rounded-lg shadow-sm shrink-0"><CalendarClock class="w-3.5 h-3.5 text-orange-700"/></div>
                                <div><p class="text-[10px] font-black text-orange-700 uppercase tracking-widest leading-none mb-0.5">Fecha límite</p><p class="text-sm font-bold text-neutral-800">{{ loan?.fecha_retorno_prevista }}</p></div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-white rounded-lg shadow-sm shrink-0"><ClockAlert class="w-3.5 h-3.5 text-orange-700"/></div>
                                <div><p class="text-[10px] font-black text-orange-700 uppercase tracking-widest leading-none mb-0.5">Hora fin</p><p class="text-sm font-bold text-neutral-800">{{ loan?.hora_fin_prevista }}</p></div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5 border-l border-neutral-100 pl-3">
                            <span class="text-[12px] font-bold text-green-700 uppercase tracking-tighter">Retorno real</span>
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-white rounded-lg shadow-sm shrink-0"><CalendarCheck2 class="w-3.5 h-3.5 text-green-700"/></div>
                                <div class="flex-1">
                                    <Label class="text-[10px] font-black text-green-700 uppercase tracking-widest leading-none mb-0.5">Fecha</Label>
                                    <Input type="date" v-model="form.fecha_retorno" :min="loan?.fecha_salida" class="h-7 text-xs rounded-lg"/>
                                    <InputError :message="form.errors.fecha_retorno"/>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-white rounded-lg shadow-sm shrink-0"><History class="w-3.5 h-3.5 text-green-700"/></div>
                                <div class="flex-1">
                                    <Label class="text-[10px] font-black text-green-700 uppercase tracking-widest leading-none mb-0.5">Hora entrada</Label>
                                    <Input type="time" v-model="form.hora_fin" class="h-7 text-xs rounded-lg"/>
                                    <InputError :message="form.errors.hora_fin"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ítems -->
                <div class="space-y-2">
                    <h3 class="text-[12px] font-black uppercase tracking-widest text-neutral-700 flex items-center gap-2">
                        <Package class="w-3.5 h-3.5"/> Revisión de equipos y herramientas
                    </h3>
                    <div v-for="(item, index) in form.items" :key="item.id"
                        class="border border-neutral-200 bg-white rounded-2xl overflow-hidden shadow-sm">
                        <div class="flex items-center justify-between p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0">
                                    <img v-if="item.foto_equipo || item.foto_herramienta || item.foto"
                                        :src="'/storage/' + (item.foto_equipo || item.foto_herramienta || item.foto)" class="object-cover w-full h-full"/>
                                    <Image v-else class="w-5 h-5 text-neutral-300"/>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-neutral-900">{{ item.nombre_mostrar }}</p>
                                    <span :class="['px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border', item.es_equipo ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100']">
                                        {{ item.es_equipo ? 'EQUIPO' : 'HERRAMIENTA' }}
                                    </span>
                                </div>
                            </div>
                            <select v-model="item.estado_devolucion" @change="handleItemStatusChange(Number(index))"
                                class="text-[13px] font-bold rounded-xl border-neutral-200 bg-neutral-50 focus:ring-black focus:border-black py-1.5 px-3">
                                <option value="Disponible">Disponible</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                                <option value="Incompleto">Incompleto</option>
                            </select>
                        </div>
                        <div v-if="item.accessories?.length" class="bg-neutral-50/80 p-3 border-t border-neutral-100 space-y-2">
                            <p class="text-[10px] font-black text-neutral-600 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-blue-400 rounded-full inline-block"></span>Accesorios
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div v-for="(acc, accIndex) in item.accessories" :key="acc.id"
                                    class="flex items-center justify-between bg-white p-2 rounded-xl border border-neutral-200/60 shadow-sm">
                                    <span class="text-[12px] font-semibold text-neutral-800 flex items-center gap-1.5">
                                        <CornerDownRight class="w-3.5 h-3.5 text-blue-400 shrink-0"/>
                                        <span class="w-8 h-8 rounded-md bg-neutral-50 flex items-center justify-center overflow-hidden border border-neutral-100 shrink-0">
                                            <img v-if="acc.foto_accesorio" :src="'/storage/' + acc.foto_accesorio" class="object-cover w-full h-full"/>
                                            <Image v-else class="w-3 h-3 text-neutral-300"/>
                                        </span>
                                        {{ acc.nombre_accesorio }}
                                    </span>
                                    <select v-model="form.items[index].accessories[accIndex].estado_accesorio"
                                        @change="handleAccessoryStatusChange(Number(index), Number(accIndex))"
                                        class="text-[12px] py-1 px-2 border-neutral-100 rounded-lg bg-neutral-50 font-bold focus:ring-black outline-none">
                                        <option value="Bueno">Bueno</option>
                                        <option value="Dañado">Dañado</option>
                                        <option value="Extraviado">Extraviado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="space-y-1.5">
                    <Label class="text-[12px] font-black uppercase text-neutral-600 tracking-widest flex items-center gap-1.5">
                        <AlignLeft class="w-3.5 h-3.5"/> Notas adicionales
                    </Label>
                    <Textarea v-model="form.observacion" placeholder="Escriba aquí si hubo algún incidente..."
                        class="bg-white border-neutral-200 text-sm rounded-2xl min-h-20 italic text-neutral-600"/>
                </div>

                <!-- Banner de advertencia -->
                <div v-if="hayProblemas" class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                    <AlertTriangle class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"/>
                    <div>
                        <p class="text-sm text-amber-800 font-bold">
                            {{ itemsConProblema.length }} ítem{{ itemsConProblema.length > 1 ? 's' : '' }} con problema detectado{{ itemsConProblema.length > 1 ? 's' : '' }}
                        </p>
                        <p class="text-xs text-amber-700 mt-0.5">Al confirmar podrás crear acuerdos de reposición. Es opcional.</p>
                    </div>
                </div>
            </div>

            <!-- ══ PASO 2: REPOSICIÓN (multi-ítem) ══ -->
            <div v-else class="p-7 pt-4 overflow-y-auto custom-scrollbar space-y-5 flex-1 bg-neutral-50/30">

                <!-- Ítem actual -->
                <div v-if="itemActual" class="p-4 bg-white rounded-2xl border-2 border-amber-200 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex flex-col gap-1">
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">{{ itemActual.tipo }} con problema</p>
                            <p class="text-base font-black text-neutral-900">{{ itemActual.label }}</p>
                            <p v-if="itemActual.contexto" class="text-xs text-neutral-400 italic">{{ itemActual.contexto }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            <span :class="['px-2.5 py-1 rounded-full text-[11px] font-black uppercase border', colorEstado(itemActual.estado)]">
                                {{ itemActual.estado }}
                            </span>
                            <span class="text-[10px] text-neutral-400 italic">
                                Se repone: <strong class="text-neutral-600">{{ itemActual.tipo === 'Accesorio' ? 'el accesorio' : 'el ítem' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tipo de acuerdo -->
                <div>
                    <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-3">Tipo de acuerdo</p>
                    <div class="grid grid-cols-3 gap-3">
                        <button v-for="tipo in tiposReposicion" :key="tipo.value"
                            @click="tipoReposicion = tipo.value"
                            :class="['flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all text-center',
                                tipoReposicion === tipo.value ? tipo.active : tipo.color + ' hover:opacity-80']">
                            <component :is="tipo.icon" class="w-5 h-5"/>
                            <span class="text-[12px] font-black uppercase">{{ tipo.label }}</span>
                            <span class="text-[10px] leading-tight opacity-80">{{ tipo.desc }}</span>
                        </button>
                    </div>
                </div>

                <!-- Fecha límite + Observación -->
                <div v-if="tipoReposicion" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">
                            Fecha límite <span class="text-neutral-300 font-normal normal-case">(opcional)</span>
                        </label>
                        <input type="date" v-model="fechaLimite" :min="new Date().toISOString().split('T')[0]"
                            class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] bg-white"/>
                        <p class="text-[10px] text-neutral-400 mt-1">Sin fecha = acuerdo abierto</p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">Observación</label>
                        <textarea v-model="obsAcuerdo" rows="3" placeholder="Condiciones del acuerdo..."
                            class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] bg-white resize-none"/>
                    </div>
                </div>

                <!-- Resumen de acuerdos ya preparados -->
                <div v-if="acuerdosAcumulados.length > 0"
                    class="flex items-center gap-2 p-3 bg-green-50 border border-green-200 rounded-xl">
                    <CheckCircle class="w-4 h-4 text-green-600 shrink-0"/>
                    <p class="text-xs text-green-700 font-medium">
                        {{ acuerdosAcumulados.length }} acuerdo{{ acuerdosAcumulados.length > 1 ? 's' : '' }} preparado{{ acuerdosAcumulados.length > 1 ? 's' : '' }}.
                        Se registrarán junto con la devolución.
                    </p>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="px-7 py-4 bg-neutral-50 border-t border-neutral-100 flex gap-3 shrink-0">

                <!-- Footer paso 1 -->
                <template v-if="currentStep === 'devolucion'">
                    <button @click="handleClose"
                        class="flex-1 py-3 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all">
                        Cerrar
                    </button>
                    <button @click="handleConfirm" :disabled="form.processing"
                        class="flex-1 py-3 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all flex items-center justify-center gap-2 shadow-lg active:scale-95 disabled:opacity-50">
                        <Loader2 v-if="form.processing" class="animate-spin w-4 h-4"/>
                        <span v-else>{{ hayProblemas ? 'Confirmar y gestionar reposiciones →' : 'Confirmar Devolución' }}</span>
                    </button>
                </template>

                <!-- Footer paso 2 -->
                <template v-else>
                    <button @click="omitirTodo" :disabled="props.form.processing"
                        class="py-3 px-4 bg-white border border-neutral-200 text-neutral-500 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        Omitir todo
                    </button>
                    <button @click="saltarItem" :disabled="props.form.processing"
                        class="flex-1 py-3 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                        <ChevronRight class="w-4 h-4"/>
                        {{ esUltimo ? 'Saltar y finalizar' : 'Saltar este ítem' }}
                    </button>
                    <button @click="acumularYSiguiente" :disabled="!puedeAcumular"
                        class="flex-1 py-3 bg-amber-500 text-white rounded-2xl font-bold text-sm hover:bg-amber-600 transition-all flex items-center justify-center gap-2 shadow-lg active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed">
                        <CheckCircle class="w-4 h-4"/>
                        {{ esUltimo ? 'Registrar y finalizar' : 'Registrar y siguiente →' }}
                    </button>
                </template>
            </div>

        </div>
    </div>
</template>
