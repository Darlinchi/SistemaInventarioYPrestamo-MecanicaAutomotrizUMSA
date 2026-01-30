<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { FileInput } from '@/components/ui/file-input';
import { Button } from '@/components/ui/button';
import { Save, ArrowLeft, Plus, Trash2, Loader2 } from 'lucide-vue-next';
import items from '@/routes/items';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

// Recibe el item desde el controlador
const props = defineProps<{ item: any }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Editar Item',
        href: items.edit.url(props.item.id),
    },
];

// Estado de la pestaña y previsualización de foto
const tipoItem = ref(props.item.equipment ? 'equipo' : 'herramienta');
const photoPreview = ref<string | null>(props.item.foto ? `/storage/${props.item.foto}` : null);

// Formulario con datos iniciales
const form = useForm({
    _method: 'put', // para las fotos
    nombre_item: props.item.nombre_item,
    descripcion_item: props.item.descripcion_item,
    estado: props.item.estado,
    foto: null as File | null,

    // Datos de equipo
    es_equipo: props.item.equipment !== null,
    codigo_qr: props.item.equipment?.codigo_qr || '',
    ubicacion: props.item.equipment?.ubicacion || '',
    color: props.item.equipment?.color || '',
    marca: props.item.equipment?.marca || '',
    modelo: props.item.equipment?.modelo || '',
    serie: props.item.equipment?.serie || '',
    rubro: props.item.equipment?.rubro || '',
    fecha_adquisicion: props.item.equipment?.fecha_adquisicion || '',
    observacion_equipo: props.item.equipment?.observacion_equipo || '',

    // Mapeado de los accesorios
    accesorios: props.item.equipment?.accessories?.map((a: any) => ({
        nombre: a.nombre_accesorio,
        estado: a.estado_accesorio
    })) || [],
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.foto = file;
        const reader = new FileReader();
        reader.onload = (e) => photoPreview.value = e.target?.result as string;
        reader.readAsDataURL(file);
    }
};

const resetFoto = () => {
    form.foto = null;
    // Muestra la foto que viene de la DB
    photoPreview.value = props.item.foto ? `/storage/${props.item.foto}` : null;
};

const addAccesorio = () => form.accesorios.push({ nombre: '', estado: 'Bueno' });
const removeAccesorio = (index: number) => form.accesorios.splice(index, 1);

function submit() {
    // Se usa post porque Laravel requiere _method: 'put' para procesar archivos
    form.post(items.update.url(props.item.id), {
        forceFormData: true,
        preserveScroll: true,
    });
}

</script>

<template>
    <Head :title="'Editar ' + form.nombre_item" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-4 w-full">
            <div class="mb-4">
                <Link :href="items.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black transition">
                    <ArrowLeft class="w-4 h-4 mr-2"/> Volver al inventario
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2">Editar Información General</h3>

                    <div class="grid gap-2">
                        <Label>Nombre</Label>
                        <Input id="nombre_item" v-model="form.nombre_item" />
                        <InputError :message="form.errors.nombre_item" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Foto Actual / Nueva</Label>
                        <div v-if="photoPreview" class="relative w-32 h-32 mb-2 group">
                            <img :src="photoPreview" class="w-full h-full object-cover rounded-lg border shadow-sm" />
                            <Button
                                v-if="form.foto"
                                type="button"
                                @click="resetFoto"
                                class="absolute -top-2 -right-2 bg-orange-500 text-white rounded-full p-1 shadow-md"
                            >
                                <Trash2 class="w-3 h-3" />
                            </Button>
                        </div>
                        <FileInput accept="image/*" @change="handleFileChange" />
                        <InputError :message="form.errors.foto" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="estado">Estado del Item</Label>
                        <select
                            id="estado"
                            v-model="form.estado"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="Disponible">Disponible</option>
                            <option value="Prestado">Prestado</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                            <option value="Dañado">Dañado</option>
                            <option value="Baja">Baja</option>
                        </select>
                        <InputError :message="form.errors.estado" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Descripción</Label>
                        <Textarea id="descripcion_item" v-model="form.descripcion_item" />
                        <InputError :message="form.errors.descripcion_item" />
                    </div>
                </div>

                <div v-if="form.es_equipo" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2">Detalles del Equipo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Codigo QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" />
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Color</Label>
                            <Input id="color" v-model="form.color" />
                            <InputError :message="form.errors.color" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Marca</Label>
                            <Input id="marca" v-model="form.marca" />
                            <InputError :message="form.errors.marca" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" />
                            <InputError :message="form.errors.modelo" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Serie</Label>
                            <Input id="serie" v-model="form.serie" />
                            <InputError :message="form.errors.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" />
                            <InputError :message="form.errors.rubro" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Fecha de adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" />
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Observación</Label>
                        <Textarea id="observacion_equipo" v-model="form.observacion_equipo" />
                        <InputError :message="form.errors.observacion_equipo" />
                    </div>
                </div>

                <div v-if="form.es_equipo" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg">Accesorios</h3>
                        <Button type="button" variant="outline" size="sm" @click="addAccesorio">
                            <Plus class="w-4 h-4 mr-1" /> Agregar
                        </Button>
                    </div>

                    <div v-for="(acc, index) in form.accesorios" :key="index" class="flex flex-col md:flex-row gap-2 mb-4 p-3 border rounded-lg bg-neutral-50 relative">
                        <div class="flex-1">
                            <Label class="text-xs uppercase text-neutral-500">Nombre</Label>
                            <Input id="acc.nombre" v-model="acc.nombre" placeholder="Ej: Cargador, Estuche..." />
                        </div>

                        <div class="w-full md:w-40">
                            <Label class="text-xs uppercase text-neutral-500">Estado</Label>
                            <select
                                id="acc.estado"
                                v-model="acc.estado"
                                class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            >
                                <option value="Bueno">Bueno</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Perdido">Perdido</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <Button
                                type="button"
                                variant="destructive"
                                size="icon"
                                @click="removeAccesorio(Number(index))"
                            >
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>
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
