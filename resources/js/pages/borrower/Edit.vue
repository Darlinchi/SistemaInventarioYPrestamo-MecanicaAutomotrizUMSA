<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import {
    ArrowLeft, Save, Trash2, Book, CreditCard,
    User, Phone, GraduationCap, Calendar, Hash, Loader2,
    PencilLine, Users, BookMarked
} from 'lucide-vue-next';

// Rutas Wyfinder
import borrowerRoutes from '@/routes/borrowers';

const props = defineProps<{
    borrower: any;
    subjects: any[];
    subjectTeachers: any[];
}>();

const form = useForm({
    cedula_identidad: props.borrower.cedula_identidad,
    nombres: props.borrower.nombres,
    apellidos: props.borrower.apellidos,
    telefono: props.borrower.telefono,

    // Datos específicos de Auxiliar
    registro_universitario: props.borrower.assistant?.registro_universitario || '',
    fecha_inicio: props.borrower.assistant?.fecha_inicio || '',
    fecha_fin: props.borrower.assistant?.fecha_fin || '',

    // Para agregar nueva materia en la edición
    subject_id: '',
    paralelo: 'A',
    subject_teacher_id: '',
});

const submitUpdate = () => {
    const url = borrowerRoutes.update.url(props.borrower.id);
    form.put(url, {
        preserveScroll: true,
        onSuccess: () => {
            form.subject_id = '';
            form.subject_teacher_id = '';
        }
    });
};

const removeMateria = (id: number) => {
    if(confirm('¿Seguro que desea quitar esta asignación?')) {
        let url = '';
        let payload = {};

        if (props.borrower.teacher) {
            url = (borrowerRoutes as any).removeSubjectTeacher?.url(props.borrower.id)
                  || `/dashboard/borrowers/${props.borrower.id}/remove-teacher-subject`;
            payload = { subject_teacher_id: id };
        } else {
            url = (borrowerRoutes as any).removeSubjectAssistant?.url(props.borrower.id)
                  || `/dashboard/borrowers/${props.borrower.id}/remove-assistant-subject`;
            payload = { assistant_subject_id: id };
        }
        router.post(url, payload, { preserveScroll: true });
    }
};
</script>

<template>
    <Head :title="'Editar ' + borrower.nombres" />
    <AppLayout>
        <div class="max-w-5xl mx-auto p-4 w-full">

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
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">Editar Responsable</h2>
                    <p class="text-neutral-500">Actualice la información del {{ borrower.teacher ? 'Docente' : 'Auxiliar' }}</p>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <form @submit.prevent="submitUpdate" class="space-y-6">
                        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                            <h3 class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                                <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Información Personal
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <User class="w-4 h-4 text-[#1a3a5a]"/> Nombre(s)
                                    </label>
                                    <input v-model="form.nombres" type="text" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a] focus-visible:ring-offset-2" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <User class="w-4 h-4 text-[#1a3a5a]"/> Apellido(s)
                                    </label>
                                    <input v-model="form.apellidos" type="text" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a] focus-visible:ring-offset-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <CreditCard class="w-4 h-4 text-[#1a3a5a]"/> Cédula de Identidad
                                    </label>
                                    <input v-model="form.cedula_identidad" type="text" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a] focus-visible:ring-offset-2" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                        <Phone class="w-4 h-4 text-[#1a3a5a]"/> Teléfono
                                    </label>
                                    <input v-model="form.telefono" type="text" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1a3a5a] focus-visible:ring-offset-2" />
                                </div>
                            </div>

                            <div v-if="borrower.assistant" class="pt-4 space-y-5 border-t border-dashed border-neutral-100">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium flex items-center gap-2 text-emerald-700">
                                        <GraduationCap class="w-4 h-4 text-emerald-600"/> Registro Universitario
                                    </label>
                                    <input v-model="form.registro_universitario" type="text" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2" />
                                </div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                            <Calendar class="w-4 h-4 text-emerald-600"/> Inicio Designación
                                        </label>
                                        <input v-model="form.fecha_inicio" type="date" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2" />
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                            <Calendar class="w-4 h-4 text-red-600"/> Fin Designación
                                        </label>
                                        <input v-model="form.fecha_fin" type="date" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-sm space-y-5">
                            <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                                <BookMarked class="w-5 h-5 text-[#1a3a5a]"/> Asignar nueva materia
                            </CardTitle>

                            <div v-if="borrower.teacher" class="grid md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <select v-model="form.subject_id" class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5a]/20">
                                        <option value="">Seleccionar Materia...</option>
                                        <option v-for="s in subjects" :key="s.id" :value="s.id">[{{ s.sigla }}] {{ s.nombre_materia }}</option>
                                    </select>
                                </div>
                                <input v-model="form.paralelo" type="text" placeholder="Paralelo" class="flex h-10 w-full rounded-md border border-neutral-200 bg-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#1a3a5a]/20" />
                            </div>

                            <div v-if="borrower.assistant">
                                <select v-model="form.subject_teacher_id" class="w-full px-4 py-2.5 rounded-xl border border-neutral-200 bg-neutral-50/50 text-sm outline-none focus:ring-2 focus:ring-emerald-600/20">
                                    <option value="">Vincular a Docente y Materia...</option>
                                    <option v-for="st in subjectTeachers" :key="st.id" :value="st.id">
                                        {{ st.teacher?.borrower?.apellidos }} - {{ st.subject?.sigla }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                            <Link
                                :href="borrowerRoutes.index.url()"
                                class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-500 rounded-2xl font-semibold text-[20px] hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                            >
                                Cancelar
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="h-14 bg-[#1a3a5a] text-white rounded-2xl font-semibold text-[20px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full flex items-center justify-center gap-2"
                            >
                                <Loader2 v-if="form.processing" class="w-6 h-6 animate-spin" />
                                <Save v-else class="w-6 h-6" />
                                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="space-y-6">
                    <div class="bg-neutral-50 p-6 rounded-3xl border border-dashed border-neutral-300">
                        <h3 class="font-black text-xs uppercase tracking-widest text-neutral-400 mb-4 flex items-center gap-2">
                            <Book class="w-4 h-4"/> Materias Actuales
                        </h3>

                        <div v-if="borrower.teacher" class="space-y-3">
                            <div v-for="st in borrower.teacher.subject_teachers || []" :key="st.id"
                                class="flex items-center justify-between p-4 bg-white rounded-2xl border shadow-sm group">
                                <div class="overflow-hidden">
                                    <p class="text-[10px] font-black text-blue-700 uppercase">{{ st.subject?.sigla }}</p>
                                    <p class="text-xs font-bold text-neutral-800 leading-tight truncate">{{ st.subject?.nombre_materia }}</p>
                                    <p class="text-[10px] text-neutral-400 font-bold">Paralelo: {{ st.paralelo }}</p>
                                </div>
                                <button @click="removeMateria(st.id)" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-all active:scale-90">
                                    <Trash2 class="w-5 h-5"/>
                                </button>
                            </div>
                        </div>

                        <div v-if="borrower.assistant" class="space-y-3">
                            <div v-for="st in borrower.assistant.subject_teachers || []" :key="st.id"
                                class="flex items-center justify-between p-4 bg-white rounded-2xl border shadow-sm group">
                                <div class="overflow-hidden">
                                    <p class="text-[10px] font-black text-emerald-700 uppercase">{{ st.subject?.sigla }}</p>
                                    <p class="text-xs font-bold text-neutral-800 leading-tight truncate">{{ st.subject?.nombre_materia }}</p>
                                </div>
                                <button @click="removeMateria(st.id)" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-xl transition-all active:scale-90">
                                    <Trash2 class="w-5 h-5"/>
                                </button>
                            </div>
                        </div>

                        <div v-if="(!borrower.teacher?.subject_teachers?.length) && (!borrower.assistant?.subject_teachers?.length)"
                            class="text-center py-12">
                            <div class="mb-2 flex justify-center text-neutral-200">
                                <Book class="w-12 h-12" />
                            </div>
                            <p class="text-xs text-neutral-400 italic font-medium">No hay materias asignadas actualmente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
