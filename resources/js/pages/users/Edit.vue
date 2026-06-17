<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft, Save, Loader2, UserPen,
    User, Mail, KeyRound, ShieldCheck,
    AtSign, Power, RefreshCw, Hash, Phone
} from 'lucide-vue-next';

interface UserData {
    id: number;
    cedula_identidad: string;
    name: string;
    apellidoPaterno: string;
    apellidoMaterno: string;
    username: string;
    celular: string;
    email: string;
    activo: boolean;
    roles: string[];
}

const props = defineProps<{
    user: UserData;
    roles: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gestión de Personal', href: '/dashboard/usuarios' },
    { title: `Editar — ${props.user.name}`, href: '#' },
];

const form = useForm({
    cedula_identidad: props.user.cedula_identidad ?? '',
    name:             props.user.name,
    apellidoPaterno:  props.user.apellidoPaterno ?? '',
    apellidoMaterno:  props.user.apellidoMaterno ?? '',
    username:         props.user.username,
    celular:          props.user.celular ?? '',
    email:            props.user.email ?? '',
    role:             props.user.roles[0] ?? '',
});

function submitPersonal() {
    form.put(`/dashboard/usuarios/${props.user.id}`, {
        preserveScroll: true,
    });
}

// 👇 Usa CI + apellido en el confirm para que el admin sepa exactamente cuál será
function resetPassword() {
    const passwordMostrar = `${props.user.cedula_identidad}${props.user.apellidoPaterno}`;
    if (confirm(`¿Resetear la contraseña de ${props.user.name} a "${passwordMostrar}"?`)) {
        router.post(`/dashboard/usuarios/${props.user.id}/reset-password`, {}, {
            preserveScroll: true,
        });
    }
}

function toggleActivo() {
    router.post(`/dashboard/usuarios/${props.user.id}/toggle-status`, {}, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="'Editar — ' + user.name" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-4 w-full">

            <div class="mb-6">
                <Link
                    href="/dashboard/usuarios"
                    class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group"
                >
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    Volver a Gestión de Personal
                </Link>
            </div>

            <div class="flex items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="p-4 rounded-2xl shadow-lg bg-[#1a3a5a]">
                        <UserPen class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">
                            {{ user.name }}
                        </h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-black uppercase border border-blue-100">
                                {{ user.roles[0] || 'Sin Rol' }}
                            </span>
                            <span :class="user.activo ? 'text-green-600' : 'text-red-500'" class="text-xs font-semibold">
                                {{ user.activo ? '● Activo' : '● Inactivo' }}
                            </span>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    @click="toggleActivo"
                    :class="user.activo
                        ? 'border-red-200 text-red-600 hover:bg-red-50'
                        : 'border-green-200 text-green-600 hover:bg-green-50'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border bg-white text-sm font-bold transition-all shadow-sm"
                >
                    <Power class="w-4 h-4"/>
                    {{ user.activo ? 'Deshabilitar' : 'Habilitar' }}
                </button>
            </div>

            <div class="space-y-6 pb-10">

                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <User class="w-5 h-5 text-[#1a3a5a]"/> Datos del Usuario
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="cedula_identidad">
                                <Hash class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Cédula de Identidad
                            </Label>
                            <Input id="cedula_identidad" v-model="form.cedula_identidad" placeholder="Ej. 12345678" />
                            <InputError :message="form.errors.cedula_identidad" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="name">
                                <User class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Nombre(s)
                            </Label>
                            <Input id="name" v-model="form.name" />
                            <InputError :message="form.errors.name" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="apellidoPaterno">
                                <User class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Apellido Paterno
                            </Label>
                            <Input id="apellidoPaterno" v-model="form.apellidoPaterno" />
                            <InputError :message="form.errors.apellidoPaterno" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="apellidoMaterno">
                                <User class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Apellido Materno
                            </Label>
                            <Input id="apellidoMaterno" v-model="form.apellidoMaterno" />
                            <InputError :message="form.errors.apellidoMaterno" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label class="text-sm font-medium flex items-center gap-2 text-neutral-700">
                                <Phone class="w-4 h-4 text-[#1a3a5a]"/> Celular
                            </Label>
                            <Input v-model="form.celular" type="text" />
                            <InputError :message="form.errors.celular" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="email">
                                <Mail class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Correo Electrónico
                                <span class="text-neutral-400 font-normal ml-1 text-xs">(opcional)</span>
                            </Label>
                            <Input id="email" v-model="form.email" type="email" />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="username">
                                <AtSign class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Nombre de Usuario
                            </Label>
                            <Input id="username" v-model="form.username" />
                            <InputError :message="form.errors.username" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="role">
                                <ShieldCheck class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Rol
                            </Label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:ring-2 focus:ring-[#1a3a5a] outline-none"
                            >
                                <option value="">Seleccionar rol</option>
                                <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Button
                            type="button"
                            @click="submitPersonal"
                            :disabled="form.processing"
                            class="bg-[#1a3a5a] text-white rounded-xl px-6 h-11 font-semibold flex items-center gap-2 shadow-md"
                        >
                            <Loader2 v-if="form.processing" class="w-4 h-4 animate-spin"/>
                            <Save v-else class="w-4 h-4"/>
                            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </Button>
                    </div>
                </div>

                <!-- Resetear contraseña -->
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <KeyRound class="w-5 h-5 text-[#1a3a5a]"/> Contraseña
                    </CardTitle>

                    <div class="flex items-center justify-between pt-2 border-t border-neutral-100">
                        <button
                            type="button"
                            @click="resetPassword"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl border border-amber-200 text-amber-600 bg-amber-50 hover:bg-amber-100 text-sm font-bold transition-all"
                        >
                            <RefreshCw class="w-4 h-4"/>
                            Resetear contraseña
                        </button>
                    </div>

                    <p class="text-xs text-neutral-400 italic">
                        Restablece la contraseña a
                        <span class="font-mono font-bold">CI + Apellido Paterno</span>
                        — por ejemplo:
                        <span class="font-mono font-bold">
                            {{ user.cedula_identidad }}{{ user.apellidoPaterno }}
                        </span>.
                    </p>
                </div>

                <div class="flex justify-start">
                    <Link
                        href="/dashboard/usuarios"
                        class="flex items-center justify-center h-12 px-8 bg-white border border-neutral-200 text-neutral-500 rounded-xl font-semibold hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                    >
                        Volver sin guardar
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
