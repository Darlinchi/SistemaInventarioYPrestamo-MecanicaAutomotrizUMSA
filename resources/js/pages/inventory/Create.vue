<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { FileInput } from '@/components/ui/file-input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';
import items from '@/routes/items';
import { Plus, Trash2, Image as ImageIcon, ArrowLeft, Save, Loader2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Crear Item',
        href: items.create.url(),
    },
];

// Estado para controlar si es Equipo o Herramienta
const tipoItem = ref<'herramienta' | 'equipo'>('herramienta');
// Función para capturar el archivo cuando el usuario lo selecciona
const photoPreview = ref<string | null>(null);

// Formulario con los campos correspondietnes
const initialValues = {
    nombre_item: "",
    descripcion_item: "",
    estado: "Disponible", // Valor por defecto
    foto: null as File | null,

    // Datos especificos de Equipo en caso de que lo sea
    es_equipo: false,
    codigo_qr: "",
    ubicacion: "",
    color: "",
    marca: "",
    modelo: "",
    serie: "",
    rubro: "",
    fecha_adquisicion: "",
    observacion_equipo: "",
    // Lista de accesorios
    accesorios: [] as Array<{ nombre: string; estado: "Bueno" }>,
}
const form = useForm(initialValues);

// FUNCIONES DE APOYO
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    // Verificación de seguridad: si no hay archivos, salimos.
    if (!target.files || target.files.length === 0) {
        return;
    }

    const file = target.files[0];
    // Asigna el archivo al formulario de Inertia
    form.foto = file;

    // Crea la URL para la previsualización
    const reader = new FileReader();
    reader.onload = (event) => {
        // Asigna el resultado a la variable que definimos antes
        photoPreview.value = event.target?.result as string;
    };
    reader.readAsDataURL(file);
};

// Funciones para accesorios
const addAccesorio = () => {
    form.accesorios.push({ nombre: '', estado: 'Bueno' });
};
const removeAccesorio = (index: number) => {
    form.accesorios.splice(index, 1);
};

function submit() {
    form.post(items.store.url(), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            photoPreview.value = null;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
}
</script>

<template>
    <Head title="Crear Herramienta" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-4 w-full">
            <div class="mb-4">
                <Link :href="items.index.url()" class="inline-flex items-center text-m text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-5 h-5"/> Volver al inventario
                </Link>
            </div>

            <div class="flex gap-4 mb-6 bg-neutral-100 p-1 rounded-xl w-fit border border-neutral-200">
                <button
                    type="button"
                    @click="tipoItem = 'herramienta'; form.es_equipo = false"
                    :class="['px-6 py-2 rounded-lg text-sm font-bold transition-all', tipoItem === 'herramienta' ? 'bg-white shadow-sm text-black' : 'text-neutral-500']"
                > Herramienta </button>
                <button
                    type="button"
                    @click="tipoItem = 'equipo'; form.es_equipo = true"
                    :class="['px-6 py-2 rounded-lg text-sm font-bold transition-all', tipoItem === 'equipo' ? 'bg-white shadow-sm text-black' : 'text-neutral-500']"
                > Equipo </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2">Información General</h3>

                    <div class="grid gap-2">
                        <Label for="nombre_item">Nombre</Label>
                        <Input id="nombre_item" v-model="form.nombre_item" type="text"/>
                        <InputError :message="form.errors.nombre_item" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Foto del Item</Label>
                        <div v-if="photoPreview" class="relative w-40 h-40 mb-2 group">
                            <img
                                :src="photoPreview"
                                class="w-full h-full object-cover rounded-xl border-2 border-blue-500 shadow-md"
                            />
                            <button
                                type="button"
                                @click="photoPreview = null; form.foto = null"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition-colors"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                            <p class="text-[10px] text-blue-600 font-bold mt-1 text-center">Vista previa seleccionada</p>
                        </div>
                        <FileInput
                            accept="image/*"
                            @change="handleFileChange"
                        />
                        <InputError :message="form.errors.foto" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="descripcion">Descripción</Label>
                        <Textarea id="descripcion_item" v-model="form.descripcion_item" :rows="4" />
                        <InputError :message="form.errors.descripcion_item" />
                    </div>
                </div>

                <div v-if="tipoItem === 'equipo'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-in fade-in slide-in-from-top-4">
                    <h3 class="font-bold text-lg border-b pb-2 mb-4">Detalles del Equipo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label>Codigo QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" type="text"/>
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" type="text"/>
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Color</Label>
                            <Input id="color" v-model="form.color" type="text"/>
                            <InputError :message="form.errors.color" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Marca</Label>
                            <Input id="marca" v-model="form.marca" type="text"/>
                            <InputError :message="form.errors.marca" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" type="text"/>
                            <InputError :message="form.errors.modelo" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Serie</Label>
                            <Input id="serie" v-model="form.serie" type="text"/>
                            <InputError :message="form.errors.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" type="text"/>
                            <InputError :message="form.errors.rubro" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Fecha de Adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" type="date"/>
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Observación</Label>
                        <Textarea id="observacion_equipo" v-model="form.observacion_equipo" />
                        <InputError :message="form.errors.observacion_equipo" />
                    </div>
                </div>

                <div v-if="tipoItem === 'equipo'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg">Accesorios</h3>
                        <Button type="button" variant="outline" size="sm" @click="addAccesorio">
                            <Plus class="w-4 h-4 mr-1" /> Agregar
                        </Button>
                    </div>

                    <div v-for="(acc, index) in form.accesorios" :key="index" class="flex gap-2 mb-3 items-end p-2 border rounded-lg bg-neutral-50">
                        <div class="flex-1">
                            <Label :for="'acc_nombre_' + index" class="text-xs text-neutral-500">Nombre del accesorio</Label>
                            <Input :id="'acc_nombre_' + index" v-model="acc.nombre" type="text" placeholder="Ej. Cable de poder"/>
                        </div>
                        <div class="w-32">
                            <Label class="text-xs text-neutral-500">Estado</Label>
                            <select v-model="acc.estado" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="Bueno">Bueno</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Perdido">Perdido</option>
                            </select>
                        </div>
                        <Button type="button" variant="destructive" size="icon" @click="removeAccesorio(Number(index))">
                            <Trash2 class="w-4 h-4" />
                        </Button>
                    </div>
                    <p v-if="form.accesorios.length === 0" class="text-sm text-neutral-400 italic">No se han agregado accesorios.</p>
                </div>

                <div class="flex gap-4">
                    <Link :href="items.index.url()" class="flex-1">
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
                            Guardar {{ tipoItem === 'equipo' ? 'Equipo' : 'Herramienta' }}
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
