<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import itemRoutes from '@/routes/items';
import equipmentRoutes from '@/routes/equipments'; // Asegúrate que el nombre coincida con tus archivos de rutas
import toolRoutes from '@/routes/tools';
import loanRoutes from '@/routes/loans';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    Package, User,
    ClipboardCheck,
    Wrench,
    LayoutDashboard,
    History,
    ChevronRight,
    Search
} from 'lucide-vue-next';

// Recibimos los datos del controlador
const props = defineProps<{
    stats: {
        equipos_total: number;
        prestamos_activos: number;
        mantenimientos_pendientes: number;
    };
    recentLoans: any[];
    recentEquipments: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Panel Principal',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6 bg-neutral-50/40 min-h-screen">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Resumen del Sistema</h1>
                    <p class="text-neutral-500 font-medium">Gestión de inventarios y control de préstamos</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="equipmentRoutes.create.url()" class="bg-black text-white px-5 py-2.5 rounded-2xl font-bold text-sm hover:bg-neutral-800 transition shadow-lg shadow-black/10 flex items-center gap-2">
                        <ClipboardCheck class="w-4 h-4" /> Nuevo Préstamo
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div class="bg-white p-6 rounded-[2.5rem] border border-neutral-200/60 shadow-sm flex items-center justify-between group hover:shadow-xl hover:shadow-blue-500/5 transition-all">
                    <div>
                        <p class="text-[11px] font-black text-neutral-400 uppercase tracking-widest mb-1">Equipos en Inventario</p>
                        <h3 class="text-4xl font-black text-neutral-800">{{ stats.equipos_total }}</h3>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-2xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <Package class="w-8 h-8" />
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2.5rem] border border-neutral-200/60 shadow-sm flex items-center justify-between group hover:shadow-xl hover:shadow-green-500/5 transition-all">
                    <div>
                        <p class="text-[11px] font-black text-neutral-400 uppercase tracking-widest mb-1">Préstamos Activos</p>
                        <h3 class="text-4xl font-black text-neutral-800">{{ stats.prestamos_activos }}</h3>
                    </div>
                    <div class="p-4 bg-green-50 rounded-2xl text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <ClipboardCheck class="w-8 h-8" />
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2.5rem] border border-neutral-200/60 shadow-sm flex items-center justify-between group hover:shadow-xl hover:shadow-orange-500/5 transition-all">
                    <div>
                        <p class="text-[11px] font-black text-neutral-400 uppercase tracking-widest mb-1">En Mantenimiento</p>
                        <h3 class="text-4xl font-black text-neutral-800">{{ stats.mantenimientos_pendientes }}</h3>
                    </div>
                    <div class="p-4 bg-orange-50 rounded-2xl text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <Wrench class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-neutral-200/60 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-neutral-100 flex justify-between items-center">
                    <h2 class="text-lg font-black text-neutral-800 uppercase tracking-tight flex items-center gap-2">
                        <History class="w-5 h-5 text-blue-600" /> Últimos Préstamos
                    </h2>
                    <Link :href="loanRoutes.index.url()" class="text-xs font-black text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-full transition uppercase tracking-wider">
                        Ver historial completo
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-neutral-50/50 text-[10px] font-black text-neutral-400 uppercase tracking-[0.2em]">
                                <th class="p-4 pl-8">Solicitante</th>
                                <th class="p-4">Materia / Unidad</th>
                                <th class="p-4">Fecha Salida</th>
                                <th class="p-4 text-center">Estado</th>
                                <th class="p-4 pr-8 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            <tr v-for="loan in recentLoans" :key="loan.id" class="group hover:bg-neutral-50/50 transition-colors">
                                <td class="p-4 pl-8">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-neutral-100 flex items-center justify-center text-[10px] font-bold text-neutral-500 uppercase">
                                            <template v-if="loan.borrower?.nombresP">
                                                {{ loan.borrower.apellidosP}} {{ loan.borrower.nombresP}}
                                            </template>
                                            <User v-else class="w-4 h-4 text-neutral-300" />
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-sm text-neutral-600 font-medium">
                                    {{ loan.subject?.nombre_materia || 'Uso General' }}
                                </td>
                                <td class="p-4 text-sm text-neutral-600">
                                    {{ loan.fecha_salida }}
                                </td>
                                <td class="p-4 text-center">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter',
                                        loan.estado_prestamo === 'En Curso' ? 'bg-green-100 text-green-700' : 'bg-neutral-100 text-neutral-500'
                                    ]">
                                        {{ loan.estado_prestamo }}
                                    </span>
                                </td>
                                <td class="p-4 pr-8 text-right">
                                    <Link :href="loanRoutes.index.url()" class="text-neutral-300 group-hover:text-blue-600 transition-colors">
                                        <ChevronRight class="w-5 h-5 inline" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between px-2">
                    <h2 class="text-lg font-black text-neutral-800 uppercase tracking-tight flex items-center gap-2">
                        <Package class="w-5 h-5 text-blue-600" /> Equipos Agregados Recientemente
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="equipo in recentEquipments" :key="equipo.id"
                         class="bg-white rounded-4xl border border-neutral-200/60 p-4 shadow-sm hover:shadow-xl transition-all group overflow-hidden">

                        <div class="aspect-square rounded-3xl bg-neutral-100 mb-4 overflow-hidden relative">
                            <img v-if="equipo.foto" :src="'/storage/' + equipo.foto"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                            <div v-else class="w-full h-full flex items-center justify-center bg-neutral-50">
                                <Package class="w-12 h-12 text-neutral-200" />
                            </div>

                            <div class="absolute top-3 right-3">
                                <span :class="[
                                    'px-3 py-1 rounded-xl text-[10px] font-black uppercase shadow-lg',
                                    equipo.estado_equipo === 'Disponible' ? 'bg-green-500 text-white' : 'bg-orange-500 text-white'
                                ]">
                                    {{ equipo.estado_equipo }}
                                </span>
                            </div>
                        </div>

                        <div class="px-2">
                            <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.15em] mb-1">{{ equipo.marca || 'S/M' }}</p>
                            <h3 class="font-bold text-neutral-900 truncate text-base leading-tight">{{ equipo.nombre_equipo }}</h3>
                            <p class="text-xs text-neutral-400 font-medium mt-1">S/N: {{ equipo.serie || 'No registrado' }}</p>
                        </div>

                        <Link :href="itemRoutes.index.url()" class="mt-5 w-full py-3 bg-neutral-50 group-hover:bg-black group-hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                            Ver Detalles
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
