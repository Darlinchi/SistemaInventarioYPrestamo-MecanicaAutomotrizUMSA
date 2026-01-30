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
import { ArrowLeft,  Loader2, Save, Building2, Phone, MapPin, AlignLeft } from 'lucide-vue-next';

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Editar Empresa de Mantenimiento',
        href: maintenanceCompanyRoutes.edit.url(props.maintenanceCompany.id),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-6 w-full">
            <div class="mb-4">
                <Link :href="maintenanceCompanyRoutes.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-4 h-4 mr-1"/> Volver a Empresas
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="bg-white p-8 rounded-3xl border border-neutral-200 shadow-sm space-y-6">
                    <h3 class="font-bold text-lg border-b pb-2">Editar Información</h3>

                    <div class="grid gap-2">
                        <Label><Building2 class="w-4 h-4 text-blue-500"/> Nombre de la Empresa</Label>
                        <Input id="nombre_empresa" v-model="form.nombre_empresa" />
                        <InputError :message="form.errors.nombre_empresa" />
                    </div>

                    <div class="grid gap-2">
                        <Label><Phone class="w-4 h-4 text-green-500"/> Teléfono de Contacto</Label>
                        <Input id="telefono" v-model="form.telefono" />
                        <InputError :message="form.errors.telefono" />
                    </div>

                    <div class="grid gap-2">
                        <Label><MapPin class="w-4 h-4 text-red-500"/> Dirección / Ubicación</Label>
                        <Input id="direccion" v-model="form.direccion" />
                        <InputError :message="form.errors.direccion" />
                    </div>

                    <div class="grid gap-2">
                        <Label><AlignLeft class="w-4 h-4 text-orange-500"/> Descripción de Servicios</Label>
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
