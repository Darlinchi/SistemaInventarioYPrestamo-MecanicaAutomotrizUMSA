<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import SearchInput from '@/components/shared/SearchInput.vue';
import { ref, computed } from 'vue';
import maintenancesRoutes from '@/routes/maintenances';
import { ArrowLeft, Wrench, Building2, Cog, Loader2, Save, ClipboardPen, Check, Package,
        Calendar, Clock, CalendarClock, ClockAlert, CalendarCheck2, Image
} from 'lucide-vue-next';

// Props: Recibimos los equipos (Epson, Osciloscopios, etc.) y las empresas registradas
const props = defineProps<{
    equipment: Array<any>;
    companies: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mantenimientos', href: maintenancesRoutes.index.url() },
    { title: 'Registrar Mantenimiento', href: maintenancesRoutes.create.url() },
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
    tipo_mantenimiento: 'Preventivo',
    fecha_mantenimiento: today,
    hora_inicio: now,
    hora_fin_estimado: '',
    fecha_retorno_estimado: '',
});

const filteredItems = computed(() => {
    const search = searchTerm.value.toLowerCase();
    return props.equipment.filter(unit => {
        // Filtro de disponibilidad
        const isAvailable = unit.estado_equipo === 'Disponible' || form.equipment_id === unit.id;
        if (!isAvailable) return false;

        // CORRECCIÓN AQUÍ: Quitamos el ".item"
        // Asegúrate de usar el nombre de la columna real (ej. nombre_equipo)
        const nameMatch = unit.nombre_equipo ? unit.nombre_equipo.toLowerCase().includes(search) : false;
        const serieMatch = unit.serie ? unit.serie.toLowerCase().includes(search) : false;
        const codeMatch = unit.codigo_qr ? unit.codigo_qr.toLowerCase().includes(search) : false;

        return nameMatch || serieMatch || codeMatch;
    });
});

// Función para seleccionar un solo equipo
const toggleItemSelection = (id: number) => {
    form.equipment_id = form.equipment_id === id ? null : id;
};

const submit = () => {
    if (!form.equipment_id) {
        alert("Por favor, seleccione un equipo antes de continuar.");
        return;
    }
    // Forzamos el procesamiento para que el botón muestre el estado de carga
    form.post(maintenancesRoutes.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            // Si usas flash messages, Inertia los manejará
            form.reset();
        },
        onError: (errors) => {
            console.error("Errores del servidor:", errors);
        }
    });
};

</script>

<template>
    <Head title="Registrar Mantenimiento" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-5xl mx-auto p-4 w-full">
            <div class="mb-6">
                <Link :href="maintenancesRoutes.index.url()" class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group">
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/> Volver a mantenimientos
                </Link>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-neutral-50 p-6 rounded-3xl border border-neutral-200 space-y-2 shadow-sm">
                        <h3 class="font-bold text-lg border-b pb-2 flex items-center">
                            <ClipboardPen class="w-5 h-5 mr-2 text-[#1a3a5a]"/> Información
                        </h3>

                        <div class="grid gap-2">
                            <Label for="borrower_id" class="text-[13px] font-black uppercase text-neutral-800 tracking-wider flex items-center gap-1">
                                <Building2 class="w-4 h-4 text-neutral-700" /> Empresa Encargada
                            </Label>
                            <select
                                v-model="form.maintenance_company_id"
                                :class="['flex h-10 w-full rounded-md border bg-white px-3 py-2 text-sm',
                                        form.errors.maintenance_company_id ? 'border-red-500' : 'border-input']"
                            >
                                <option :value="null" disabled>Seleccionar empresa</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.nombre_empresa }}
                                </option>
                            </select>
                            <InputError :message="form.errors.maintenance_company_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="borrower_id" class="text-[13px] font-black uppercase text-neutral-800 tracking-wider flex items-center gap-1">
                                <Wrench class="w-4 h-4 text-neutral-700" /> Tipo de Mantenimiento
                            </Label>
                            <select v-model="form.tipo_mantenimiento"
                                    :class="['flex h-10 w-full rounded-md border bg-white px-3 py-2 text-sm',
                                        form.errors.tipo_mantenimiento ? 'border-red-500' : 'border-input']">
                                <option :value="null" disabled>Seleccionar tipo</option>
                                <option value="Preventivo">Preventivo</option>
                                <option value="Correctivo">Correctivo</option>
                            </select>
                            <InputError :message="form.errors.tipo_mantenimiento" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 ">
                            <div class="space-y-2">
                                <Label for="fecha_mantenimiento" class="flex items-center gap-1 text-[12px] font-black uppercase text-blue-700 tracking-wider">
                                    <Calendar class="w-4 h-4" />
                                    <span>Fecha Salida</span>
                                </Label>
                                <Input v-model="form.fecha_mantenimiento" type="date" readonly />
                                <InputError :message="form.errors.fecha_mantenimiento" />
                            </div>
                            <div class="space-y-2">
                                <Label for="hora_inicio" class="flex items-center gap-1.5 text-[12px] font-black uppercase text-blue-700 tracking-wider">
                                    <Clock class="w-4 h-4" />
                                    <span>Hora Inicio</span>
                                </Label>
                                <Input v-model="form.hora_inicio" type="time" readonly />
                                <InputError :message="form.errors.hora_inicio" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="fecha_retorno_estimado" class="flex items-center gap-1 text-[12px] font-black uppercase text-orange-700 tracking-wider">
                                    <CalendarCheck2 class="w-4 h-4" />
                                    <span>F. Retorno</span>
                                </Label>
                                <Input v-model="form.fecha_retorno_estimado" type="date" class="w-fit min-w-[120px] px-1"/>
                                <InputError :message="form.errors.fecha_retorno_estimado" />
                            </div>
                            <div class="space-y-2">
                                <Label for="hora_fin_estimado" class="flex items-center gap-1.5 text-[12px] font-black uppercase text-orange-700 tracking-wider">
                                    <ClockAlert class="w-4 h-4" />
                                    <span>H. Retorno</span>
                                </Label>
                                <Input v-model="form.hora_fin_estimado" type="time"/>
                                <InputError :message="form.errors.hora_fin_estimado" />
                            </div>
                        </div>
                    </div>

                    <Button type="submit"
                    class="w-full py-7 rounded-2xl text-lg font-bold shadow-lg shadow-neutral-200" :disabled="form.processing">
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-6 w-6 animate-spin" /> Registrando...
                        </template>
                        <template v-else><Save class="mr-2 h-5 w-5" />
                            Registrar Mantenimiento
                        </template>
                    </Button>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-neutral-200 shadow-sm min-h-[500px] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-lg flex items-center">
                                <Cog class="w-5 h-5 mr-2 text-[#1a3a5a]"/> Seleccionar Equipo para Mantenimiento
                            </h3>
                        </div>

                        <div class="flex flex-col md:flex-row items-center gap-3 mb-6 w-full">
                            <SearchInput v-model="searchTerm" placeholder="Buscar por nombre, código QR o número de serie..." class="flex-1" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
                            <div
                                v-for="unit in filteredItems" :key="unit.id"
                                @click="toggleItemSelection(unit.id)"
                                :class="['p-3 border rounded-xl cursor-pointer transition-all flex items-center gap-3',
                                    form.equipment_id === unit.id ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-neutral-200 hover:border-neutral-400']"
                            >
                            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex items-center justify-center overflow-hidden border border-neutral-100 shadow-inner">
                                    <img
                                        v-if="unit.foto_equipo || unit.foto_herramienta || unit.foto"
                                        :src="'/storage/' + (unit.foto_equipo || unit.foto)"
                                        class="object-cover w-full h-full"
                                        alt="Foto del item"
                                    />
                                    <Image v-else class="w-6 h-6 text-neutral-300" />
                                </div>

                                <div class="flex-1">
                                    <p class="text-sm font-bold text-black leading-tight">
                                        {{ unit.nombre_equipo }}
                                    </p>
                                    <p class="text-[12px] font-mono text-blue-600 font-bold uppercase">Cód: {{ unit.codigo_qr }}</p>
                                </div>

                                <div v-if="form.equipment_id === unit.id" class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shrink-0">
                                    <Check class="w-3 h-3 text-white" />
                                </div>
                            </div>
                        </div>
                        <InputError :message="form.errors.equipment_id" class="mt-2" />
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
