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
import equipmentRoutes from '@/routes/equipments'; // Ajusta según tu archivo de rutas
import items from '@/routes/items';
import {
    Plus, Trash2, ArrowLeft, Save, Loader2, Package, QrCode,
    Rows3, ImageUp, ListPlus, AlignLeft, Hash, PencilLine,
    PaintBucket, BookText, CalendarDays
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventario', href: items.index.url() },
    { title: 'Nuevo Equipo', href: equipmentRoutes.create.url() },
];

const photoPreview = ref<string | null>(null);

// Formulario específico para Equipos
const form = useForm({
    codigo_qr: "",
    nombre: "",
    ubicacion: "",
    descripcion: "",
    observacion: "",
    foto: null as File | null,
    estado_equipo: "Disponible",
    color: "",
    marca: "",
    modelo: "",
    serie: "",
    rubro: "",
    fecha_adquisicion: "",
    accesorios: [] as { nombre: string; estado: string }[],
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.foto = file;
        const reader = new FileReader();
        reader.onload = (event) => photoPreview.value = event.target?.result as string;
        reader.readAsDataURL(file);
    }
};

const addAccesorio = () => form.accesorios.push({ nombre: '', estado: 'Bueno' });
const removeAccesorio = (index: number) => form.accesorios.splice(index, 1);

function submit() {
    form.post(equipmentRoutes.store.url(), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Nuevo Equipo" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-4 w-full">
            <div class="mb-4">
                <Link :href="items.index.url()" class="inline-flex items-center text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-5 h-5 mr-1"/> Volver al inventario
                </Link>
            </div>

            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-red-100 rounded-xl">
                    <Package class="w-6 h-6 text-red-600" />
                </div>
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">Nuevo Equipo</h2>
                    <p class="text-sm text-neutral-500">Registro de activos y equipos de diagnóstico</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2 flex items-center gap-2">
                        <PencilLine class="w-5 h-5"/> Datos Generales
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-5 h-5 inline mr-1"/> Código QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" placeholder="Ej. EQ-001" />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="nombre"><PencilLine class="w-5 h-5 inline mr-1"/> Nombre del Equipo</Label>
                            <Input id="nombre" v-model="form.nombre" placeholder="Ej. Escáner Automotriz" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="ubicacion"><Rows3 class="w-4 h-4 inline mr-1"/> Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" placeholder="Ej. Estante A-1"/>
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="estado_equipo"><Rows3 class="w-4 h-4 text-neutral-900"/> Estado del Equipo</Label>
                            <select v-model="form.estado_equipo" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="Disponible">Disponible</option>
                                <option value="Nuevo">Nuevo</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="foto"><ImageUp class="w-5 h-5 text-neutral-900"/> Foto del Equipo</Label>
                        <div v-if="photoPreview" class="relative w-40 h-40 group">
                            <img :src="photoPreview" class="w-full h-full object-cover rounded-xl border shadow-md" />
                            <button type="button" @click="photoPreview = null; form.foto = null" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg">
                                <Trash2 class="w-4 h-4"/>
                            </button>
                        </div>
                        <FileInput accept="image/*" @change="handleFileChange" />
                        <InputError :message="form.errors.foto" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="serie"><Hash class="w-4 h-4 text-neutral-900"/> Número de Serie</Label>
                            <Input id="serie" v-model="form.serie" placeholder="Ej. XYZ1234"/>
                            <InputError :message="form.errors.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca"><Package class="w-4 h-4 text-neutral-900"/> Marca</Label>
                            <Input id="marca" v-model="form.marca" placeholder="Ej. Launch"/>
                            <InputError :message="form.errors.marca" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="modelo"><Package class="w-4 h-4 text-neutral-900"/> Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" placeholder="Ej. X123"/>
                            <InputError :message="form.errors.modelo" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="fecha_adquisicion"><CalendarDays class="w-4 h-4 text-neutral-900"/> Fecha de Adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" type="date"/>
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="rubro"><BookText class="w-4 h-4 text-neutral-900"/> Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" placeholder="Ej. Equipos de diagnóstico"/>
                            <InputError :message="form.errors.rubro" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="color"><PaintBucket class="w-4 h-4 text-neutral-900"/> Color</Label>
                            <Input id="color" v-model="form.color" type="text" placeholder="Ej. Negro"/>
                            <InputError :message="form.errors.color" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-4 h-4 inline mr-1"/> Descripción del Equipo</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" placeholder="Detalles técnicos..."/>
                            <InputError :message="form.errors.descripcion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-4 h-4 inline mr-1"/> Observación del Equipo</Label>
                            <Textarea id="observacion" v-model="form.observacion" placeholder="Notas adicionales..."/>
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <ListPlus class="w-5 h-5"/> Accesorios del Equipo
                        </h3>
                        <Button type="button" variant="outline" size="sm" @click="addAccesorio">
                            <Plus class="w-4 h-4 mr-1" /> Agregar Accesorio
                        </Button>
                    </div>

                    <div v-for="(acc, index) in form.accesorios" :key="index" class="flex gap-2 p-3 bg-neutral-50 rounded-lg border">
                        <div class="flex-1">
                            <Label :for="'acc_nombre_' + index" class="text-s text-neutral-800">Nombre del accesorio</Label>
                            <Input :id="'acc_nombre_' + index" v-model="acc.nombre" class="mt-1.5" type="text" placeholder="Ej. Cable de poder"/>
                        </div>
                        <div class="w-32">
                            <Label class="text-s text-neutral-800">Estado</Label>
                            <select v-model="acc.estado" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm mt-1.5">
                                <option value="Bueno">Bueno</option>
                                <option value="Dañado">Dañado</option>
                            </select>
                        </div>
                        <Button type="button" variant="destructive" size="icon" @click="removeAccesorio(index)">
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

                <Button type="submit" class="w-full py-6 bg-red-600 hover:bg-red-700 font-bold" :disabled="form.processing">
                    <Loader2 v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                    <Save v-else class="w-5 h-5 mr-2" />
                    Registrar Equipo
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
