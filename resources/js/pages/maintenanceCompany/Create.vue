<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';
import { type BreadcrumbItem } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { CardTitle } from '@/components/ui/card';
import { ArrowLeft, Loader2, Save, Building2, Phone, MapPin, AlignLeft, PencilLine } from 'lucide-vue-next';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Empresas de Mantenimiento', href: maintenanceCompanyRoutes.index.url() },
    { title: 'Registrar Empresa de Mantenimiento', href: maintenanceCompanyRoutes.create.url() },
];

const form = useForm({
    id: '',
    nombre_empresa: '',
    telefono: '',
    descripcion_empresa: '',
    direccion: '',
});

// Función para capitalizar la primera letra de cada palabra
const handleNameInput = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value;

    // Capitaliza la primera letra de cada palabra
    form.nombre_empresa = value.toLowerCase().replace(/\b\w/g, (l) => l.toUpperCase());
};

function submit() {
    form.post('/dashboard/maintenanceCompanies', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

</script>

<template>
    <Head title="Nueva Empresa de Mantenimiento" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-6 w-full">
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
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Registrar Empresa de Mantenimiento</h2>
                    <p class="text-neutral-500">Registro de empresas de mantenimineto</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">

                <div class="bg-white p-8 rounded-3xl border border-neutral-200 shadow-sm space-y-6">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Información de Empresa
                    </CardTitle>

                    <div class="grid gap-2">
                        <Label for="nombre_empresa"><Building2 class="w-5 h-5 text-[#1a3a5a]"/> Nombre de la Empresa</Label>
                        <Input id="nombre_empresa" v-model="form.nombre_empresa" type="text" placeholder="Ej. Repuestos La Paz" @input="handleNameInput"/>
                        <InputError :message="form.errors.nombre_empresa" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="telefono"><Phone class="w-5 h-5 text-[#1a3a5a]"/> Teléfono de Contacto</Label>
                            <Input id="telefono" v-model="form.telefono" type="text" placeholder="Ej. 2224455"/>
                            <InputError :message="form.errors.telefono" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="direccion"><MapPin class="w-5 h-5 text-[#1a3a5a]"/> Dirección / Ubicación</Label>
                            <Input id="direccion" v-model="form.direccion" type="text" placeholder="Ej. Av. 6 de Agosto #123"/>
                            <InputError :message="form.errors.direccion" />
                        </div>

                    </div>

                    <div class="grid gap-2">
                        <Label for="descripcion_empresa"><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Descripción de Servicios</Label>
                        <Textarea id="descripcion_empresa" v-model="form.descripcion_empresa" :rows="3" placeholder="Describa los servicios que ofrece la empresa..." />
                        <InputError :message="form.errors.descripcion_empresa"/>
                    </div>
                </div>

                <div class="flex gap-4">
                    <Link :href="maintenanceCompanyRoutes.index.url()" class="flex-1">
                        <Button variant="outline" class="w-full">Cancelar</Button>
                    </Link>
                    <Button
                        type="submit"
                        class="flex-1 py-6 text-lg font-bold transition-all"
                        :disabled="form.processing"
                    >
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-5 w-5 animate-spin" />
                            Guardando...
                        </template>

                        <template v-else>
                            <Save class="w-6 h-6 mr-2"/>
                            Guardar
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
