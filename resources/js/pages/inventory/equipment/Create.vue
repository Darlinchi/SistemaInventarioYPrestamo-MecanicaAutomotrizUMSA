<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { FileInput } from '@/components/ui/file-input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import CreateActionButton from '@/components/CreateActionButton.vue';
import { ref } from 'vue';
import equipmentRoutes from '@/routes/equipments';
import itemsRoutes from '@/routes/items';
import {
    Plus, Trash2, ArrowLeft, Save, Loader2, Package, QrCode,
    Rows3, ImageUp, ListPlus, AlignLeft, Hash, PencilLine,
    PaintBucket, BookText, CalendarDays
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Inventario', href: itemsRoutes.index.url() },
    { title: 'Registrar Equipo', href: equipmentRoutes.create.url() },
];

const photoPreview = ref<string | null>(null);

// Detectar si venimos desde una reposición por Reemplazo
const urlParams_init = new URLSearchParams(window.location.search);
const repositionId   = urlParams_init.get('reposition_id') || '';
const esReemplazo    = !!repositionId;

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
    accesorios: [] as { nombre: string; estado: string; foto: File | null; preview: string | null }[],
    reposition_id: repositionId,  // para marcar la reposición Cumplida al guardar
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

// NUEVA función para manejar fotos de accesorios individuales
const handleAccFileChange = (e: Event, index: number) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.accesorios[index].foto = file;

        const reader = new FileReader();
        reader.onload = (event) => {
            form.accesorios[index].preview = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const addAccesorio = () => {
    form.accesorios.push({
        nombre: '',
        estado: 'Bueno',
        foto: null,
        preview: null
    });
};

const removeAccesorio = (index: number) => form.accesorios.splice(index, 1);

const urlParams = new URLSearchParams(window.location.search);
const fromTab = urlParams.get('tab') || 'herramientas';

function submit() {
    // Creamos una copia para no alterar el formulario original en la vista
    form.post(equipmentRoutes.store.url(), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            photoPreview.value = null;
        },
        onError: (errors) => {
            console.error("Errores detectados:", errors);
            // Esto forzará a que el botón deje de decir "Guardando..."
        },
        onFinish: () => {
            // Esto se ejecuta siempre, falle o gane
            form.processing = false;
        }
    });
}
</script>

<template>
    <Head title="Registrar Equipo" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-4 w-full">
            <!-- Banner de contexto: reposición por reemplazo -->
            <div
                v-if="esReemplazo"
                class="mb-5 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4"
            >
                <span class="text-2xl">🔁</span>
                <div>
                    <p class="text-sm font-black text-amber-800">Registrando equipo de reemplazo</p>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Al guardar este equipo, la reposición quedará marcada automáticamente como
                        <strong>Cumplida</strong> y serás redirigido a Reposiciones.
                    </p>
                </div>
            </div>

            <div class="mb-6">
                <Link
                    :href="esReemplazo ? '/dashboard/repositions' : itemsRoutes.index.url() + '?tab=' + fromTab"
                    class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group"
                >
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    {{ esReemplazo ? 'Volver a Reposiciones' : 'Volver al inventario' }}
                </Link>
            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 bg-[#1a3a5a] rounded-2xl shadow-lg shadow-blue-900/20">
                    <Package class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Registrar Equipo</h2>
                    <p class="text-neutral-500">Registro de activos y equipos de diagnóstico</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Datos Generales
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="codigo_qr"><QrCode class="w-5 h-5 text-[#1a3a5a]"/> Código QR</Label>
                            <Input id="codigo_qr" v-model="form.codigo_qr" placeholder="Ej. EQ-001" />
                            <InputError :message="form.errors.codigo_qr" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="nombre"><Package class="w-5 h-5 text-[#1a3a5a]"/> Nombre del Equipo</Label>
                            <Input id="nombre" v-model="form.nombre" placeholder="Ej. Escáner Automotriz" />
                            <InputError :message="form.errors.nombre" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="ubicacion"><Rows3 class="w-5 h-5 text-[#1a3a5a]"/> Ubicación en Taller</Label>
                            <Input id="ubicacion" v-model="form.ubicacion" placeholder="Ej. Estante A-1"/>
                            <InputError :message="form.errors.ubicacion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="estado_equipo"><Rows3 class="w-5 h-5 text-[#1a3a5a]"/> Estado del Equipo</Label>
                            <select v-model="form.estado_equipo" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                <option value="Disponible">Disponible</option>
                                <option value="Nuevo">Nuevo</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label class="mb-4 flex items-center gap-2">
                            <ImageUp class="w-5 h-5 text-[#1a3a5a]"/> Fotografía del Equipo
                        </Label>

                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div v-if="photoPreview" class="relative group">
                                <img :src="photoPreview"
                                    class="w-40 h-40 object-cover rounded-3xl shadow-lg border-2 border-white ring-1 ring-[#1a3a5a]/5" />

                                <button
                                    type="button"
                                    @click="photoPreview = null; form.foto = null"
                                    class="absolute -top-2 -right-2 bg-[#d90000] text-white rounded-full p-1.5 shadow-lg hover:bg-red-600 transition-all hover:scale-110"
                                    title="Quitar imagen"
                                >
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="serie"><Hash class="w-5 h-5 text-[#1a3a5a]"/> Número de Serie</Label>
                            <Input id="serie" v-model="form.serie" placeholder="Ej. XYZ1234"/>
                            <InputError :message="form.errors.serie" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="marca"><Package class="w-5 h-5 text-[#1a3a5a]"/> Marca</Label>
                            <Input id="marca" v-model="form.marca" placeholder="Ej. Launch"/>
                            <InputError :message="form.errors.marca" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="modelo"><Package class="w-5 h-5 text-[#1a3a5a]"/> Modelo</Label>
                            <Input id="modelo" v-model="form.modelo" placeholder="Ej. X123"/>
                            <InputError :message="form.errors.modelo" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="fecha_adquisicion"><CalendarDays class="w-5 h-5 text-[#1a3a5a]"/> Fecha de Adquisición</Label>
                            <Input id="fecha_adquisicion" v-model="form.fecha_adquisicion" type="date"/>
                            <InputError :message="form.errors.fecha_adquisicion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="rubro"><BookText class="w-5 h-5 text-[#1a3a5a]"/> Rubro</Label>
                            <Input id="rubro" v-model="form.rubro" placeholder="Ej. Equipos de diagnóstico"/>
                            <InputError :message="form.errors.rubro" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="color"><PaintBucket class="w-5 h-5 text-[#1a3a5a]"/> Color</Label>
                            <Input id="color" v-model="form.color" type="text" placeholder="Ej. Negro"/>
                            <InputError :message="form.errors.color" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="descripcion"><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Descripción del Equipo</Label>
                            <Textarea id="descripcion" v-model="form.descripcion" placeholder="Detalles técnicos..."/>
                            <InputError :message="form.errors.descripcion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="observacion"><AlignLeft class="w-5 h-5 text-[#1a3a5a]"/> Observación del Equipo</Label>
                            <Textarea id="observacion" v-model="form.observacion" placeholder="Notas adicionales..."/>
                            <InputError :message="form.errors.observacion" />
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b pb-2">
                        <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                            <ListPlus class="w-5 h-5 text-[#1a3a5a]"/> Accesorios del Equipo
                        </CardTitle>
                        <Button type="button" variant="outline" size="sm" @click="addAccesorio">
                            <Plus class="w-4 h-4 mr-1" /> Agregar Accesorio
                        </Button>
                    </div>

                    <div v-for="(acc, index) in form.accesorios" :key="index" class="flex flex-col gap-4 p-4 bg-neutral-50 rounded-lg border border-neutral-200">
                        <div class="flex gap-4 items-start">
                            <div class="flex flex-col items-center gap-2">
                                <div v-if="acc.preview" class="relative">
                                    <img :src="acc.preview" class="w-20 h-20 object-cover rounded-xl border border-neutral-300 shadow-sm" />
                                    <button
                                        type="button"
                                        @click="acc.preview = null; acc.foto = null"
                                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors"
                                    >
                                        <Trash2 class="w-3 h-3"/>
                                    </button>
                                </div>
                                <div v-else class="w-20 h-20 rounded-xl bg-white border-2 border-dashed border-neutral-200 flex items-center justify-center text-neutral-300">
                                    <ImageUp class="w-6 h-6" />
                                </div>

                                <label :for="'acc_foto_' + index" class="text-[10px] font-bold text-[#1a3a5a] cursor-pointer hover:underline">
                                    SUBIR FOTO
                                </label>
                                <input
                                    :id="'acc_foto_' + index"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleAccFileChange($event, index)"
                                />
                            </div>

                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="grid gap-1.5">
                                    <Label :for="'acc_nombre_' + index" class="text-xs font-semibold text-neutral-700">Nombre</Label>
                                    <Input :id="'acc_nombre_' + index" v-model="acc.nombre" placeholder="Ej. Cargador" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs font-semibold text-neutral-700">Estado</Label>
                                    <select v-model="acc.estado" class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm">
                                        <option value="Bueno">Bueno</option>
                                        <option value="Dañado">Dañado</option>
                                        <option value="Extraviado">Extraviado</option>
                                    </select>
                                </div>
                            </div>

                            <Button type="button" variant="destructive" size="icon" class="mt-6" @click="removeAccesorio(index)">
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors[`accesorios.${index}.foto`]" />
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
                        class="h-14 bg-[#1a3a5a] text-white rounded-xl font-semibold text-[20px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full"
                    >
                        <template v-if="form.processing">
                            <Loader2 class="w-5 h-5 animate-spin mr-2" />
                            Guardando...
                        </template>
                        <template v-else>
                            Registrar Equipo
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
