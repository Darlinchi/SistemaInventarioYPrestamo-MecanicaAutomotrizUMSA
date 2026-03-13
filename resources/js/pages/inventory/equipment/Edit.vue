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
import {
    Save, ArrowLeft, Plus, Trash2, Loader2, AlignLeft, Hash, PencilLine,
    Rows3, PaintBucket, BookText, CalendarDays, Image, QrCode, Package,
    ListPlus, ListTodo
} from 'lucide-vue-next';
import itemsRoutes from '@/routes/items';
import equipmentsRoutes from '@/routes/equipments';
import { ref } from 'vue';

// Recibe el equipo con sus accesorios cargados desde el controlador
const props = defineProps<{ equipment: any }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Editar Equipo',
        href: equipmentsRoutes.edit.url(props.equipment.id),
    },
];
const photoPreview = ref<string | null>(props.equipment.foto ? `/storage/${props.equipment.foto}` : null);

const form = useForm({
    _method: 'put',
    nombre: props.equipment.nombre_equipo,
    codigo_qr: props.equipment.codigo_qr,
    ubicacion: props.equipment.ubicacion_equipo,
    descripcion: props.equipment.descripcion_equipo,
    observacion: props.equipment.observacion_equipo,
    estado_equipo: props.equipment.estado_equipo,
    marca: props.equipment.marca,
    modelo: props.equipment.modelo,
    serie: props.equipment.serie,
    color: props.equipment.color,
    rubro: props.equipment.rubro,
    fecha_adquisicion: props.equipment.fecha_adquisicion
        ? props.equipment.fecha_adquisicion.substring(0, 10) // Corta los primeros 10 caracteres (YYYY-MM-DD)
        : '',
    foto: null as File | null,

    // Mapeo de accesorios existentes
    accesorios: props.equipment.accessories?.map((a: any) => ({
        id: a.id,
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

const addAccesorio = () => form.accesorios.push({ id: null, nombre: '', estado: 'Bueno' });
const removeAccesorio = (index: number) => form.accesorios.splice(index, 1);

function submit() {
    form.post(equipmentsRoutes.update.url(props.equipment.id), {
        forceFormData: true,
        preserveScroll: true,
    });
}

</script>

<template>
    <Head :title="'Editar ' + form.nombre" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-4 w-full">
            <div class="mb-4">
                <Link :href="itemsRoutes.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-4 h-4 mr-2"/> Volver al inventario
                </Link>
            </div>

            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-red-100 rounded-xl">
                    <Package class="w-6 h-6 text-red-600" />
                </div>
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">Editar {{ equipment.nombre_equipo }}</h2>
                    <p class="text-sm text-neutral-500">Edite la información necesaria</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6 pb-10">
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2 flex items-center gap-2">
                        <PencilLine class="w-5 h-5"/> Información General
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-5 h-5 inline mr-1"/> Código QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="nombre"><PencilLine class="w-5 h-5 inline mr-1"/> Nombre del Equipo</Label>
                            <Input id="nombre" v-model="form.nombre" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
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
                                <option value="Mantenimiento">Mantenimiento</option>
                                <option value="Disponible">Disponible</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                                <option value="Baja">Baja</option>
                            </select>
                            <InputError :message="form.errors.estado_equipo" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label><Image class="w-4 h-4 inline mr-1"/> Foto Actual / Nueva</Label>
                        <div v-if="photoPreview" class="relative w-40 h-40 mb-2">
                            <img :src="photoPreview" class="w-full h-full object-cover rounded-xl border shadow-md" />
                        </div>
                        <FileInput accept="image/*" @change="handleFileChange" />
                        <p class="text-[12px] text-neutral-600 italic">Formatos permitidos: JPG, PNG. Máximo 2MB.</p>
                        <InputError :message="form.errors.foto" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="serie"><Hash class="w-4 h-4 text-neutral-900"/> Número de Serie</Label>
                            <Input id="serie" v-model="form.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca"><Package class="w-4 h-4 text-neutral-900"/> Marca</Label>
                            <Input id="marca" v-model="form.marca" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="modelo"><Package class="w-4 h-4 text-neutral-900"/> Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="fecha_adquisicion"><CalendarDays class="w-4 h-4 inline mr-1"/> Fecha de Adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" type="date" />
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="rubro"><BookText class="w-4 h-4 text-neutral-900"/> Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" />
                            <InputError :message="form.errors.rubro" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="color"><PaintBucket class="w-4 h-4 text-neutral-900"/> Color</Label>
                            <Input id="color" v-model="form.color" />
                            <InputError :message="form.errors.color" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-5 h-5 text-neutral-900"/> Descripción del Equipo</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-5 h-5 text-neutral-900"/> Observación del Equipo</Label>
                            <Textarea id="observacion" v-model="form.observacion" />
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <ListPlus class="w-5 h-5"/> Accesorios del Equipo
                        </h3>
                        <Button type="button" variant="outline" size="sm" @click="addAccesorio" class="hover:bg-green-50 hover:text-green-600">
                            <Plus class="w-4 h-4 mr-1" /> Agregar Accesorio
                        </Button>
                    </div>

                    <div v-for="(acc, index) in form.accesorios" :key="index" class="flex flex-col md:flex-row gap-2 p-3 bg-neutral-50 rounded-lg border relative group">
                        <div class="flex-1">
                            <Label :for="'acc_nombre_' + index" class="text-s text-neutral-800">Nombre del accesorio</Label>
                            <Input v-model="acc.nombre" class="mt-1.5" placeholder="Nombre..." />
                        </div>
                        <div class="w-full md:w-40">
                            <Label :for="'acc_estado_' + index" class="text-s text-neutral-800">Estado</Label>
                            <select id="acc.estado" v-model="acc.estado" class="flex h-9 w-full rounded-md border border-input bg-white px-3 py-2 text-sm mt-1.5">
                                <option value="Bueno">Bueno</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <Button type="button" variant="destructive" size="icon" @click="removeAccesorio(Number(index))">
                                <Trash2 class="w-4 h-4"/>
                            </Button>
                        </div>
                    </div>
                    <p v-if="form.accesorios.length === 0" class="text-center text-sm text-neutral-400 py-4 italic">No hay accesorios registrados.</p>

                    </div>

                <div class="flex gap-4">
                    <Button type="submit" class="w-full py-6 bg-red-600 hover:bg-red-700 text-lg font-bold shadow-lg" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                        <Save v-else class="w-5 h-5 mr-2" />
                        Guardar Cambios en Equipo
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
