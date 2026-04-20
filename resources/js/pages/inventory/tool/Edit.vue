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
    Save, ArrowLeft, Trash2, Loader2, AlignLeft, PencilLine, Layers, Hash,
    Rows3, Image, QrCode, Package, ImageUp, Wrench
} from 'lucide-vue-next';
import itemsRoutes from '@/routes/items';
import toolsRoutes from '@/routes/tools';
import { ref } from 'vue';

const props = defineProps<{ tool: any }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventario', href: itemsRoutes.index.url() },
    { title: 'Editar Herramienta', href: toolsRoutes.edit.url(props.tool.id) },
];
// Previsualización de la foto existente
const photoPreview = ref<string | null>(props.tool.foto_herramienta ? `/storage/${props.tool.foto_herramienta}` : null);

const form = useForm({
    _method: 'put', // Spoofing para que Laravel procese el archivo con PUT
    nombre: props.tool.nombre_herramienta,
    codigo_qr: props.tool.codigo_qr,
    ubicacion: props.tool.ubicacion_herramienta,
    descripcion: props.tool.descripcion_herramienta,
    observacion: props.tool.observacion_herramienta,
    marca_modelo: props.tool.marca_modelo,
    cantidad_piezas: props.tool.cantidad_piezas,
    estado_herramienta: props.tool.estado_herramienta,
    foto: null as File | null,
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
    photoPreview.value = props.tool.foto_herramienta ? `/storage/${props.tool.foto_herramienta}` : null;
};

const urlParams = new URLSearchParams(window.location.search);
const fromTab = urlParams.get('tab') || 'herramientas';

function submit() {
    form.post(toolsRoutes.update.url(props.tool.id), {
        forceFormData: true,
        preserveScroll: true,
    });
}

</script>

<template>
    <Head :title="'Editar ' + form.nombre" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-4 w-full">
            <div class="mb-6">
                <Link :href="itemsRoutes.index.url()+ '?tab=' + fromTab" class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group">
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    Volver al inventario
                </Link>
            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 bg-[#1a3a5a] rounded-2xl shadow-lg shadow-blue-900/20">
                    <Wrench class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Editar {{ tool.nombre_herramienta }}</h2>
                    <p class="text-neutral-500">Edite la información necesaria</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Información General
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-5 h-5 text-[#1a3a5a]"/> Código QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" placeholder="Escanear..." />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="nombre"><Wrench class="w-5 h-5 text-[#1a3a5a]"/> Nombre de la Herramienta</Label>
                            <Input id="nombre" v-model="form.nombre" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="ubicacion"><Rows3 class="w-5 h-5 text-[#1a3a5a]"/> Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" />
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="estado_herramienta"><Hash class="w-5 h-5 text-[#1a3a5a]"/> Estado de la Herramienta</Label>
                            <select
                                id="estado_herramienta"
                                v-model="form.estado_herramienta"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-[#1a3a5a] outline-none"
                            >
                                <option value="Disponible">Disponible</option>
                                <option value="Prestado">Prestado</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                                <option value="Baja">Baja</option>
                            </select>
                            <InputError :message="form.errors.estado_herramienta" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label class="mb-4 flex items-center gap-2">
                            <ImageUp class="w-5 h-5 text-[#1a3a5a]"/> Fotografía de la Herramienta
                        </Label>

                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div v-if="photoPreview" class="relative group">
                                <img :src="photoPreview"
                                    class="w-40 h-40 object-cover rounded-3xl shadow-lg border-2 border-white ring-1 ring-[#1a3a5a]/5" />

                                <button
                                    v-if="form.foto"
                                    type="button"
                                    @click="resetFoto"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-all hover:scale-110"
                                    title="Quitar nueva foto seleccionada"
                                >
                                    <Trash2 class="w-4 h-4"/>
                                </button>

                                <p v-if="form.foto" class="text-[12px] text-blue-600 font-bold mt-2 text-center uppercase tracking-tighter">
                                    Nueva imagen lista
                                </p>
                            </div>

                            <div v-else class="w-40 h-40 rounded-3xl bg-white border-2 border-dashed border-neutral-200 flex items-center justify-center text-neutral-300">
                                <ImageUp class="w-12 h-12" />
                            </div>

                            <div class="flex-1 space-y-3">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="cantidad_piezas"><Layers class="w-5 h-5 text-[#1a3a5a]"/> Cantidad de piezas</Label>
                            <Input id="cantidad_piezas" v-model="form.cantidad_piezas" />
                            <InputError :message="form.errors.cantidad_piezas" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca_modelo"><Package class="w-5 h-5 text-[#1a3a5a]"/> Marca / Modelo</Label>
                            <Input id="marca_modelo" v-model="form.marca_modelo" />
                            <InputError :message="form.errors.marca_modelo" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="ubicacion"><Rows3 class="w-5 h-5 text-[#1a3a5a]"/> Ubicación en Taller</Label>
                        <Input id="ubicacion" v-model="form.ubicacion" />
                        <InputError :message="form.errors.ubicacion" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Descripción de la Herramienta</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" class="min-h-[100px]" />
                            <InputError :message="form.errors.descripcion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Observaciones de la Herramienta</Label>
                            <Textarea id="observacion" v-model="form.observacion" class="min-h-[100px]" />
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 justify-end gap-4 w-full">
                    <Link
                        :href="itemsRoutes.index.url()+ '?tab=' + fromTab"
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
                            <span>Actualizar Herramienta</span>
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
