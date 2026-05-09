<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import StatusBadge from '@/components/shared/StatusBadge.vue';
import { XIcon, AlertTriangle, Wrench, RefreshCw, UserCheck, CheckCircle } from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    // ítems con problema del préstamo recién devuelto
    itemsConProblema: Array<{
        // Para equipos/herramientas:
        return_detail_id?: number;
        // Para accesorios:
        return_detail_accessory_id?: number;
        nombre: string;
        tipo: string; // 'Equipo' | 'Herramienta' | 'Accesorio'
        estado: string;
    }>;
    borrowerId: number;
    borrowerNombre: string;
}>();

const emit = defineEmits(['close', 'created']);

// Form para cada ítem con problema — uno por uno
const selectedItem = ref<any>(null);

const form = useForm<{
    originable_id:   number;
    originable_type: 'return_detail' | 'return_detail_accessory';
    borrower_id:     number;
    tipo_reposicion: 'Reparacion' | 'Reemplazo' | 'Desbloqueo' | null;
    fecha_limite:    string;
    observacion:     string;
}>({
    originable_id:   0,
    originable_type: 'return_detail',
    borrower_id:     props.borrowerId,
    tipo_reposicion: null,
    fecha_limite:    '',
    observacion:     '',
});

const seleccionarItem = (item: any) => {
    selectedItem.value = item;
    form.originable_id   = item.return_detail_id ?? item.return_detail_accessory_id;
    form.originable_type = item.return_detail_id ? 'return_detail' : 'return_detail_accessory';
    form.borrower_id     = props.borrowerId;
    form.tipo_reposicion = null;
    form.fecha_limite    = '';
    form.observacion     = '';
};

const tiposReposicion = [
    {
        value: 'Reparacion',
        label: 'Reparación',
        desc: 'El ítem se debe reparar',
        icon: Wrench,
        color: 'border-purple-200 bg-purple-50 text-purple-700',
        activeColor: 'border-purple-500 bg-purple-100 ring-2 ring-purple-300',
    },
    {
        value: 'Reemplazo',
        label: 'Reemplazo',
        desc: 'El responsable entrega un ítem equivalente',
        icon: RefreshCw,
        color: 'border-blue-200 bg-blue-50 text-blue-700',
        activeColor: 'border-blue-500 bg-blue-100 ring-2 ring-blue-300',
    },
    {
        value: 'Desbloqueo',
        label: 'Desbloqueo',
        desc: 'Acuerdo administrativo',
        icon: UserCheck,
        color: 'border-amber-200 bg-amber-50 text-amber-700',
        activeColor: 'border-amber-500 bg-amber-100 ring-2 ring-amber-300',
    },
];

const puedeEnviar = computed(() =>
    selectedItem.value && form.tipo_reposicion !== null
);

const enviar = () => {
    form.post('/dashboard/repositions', {
        preserveScroll: true,
        onSuccess: () => {
            selectedItem.value = null;
            form.reset();
            emit('created');
        },
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-200 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md" @click="$emit('close')"></div>

        <div class="relative bg-white rounded-4xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col animate-in fade-in zoom-in duration-300">

            <!-- Header -->
            <div class="px-7 py-5 border-b border-neutral-100 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-50 rounded-xl border border-amber-100">
                        <AlertTriangle class="w-5 h-5 text-amber-600" />
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-neutral-800 uppercase tracking-tighter leading-none">
                            Crear Acuerdo de Reposición
                        </h2>
                        <p class="text-neutral-500 text-sm mt-0.5">
                            Responsable: <span class="font-bold text-neutral-700">{{ borrowerNombre }}</span>
                        </p>
                    </div>
                </div>
                <button @click="$emit('close')" class="p-2 hover:bg-neutral-100 rounded-full transition-colors group">
                    <XIcon class="w-6 h-6 text-neutral-300 group-hover:text-red-500 transition-colors" />
                </button>
            </div>

            <div class="overflow-y-auto flex-1 p-7 space-y-6">

                <!-- PASO 1: Seleccionar ítem con problema -->
                <div>
                    <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-3">
                        1. Selecciona el ítem a reponer
                    </p>
                    <div class="space-y-2">
                        <button
                            v-for="item in itemsConProblema"
                            :key="item.return_detail_id ?? item.return_detail_accessory_id"
                            @click="seleccionarItem(item)"
                            :class="[
                                'w-full flex items-center justify-between px-4 py-3 rounded-xl border-2 text-left transition-all',
                                selectedItem?.return_detail_id === item.return_detail_id &&
                                selectedItem?.return_detail_accessory_id === item.return_detail_accessory_id
                                    ? 'border-[#1a3a5a] bg-[#1a3a5a]/5 ring-2 ring-[#1a3a5a]/20'
                                    : 'border-neutral-200 hover:border-neutral-300 bg-white'
                            ]"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-bold text-neutral-800">{{ item.nombre }}</span>
                                    <span class="text-[11px] font-black uppercase text-neutral-400">{{ item.tipo }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <StatusBadge :status="item.estado" />
                                <div v-if="selectedItem?.return_detail_id === item.return_detail_id &&
                                    selectedItem?.return_detail_accessory_id === item.return_detail_accessory_id"
                                    class="w-5 h-5 rounded-full bg-[#1a3a5a] flex items-center justify-center shrink-0">
                                    <CheckCircle class="w-3 h-3 text-white" />
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- PASO 2: Tipo de reposición -->
                <div v-if="selectedItem">
                    <p class="text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-3">
                        2. Tipo de acuerdo
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            v-for="tipo in tiposReposicion"
                            :key="tipo.value"
                            @click="form.tipo_reposicion = tipo.value as any"
                            :class="[
                                'flex flex-col items-center gap-2 p-3 rounded-xl border-2 transition-all text-center',
                                form.tipo_reposicion === tipo.value ? tipo.activeColor : tipo.color + ' hover:opacity-80'
                            ]"
                        >
                            <component :is="tipo.icon" class="w-5 h-5" />
                            <span class="text-[12px] font-black uppercase">{{ tipo.label }}</span>
                            <span class="text-[10px] font-medium leading-tight">{{ tipo.desc }}</span>
                        </button>
                    </div>
                </div>

                <!-- PASO 3: Fecha límite + Observación -->
                <div v-if="form.tipo_reposicion" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">
                            Fecha límite (opcional)
                        </label>
                        <input
                            type="date"
                            v-model="form.fecha_limite"
                            :min="new Date().toISOString().split('T')[0]"
                            class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] focus:ring-2 focus:ring-[#1a3a5a]/20 bg-white"
                        />
                        <p class="text-[10px] text-neutral-400 mt-1">Sin fecha = acuerdo abierto</p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-neutral-500 uppercase tracking-widest mb-1.5">
                            Observación
                        </label>
                        <textarea
                            v-model="form.observacion"
                            rows="3"
                            placeholder="Condiciones del acuerdo..."
                            class="w-full rounded-xl border border-neutral-200 px-3 py-2 text-sm focus:outline-none focus:border-[#1a3a5a] focus:ring-2 focus:ring-[#1a3a5a]/20 bg-white resize-none"
                        />
                    </div>
                </div>

                <!-- Error -->
                <p v-if="form.errors.originable_id" class="text-red-600 text-xs">{{ form.errors.originable_id }}</p>
            </div>

            <!-- Footer -->
            <div class="px-7 py-4 bg-neutral-50 border-t border-neutral-100 flex gap-3 shrink-0">
                <button
                    @click="$emit('close')"
                    class="flex-1 py-3 bg-white border border-neutral-200 text-neutral-600 rounded-2xl font-bold text-sm hover:bg-neutral-100 transition-all"
                >
                    Cancelar
                </button>
                <button
                    @click="enviar"
                    :disabled="!puedeEnviar || form.processing"
                    class="flex-1 py-3 bg-[#1a3a5a] text-white rounded-2xl font-bold text-sm hover:bg-[#122a42] transition-all disabled:opacity-40 disabled:cursor-not-allowed active:scale-95 flex items-center justify-center gap-2"
                >
                    <CheckCircle class="w-4 h-4" />
                    Registrar Acuerdo
                </button>
            </div>
        </div>
    </div>
</template>
