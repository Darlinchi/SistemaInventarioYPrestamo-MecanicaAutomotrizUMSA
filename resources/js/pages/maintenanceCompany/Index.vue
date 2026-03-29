<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';
import { type BreadcrumbItem } from '@/types';
import { Plus, SquarePen, Trash } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import CompanyTable from '@/components/CompanyTable.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Empresas de Mantenimiento',
        href: maintenanceCompanyRoutes.index.url()
    },
];

// TABLA DE EMPRESAS
const props = defineProps<{
    maintenanceCompanies: Array<{
        id: number;
        nombre_empresa: string;
        telefono: string;
        descripcion_empresa: string;
        direccion: string;
    }>;
}>();

// Función para eliminar con confirmación
const deleteCompany = (id: number) => {
    if (confirm('¿Estás seguro de eliminar esta empresa?')) {
        // Usamos la ruta de Wayfinder directamente
        router.delete(maintenanceCompanyRoutes.destroy.url(id), {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Eliminado con éxito");
            },
            onError: (errors) => {
                console.error("Error al eliminar:", errors);
            }
        });
    }
};

</script>

<template>
    <Head title="Empresas de Mantenimiento" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <PageHeader
                title="Empresas de Mantenimiento"
                description="Información sobre las empresas que realizan mantenimiento de equipos y herramientas del taller"
            >
                <template #action>
                    <CreateActionButton
                        type="button" :href="maintenanceCompanyRoutes.create.url()"
                        :label="`Registrar Empresa`"
                    />
                </template>
            </PageHeader>

            <!--<div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Empresas de Mantenimiento</h1>
                    <p class="text-sm text-neutral-500">Información sobre las empresas que realizan mantenimiento de equipos y herramientas del taller</p>
                </div>
                <Link :href="maintenanceCompanyRoutes.create.url()" class="bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-neutral-800 transition shadow-lg">
                    <Plus class="w-5 h-5"/> Nueva Empresa
                </Link>
            </div>-->

            <!--<div class="relative bg-white border border-neutral-200 rounded-xl shadow-sm overflow-x-auto">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-max border-separate border-spacing-0">
                        <thead class="bg-neutral-200 border-b border-neutral-300 text-xs font-bold uppercase tracking-widest text-neutral-800">
                            <tr>
                                <th class="p-4">Nombre</th>
                                <th class="p-4">Contacto</th>
                                <th class="p-4">Ubicación/Dirección</th>
                                <th class="p-4">Descripción</th>
                                <th class="p-4 text-right sticky right-0 bg-neutral-200 border-l border-neutral-300 shadow-l">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 text-sm">
                            <tr v-for="company in maintenanceCompanies" :key="company.id" class="hover:bg-neutral-50 transition-colors group">
                                <td class="p-4 font-bold text-black">{{ company.nombre_empresa }}</td>
                                <td class="p-4 text-neutral-900">{{ company.telefono || 'Sin teléfono' }} </td>
                                <td class="p-4 text-neutral-900">{{ company.direccion }} </td>
                                <td class="p-4 text-neutral-500 max-w-xs truncate">{{ company.descripcion_empresa }}</td>

                                <td class="p-4 text-right space-x-3 sticky right-0 bg-white group-hover:bg-neutral-50 border-l border-neutral-100">
                                    <Link :href="maintenanceCompanyRoutes.edit.url(company.id)">
                                        <Button class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-blue-500"><SquarePen class="w-4.5 h-4.5 stroke-blue-500"/></Button>
                                    </Link>

                                    <Button
                                        type="button"
                                        @click.prevent="deleteCompany(company.id)"
                                        class="p-2 bg-white border border-neutral-200 rounded-lg hover:bg-red-50 group shadow-sm transition text-red-500"
                                    >
                                        <Trash class="w-4.5 h-4.5 stroke-red-500"/>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>-->

            <CompanyTable
                :maintenanceCompanies="maintenanceCompanies"
                @delete="id => deleteCompany(id)"
            />
        </div>
    </AppLayout>
</template>
