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
import { Plus, Trash2, Image as ImageIcon, ArrowLeft, Save, Loader2, Wrench, Package, QrCode, Rows3,
    ImageUp, ListPlus, AlignLeft, Hash, PencilLine, PaintBucket, BookText, CalendarDays } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Nuevo Item', href: items.create.url(), },
];

// Estado para controlar si es Equipo o Herramienta
const tipoItem = ref<'equipo' | 'herramienta'>('equipo');
// Interfaz para los accesorios nuevos
interface NewAccessory {
    nombre: string;
    estado: 'Bueno' | 'Dañado' | 'Extraviado';
}
// Función para capturar el archivo cuando el usuario lo selecciona
const photoPreview = ref<string | null>(null);

// Formulario con los campos correspondietnes
const initialValues = {
    tipo: 'equipo' as 'equipo' | 'herramienta', // Campo clave para el controlador
    codigo_qr: "",
    nombre: "", // Simplificado
    ubicacion: "",
    descripcion: "",
    observacion: "",
    foto: null as File | null,

    // Datos Equipo
    estado_equipo: "Disponible",
    color: "",
    marca: "",
    modelo: "",
    serie: "",
    rubro: "",
    fecha_adquisicion: "",
    accesorios: [] as NewAccessory[],

    // Datos Herramienta
    estado_herramienta: "Disponible",
    marca_modelo: "",
}
const form = useForm(initialValues);

// Limpia el formulario
const setTipoItem = (tipo: 'herramienta' | 'equipo') => {
    tipoItem.value = tipo;
    form.tipo = tipo;
    form.clearErrors();
    if (tipo === 'herramienta') {
        form.marca = "";
        form.modelo = "";
        form.serie = "";
        form.color = "";
        form.rubro = "";
        form.fecha_adquisicion = "";
        form.accesorios = [];
    } else {
        // Limpiamos campos de herramienta
        form.marca_modelo = "";
    }
};

// FUNCIONES DE APOYO
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    // Si no hay archivos, salimos.
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
    // Importante: Como enviamos archivos, usamos post
    form.post(items.store.url(), { // Asegúrate que 'items.store' exista en tus rutas
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            form.reset();
        },
        onError: (errors) => {
            console.error("Errores del servidor:", errors);
        }
    });
}
</script>

<template>
    <Head title="Crear Item" />
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
                    @click="setTipoItem('equipo')"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    tipoItem === 'equipo' ? 'bg-white shadow-sm text-black' : 'text-neutral-500 hover:text-black hover:bg-neutral-200/50']"
                >
                    <Package :class="['w-5 h-5', tipoItem === 'equipo' ? 'text-red-600' : 'text-neutral-400']"w/> Equipo
                </button>

                <button
                    type="button"
                    @click="setTipoItem('herramienta')"
                    :class="['flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-bold transition-all',
                    tipoItem === 'herramienta' ? 'bg-white shadow-sm text-black' : 'text-neutral-500 hover:text-black hover:bg-neutral-200/50']"
                >
                    <Wrench :class="['w-5 h-5', tipoItem === 'herramienta' ? 'text-blue-600' : 'text-neutral-400']"/> Herramienta
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2">Información General</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-5 h-5 text-neutral-900"/> Codigo QR </Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" type="text"placeholder="Ej. 1234567"/>
                            <InputError :message="form.errors.codigo_qr" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="nombre"><PencilLine class="w-5 h-5 text-neutral-900"/> Nombre del Item</Label>
                            <Input id="nombre" v-model="form.nombre" type="text" placeholder="Ej. Éscaner automotriz..." />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="foto"><ImageUp class="w-5 h-5 text-neutral-900"/> Foto del Item</Label>
                        <div v-if="photoPreview" class="relative w-40 h-40 mb-4 group">
                            <img :src="photoPreview"
                                class="w-full h-full object-cover rounded-2xl border-4 border-white shadow-xl ring-2 ring-blue-500/20"
                            />
                            <button
                                type="button"
                                @click="photoPreview = null; form.foto = null"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 hover:scale-110 transition-all"
                                title="Quitar foto"
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
                    <div v-if="tipoItem === 'herramienta'" class="grid gap-2">
                        <Label for="ubicacion"><Rows3 class="w-4 h-4 text-neutral-900"/> Ubicación en Taller </Label>
                        <Input id="ubicacion" v-model="form.ubicacion" type="text" placeholder="Ej. Estante A-1"/>
                        <InputError :message="form.errors.ubicacion" />
                    </div>

                    <div v-if="tipoItem === 'equipo'" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="ubicacion"><Rows3 class="w-4 h-4 text-neutral-900"/> Ubicación en Taller </Label>
                            <Input id="ubicacion" v-model="form.ubicacion" type="text" placeholder="Ej. Estante A-1"/>
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="estado_equipo"><Hash class="w-4 h-4 text-neutral-900"/> Estado del Equipo</Label>
                            <select v-model="form.estado_equipo" class="flex h-9 w-full rounded-md border border-input bg-white px-3 py-1 text-sm">
                                <option value="Disponible">Disponible</option>
                                <option value="Nuevo">Nuevo</option>
                            </select>
                            <InputError :message="form.errors.estado_equipo" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-5 h-5 text-neutral-900"/> Descripción del Item</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" :rows="4" placeholder="Detalles técnicos..." />
                            <InputError :message="form.errors.descripcion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-5 h-5 text-neutral-900"/> Observación del Item</Label>
                            <Textarea id="observacion" v-model="form.observacion" :rows="4" placeholder="Notas adicionales..." />
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div v-if="tipoItem === 'equipo'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-in fade-in slide-in-from-top-4">
                    <h3 class="font-bold text-lg border-b pb-2 mb-4">Detalles del Equipo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">

                        <div class="grid gap-2">
                            <Label for="serie"><Hash class="w-4 h-4 text-neutral-900"/> Serie</Label>
                            <Input id="serie" v-model="form.serie" type="text" placeholder="Ej. XYZ1234"/>
                            <InputError :message="form.errors.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca"><Package class="w-4 h-4 text-neutral-900"/> Marca</Label>
                            <Input id="marca" v-model="form.marca" type="text" placeholder="Ej. Launch"/>
                            <InputError :message="form.errors.marca" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="modelo"><Package class="w-4 h-4 text-neutral-900"/> Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" type="text" placeholder="Ej. X123"/>
                            <InputError :message="form.errors.modelo" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="rubro"><BookText class="w-4 h-4 text-neutral-900"/> Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" type="text" placeholder="Ej. Equipos de diagnóstico"/>
                            <InputError :message="form.errors.rubro"/>
                        </div>
                        <div class="grid gap-2">
                            <Label for="fecha_adquisicion"><CalendarDays class="w-4 h-4 text-neutral-900"/> Fecha de Adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" type="date"/>
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="color"><PaintBucket class="w-4 h-4 text-neutral-900"/> Color</Label>
                            <Input id="color" v-model="form.color" type="text" placeholder="Ej. Negro"/>
                            <InputError :message="form.errors.color" />
                        </div>
                    </div>
                </div>

                <div v-if="tipoItem === 'equipo'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-in fade-in slide-in-from-top-4 duration-300 space-y-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="flex items-center font-bold text-lg"><ListPlus class="w-6 h-6 text-neutral-900"/> Accesorios del Equipo</h3>
                        <Button type="button" variant="outline" size="sm" @click="addAccesorio">
                            <Plus class="w-4 h-4 mr-1" /> Agregar
                        </Button>
                    </div>

                    <div v-for="(acc, index) in form.accesorios" :key="index" class="flex gap-2 mb-3 items-end p-2 border rounded-lg bg-neutral-50">
                        <div class="flex-1">
                            <Label :for="'acc_nombre_' + index" class="text-s text-neutral-800">Nombre del accesorio</Label>
                            <Input :id="'acc_nombre_' + index" v-model="acc.nombre" type="text" placeholder="Ej. Cable de poder"/>
                        </div>
                        <div class="w-32">
                            <Label class="text-s text-neutral-800">Estado</Label>
                            <select v-model="acc.estado" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="Bueno">Bueno</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                            </select>
                        </div>
                        <Button type="button" variant="destructive" size="icon" @click="removeAccesorio(Number(index))">
                            <Trash2 class="w-4 h-4" />
                        </Button>
                    </div>
                    <div v-if="form.accesorios.length === 0"
                        class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-neutral-100 rounded-xl bg-neutral-50/50">
                        <ListPlus class="w-8 h-8 text-neutral-300 mb-2" />
                        <p class="text-sm text-neutral-400 font-medium text-center">
                            ¿Este equipo tiene accesorios?<br>
                            <span class="text-[11px]">Ej: Cables, estuches, puntas de prueba...</span>
                        </p>
                    </div>
                </div>

                <div v-if="tipoItem === 'herramienta'" class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm animate-in fade-in slide-in-from-top-4">
                    <h3 class="font-bold text-lg border-b pb-2 mb-4">Detalles de la Herramienta</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="estado_herramienta"><Hash class="w-4 h-4 text-neutral-900"/> Estado de la Herramienta</Label>
                            <select v-model="form.estado_herramienta" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="Disponible">Disponible</option>
                                <option value="Nuevo">Nuevo</option>
                            </select>
                            <InputError :message="form.errors.estado_herramienta" />
                        </div>
                        <div class="grid gap-2">
                            <Label for=""><Package class="w-4 h-4 text-neutral-900"/> Marca/Modelo </Label>
                            <Input id="marca_modelo" v-model="form.marca_modelo" type="text" placeholder="Ej. Launch"/>
                            <InputError :message="form.errors.marca_modelo" />
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <Link :href="items.index.url()" class="flex-1">
                        <Button variant="outline" class="w-full py-6" :disabled="form.processing">
                            Cancelar
                        </Button>
                    </Link>
                    <Button
                        type="submit"
                        class="flex-1 py-6 text-lg font-bold transition-all shadow-lg active:scale-95"
                        :disabled="form.processing"
                    >
                        <template v-if="form.processing">
                            <Loader2 class="mr-2 h-5 w-5 animate-spin" />
                            Guardando...
                        </template>
                        <template v-else>
                            <Save class="w-5 h-5 mr-2" />
                            Guardar {{ tipoItem === 'equipo' ? 'Equipo' : 'Herramienta' }}
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
