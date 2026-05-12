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
                description="Información sobre las empresas que realizan mantenimiento de equipos y herramientas del taller"
            >
                <template #action>
                    <CreateActionButton
                        type="button" :href="maintenanceCompanyRoutes.create.url()"
                        :label="`Registrar Empresa`"
                    />
                </template>
            </PageHeader>

            <CompanyTable
                :maintenanceCompanies="maintenanceCompanies"
                @delete="id => deleteCompany(id)"
            />
        </div>
    </AppLayout>
</template>
