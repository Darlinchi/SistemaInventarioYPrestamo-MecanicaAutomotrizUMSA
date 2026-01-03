<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Package, Wrench, SquarePen, Ban } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    items: Array<{
        id: number;
        nombre_item: string;
        foto: string;
        descripcion_item: string;
        estado: string;
        equipment?: any; 
    }>;
}>();

const activeTab = ref<'equipos' | 'herramientas'>('equipos');

const filteredItems = computed(() => {
    if (activeTab.value === 'equipos') {
        return props.items.filter(item => item.equipment !== null);
    } else {
        return props.items.filter(item => item.equipment === null);
    }
});

const countEquipos = computed(() => props.items.filter(item => item.equipment !== null).length);
const countHerramientas = computed(() => props.items.filter(item => item.equipment === null).length);

const statusColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'disponible': return 'bg-green-100 text-green-800 border-green-200';
        case 'prestado': return 'bg-orange-100 text-orange-800 border-orange-200';
        case 'mantenimiento': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'dañado': return 'bg-red-100 text-red-800 border-red-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>
    <Head title="Inventario" />
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Inventario</h1>
                    <p class="text-sm text-neutral-500">Gestión de activos de la carrera</p>
                </div>
            </div>

            <div class="flex p-1 bg-neutral-100 rounded-xl w-fit mb-6 border border-neutral-200">
                <button @click="activeTab = 'equipos'" :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all', activeTab === 'equipos' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <Package class="w-4 h-4"/>Equipos ({{ countEquipos }})
                </button>
                <button @click="activeTab = 'herramientas'" :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all', activeTab === 'herramientas' ? 'bg-white text-black shadow-sm' : 'text-neutral-500 hover:text-black']">
                    <Wrench class="w-4 h-4"/>Herramientas ({{ countHerramientas }})
                </button>
            </div>

            <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-max border-separate border-spacing-0">
                        <thead class="bg-neutral-200 border-b border-neutral-300 text-xs font-bold uppercase tracking-widest text-neutral-800">
                            <tr>
                                <th v-if="activeTab === 'equipos'" class="p-4">Código QR</th>
                                <th class="p-4">Nombre</th>
                                <th class="p-4">Foto</th>
                                <th class="p-4">Descripción</th>
                                <th class="p-4">Estado</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Ubicación</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Color</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Marca</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Modelo</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Serie</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Rubro</th>
                                <th v-if="activeTab === 'equipos'" class="p-4 whitespace-nowrap">F. Adquisición</th>
                                <th v-if="activeTab === 'equipos'" class="p-4">Observación</th>
                                <th class="p-4 text-right sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            <tr v-for="item in filteredItems" :key="item.id" class="hover:bg-neutral-50 transition-colors group">
                                <td v-if="activeTab === 'equipos'" class="p-4 font-mono text-blue-600">{{ item.equipment?.codigo_qr }}</td>
                                <td class="p-4 font-bold text-black">{{ item.nombre_item }}</td>
                                <td class="p-4 text-neutral-400 italic text-xs">{{ item.foto || 'Sin foto' }}</td>
                                <td class="p-4 text-neutral-500 max-w-xs truncate">{{ item.descripcion_item }}</td>
                                <td class="p-4">
                                    <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border', statusColor(item.estado)]">
                                        {{ item.estado }}
                                    </span>
                                </td>
                                <td v-if="activeTab === 'equipos'" class="p-4 text-neutral-600">{{ item.equipment?.ubicacion }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 text-neutral-600">{{ item.equipment?.color }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 font-semibold">{{ item.equipment?.marca }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ item.equipment?.modelo }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 font-mono">{{ item.equipment?.serie }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ item.equipment?.rubro }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4">{{ item.equipment?.fecha_adquisicion }}</td>
                                <td v-if="activeTab === 'equipos'" class="p-4 italic text-neutral-400 max-w-xs truncate">{{ item.equipment?.observacion_equipo }}</td>
                                
                                <td class="p-4 text-right space-x-3 sticky right-0 bg-white group-hover:bg-neutral-50 border-l border-neutral-100">
                                    <button class="text-[10px] font-bold uppercase text-neutral-400 hover:text-black"><SquarePen class="w-4 h-4 stroke-black"/></button>
                                    <button class="text-[10px] font-bold uppercase text-black hover:underline"><Ban class="w-4 h-4 stroke-red-500"/></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
