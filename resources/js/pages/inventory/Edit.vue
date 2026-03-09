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
import { Save, ArrowLeft, Plus, Trash2, Loader2, AlignLeft, Hash, PencilLine, Rows3, PaintBucket, BookText,
    CalendarDays, Image, QrCode, Package, ListPlus, Pencil, ListTodo } from 'lucide-vue-next';
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
const tipoItemm = ref(props.item.equipment ? 'equipo' : 'herramienta');
const photoPrevieww = ref<string | null>(props.item.foto ? `/storage/${props.item.foto}` : null);

// Estado de la pestaña basado en el campo 'tipo' que enviamos desde el controlador
const tipoItem = ref(props.item.tipo);
const photoPreview = ref<string | null>(props.item.foto ? `/storage/${props.item.foto}` : null);

// Formulario con datos iniciales
const form = useForm({
    _method: 'put',
    tipo: props.item.tipo, // 'equipo' o 'herramienta'
    codigo_qr: props.item.codigo_qr,
    nombre: props.item.nombre, // Usamos el nombre mapeado del controlador
    ubicacion: props.item.ubicacion,
    descripcion: props.item.descripcion,
    observacion: props.item.observacion,
    foto: null as File | null,

    // Datos de equipo
    estado_equipo: props.item.estado_equipo || 'Disponible',
    color: props.item.color || '',
    marca: props.item.marca || '',
    modelo: props.item.modelo || '',
    serie: props.item.serie || '',
    rubro: props.item.rubro || '',
    fecha_adquisicion: props.item.fecha_adquisicion || '',

    // Accesorios (ahora están en la raíz del item según el controlador)
    accesorios: props.item.accessories?.map((a: any) => ({
        id: a.id,
        nombre: a.nombre_accesorio,
        estado: a.estado_accesorio
    })) || [],

    // Datos de la herramienta
    estado_herramienta: props.item.estado_herramienta || 'Disponible',
    marca_modelo: props.item.marca_modelo || '',
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

const addAccesorio = () => form.accesorios.push({ id: null, nombre: '', estado: 'Bueno' });
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
    <Head :title="'Editar ' + form.nombre" />
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="codigo_qr" ><QrCode class="w-5 h-5 text-neutral-900"/> Codigo QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="nombre" ><PencilLine class="w-5 h-5 text-neutral-900"/> Nombre del Item</Label>
                            <Input id="nombre" v-model="form.nombre" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="foto" ><Image class="w-5 h-5 text-neutral-900"/> Foto Actual / Nueva</Label>
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

                    <div v-if="tipoItem === 'herramienta'" class="grid gap-2">
                        <div class="grid gap-2">
                            <Label for="ubicacion" ><Rows3 class="w-4 h-4 text-neutral-900"/> Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" />
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                    </div>

                    <div v-if="tipoItem === 'equipo'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="ubicacion" ><Rows3 class="w-4 h-4 text-neutral-900"/> Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" />
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="estado_equipo"><ListTodo class="w-5 h-5 text-neutral-900"/> Estado del Equipo</Label>
                            <select
                                id="estado_equipo"
                                v-model="form.estado_equipo"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="Nuevo">Nuevo</option>
                                <option value="Disponible">Disponible</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Baja">Baja</option>
                                <option value="Extraviado">Extraviado</option>
                            </select>
                            <InputError :message="form.errors.estado_equipo" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-5 h-5 text-neutral-900"/> Descripción del Item</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-5 h-5 text-neutral-900"/> Observación del Item</Label>
                            <Textarea id="observacion" v-model="form.observacion" />
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div v-if="form.tipo === 'equipo'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2">Editar Detalles del Equipo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="serie"><Hash class="w-4 h-4 text-neutral-900"/> Serie</Label>
                            <Input id="serie" v-model="form.serie" />
                            <InputError :message="form.errors.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca"><Package class="w-4 h-4 text-neutral-900"/> Marca</Label>
                            <Input id="marca" v-model="form.marca" />
                            <InputError :message="form.errors.marca" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="modelo"><Package class="w-4 h-4 text-neutral-900"/> Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" />
                            <InputError :message="form.errors.modelo" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="rubro"><BookText class="w-4 h-4 text-neutral-900"/> Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" />
                            <InputError :message="form.errors.rubro" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="estado_equipo"><CalendarDays class="w-4 h-4 text-neutral-900"/> Fecha de adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" type="date"/>
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="color"><PaintBucket class="w-4 h-4 text-neutral-900"/> Color</Label>
                            <Input id="color" v-model="form.color" />
                            <InputError :message="form.errors.color" />
                        </div>
                    </div>
                </div>

                <div v-if="form.tipo === 'equipo'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="flex items-center font-bold text-lg"><ListPlus class="w-6 h-6 text-neutral-900"/> Accesorios del Equipo</h3>
                        <Button type="button" variant="outline" size="sm" @click="addAccesorio">
                            <Plus class="w-4 h-4 mr-1" /> Agregar
                        </Button>
                    </div>

                    <div v-for="(acc, index) in form.accesorios" :key="acc.id || index" class="flex flex-col md:flex-row gap-2 mb-4 p-3 border rounded-lg bg-neutral-50 relative">
                        <div class="flex-1">
                            <Label :for="'acc_nombre_' + index" class="text-s text-neutral-800">Nombre del accesorio</Label>
                            <Input :id="'acc_nombre_' + index" v-model="acc.nombre" placeholder="Ej: Cargador, Estuche..." />
                        </div>

                        <div class="w-full md:w-40">
                            <Label :for="'acc_estado_' + index" class="text-s text-neutral-800">Estado</Label>
                            <select
                                id="acc.estado"
                                v-model="acc.estado"
                                class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            >
                                <option value="Bueno">Bueno</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
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

                <div v-if="form.tipo === 'herramienta'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2">Editar Detalles de la Herramienta</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="estado_herramienta"><ListTodo class="w-5 h-5 text-neutral-900"/> Estado del Equipo</Label>
                            <select
                                id="estado"
                                v-model="form.estado_herramienta"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="Disponible">Disponible</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Baja">Baja</option>
                                <option value="Extraviado">Extraviado</option>
                            </select>
                            <InputError :message="form.errors.estado_herramienta" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca_modelo"><Package class="w-4 h-4 text-neutral-900"/> Marca/Modelo</Label>
                            <Input id="marca_modelo" v-model="form.marca_modelo" />
                            <InputError :message="form.errors.marca_modelo" />
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
