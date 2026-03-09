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
    Save, ArrowLeft, Trash2, Loader2, AlignLeft, PencilLine,
    Rows3, Image, QrCode, Package, ListTodo, Wrench
} from 'lucide-vue-next';
import itemsRoutes from '@/routes/items';
import toolsRoutes from '@/routes/tools';
import { ref } from 'vue';

// Recibe la herramienta desde el controlador
const props = defineProps<{ tool: any }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Editar Herramienta',
        href: toolsRoutes.edit.url(props.tool.id),
    },
];
// Previsualización de la foto existente
const photoPreview = ref<string | null>(props.tool.foto ? `/storage/${props.tool.foto}` : null);

const form = useForm({
    _method: 'put', // Spoofing para que Laravel procese el archivo con PUT
    nombre: props.tool.nombre_herramienta,
    codigo_qr: props.tool.codigo_qr,
    ubicacion: props.tool.ubicacion_herramienta,
    descripcion: props.tool.descripcion_herramienta,
    observacion: props.tool.observacion_herramienta,
    marca_modelo: props.tool.marca_modelo,
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
            <div class="mb-4">
                <Link :href="itemsRoutes.index.url()" class="inline-flex items-center text-sm text-neutral-500 hover:text-black transition">
                    <ArrowLeft class="w-4 h-4 mr-2"/> Volver al inventario
                </Link>
            </div>

            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <Wrench class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">Editar {{ tool.nombre_herramienta }}</h2>
                    <p class="text-sm text-neutral-500">Edite la información necesaria</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2 flex items-center gap-2">
                        <PencilLine class="w-5 h-5"/> Información General
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-5 h-5 inline mr-1"/> Código QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" placeholder="Escanear..." />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="nombre"><Wrench class="w-5 h-5 inline mr-1"/> Nombre de la Herramienta</Label>
                            <Input id="nombre" v-model="form.nombre" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="foto"><Image class="w-5 h-5 inline mr-1"/> Foto de la Herramienta</Label>
                        <div v-if="photoPreview" class="relative w-40 h-40 mb-2 group">
                            <img :src="photoPreview" class="w-full h-full object-cover rounded-xl border-2 border-white shadow-lg ring-1 ring-neutral-200" />
                            <Button
                                v-if="form.foto"
                                type="button"
                                @click="resetFoto"
                                class="absolute -top-2 -right-2 bg-orange-500 text-white rounded-full p-1.5 shadow-md h-8 w-8"
                            >
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>
                        <FileInput accept="image/*" @change="handleFileChange" />
                        <p class="text-[12px] text-neutral-600 italic">Formatos permitidos: JPG, PNG. Máximo 2MB.</p>
                        <InputError :message="form.errors.foto" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="estado_herramienta">Estado de la Herramienta</Label>
                            <select
                                v-model="form.estado_herramienta"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            >
                                <option value="Disponible">Disponible</option>
                                <option value="Dañado">Dañado</option>
                                <option value="Extraviado">Extraviado</option>
                                <option value="Baja">Baja</option>
                            </select>
                            <InputError :message="form.errors.estado_herramienta" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca_modelo">Marca / Modelo</Label>
                            <Input id="marca_modelo" v-model="form.marca_modelo" />
                            <InputError :message="form.errors.marca_modelo" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="ubicacion"><Rows3 class="w-5 h-5 inline mr-1"/> Ubicación en Taller</Label>
                        <Input id="ubicacion" v-model="form.ubicacion" />
                        <InputError :message="form.errors.ubicacion" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-5 h-5 inline mr-1"/> Descripción de la Herramienta</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" class="min-h-[100px]" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-5 h-5 inline mr-1"/> Observaciones de la Herramienta</Label>
                            <Textarea id="observacion" v-model="form.observacion" class="min-h-[100px]" />
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <Button type="submit" class="flex-1 py-6 text-lg font-bold bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-100" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                        <Save v-else class="w-5 h-5 mr-2"/>
                        Actualizar Herramienta
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
