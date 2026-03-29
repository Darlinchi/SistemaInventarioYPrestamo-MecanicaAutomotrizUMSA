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
import { CardTitle } from '@/components/ui/card';
import {
    Save, ArrowLeft, Plus, Trash2, Loader2, AlignLeft, Hash, PencilLine,
    Rows3, PaintBucket, BookText, CalendarDays, Image, QrCode, Package,
    ListPlus, ListTodo, ImageUp
} from 'lucide-vue-next';
import itemsRoutes from '@/routes/items';
import equipmentsRoutes from '@/routes/equipments';
import { ref } from 'vue';

// Recibe el equipo con sus accesorios cargados desde el controlador
const props = defineProps<{ equipment: any }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventario', href: equipmentsRoutes.index.url() },
    { title: 'Editar Equipo', href: equipmentsRoutes.edit.url(props.equipment.id) },
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

const resetFoto = () => {
    form.foto = null;
    photoPreview.value = props.equipment.foto_equipo ? `/storage/${props.equipment.foto_equipo }` : null;
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
            <div class="mb-6">
                <Link :href="itemsRoutes.index.url()" class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group">
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    Volver al inventario
                </Link>
            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 bg-[#1a3a5a] rounded-2xl shadow-lg shadow-blue-900/20">
                    <Package class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Editar {{ equipment.nombre_equipo }}</h2>
                    <p class="text-neutral-500">Edite la información necesaria</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6 pb-10">
                <div class="bg-white p-6 rounded-3xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Información General
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-5 h-5 text-[#1a3a5a]"/> Código QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="nombre"><Package class="w-5 h-5 text-[#1a3a5a]"/> Nombre del Equipo</Label>
                            <Input id="nombre" v-model="form.nombre" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="estado_equipo"><Hash class="w-5 h-5 text-[#1a3a5a]"/> Estado del Equipo</Label>
                            <select
                                id="estado_equipo"
                                v-model="form.estado_equipo"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-[#1a3a5a] outline-none"
                            >
                                <option value="Disponible">Disponible</option>
                                <option value="Prestado">Prestado</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                                <option value="Baja">Baja</option>
                            </select>
                            <InputError :message="form.errors.estado_equipo" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="ubicacion"><Rows3 class="w-5 h-5 text-[#1a3a5a]"/> Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" />
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label class="mb-4 flex items-center gap-2">
                            <ImageUp class="w-5 h-5 text-[#1a3a5a]"/> Foto Actual / Nueva
                        </Label>

                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div v-if="photoPreview" class="relative group">
                                <img :src="photoPreview"
                                    class="w-40 h-40 object-cover rounded-3xl shadow-lg border-2 border-white ring-1 ring-[#1a3a5a]/5 transition-all duration-300" />

                                <button
                                    v-if="form.foto"
                                    type="button"
                                    @click="resetFoto"
                                    class="absolute -top-2 -right-2 bg-[#d90000] text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-all hover:scale-110"
                                    title="Quitar nueva foto seleccionada"
                                >
                                    <Trash2 class="w-4 h-4"/>
                                </button>

                                <p v-if="form.foto" class="text-[11px] text-blue-600 font-semibold mt-2 text-center tracking-tight">
                                    NUEVA IMAGEN LISTA
                                </p>
                            </div>

                            <div v-else class="w-40 h-40 rounded-3xl bg-white border-2 border-dashed border-neutral-200 flex items-center justify-center text-neutral-300">
                                <ImageUp class="w-12 h-12" />
                            </div>

                            <div class="flex-1 space-y-4">
                                <FileInput accept="image/*" @change="handleFileChange" />

                                <div class="space-y-1">
                                    <p class="text-[13px] text-neutral-700 leading-tight">
                                        Puedes actualizar la foto actual subiendo una nueva.
                                    </p>
                                    <p class="text-[13px] text-neutral-600 italic">
                                        Formatos: JPG, PNG. Máximo 2MB.
                                    </p>
                                </div>

                                <InputError :message="form.errors.foto" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="serie"><Hash class="w-5 h-5 text-[#1a3a5a]"/> Número de Serie</Label>
                            <Input id="serie" v-model="form.serie" />
                            <InputError :message="form.errors.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca"><Package class="w-5 h-5 text-[#1a3a5a]"/> Marca</Label>
                            <Input id="marca" v-model="form.marca" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="modelo"><Package class="w-5 h-5 text-[#1a3a5a]"/> Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="fecha_adquisicion"><CalendarDays class="w-5 h-5 text-[#1a3a5a]"/> Fecha de Adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" type="date" />
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="rubro"><BookText class="w-5 h-5 text-[#1a3a5a]"/> Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" />
                            <InputError :message="form.errors.rubro" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="color"><PaintBucket class="w-5 h-5 text-[#1a3a5a]"/> Color</Label>
                            <Input id="color" v-model="form.color" />
                            <InputError :message="form.errors.color" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Descripción del Equipo</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Observación del Equipo</Label>
                            <Textarea id="observacion" v-model="form.descripcion" />
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                            <ListPlus class="w-5 h-5 text-[#1a3a5a]"/> Accesorios del Equipo
                        </CardTitle>
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
                            <select id="acc.estado" v-model="acc.estado" class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-[#1a3a5a] outline-none mt-1.5">
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

                <div class="grid grid-cols-1 md:grid-cols-2 justify-end gap-4 w-full">
                    <Link
                        :href="itemsRoutes.index.url()"
                        class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-500 rounded-xl font-semibold text-[20px] hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                    >
                        Cancelar
                    </Link>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="h-14 bg-[#1a3a5a] text-white rounded-xl font-semibold text-[20px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all flex items-center justify-center gap-3"
                    >
                        <template v-if="form.processing">
                            <Loader2 class="w-6 h-6 animate-spin" />
                            <span>Actualizando...</span>
                        </template>
                        <template v-else>
                            <Save class="w-6 h-6" />
                            <span>Actualizar Equipo</span>
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
