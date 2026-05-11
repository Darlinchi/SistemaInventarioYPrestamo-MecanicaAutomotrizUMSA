<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    UserCog, UserCheck, ArrowLeft, Save, Loader2, Calendar,
    CreditCard, User, Phone, Book, GraduationCap, Hash, PencilLine,
    Users
} from 'lucide-vue-next';

// IMPORTANTE: Importamos tus rutas de wyfinder
import borrowerRoutes from '@/routes/borrowers';

const props = defineProps<{
    subjects: any[];
    subjectTeachers: any[];
}>();

const tipoResponsable = ref<'docente' | 'auxiliar'>('docente');

const form = useForm({
    tipo: 'docente' as 'docente' | 'auxiliar',
    cedula_identidad: '',
    nombres: '',
    apellidos: '',
    telefono: '',
    // Campos Docente
    subject_id: '',
    paralelo: 'A',
    // Campos Auxiliar
    registro_universitario: '',
    subject_teacher_id: '',
    fecha_inicio: '',
    fecha_fin: '',
});

const setTipo = (tipo: 'docente' | 'auxiliar') => {
    tipoResponsable.value = tipo;
    form.tipo = tipo;
    form.clearErrors();
};

const submit = () => {
    form.post(borrowerRoutes.store.url(), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nuevo Responsable" />
    <AppLayout>
        <div class="max-w-2xl mx-auto p-4 w-full">

            <div class="mb-6">
                <Link
                    :href="borrowerRoutes.index.url()"
                    class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group"
                >
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    Volver al listado
                </Link>
            </div>

            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 bg-[#1a3a5a] rounded-2xl shadow-lg shadow-blue-900/20">
                    <Users class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Registrar Responsable</h2>
                    <p class="text-neutral-500">Registre un nuevo docente o auxiliar autorizado para préstamos</p>
                </div>
            </div>

            <div class="flex gap-4 mb-8 bg-neutral-100 p-1.5 rounded-2xl w-fit border border-neutral-200 shadow-sm">
                <button type="button" @click="setTipo('docente')"
                    :class="['flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold transition-all',
                    tipoResponsable === 'docente' ? 'bg-white shadow-sm text-[#1a3a5a]' : 'text-neutral-500 hover:text-black']">
                    <UserCog class="w-5 h-5"/> Docente
                </button>
                <button type="button" @click="setTipo('auxiliar')"
                    :class="['flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold transition-all',
                    tipoResponsable === 'auxiliar' ? 'bg-white shadow-sm text-emerald-600' : 'text-neutral-500 hover:text-black']">
                    <UserCheck class="w-5 h-5"/> Auxiliar
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Información Personal
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="nombres" class="flex items-center gap-2">
                                <User class="w-5 h-5 text-[#1a3a5a]"/> Nombre(s)
                            </Label>
                            <Input id="nombres" v-model="form.nombres" placeholder="Ej. Juan Pedro" />
                            <InputError :message="form.errors.nombres" />
                        </div>
                        <div class="space-y-2">
                            <Label for="apellidos" class="flex items-center gap-2">
                                <User class="w-5 h-5 text-[#1a3a5a]"/> Apellido(s)
                            </Label>
                            <Input id="apellidos" v-model="form.apellidos" placeholder="Ej. Perez Garcia" />
                            <InputError :message="form.errors.apellidos" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="cedula_identidad" class="flex items-center gap-2">
                                <CreditCard class="w-5 h-5 text-[#1a3a5a]"/> Cédula de Identidad
                            </Label>
                            <Input id="cedula_identidad" v-model="form.cedula_identidad" placeholder="Ej. 1234567" />
                            <InputError :message="form.errors.cedula_identidad" />
                        </div>
                        <div class="space-y-2">
                            <Label for="telefono" class="flex items-center gap-2">
                                <Phone class="w-5 h-5 text-[#1a3a5a]"/> Teléfono
                            </Label>
                            <Input id="telefono" v-model="form.telefono" type="text" placeholder="Ej. 78945612" />
                            <InputError :message="form.errors.telefono" />
                        </div>
                    </div>
                </div>

                <div v-if="tipoResponsable === 'docente'" class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <Book class="w-5 h-5 text-[#1a3a5a]"/> Asignación de Materia
                    </CardTitle>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2 space-y-2">
                            <Label for="subject_id" class="flex items-center gap-2">
                                Materia
                            </Label>
                            <select v-model="form.subject_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5 outline-none transition-all">
                                <option value="">Seleccione una materia</option>
                                <option v-for="s in subjects" :key="s.id" :value="s.id">[{{ s.sigla }}] {{ s.nombre_materia }}</option>
                            </select>
                            <InputError :message="form.errors.subject_id" />
                        </div>
                        <div class="space-y-2">
                            <Label for="paralelo" class="flex items-center gap-2">
                                <Hash class="w-5 h-5 text-[#1a3a5a]"/> Paralelo
                            </Label>
                            <Input id="paralelo" v-model="form.paralelo" type="text" placeholder="Ej. A" />
                            <InputError :message="form.errors.paralelo" />
                        </div>
                    </div>
                </div>

                <div v-if="tipoResponsable === 'auxiliar'" class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                    <CardTitle class="text-lg font-semibold text-emerald-700 flex items-center gap-2">
                        <UserCheck class="w-5 h-5 text-emerald-600"/> Datos de Auxiliatura
                    </CardTitle>
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="registro_universitario" class="flex items-center gap-2 font-medium">
                                    R.U.
                                </Label>
                                <Input id="registro_universitario" v-model="form.registro_universitario" type="text" placeholder="87654321" />
                                <InputError :message="form.errors.registro_universitario" />
                            </div>
                            <div class="space-y-2">
                                <Label for="subject_teacher_id" class="flex items-center gap-2 font-medium">
                                    Docente / Materia
                                </Label>
                                <select v-model="form.subject_teacher_id"
                                    class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5 outline-none transition-all">
                                    <option value="">Seleccione la asignación</option>
                                    <option v-for="st in subjectTeachers" :key="st.id" :value="st.id">
                                        {{ st.teacher.borrower.apellidos }} - {{ st.subject.sigla }} (Par. {{ st.paralelo }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.subject_teacher_id" />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 pt-2 border-t border-dashed border-neutral-100">
                            <div class="space-y-2">
                                <Label for="fecha_inicio" class="flex items-center gap-2 font-medium">
                                    <Calendar class="w-5 h-5 text-emerald-600"/> Inicio Designación
                                </Label>
                                <Input id="fecha_inicio" v-model="form.fecha_inicio" type="date" class="w-full" />
                                <InputError :message="form.errors.fecha_inicio" />
                            </div>

                            <div class="space-y-2">
                                <Label for="fecha_fin" class="flex items-center gap-2 font-medium">
                                    <Calendar class="w-5 h-5 text-red-600"/> Fin Designación
                                </Label>
                                <Input id="fecha_fin" v-model="form.fecha_fin" type="date" class="w-full" />
                                <InputError :message="form.errors.fecha_fin" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 justify-end gap-4 w-full pt-4">
                    <Link
                        :href="borrowerRoutes.index.url()"
                        class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-500 rounded-2xl font-semibold text-[20px] hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                    >
                        Cancelar
                    </Link>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="h-14 bg-[#1a3a5a] text-white rounded-2xl font-semibold text-[20px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full"
                    >
                        <template v-if="form.processing">
                            <Loader2 class="w-5 h-5 animate-spin mr-2" />
                            Guardando...
                        </template>
                        <template v-else>
                            <Save class="w-5 h-5 mr-2" />
                            Guardar Responsable
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
