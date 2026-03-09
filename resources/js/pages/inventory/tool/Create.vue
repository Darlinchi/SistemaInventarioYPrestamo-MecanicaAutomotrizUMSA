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
import toolsRoutes from '@/routes/tools'; // Importamos las rutas de herramientas
import items from '@/routes/items';
import { Trash2, ArrowLeft, Save, Loader2, Wrench, QrCode, Rows3, ImageUp, AlignLeft, PencilLine, Package, Hash } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventario', href: items.index.url() },
    { title: 'Nueva Herramienta', href: toolsRoutes.create.url() },
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
            <div class="mb-4">
                <Link :href="items.index.url()" class="inline-flex items-center text-neutral-500 hover:text-black">
                    <ArrowLeft class="w-5 h-5 mr-1"/> Volver al inventario
                </Link>
            </div>

            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-blue-100 rounded-xl">
                    <Wrench class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tighter">Nueva Herramienta</h2>
                    <p class="text-sm text-neutral-500">Registro de herramienta manual o eléctrica</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg border-b pb-2 flex items-center gap-2">
                        <PencilLine class="w-5 h-5"/> Datos Generales
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-4 h-4 inline mr-1"/> Código QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" placeholder="Ej. EH-001"/>
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="nombre"><PencilLine class="w-4 h-4 inline mr-1"/> Nombre de la Herramienta</Label>
                            <Input id="nombre" v-model="form.nombre" placeholder="Ej. Llave Inglesa 12'"/>
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="foto"><ImageUp class="w-4 h-4 inline mr-1"/> Foto de la Herramienta</Label>
                        <div v-if="photoPreview" class="relative w-40 h-40 mb-4 group">
                            <img :src="photoPreview"
                            class="w-full h-full object-cover rounded-2xl border-4 border-white shadow-xl ring-2 ring-blue-500/20" />
                            <button type="button" @click="photoPreview = null; form.foto = null" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 hover:scale-110 transition-all">
                                <Trash2 class="w-4 h-4"/>
                            </button>
                            <p class="text-[10px] text-blue-600 font-bold mt-1 text-center">Vista previa seleccionada</p>
                        </div>
                        <FileInput accept="image/*" @change="handleFileChange" />
                        <p class="text-[12px] text-neutral-600 italic">Formatos permitidos: JPG, PNG. Máximo 2MB.</p>
                        <InputError :message="form.errors.foto" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="estado_herramienta"><Hash class="w-4 h-4 inline mr-1"/> Estado de la Herramienta</Label>
                            <select v-model="form.estado_herramienta" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="Disponible">Disponible</option>
                                <option value="Nuevo">Nuevo</option>
                            </select>
                            <InputError :message="form.errors.estado_herramienta" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca_modelo"><Package class="w-4 h-4 inline mr-1"/> Marca / Modelo</Label>
                            <Input id="marca_modelo" v-model="form.marca_modelo" placeholder="Ej. Stanley / Classic"/>
                            <InputError :message="form.errors.marca_modelo" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="ubicacion"><Rows3 class="w-4 h-4 inline mr-1"/> Ubicación en Taller</Label>
                        <Input id="ubicacion" v-model="form.ubicacion" placeholder="Ej. Caja de Herramientas 01"/>
                        <InputError :message="form.errors.ubicacion" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-4 h-4 inline mr-1"/> Descripción de la Herramienta</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" placeholder="Detalles técnicos..."/>
                        </div>
                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-4 h-4 inline mr-1"/> Observación de la Herramienta</Label>
                            <Textarea id="observacion" v-model="form.observacion" placeholder="Notas adicionales..."/>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <Button type="submit" class="flex-1 py-6 bg-blue-600 hover:bg-blue-700 font-bold shadow-lg shadow-blue-100" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" />
                        <Save v-else class="w-5 h-5 mr-2" />
                        Guardar Herramienta
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
