<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { CardTitle } from '@/components/ui/card';
import { ArrowLeft,  Loader2, Save, Building2, Phone, MapPin, AlignLeft, PencilLine } from 'lucide-vue-next';

const props = defineProps<{
    // CAMBIO: De plural a singular y de Array a Object
    maintenanceCompany: {
        id: number;
        nombre_empresa: string;
        telefono: string;
        descripcion_empresa: string;
        direccion: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Empresas de Mantenimiento', href: maintenanceCompanyRoutes.index.url() },
    { title: 'Editar Empresa de Mantenimiento', href: maintenanceCompanyRoutes.edit.url(props.maintenanceCompany.id) },
];

const form = useForm({
    _method: 'put',
    nombre_empresa: props.maintenanceCompany.nombre_empresa,
    telefono: props.maintenanceCompany.telefono,
    direccion: props.maintenanceCompany.direccion,
    descripcion_empresa: props.maintenanceCompany.descripcion_empresa
});

function submit() {
    // Se usa post porque Laravel requiere _method: 'put' para procesar archivos
    form.post(maintenanceCompanyRoutes.update.url(props.maintenanceCompany.id), {
        forceFormData: true,
        preserveScroll: true,
    });
}

</script>

<template>
    <Head :title="'Editar ' + form.nombre_empresa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-4 w-full">
            <div class="mb-4">
                <Link :href="maintenanceCompanyRoutes.index.url()" class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group">
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/> Volver a empresas
                </Link>
            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 bg-[#1a3a5a] rounded-2xl shadow-lg shadow-blue-900/20">
                    <Building2 class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Editar {{ maintenanceCompany.nombre_empresa }}</h2>
                    <p class="text-neutral-500">Edite la información necesaria</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="bg-white p-6 rounded-3xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Información
                    </CardTitle>

                    <div class="grid gap-2">
                        <Label><Building2 class="w-5 h-5 text-[#1a3a5a]"/> Nombre de la Empresa</Label>
                        <Input id="nombre_empresa" v-model="form.nombre_empresa" />
                        <InputError :message="form.errors.nombre_empresa" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label><Phone class="w-5 h-5 text-[#1a3a5a]"/> Teléfono de Contacto</Label>
                            <Input id="telefono" v-model="form.telefono" />
                            <InputError :message="form.errors.telefono" />
                        </div>

                        <div class="grid gap-2">
                            <Label><MapPin class="w-5 h-5 text-[#1a3a5a]"/> Dirección / Ubicación</Label>
                            <Input id="direccion" v-model="form.direccion" />
                            <InputError :message="form.errors.direccion" />
                        </div>
                    </div>


                    <div class="grid gap-2">
                        <Label><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Descripción de Servicios</Label>
                        <Textarea id="descripcion_empresa" v-model="form.descripcion_empresa" />
                        <InputError :message="form.errors.descripcion_empresa" />
                    </div>
                </div>

                <div class="flex gap-4">
                    <Button class="flex-1 py-6 text-lg font-bold" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                        <Save v-else class="w-5 h-5 mr-2"/>
                        Guardar Cambios
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
