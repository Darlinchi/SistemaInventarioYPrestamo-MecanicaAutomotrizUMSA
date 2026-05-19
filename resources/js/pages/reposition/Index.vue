<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AlertNotification from '@/components/AlertNotification.vue';
import PageHeader from '@/components/PageHeader.vue';
import SearchInput from '@/components/shared/SearchInput.vue';
import SelectFilter from '@/components/shared/SelectFilter.vue';
import ClearFiltersButton from '@/components/shared/ClearFiltersButton.vue';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import TabSelector from '@/components/shared/TabSelector.vue';
import {
    RefreshCcw, RefreshCw, Wrench, DollarSign, Clock, CheckCircle, XCircle,
    Package2, User, Calendar, CalendarCheck2, AlignLeft, ClipboardList,
    AlertTriangle, UserCheck, Loader2
} from 'lucide-vue-next';
import repositionRoutes from '@/routes/repositions';
import RepositionCard from '@/components/RepositionCard.vue';
import RepositionHistoryTable from '@/components/RepositionHistoryTable.vue';
import RepositionDetailModal from '@/components/RepositionDetailModal.vue';

interface Reposition {
    id: number;
    nombre_origen: string;
    tipo_origen: string;       // 'Equipo' | 'Herramienta' | 'Accesorio'
    estado_dano: string;
    borrower_nombre: string;
    borrower_ci: string;
    tipo_reposicion: string;   // 'Reparacion' | 'Reemplazo' | 'Desbloqueo'
    estado: string;            // 'Pendiente' | 'Cumplida' | 'Incumplida'
    fecha_limite: string | null;
    fecha_cumplimiento: string | null;
    observacion: string | null;
    nombre_nuevo_item: string | null;
    registrado_por: string;
    created_at: string;
    sin_acuerdo: boolean;  // true = creada automáticamente, tipo_reposicion aún null
    equipo_padre: string | null;  // nombre del equipo al que pertenece el accesorio
}

const props = defineProps<{
    repositions: Reposition[];
}>();

const page = usePage();
const can = (permission: string) =>
    (page.props.auth.user?.permissions ?? []).includes(permission);

const flashSuccess = computed(() => (page.props.flash as any)?.success);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reposiciones', href: repositionRoutes.index.url() },
];

// ── PESTAÑAS ──────────────────────────────────────────────────────
const activeTab = ref<'Pendiente' | 'Historial'>('Pendiente');

const countPendientes = computed(() => props.repositions.filter(r => r.estado === 'Pendiente').length);
const countHistorial  = computed(() => props.repositions.filter(r => r.estado !== 'Pendiente').length);

const tabs = computed(() => [
    { id: 'Pendiente',  label: 'Pendientes', count: countPendientes.value, icon: Clock },
    { id: 'Historial',  label: 'Historial',  count: countHistorial.value,  icon: CheckCircle },
]);

// ── FILTROS ───────────────────────────────────────────────────────
const searchQuery      = ref('');
const filterTipo       = ref('');

// tiposReposicion definido abajo con icon/color/desc (usado en filtro y modal)

const repositionsFiltradas = computed(() => {
    let list = activeTab.value === 'Pendiente'
        ? props.repositions.filter(r => r.estado === 'Pendiente')
        : props.repositions.filter(r => r.estado !== 'Pendiente');

    if (filterTipo.value)
        list = list.filter(r => r.tipo_reposicion === filterTipo.value);

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        list = list.filter(r =>
            r.nombre_origen.toLowerCase().includes(q) ||
            r.borrower_nombre.toLowerCase().includes(q) ||
            r.borrower_ci.includes(q)
        );
    }
    return list;
});

// ── HELPERS VISUALES ─────────────────────────────────────────────
// Igual que ReturnLoanModal para consistencia visual
const tiposReposicion = [
    { value: 'Reparacion', label: 'Reparación', desc: 'Ítem enviado a reparar',   icon: Wrench,
      color: 'border-purple-200 bg-purple-50 text-purple-700', active: 'border-purple-500 bg-purple-100 ring-2 ring-purple-200 text-purple-700' },
    { value: 'Reemplazo',  label: 'Reemplazo',  desc: 'Entrega ítem equivalente', icon: RefreshCw,
      color: 'border-blue-200 bg-blue-50 text-blue-700',       active: 'border-blue-500 bg-blue-100 ring-2 ring-blue-200 text-blue-700' },
    { value: 'Desbloqueo', label: 'Desbloqueo', desc: 'Otra compensación',        icon: UserCheck,
      color: 'border-amber-200 bg-amber-50 text-amber-700',    active: 'border-amber-500 bg-amber-100 ring-2 ring-amber-200 text-amber-700' },
];

const colorEstado = (estado: string) => {
    if (['Dañado', 'Extraviado'].includes(estado)) return 'bg-red-50 text-red-700 border-red-100';
    if (estado === 'Incompleto')                   return 'bg-amber-50 text-amber-700 border-amber-100';
    return 'bg-neutral-100 text-neutral-600 border-neutral-200';
};

// ── MODAL DETALLE ────────────────────────────────────────────────
const modalDetalle = ref(false);
const repDetalle   = ref<Reposition | null>(null);
const verDetalle   = (rep: Reposition) => { repDetalle.value = rep; modalDetalle.value = true; };

// ── MODAL DEFINIR/COMPLETAR ACUERDO ─────────────────────────────
const modalAcuerdo = ref(false);
const repSeleccionada = ref<Reposition | null>(null);

const acuerdoForm = useForm({
    estado:          'Pendiente' as string,
    tipo_reposicion: '' as string,
    fecha_limite:    '' as string,
    observacion:     '' as string,
    fecha_cumplimiento: '' as string,
});

// Index.vue - Función corregida
const abrirModalAcuerdo = (rep: Reposition, accionDirecta?: 'Cumplida' | 'Incumplida') => {
    repSeleccionada.value = rep;
    acuerdoForm.estado          = accionDirecta ?? 'Pendiente';
    acuerdoForm.tipo_reposicion = rep.tipo_reposicion ?? '';
    acuerdoForm.fecha_limite    = rep.fecha_limite ?? '';
    acuerdoForm.observacion     = rep.observacion ?? '';

    // CORRECCIÓN AQUÍ: Obtener la fecha local en formato YYYY-MM-DD
    if (accionDirecta === 'Cumplida') {
        const hoy = new Date();
        const offset = hoy.getTimezoneOffset();
        const fechaLocal = new Date(hoy.getTime() - (offset * 60 * 1000));
        acuerdoForm.fecha_cumplimiento = fechaLocal.toISOString().split('T')[0];
    } else {
        acuerdoForm.fecha_cumplimiento = '';
    }

    modalAcuerdo.value = true;
};

const guardarAcuerdo = () => {
    if (!repSeleccionada.value) return;
    acuerdoForm.patch(`/dashboard/repositions/${repSeleccionada.value.id}`, {
        preserveScroll: true,
        onSuccess: () => { modalAcuerdo.value = false; }
    });
};

// ── ACCIONES ─────────────────────────────────────────────────────
const repForm = useForm({ estado: '', fecha_cumplimiento: '', observacion: '' });

const marcarCumplida   = (rep: Reposition) => abrirModalAcuerdo(rep, 'Cumplida');
const marcarIncumplida = (rep: Reposition) => {
    if (!confirm(`¿Marcar la reposición de "${rep.nombre_origen}" como Incumplida?`)) return;
    repForm.estado = 'Incumplida';
    repForm.patch(`/dashboard/repositions/${rep.id}`, { preserveScroll: true });
};
const definirAcuerdo = (rep: Reposition) => abrirModalAcuerdo(rep);

</script>

<template>
    <Head title="Reposiciones" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <AlertNotification :message="flashSuccess" />

            <PageHeader
                description="Seguimiento de acuerdos de reposición por ítems dañados o extraviados"
            />

            <!-- Pestañas -->
            <TabSelector
                :tabs="tabs"
                :activeTab="activeTab"
                @update:activeTab="val => activeTab = (val as any)"
            />

            <!-- Filtros -->
            <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                <SearchInput
                    v-model="searchQuery"
                    placeholder="Buscar por ítem o responsable..."
                    class="flex-1"
                />
                <SelectFilter
                    v-model="filterTipo"
                    label="Tipo"
                    :options="tiposReposicion"
                    option-value="value"
                    option-label="label"
                    icon="RefreshCcw"
                    class="md:w-44"
                />
                <ClearFiltersButton @clear="() => { searchQuery = ''; filterTipo = '' }" />
            </div>

            <!-- ── PENDIENTES: cards ── -->
            <div v-if="activeTab === 'Pendiente'">
                <div
                    v-if="repositionsFiltradas.length === 0"
                    class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200"
                >
                    <Package2 class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium text-sm">No hay reposiciones pendientes con esos criterios.</p>
                </div>
                <RepositionCard
                    v-for="rep in repositionsFiltradas"
                    :key="rep.id"
                    :rep="rep"
                    :can-edit="can('prestamos.editar')"
                    @cumplida="marcarCumplida"
                    @incumplida="marcarIncumplida"
                    @definir="definirAcuerdo"
                />
            </div>

            <!-- ── HISTORIAL: tabla (cumplidas + incumplidas) ── -->
            <div v-else>
                <div
                    v-if="repositionsFiltradas.length === 0"
                    class="text-center py-20 bg-neutral-50 rounded-3xl border-2 border-dashed border-neutral-200"
                >
                    <Package2 class="w-12 h-12 mx-auto text-neutral-300 mb-4" />
                    <p class="text-neutral-500 font-medium text-sm">No hay reposiciones en el historial con esos criterios.</p>
                </div>
                <RepositionHistoryTable
                    v-else
                    :repositions="repositionsFiltradas"
                    @view="verDetalle"
                />
            </div>
        </div>

        <!-- ── MODAL: Detalle de reposición ──────────────────────────── -->
        <RepositionDetailModal
            :show="modalDetalle"
            :rep="repDetalle"
            @close="modalDetalle = false"
        />

        <!-- ── MODAL: Definir o completar acuerdo ──────────────────────── -->
        <Teleport to="body">
            <div
                v-if="modalAcuerdo"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-md p-6"
                @click.self="modalAcuerdo = false"
            >
                <div class="bg-white w-full max-w-lg rounded-4xl shadow-2xl overflow-hidden flex flex-col max-h-[88vh] animate-in zoom-in duration-300">

                    <!-- Header — igual al ReturnLoanModal -->
                    <div class="px-8 py-5 border-b border-neutral-100 flex justify-between items-center bg-white shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl" :class="acuerdoForm.estado === 'Cumplida' ? 'bg-green-100' : 'bg-amber-100'">
                                <CheckCircle v-if="acuerdoForm.estado === 'Cumplida'" class="w-5 h-5 text-green-600"/>
                                <Wrench v-else class="w-5 h-5 text-amber-600"/>
                            </div>
                            <div>
                                <h2 class="text-[15px] font-black text-neutral-900">
                                    {{ acuerdoForm.estado === 'Cumplida' ? 'Registrar cumplimiento' : 'ACUERDO DE REPOSICIÓN' }}
                                </h2>
                                <p v-if="repSeleccionada" class="text-xs text-neutral-400 font-medium mt-0.5">
                                    {{ repSeleccionada.tipo_origen }} · {{ repSeleccionada.borrower_nombre }}
                                </p>
                            </div>
                        </div>
                        <button @click="modalAcuerdo = false"
                            class="p-2 rounded-xl text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-all">
                            <XCircle class="w-5 h-5"/>
                        </button>
                    </div>

                    <!-- Cuerpo scrollable -->
                    <div class="p-7 pt-5 overflow-y-auto space-y-5 flex-1 bg-neutral-50/30">

                        <!-- Card del ítem — igual al paso 2 del ReturnLoanModal -->
                        <div v-if="repSeleccionada" class="p-4 bg-white rounded-2xl border-2 border-amber-200 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex flex-col gap-1">
                                    <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">
                                        {{ repSeleccionada.tipo_origen }} con problema
                                    </p>
                                    <p class="text-base font-black text-neutral-900">{{ repSeleccionada.nombre_origen }}</p>
                                    <p v-if="repSeleccionada.tipo_origen === 'Accesorio'" class="text-xs text-neutral-400 italic">
                                        Accesorio del equipo prestado
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-1.5 shrink-0">
                                    <span :class="['px-2.5 py-1 rounded-full text-[11px] font-black uppercase border', colorEstado(repSeleccionada.estado_dano)]">
                                        {{ repSeleccionada.estado_dano }}
                                    </span>
                                    <span class="text-[10px] text-neutral-400 italic">
                                        Se repone: <strong class="text-neutral-600">
                                            {{ repSeleccionada.tipo_origen === 'Accesorio' ? 'el accesorio' : 'el ítem' }}
                                        </strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de acuerdo — mismos botones del ReturnLoanModal -->
                        <div>
                            <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-3">Tipo de acuerdo</p>
                            <div class="grid grid-cols-3 gap-3">
                                <button
                                    v-for="tipo in tiposReposicion"
                                    :key="tipo.value"
                                    type="button"
                                    @click="acuerdoForm.tipo_reposicion = tipo.value"
                                    :class="[
                                        'flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all text-center',
                                        acuerdoForm.tipo_reposicion === tipo.value ? tipo.active : tipo.color + ' hover:opacity-80'
                                    ]"
                                >
                                    <component :is="tipo.icon" class="w-5 h-5"/>
                                    <span class="text-[12px] font-black uppercase">{{ tipo.label }}</span>
                                    <span class="text-[10px] leading-tight opacity-80">{{ tipo.desc }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Fecha límite + Observación — igual al paso 2 -->
                        <div v-if="acuerdoForm.tipo_reposicion && acuerdoForm.estado !== 'Cumplida'"
                            class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">
                                    Fecha límite <span class="text-neutral-300 font-normal normal-case">(opcional)</span>
                                </label>
                                <input type="date" v-model="acuerdoForm.fecha_limite"
                                    :min="new Date().toISOString().split('T')[0]"
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] bg-white"/>
                                <p class="text-[10px] text-neutral-400 mt-1">Sin fecha = acuerdo abierto</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">
                                    Observación
                                </label>
                                <textarea v-model="acuerdoForm.observacion" rows="3"
                                    placeholder="Condiciones del acuerdo..."
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] bg-white resize-none"/>
                            </div>
                        </div>

                        <!-- Fecha cumplimiento + Observación — si es Cumplida -->
                        <div v-if="acuerdoForm.estado === 'Cumplida'" class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">
                                    Fecha de cumplimiento
                                </label>
                                <input type="date" v-model="acuerdoForm.fecha_cumplimiento"
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] bg-white"/>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">
                                    Observación
                                </label>
                                <textarea v-model="acuerdoForm.observacion" rows="3"
                                    placeholder="Cómo se resolvió..."
                                    class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] bg-white resize-none"/>
                            </div>
                        </div>

                        <!-- Responsable -->
                        <div v-if="repSeleccionada"
                            class="flex items-center gap-2 p-3 bg-neutral-100 rounded-xl border border-neutral-200">
                            <User class="w-4 h-4 text-neutral-400 shrink-0"/>
                            <p class="text-xs text-neutral-600 font-medium">
                                Responsable: <strong>{{ repSeleccionada.borrower_nombre }}</strong>
                                · CI: {{ repSeleccionada.borrower_ci }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer — mismo estilo que ReturnLoanModal -->
                    <div class="px-7 py-4 bg-neutral-50 border-t border-neutral-100 flex gap-3 shrink-0">
                        <button
                            @click="modalAcuerdo = false"
                            class="py-3 px-5 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="guardarAcuerdo"
                            :disabled="acuerdoForm.processing || !acuerdoForm.tipo_reposicion"
                            :class="[
                                'flex-1 py-3 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2 shadow-lg active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed',
                                acuerdoForm.estado === 'Cumplida'
                                    ? 'bg-green-600 text-white hover:bg-green-700'
                                    : 'bg-amber-500 text-white hover:bg-amber-600'
                            ]"
                        >
                            <Loader2 v-if="acuerdoForm.processing" class="animate-spin w-4 h-4"/>
                            <CheckCircle v-else class="w-4 h-4"/>
                            {{ acuerdoForm.estado === 'Cumplida' ? 'Marcar Cumplida' : 'Guardar acuerdo' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
