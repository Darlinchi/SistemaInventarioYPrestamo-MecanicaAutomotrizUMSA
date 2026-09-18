<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';
import { type BreadcrumbItem } from '@/types';
import { usePage, Head, router } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import CompanyTable from '@/components/CompanyTable.vue';

interface Company {
    id: number;
    nombre_empresa: string;
    telefono: string;
    descripcion_empresa: string;
    direccion: string;
    puede_eliminarse?: boolean;
}

const props = defineProps<{
    maintenanceCompanies: Company[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Empresas de Mantenimiento',
        href: maintenanceCompanyRoutes.index.url(),
    },
];

const page = usePage();

const can = (permission: string): boolean => {
    const auth = (page.props.auth as any) || {};
    const permissions: string[] = auth.permissions || auth.user?.permissions || [];
    return permissions.includes(permission);
};

const deleteCompany = (id: number): void => {
    if (!can('empresas_mant.eliminar')) return;

    if (confirm('¿Estás seguro de eliminar esta empresa?')) {
        router.delete(maintenanceCompanyRoutes.destroy.url(id), {
            preserveScroll: true,
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
                        v-if="can('empresas_mant.crear')"
                        :href="maintenanceCompanyRoutes.create.url()"
                        :label="`Registrar Empresa`"
                    />
                </template>
            </PageHeader>

            <CompanyTable
                :maintenanceCompanies="maintenanceCompanies"
                :can-edit="can('empresas_mant.editar')"
                :can-delete="can('empresas_mant.eliminar')"
                @delete="id => deleteCompany(id)"
            />
        </div>
    </AppLayout>
</template>