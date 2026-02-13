<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';
import { type BreadcrumbItem } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Loader2, Save, Building2, Phone, MapPin, AlignLeft } from 'lucide-vue-next';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nueva Empresa de Mantenimiento',
        href: maintenanceCompanyRoutes.create.url()
    },
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
                <Link :href="maintenanceCompanyRoutes.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-4 h-4 mr-1"/> Volver a empresas
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">

                <div class="bg-white p-8 rounded-3xl border border-neutral-200 shadow-sm space-y-6">
                    <h3 class="font-bold text-lg border-b pb-2">Información de Empresa</h3>

                    <div class="grid gap-2">
                        <Label for="nombre_empresa"><Building2 class="w-4 h-4 text-blue-500"/> Nombre de la Empresa</Label>
                        <Input id="nombre_empresa" v-model="form.nombre_empresa" type="text" placeholder="Ej. Repuestos La Paz" @input="handleNameInput"/>
                        <InputError :message="form.errors.nombre_empresa" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="telefono"><Phone class="w-4 h-4 text-green-500"/> Teléfono de Contacto</Label>
                            <Input id="telefono" v-model="form.telefono" type="text" placeholder="Ej. 2224455"/>
                            <InputError :message="form.errors.telefono" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="direccion"><MapPin class="w-4 h-4 text-red-500"/> Dirección / Ubicación</Label>
                            <Input id="direccion" v-model="form.direccion" type="text" placeholder="Ej. Av. 6 de Agosto #123"/>
                            <InputError :message="form.errors.direccion" />
                        </div>

                    </div>


                    <div class="grid gap-2">
                        <Label for="descripcion_empresa"><AlignLeft class="w-4 h-4 text-orange-500"/> Descripción de Servicios</Label>
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
