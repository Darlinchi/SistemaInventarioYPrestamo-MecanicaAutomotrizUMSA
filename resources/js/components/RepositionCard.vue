<script setup lang="ts">
import {
    Calendar, CalendarCheck2, User, Wrench,
    RefreshCw, UserCheck, AlertTriangle,
    CheckCircle, XCircle, ClockAlert, Package2
} from 'lucide-vue-next';

const props = defineProps<{
    rep: any;
    canEdit: boolean;
}>();

const emit = defineEmits(['cumplida', 'incumplida', 'definir', 'eliminar']);

// ── Helpers visuales ──────────────────────────────────────────────
const iconoTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return Wrench;
    if (tipo === 'Reemplazo')  return RefreshCw;
    return UserCheck;
};

const labelTipo = (tipo: string) => {
    if (tipo === 'Reparacion') return 'Reparación';
    if (tipo === 'Reemplazo')  return 'Reemplazo';
    return 'Desbloqueo';
};

// Colores del tipo — igual que ReturnLoanModal
const colorTipoBadge = (tipo: string) => {
    if (tipo === 'Reparacion') return 'bg-purple-50 text-purple-700 border-purple-200';
    if (tipo === 'Reemplazo')  return 'bg-blue-50 text-blue-700 border-blue-200';
    return 'bg-amber-50 text-amber-700 border-amber-200';
};

const colorTipoOrigen = (tipo: string) => {
    if (tipo === 'Equipo')      return 'bg-red-50 text-red-700 border-red-100';
    if (tipo === 'Herramienta') return 'bg-blue-50 text-blue-700 border-blue-100';
    return 'bg-emerald-50 text-emerald-700 border-emerald-100';
};

const colorEstadoDano = (estado: string) => {
    if (['Dañado', 'Extraviado', 'Baja'].includes(estado)) return 'bg-red-50 text-red-700 border-red-100';
    if (estado === 'Incompleto') return 'bg-amber-50 text-amber-700 border-amber-100';
    return 'bg-neutral-100 text-neutral-500 border-neutral-200';
};

// Borde y fondo de la card según estado
const colorCard = (rep: any) => {
    if (estaVencida(rep))         return 'border-red-200 bg-red-50/20 shadow-red-50/50';
    if (rep.estado === 'Cumplida')   return 'border-green-200 bg-green-50/20';
    if (rep.estado === 'Incumplida') return 'border-red-200 bg-red-50/10';
    return 'border-blue-100 bg-blue-50/30'; // Pendiente — mismo que LoanActiveCard
};

const estaVencida = (rep: any) =>
    rep.estado === 'Pendiente' && !!rep.fecha_limite &&
    new Date(rep.fecha_limite) < new Date();

</script>

<template>
    <div :class="[
        'group border rounded-4xl p-6 flex flex-col md:flex-row justify-between items-start',
        'transition-all duration-300 shadow-sm hover:shadow-xl hover:-translate-y-1 mb-6 relative',
        colorCard(rep)
    ]">
        <!-- ── Cuerpo principal: 3 columnas igual que LoanActiveCard ── -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-12 w-full">

            <!-- COLUMNA 1: Fechas -->
            <div class="flex flex-col justify-center space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Fecha registro -->
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-blue-700 uppercase tracking-widest leading-none">
                            <Calendar class="w-4 h-4"/> Registrado
                        </p>
                        <p class="text-sm font-bold text-neutral-800">{{ rep.created_at }}</p>
                    </div>
                    <!-- Fecha límite -->
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-700 uppercase tracking-widest leading-none">
                            <ClockAlert class="w-4 h-4"/> F. Límite
                        </p>
                        <p :class="['text-sm font-bold', estaVencida(rep) ? 'text-red-600' : 'text-neutral-800']">
                            {{ rep.fecha_limite ?? 'Sin límite' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Tipo de reposición -->
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest leading-none">
                            <Wrench class="w-4 h-4"/> Tipo
                        </p>
                        <!-- Sin acuerdo definido -->
                        <span v-if="rep.sin_acuerdo"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-black uppercase border bg-neutral-100 text-neutral-500 border-neutral-200">
                            <AlertTriangle class="w-3 h-3"/> Sin definir
                        </span>
                        <span v-else :class="['inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[11px] font-black uppercase border', colorTipoBadge(rep.tipo_reposicion)]">
                            <component :is="iconoTipo(rep.tipo_reposicion)" class="w-3.5 h-3.5"/>
                            {{ labelTipo(rep.tipo_reposicion) }}
                        </span>
                    </div>
                    <!-- Fecha cumplimiento -->
                    <div class="space-y-1">
                        <p class="flex items-center gap-2 text-[13px] font-black text-orange-700 uppercase tracking-widest leading-none">
                            <CalendarCheck2 class="w-4 h-4"/> Cumplida
                        </p>
                        <p class="text-sm font-bold" :class="rep.fecha_cumplimiento ? 'text-green-700' : 'text-neutral-400'">
                            {{ rep.fecha_cumplimiento ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- COLUMNA 2: Ítem y responsable -->
            <div class="space-y-4">
                <!-- Ítem con problema -->
                <div class="space-y-1">
                    <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                        <Package2 class="w-4 h-4"/> Ítem
                        <span :class="['px-2 py-0.5 rounded-md text-[11px] font-black uppercase border', colorTipoOrigen(rep.tipo_origen)]">
                            {{ rep.tipo_origen }}
                        </span>
                    </p>
                    <p class="text-[14px] font-bold text-neutral-800 leading-tight mt-1">
                        {{ rep.nombre_origen }}
                    </p>
                    <!-- Equipo padre (si el ítem es un accesorio) -->
                    <p v-if="rep.equipo_padre" class="text-[12px] text-neutral-500 font-medium italic">
                        Accesorio de: <span class="font-bold text-neutral-700">{{ rep.equipo_padre }}</span>
                    </p>
                    <span :class="['inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border mt-0.5', colorEstadoDano(rep.estado_dano)]">
                        {{ rep.estado_dano }}
                    </span>
                </div>

                <!-- Responsable -->
                <div class="space-y-1">
                    <p class="flex items-center gap-2 text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                        <User class="w-4 h-4"/> Responsable
                    </p>
                    <p class="text-[14px] font-bold text-neutral-800 leading-tight mt-1">
                        {{ rep.borrower_nombre }}
                        <span class="block text-[12px] text-neutral-500 font-medium mt-0.5">
                            CI: {{ rep.borrower_ci }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- COLUMNA 3: Observación + registrado por + nuevo ítem -->
            <div class="space-y-4">
                <!-- Registrado por -->
                <div class="space-y-1">
                    <p class="text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                        Registrado por
                    </p>
                    <p class="text-sm font-bold text-neutral-800">{{ rep.registrado_por }}</p>
                </div>

                <!-- Ítem nuevo (Reemplazo cumplido) -->
                <div v-if="rep.nombre_nuevo_item" class="space-y-1">
                    <p class="text-[13px] font-black text-green-700 uppercase tracking-widest flex items-center gap-1.5">
                        <RefreshCw class="w-4 h-4"/> Ítem nuevo
                    </p>
                    <p class="text-sm font-bold text-green-800">{{ rep.nombre_nuevo_item }}</p>
                </div>

                <!-- Observación -->
                <div v-if="rep.observacion" class="space-y-1">
                    <p class="text-[13px] font-black text-[#1a3a5a] uppercase tracking-widest">
                        Observación
                    </p>
                    <p class="text-xs text-neutral-500 italic leading-relaxed">{{ rep.observacion }}</p>
                </div>

                <!-- Badge vencida -->
                <div v-if="estaVencida(rep)"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 border border-red-200 text-[11px] font-black uppercase">
                    <AlertTriangle class="w-3.5 h-3.5"/> Plazo vencido
                </div>
            </div>
        </div>

        <!-- ── Botones de acción — mismo estilo que LoanActiveCard ── -->
        <div class="flex flex-row md:flex-col gap-3 mt-6 md:mt-0 md:ml-8 w-full md:w-auto shrink-0">
            <template v-if="rep.estado === 'Pendiente' && canEdit">

                <!-- Siempre visible: Definir acuerdo (si no tiene) o Editar (si ya tiene) -->
                <button
                    @click="emit('definir', rep)"
                    :class="[
                        'flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition shadow-sm',
                        rep.sin_acuerdo
                            ? 'bg-amber-500 text-white border border-amber-500 hover:bg-amber-600'
                            : 'bg-white border border-neutral-200 text-neutral-700 hover:bg-neutral-100'
                    ]"
                >
                    <Wrench class="w-3.5 h-3.5"/>
                    {{ rep.sin_acuerdo ? 'Definir acuerdo' : 'Editar acuerdo' }}
                </button>

                <!-- Marcar Incumplida (solo si tiene acuerdo definido)
                <button v-if="!rep.sin_acuerdo"
                    @click="emit('incumplida', rep)"
                    class="flex items-center justify-center gap-2 bg-white border border-red-200 px-5 py-2.5 rounded-xl text-xs font-black text-red-600 hover:bg-red-50 transition shadow-sm uppercase tracking-wider">
                    <XCircle class="w-3.5 h-3.5"/> Incumplida
                </button> -->

                <!-- Marcar Cumplida — siempre visible (abre modal para definir tipo si falta) -->
                <button
                    @click="emit('cumplida', rep)"
                    class="flex-1 flex items-center justify-center gap-2 bg-[#1a3a5a] border border-[#1a3a5a] px-5 py-2.5 rounded-xl text-xs font-black text-white hover:bg-[#122a42] transition shadow-md uppercase tracking-wider active:scale-95">
                    <CheckCircle class="w-3.5 h-3.5"/> Cumplida
                </button>

            </template>

            <!-- Estado final → badge estático -->
            <template v-else-if="rep.estado !== 'Pendiente'">
                <span :class="[
                    'inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-black uppercase border',
                    rep.estado === 'Cumplida'
                        ? 'bg-green-50 text-green-700 border-green-200'
                        : 'bg-red-50 text-red-700 border-red-200'
                ]">
                    <CheckCircle v-if="rep.estado === 'Cumplida'" class="w-3.5 h-3.5"/>
                    <XCircle v-else class="w-3.5 h-3.5"/>
                    {{ rep.estado }}
                </span>
            </template>
        </div>
    </div>
</template>
