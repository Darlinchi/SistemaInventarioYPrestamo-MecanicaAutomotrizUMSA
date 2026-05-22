<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    UserCog, UserCheck, ArrowLeft, Save, Loader2, Calendar,
    CreditCard, User, Phone, Book, Hash, Users, ShieldCheck
} from 'lucide-vue-next';
import borrowerRoutes from '@/routes/borrowers';

const props = defineProps<{
    subjects: any[];
    subjectTeachers: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Responsables', href: borrowerRoutes.index.url() },
    { title: 'Registrar Responsable', href: borrowerRoutes.create.url() },
];

const tipoResponsable = ref<'docente' | 'auxiliar'>('docente');

const form = useForm({
    tipo:             'docente' as 'docente' | 'auxiliar',
    cedula_identidad: '',
    nombres:          '',
    apellidoPaterno:  '',
    apellidoMaterno:  '',
    celular:          '',
    // Docente
    titulo:     '',
    categoria:  'Titular' as 'Titular' | 'Invitado',
    subject_id: '' as string | number,
    paralelo:   'A',
    // Auxiliar
    registro_universitario: '',
    subject_teacher_id:     '' as string | number,
    fecha_inicio: '',
    fecha_fin:    '',
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
    <Head title="Registrar Responsable" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-4 w-full">

            <!-- Volver -->
            <div class="mb-6">
                <Link
                    :href="borrowerRoutes.index.url()"
                    class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group"
                >
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    Volver al listado
                </Link>
            </div>

            <!-- Header -->
            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 bg-[#1a3a5a] rounded-2xl shadow-lg shadow-blue-900/20">
                    <Users class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Registrar Responsable</h2>
                    <p class="text-neutral-500">Registre un nuevo docente o auxiliar autorizado para préstamos</p>
                </div>
            </div>

            <!-- Selector tipo -->
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

                <!-- ── Información Personal ── -->
                <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <User class="w-5 h-5 text-[#1a3a5a]"/> Información Personal
                    </CardTitle>

                    <!-- Nombres -->
                    <div class="grid gap-2">
                        <Label for="nombres" class="flex items-center gap-2">
                            <User class="w-4 h-4 text-[#1a3a5a]"/> Nombre(s)
                        </Label>
                        <Input id="nombres" v-model="form.nombres" placeholder="Ej. Juan Pedro" />
                        <InputError :message="form.errors.nombres" />
                    </div>

                    <!-- Apellidos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="apellidoPaterno" class="flex items-center gap-2">
                                <User class="w-4 h-4 text-[#1a3a5a]"/> Apellido Paterno
                            </Label>
                            <Input id="apellidoPaterno" v-model="form.apellidoPaterno" placeholder="Ej. Mamani" />
                            <InputError :message="form.errors.apellidoPaterno" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="apellidoMaterno" class="flex items-center gap-2">
                                <User class="w-4 h-4 text-[#1a3a5a]"/> Apellido Materno
                            </Label>
                            <Input id="apellidoMaterno" v-model="form.apellidoMaterno" placeholder="Ej. Quispe" />
                            <InputError :message="form.errors.apellidoMaterno" />
                        </div>
                    </div>

                    <!-- CI y Celular -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="cedula_identidad" class="flex items-center gap-2">
                                <CreditCard class="w-4 h-4 text-[#1a3a5a]"/> Cédula de Identidad
                            </Label>
                            <Input id="cedula_identidad" v-model="form.cedula_identidad" placeholder="Ej. 1234567 LP" />
                            <InputError :message="form.errors.cedula_identidad" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="celular" class="flex items-center gap-2">
                                <Phone class="w-4 h-4 text-[#1a3a5a]"/> Celular
                                <span class="text-neutral-400 text-xs font-normal">(opcional)</span>
                            </Label>
                            <Input id="celular" v-model="form.celular" placeholder="Ej. 78945612" />
                            <InputError :message="form.errors.celular" />
                        </div>
                    </div>
                </div>

                <!-- ── DOCENTE: Título, categoría, materia ── -->
                <div v-if="tipoResponsable === 'docente'"
                    class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <Book class="w-5 h-5 text-[#1a3a5a]"/> Datos del Docente
                    </CardTitle>

                    <!-- Título y categoría -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="titulo">
                                <ShieldCheck class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Título
                                <span class="text-neutral-400 text-xs font-normal ml-1">(opcional, Ej: Lic., Ing.)</span>
                            </Label>
                            <Input id="titulo" v-model="form.titulo" placeholder="Ej. Lic." />
                            <InputError :message="form.errors.titulo" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="categoria_doc">Categoría</Label>
                            <select id="categoria_doc" v-model="form.categoria"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm focus:border-[#1a3a5a] outline-none transition-all">
                                <option value="Titular">Titular</option>
                                <option value="Invitado">Invitado</option>
                            </select>
                            <InputError :message="form.errors.categoria" />
                        </div>
                    </div>

                    <!-- Materia y paralelo -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-dashed border-neutral-100">
                        <div class="md:col-span-2 grid gap-2">
                            <Label for="subject_id">Materia</Label>
                            <select id="subject_id" v-model="form.subject_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm focus:border-[#1a3a5a] outline-none transition-all">
                                <option value="">Seleccione una materia</option>
                                <option v-for="s in subjects" :key="s.id" :value="s.id">
                                    [{{ s.sigla }}] {{ s.nombre_materia }}
                                </option>
                            </select>
                            <InputError :message="form.errors.subject_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="paralelo">
                                <Hash class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Paralelo
                            </Label>
                            <Input id="paralelo" v-model="form.paralelo" placeholder="Ej. A" />
                            <InputError :message="form.errors.paralelo" />
                        </div>
                    </div>
                </div>

                <!-- ── AUXILIAR: RU, categoría, docente-materia, fechas ── -->
                <div v-if="tipoResponsable === 'auxiliar'"
                    class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                    <CardTitle class="text-lg font-semibold text-emerald-700 flex items-center gap-2">
                        <UserCheck class="w-5 h-5 text-emerald-600"/> Datos de Auxiliatura
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="subject_teacher_id">Docente — Materia</Label>
                            <select id="subject_teacher_id" v-model="form.subject_teacher_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm focus:border-emerald-500 outline-none transition-all">
                                <option value="">Seleccione la asignación</option>
                                <option v-for="st in subjectTeachers" :key="st.id" :value="st.id">
                                    {{ st.teacher?.borrower?.apellidoPaterno }} {{ st.teacher?.borrower?.nombres }}
                                    — {{ st.subject?.sigla }} (Par. {{ st.paralelo }})
                                </option>
                            </select>
                            <InputError :message="form.errors.subject_teacher_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="categoria_aux">Categoría</Label>
                            <select id="categoria_aux" v-model="form.categoria"
                                class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm focus:border-emerald-500 outline-none transition-all">
                                <option value="Titular">Titular</option>
                                <option value="Invitado">Invitado</option>
                            </select>
                            <InputError :message="form.errors.categoria" />
                        </div>
                    </div>


                    <div class="grid md:grid-cols-2 gap-4 pt-2 border-t border-dashed border-neutral-100">
                        <div class="grid gap-2">
                            <Label for="fecha_inicio" class="flex items-center gap-2">
                                <Calendar class="w-4 h-4 text-emerald-600"/> Inicio Designación
                            </Label>
                            <Input id="fecha_inicio" v-model="form.fecha_inicio" type="date" />
                            <InputError :message="form.errors.fecha_inicio" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="fecha_fin" class="flex items-center gap-2">
                                <Calendar class="w-4 h-4 text-red-500"/> Fin Designación
                            </Label>
                            <Input id="fecha_fin" v-model="form.fecha_fin" type="date" />
                            <InputError :message="form.errors.fecha_fin" />
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full pt-4">
                    <Link
                        :href="borrowerRoutes.index.url()"
                        class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-500 rounded-2xl font-semibold text-[18px] hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                    >
                        Cancelar
                    </Link>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="h-14 bg-[#1a3a5a] text-white rounded-2xl font-semibold text-[18px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full"
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
