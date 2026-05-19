<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    ArrowLeft, Save, Trash2, Book, CreditCard,
    User, Phone, Calendar, Hash, Loader2,
    PencilLine, Users, BookMarked, ShieldCheck
} from 'lucide-vue-next';
import borrowerRoutes from '@/routes/borrowers';

const props = defineProps<{
    borrower: any;
    subjects: any[];
    subjectTeachers: any[];
}>();

// ── Helper para recortar fechas ISO a YYYY-MM-DD ──────────────────
const toDateInput = (val: string | null): string => {
    if (!val) return '';
    return val.split('T')[0]; // "2025-01-15T00:00:00Z" → "2025-01-15"
};

// ── Formulario principal ──────────────────────────────────────────
const form = useForm({
    cedula_identidad: props.borrower.cedula_identidad ?? '',
    nombres:          props.borrower.nombres          ?? '',
    apellidoPaterno:  props.borrower.apellidoPaterno  ?? '', // ← corregido (estaban invertidos)
    apellidoMaterno:  props.borrower.apellidoMaterno  ?? '', // ← corregido
    celular:          props.borrower.celular          ?? '',

    // Docente
    titulo:    props.borrower.teacher?.titulo    ?? '',
    categoria: props.borrower.teacher?.categoria
            ?? props.borrower.assistant?.categoria
            ?? 'Titular',

    // Auxiliar — fechas recortadas a YYYY-MM-DD
    fecha_inicio: toDateInput(props.borrower.assistant?.fecha_inicio ?? null),
    fecha_fin:    toDateInput(props.borrower.assistant?.fecha_fin    ?? null),

    // Para agregar nueva materia
    subject_id:         '' as string | number,
    paralelo:           'A',
    subject_teacher_id: '' as string | number,
});

const submitUpdate = () => {
    form.put(borrowerRoutes.update.url(props.borrower.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.subject_id         = '';
            form.subject_teacher_id = '';
            form.paralelo           = 'A';
        },
    });
};

// ── Quitar materia ────────────────────────────────────────────────
const removeMateria = (id: number) => {
    if (!confirm('¿Seguro que desea quitar esta asignación?')) return;

    if (props.borrower.teacher) {
        router.delete(
            (borrowerRoutes as any).removeSubjectTeacher?.url(props.borrower.id)
            ?? `/dashboard/borrowers/${props.borrower.id}/subject-teacher`,
            {
                data: { subject_teacher_id: id },
                preserveScroll: true,
            }
        );
    } else {
        router.delete(
            (borrowerRoutes as any).removeSubjectAssistant?.url(props.borrower.id)
            ?? `/dashboard/borrowers/${props.borrower.id}/subject-assistant`,
            {
                data: { assistant_subject_id: id },
                preserveScroll: true,
            }
        );
    }
};
</script>

<template>
    <Head :title="'Editar — ' + borrower.nombres" />
    <AppLayout>
        <div class="max-w-5xl mx-auto p-4 w-full">

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
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Editar Responsable</h2>
                    <p class="text-neutral-500">
                        Actualizando información del
                        <span :class="borrower.teacher ? 'text-[#1a3a5a]' : 'text-emerald-600'" class="font-bold">
                            {{ borrower.teacher ? 'Docente' : 'Auxiliar' }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">

                <!-- ── Columna izquierda: formulario ── -->
                <div class="lg:col-span-2 space-y-6">
                    <form @submit.prevent="submitUpdate" class="space-y-6">

                        <!-- Información Personal -->
                        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                            <h3 class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                                <PencilLine class="w-5 h-5"/> Información Personal
                            </h3>

                            <!-- Nombres -->
                            <div class="space-y-2">
                                <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                    <User class="w-4 h-4 text-[#1a3a5a]"/> Nombre(s)
                                </Label>
                                <Input v-model="form.nombres" type="text"
                                    class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a]" />
                                <InputError :message="form.errors.nombres" />
                            </div>

                            <!-- Apellidos -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <User class="w-4 h-4 text-[#1a3a5a]"/> Apellido Paterno
                                    </Label>
                                    <Input v-model="form.apellidoPaterno" type="text"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a]" />
                                        <InputError :message="form.errors.apellidoPaterno" />
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <User class="w-4 h-4 text-[#1a3a5a]"/> Apellido Materno
                                        <span class="text-neutral-400 text-xs font-normal">(opcional)</span>
                                    </Label>
                                    <Input v-model="form.apellidoMaterno" type="text"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a]" />
                                    <InputError :message="form.errors.apellidoMaterno" />
                                </div>
                            </div>

                            <!-- CI y Celular -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <CreditCard class="w-4 h-4 text-[#1a3a5a]"/> Cédula de Identidad
                                    </Label>
                                    <Input v-model="form.cedula_identidad" type="text"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a]" />
                                    <p v-if="form.errors.cedula_identidad" class="text-red-500 text-xs">{{ form.errors.cedula_identidad }}</p>
                                    <InputError :message="form.errors.cedula_identidad" />
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <Phone class="w-4 h-4 text-[#1a3a5a]"/> Celular
                                    </Label>
                                    <Input v-model="form.celular" type="text"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a]" />
                                    <InputError :message="form.errors.celular" />
                                </div>
                            </div>
                        </div>

                        <!-- ── DOCENTE: título + categoría ── -->
                        <div v-if="borrower.teacher"
                            class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                            <h3 class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                                <ShieldCheck class="w-5 h-5"/> Datos del Docente
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-neutral-700">Título</Label>
                                    <Input v-model="form.titulo" type="text" placeholder="Ej. Lic., Ing., M.Sc."
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a]" />
                                    <InputError :message="form.errors.titulo" />
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-neutral-700">Categoría</Label>
                                    <select v-model="form.categoria"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a]">
                                        <option value="Titular">Titular</option>
                                        <option value="Invitado">Invitado</option>
                                    </select>
                                    <InputError :message="form.errors.categoria" />
                                </div>
                            </div>
                        </div>

                        <!-- ── AUXILIAR: categoría + fechas ── -->
                        <div v-if="borrower.assistant"
                            class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                            <h3 class="text-lg font-semibold text-emerald-700 flex items-center gap-2">
                                <ShieldCheck class="w-5 h-5"/> Datos de Auxiliatura
                            </h3>

                            <div class="space-y-2">
                                <Label class="text-sm font-medium text-neutral-700">Categoría</Label>
                                <select v-model="form.categoria"
                                    class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                    <option value="Titular">Titular</option>
                                    <option value="Invitado">Invitado</option>
                                </select>
                                <InputError :message="form.errors.categoria" />
                            </div>

                            <div class="grid md:grid-cols-2 gap-4 pt-2 border-t border-dashed border-neutral-100">
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <Calendar class="w-4 h-4 text-emerald-600"/> Inicio Designación
                                    </Label>
                                    <Input v-model="form.fecha_inicio" type="date"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" />
                                    <InputError :message="form.errors.fecha_inicio" />
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <Calendar class="w-4 h-4 text-red-500"/> Fin Designación
                                    </Label>
                                    <Input v-model="form.fecha_fin" type="date"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500" />
                                    <InputError :message="form.errors.fecha_fin" />
                                </div>
                            </div>
                        </div>

                        <!-- ── Asignar nueva materia ── -->
                        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                            <h3 class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                                <BookMarked class="w-5 h-5"/> Asignar nueva materia
                            </h3>

                            <!-- Docente -->
                            <div v-if="borrower.teacher" class="grid md:grid-cols-3 gap-4">
                                <div class="md:col-span-2 space-y-2">
                                    <Label class="text-sm font-medium text-neutral-700">Materia</Label>
                                    <select v-model="form.subject_id"
                                        class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5a]/20">
                                        <option value="">Seleccionar materia...</option>
                                        <option v-for="s in subjects" :key="s.id" :value="s.id">
                                            [{{ s.sigla }}] {{ s.nombre_materia }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.nombres" />
                                </div>
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium flex items-center gap-1 text-neutral-700">
                                        <Hash class="w-4 h-4 text-[#1a3a5a]"/> Paralelo
                                    </Label>
                                    <Input v-model="form.paralelo" type="text" placeholder="Ej. A"
                                        class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5a]/20" />
                                    <InputError :message="form.errors.paralelo" />
                                </div>
                            </div>

                            <!-- Auxiliar -->
                            <div v-if="borrower.assistant" class="space-y-2">
                                <Label class="text-sm font-medium text-neutral-700">Docente — Materia</Label>
                                <select v-model="form.subject_teacher_id"
                                    class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm outline-none focus:ring-2 focus:ring-emerald-600/20">
                                    <option value="">Vincular a Docente y Materia...</option>
                                    <option v-for="st in subjectTeachers" :key="st.id" :value="st.id">
                                        {{ st.teacher?.borrower?.apellidoPaterno }}
                                        {{ st.teacher?.borrower?.nombres }}
                                        — {{ st.subject?.sigla }} (Par. {{ st.paralelo }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.subject_teacher_id" />
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <Link
                                :href="borrowerRoutes.index.url()"
                                class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-500 rounded-2xl font-semibold text-[18px] hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="h-14 bg-[#1a3a5a] text-white rounded-2xl font-semibold text-[18px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full flex items-center justify-center gap-2"
                            >
                                <Loader2 v-if="form.processing" class="w-6 h-6 animate-spin" />
                                <Save v-else class="w-6 h-6" />
                                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ── Columna derecha: materias actuales ── -->
                <div class="space-y-6">
                    <div class="bg-neutral-50 p-6 rounded-3xl border border-dashed border-neutral-300">
                        <h3 class="font-black text-xs uppercase tracking-widest text-neutral-400 mb-4 flex items-center gap-2">
                            <Book class="w-4 h-4"/> Materias Actuales
                        </h3>

                        <!-- Docente: subject_teachers -->
                        <div v-if="borrower.teacher" class="space-y-3">
                            <div
                                v-for="st in borrower.teacher.subject_teachers ?? []"
                                :key="st.id"
                                class="flex items-center justify-between p-4 bg-white rounded-2xl border shadow-sm"
                            >
                                <div class="overflow-hidden">
                                    <p class="text-[10px] font-black text-blue-700 uppercase">{{ st.subject?.sigla }}</p>
                                    <p class="text-xs font-bold text-neutral-800 leading-tight truncate">{{ st.subject?.nombre_materia }}</p>
                                    <p class="text-[10px] text-neutral-400 font-bold">Par. {{ st.paralelo }}</p>
                                </div>
                                <button
                                    type="button"
                                    @click="removeMateria(st.id)"
                                    class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-all active:scale-90"
                                >
                                    <Trash2 class="w-5 h-5"/>
                                </button>
                            </div>
                        </div>

                        <!-- Auxiliar: subject_teachers (via assistant_subject) -->
                        <div v-if="borrower.assistant" class="space-y-3">
                            <div
                                v-for="st in borrower.assistant.subject_teachers ?? []"
                                :key="st.id"
                                class="flex items-center justify-between p-4 bg-white rounded-2xl border shadow-sm"
                            >
                                <div class="overflow-hidden">
                                    <p class="text-[10px] font-black text-emerald-700 uppercase">{{ st.subject?.sigla }}</p>
                                    <p class="text-xs font-bold text-neutral-800 leading-tight truncate">{{ st.subject?.nombre_materia }}</p>
                                    <p class="text-[10px] text-neutral-400 font-bold">Par. {{ st.paralelo }}</p>
                                </div>
                                <button
                                    type="button"
                                    @click="removeMateria(st.pivot?.id ?? st.id)"
                                    class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-all active:scale-90"
                                >
                                    <Trash2 class="w-5 h-5"/>
                                </button>
                            </div>
                        </div>

                        <!-- Sin materias -->
                        <div
                            v-if="!(borrower.teacher?.subject_teachers?.length) && !(borrower.assistant?.subject_teachers?.length)"
                            class="text-center py-12"
                        >
                            <Book class="w-12 h-12 text-neutral-200 mx-auto mb-2" />
                            <p class="text-xs text-neutral-400 italic font-medium">Sin materias asignadas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
