<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import maintenancesRoutes from '@/routes/maintenances';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { ref, computed } from 'vue';
import { ArrowLeft, Wrench, Building2, Search, AlignLeft, Loader2, Save, ClipboardPen, Check, Package} from 'lucide-vue-next';

// Props: Recibimos los equipos (Epson, Osciloscopios, etc.) y las empresas registradas
const props = defineProps<{
    equipment: Array<any>;
    companies: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Registrar Nuevo',
        href: maintenancesRoutes.create.url(),
    },
];

// Lógica de fecha y hora local que ya tenías (¡Excelente!)
const date = new Date();
const year = date.getFullYear();
const month = String(date.getMonth() + 1).padStart(2, '0');
const day = String(date.getDate()).padStart(2, '0');
const today = `${year}-${month}-${day}`;
const now = `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;

const searchTerm = ref('');

// Formulario vinculado a tu esquema de Base de Datos
const form = useForm({
    equipment_id: null as number | null, // Cambia '' por null
    maintenance_company_id: null as number | null,
    fecha_mantenimiento: today,
    hora_inicio: now,
});

const filteredItems = computed(() => {
    const search = searchTerm.value.toLowerCase();
    return props.equipment.filter(unit => {
        // 1. Buscamos en el nombre (viene de la relación item)
        const nameMatch = unit.item.nombre_item.toLowerCase().includes(search);
        // 2. Buscamos en el número de serie (viene de equipment)
        const serieMatch = unit.serie ? unit.serie.toLowerCase().includes(search) : false;
        // 3. Buscamos el código directamente en equipment
        // Ajusta 'codigo' al nombre exacto de tu columna en la tabla equipment
        const codeMatch = unit.codigo_qr ? unit.codigo_qr.toLowerCase().includes(search) : false;

        return nameMatch || serieMatch || codeMatch;
    });
});

// Función para seleccionar un solo equipo
const toggleItemSelection = (id: number) => {
    // Si haces clic en el mismo, se deselecciona; si no, marca el nuevo
    form.equipment_id = form.equipment_id === id ? null : id;
};

const submit = () => {
    form.post(maintenancesRoutes.store.url(), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Registrar Mantenimiento" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-6 w-full">
            <div class="mb-6">
                <Link :href="maintenancesRoutes.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black font-medium transition">
                    <ArrowLeft class="w-4 h-4 mr-1"/> Volver a mantenimientos
                </Link>
                <h1 class="text-2xl font-black uppercase tracking-tight mt-2">Registrar Mantenimiento Técnico</h1>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                        <h3 class="font-bold text-lg border-b pb-2 flex items-center">
                            <ClipboardPen class="w-5 h-5 mr-2 text-blue-500"/> Información
                        </h3>

                        <div class="grid gap-2">
                            <Label>
                                <Building2 class="w-3.5 h-3.5 text-green-500"/> Empresa Encargada
                            </Label>
                            <select v-model="form.maintenance_company_id" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm disabled:bg-neutral-50">
                                <option :value="null" disabled>Seleccionar empresa</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.nombre_empresa }}
                                </option>
                            </select>
                            <InputError :message="form.errors.maintenance_company_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="fecha_mantenimiento">Fecha de Mantenimiento</Label>
                            <Input id="fecha_mantenimiento" v-model="form.fecha_mantenimiento" type="date":max="today"/>
                            <InputError :message="form.errors.fecha_mantenimiento" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="hora_inicio">Hora de Inicio</Label>
                            <Input id="hora_inicio" v-model="form.hora_inicio"  type="time":max="now" />
                            <InputError :message="form.errors.hora_inicio" />
                        </div>

                        <!--
                        <div class="grid gap-2">
                            <Label>
                                <AlignLeft class="w-3.5 h-3.5 text-orange-500"/> Detalle de la Actividad
                            </Label>
                            <Textarea
                                v-model="form.actividad"
                                placeholder="Escriba las reparaciones o revisiones realizadas..."
                                class="min-h-[120px] rounded-2xl resize-none"
                            />
                            <InputError :message="form.errors.actividad" />
                        </div>
                        -->
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm flex-1">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex items-center">
                                <Wrench class="w-5 h-5 mr-2 text-orange-500"/> Seleccionar Equipo para Mantenimiento
                            </h3>
                        </div>

                        <div class="relative mb-4">
                            <Search class="absolute left-3 top-3 w-4 h-4 text-neutral-400" />
                            <input v-model="searchTerm" type="text" placeholder="Buscar por nombre, código QR o número de serie..."
                                class="pl-10 flex h-10 w-full rounded-md border border-input bg-neutral-50 px-3 py-2 text-sm shadow-sm transition-colors focus:bg-white" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                            <div
                                v-for="unit in filteredItems" :key="unit.id"
                                @click="toggleItemSelection(unit.id)"
                                :class="['p-3 border rounded-xl cursor-pointer transition-all flex items-center gap-3',
                                    form.equipment_id === unit.id ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-neutral-200 hover:border-neutral-400']"
                            >
                                <div class="w-10 h-10 rounded-lg bg-neutral-100 flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="unit.item.foto" :src="'/storage/' + unit.item.foto" class="object-cover w-full h-full" />
                                    <Package v-else class="w-5 h-5 text-neutral-400" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-black truncate">{{ unit.item.nombre_item }}</p>
                                    <p class="text-[10px] text-neutral-500 uppercase">Serie: {{ unit.serie || 'S/N' }}</p>
                                    <p class="text-[9px] font-mono text-blue-600 font-bold uppercase">Cód: {{ unit.codigo_qr }}</p>
                                </div>

                                <div v-if="form.equipment_id === unit.id" class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shrink-0">
                                    <Check class="w-3 h-3 text-white" />
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.equipment_id" class="mt-2" />
                    </div>

                    <Button type="submit" class="w-full py-7 rounded-2xl text-lg font-bold shadow-lg shadow-neutral-200" :disabled="form.processing">
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-6 w-6 animate-spin" /> Registrando...
                        </template>
                        <template v-else><Save class="mr-2 h-5 w-5" />
                            Registrar Mantenimiento
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
