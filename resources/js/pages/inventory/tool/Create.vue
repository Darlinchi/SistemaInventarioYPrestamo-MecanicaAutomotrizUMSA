<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { FileInput } from '@/components/ui/file-input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';
import toolsRoutes from '@/routes/tools'; // Importamos las rutas de herramientas
import itemsRoutes from '@/routes/items';
import { Trash2, ArrowLeft, Save, Loader2, Wrench, QrCode, Rows3, ImageUp, AlignLeft, PencilLine, Package, Hash,
        Layers
 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventario', href: itemsRoutes.index.url() },
    { title: 'Registrar Herramienta', href: toolsRoutes.create.url() },
];

const photoPreview = ref<string | null>(null);

const form = useForm({
    codigo_qr: "",
    nombre: "",
    ubicacion: "",
    descripcion: "",
    observacion: "",
    foto: null as File | null,
    estado_herramienta: "Disponible",
    marca_modelo: "",
    cantidad_piezas: 1,
});

// FUNCIONES DE APOYO
const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (!target.files || target.files.length === 0) {
        return;
    }
    const file = target.files[0];
    form.foto = file;
    const reader = new FileReader();
    reader.onload = (event) => {
        photoPreview.value = event.target?.result as string;
    };
    reader.readAsDataURL(file);
};

function submit() {
    form.post(toolsRoutes.store.url(), {
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
    <Head title="Nueva Herramienta" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-4 w-full">
            <div class="mb-6">
                <Link :href="itemsRoutes.index.url()" class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group">
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    Volver al inventario
                </Link>
            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 bg-[#1a3a5a] rounded-2xl shadow-lg shadow-blue-900/20">
                    <Wrench class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Registrar Herramienta</h2>
                    <p class="text-neutral-500">Registre una herramienta manual o eléctrica en el sistema</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Datos Generales
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="spac-y-2">
                            <Label for="codigo_qr" class="flex items-center gap-2">
                                <QrCode class="w-5 h-5 text-[#1a3a5a]"/> Código QR
                            </Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" placeholder="Ej. EH-001" />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="space-y-2">
                            <Label for="nombre" class="flex items-center gap-2">
                                <Wrench class="w-5 h-5 text-[#1a3a5a]"/> Nombre de la Herramienta
                            </Label>
                            <Input id="nombre" v-model="form.nombre" placeholder="Ej. Llave Inglesa 12'" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="ubicacion" class="flex items-center gap-2">
                                <Rows3 class="w-5 h-5 text-[#1a3a5a]"/> Ubicación en Taller
                            </Label>
                            <Input id="ubicacion" v-model="form.ubicacion" placeholder="Ej. Caja 01" />
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="space-y-2">
                            <Label for="estado_herramienta" class="flex items-center gap-2">
                                <Hash class="w-5 h-5 text-[#1a3a5a]"/> Estado
                            </Label>
                            <select v-model="form.estado_herramienta"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5 outline-none transition-all">
                                <option value="Disponible">Disponible</option>
                                <option value="Nuevo">Nuevo</option>
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
                                <img :src="photoPreview" class="w-40 h-40 object-cover rounded-3xl shadow-lg border-2 border-white" />
                                <button type="button" @click="photoPreview = null; form.foto = null"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-all hover:scale-110">
                                    <Trash2 class="w-4 h-4"/>
                                </button>
                            </div>
                            <div v-else class="w-40 h-40 rounded-3xl bg-white border-2 border-dashed border-neutral-200 flex items-center justify-center text-neutral-300">
                                <ImageUp class="w-12 h-12" />
                            </div>

                            <div class="flex-1 space-y-2">
                                <FileInput accept="image/*" @change="handleFileChange" />
                                <p class="text-[13px] text-neutral-700 leading-tight">Sube una imagen clara de la herramienta. Máximo 2MB (JPG o PNG).</p>
                                <InputError :message="form.errors.foto" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="cantidad_piezas" class="flex items-center gap-2">
                                <Layers class="w-5 h-5 text-[#1a3a5a]"/> Cantidad de Piezas
                            </Label>
                            <Input id="cantidad_piezas" type="number" v-model="form.cantidad_piezas" placeholder="Ej. 10" />
                            <InputError :message="form.errors.cantidad_piezas" />
                        </div>

                        <div class="space-y-2">
                            <Label for="marca_modelo" class="flex items-center gap-2">
                                <Package class="w-5 h-5 text-[#1a3a5a]"/> Marca / Modelo
                            </Label>
                            <Input id="marca_modelo" v-model="form.marca_modelo" placeholder="Ej. Stanley" />
                            <InputError :message="form.errors.marca_modelo" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="descripcion" class="flex items-center gap-2">
                                <AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Descripción
                            </Label>
                            <Textarea id="descripcion" v-model="form.descripcion" rows="3" placeholder="Detalles técnicos..." />
                            <InputError :message="form.errors.descripcion" />
                        </div>
                        <div class="space-y-2">
                            <Label for="observacion" class="flex items-center gap-2">
                                <AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Observaciones
                            </Label>
                            <Textarea id="observacion" v-model="form.observacion" rows="3" placeholder="Notas adicionales..." />
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 justify-end gap-4 w-full">
                    <button
                        type="button"
                        @click="itemsRoutes.index.url()"
                        class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-600 rounded-xl font-semibold text-[20px] hover:bg-neutral-50 transition-all active:scale-95"
                    >
                        Cancelar
                    </button>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="h-14 bg-[#1a3a5a] text-white rounded-xl font-semibold text-[20px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full"
                    >
                        <template v-if="form.processing">
                            <Loader2 class="w-5 h-5 animate-spin mr-2" />
                            Guardando...
                        </template>
                        <template v-else>
                            Registrar Herramienta
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
