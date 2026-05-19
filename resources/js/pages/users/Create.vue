<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft, Save, Loader2, UserPlus, PencilLine,
    User, Mail, KeyRound, ShieldCheck, AtSign, Hash, Phone
} from 'lucide-vue-next';

const props = defineProps<{
    roles: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gestión de Personal', href: '/dashboard/usuarios' },
    { title: 'Registrar Usuario', href: '#' },
];

const form = useForm({
    cedula_identidad: '',
    name:             '',
    apellidoPaterno:  '',
    apellidoMaterno:  '',
    username:         '',
    celular:          '',
    email:            '',
    password:         '',
    password_confirmation: '',
    role:             '',
});

function submit() {
    form.post('/dashboard/usuarios', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Registrar Usuario" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-3xl mx-auto p-4 w-full">

            <!-- Volver -->
            <div class="mb-6">
                <Link
                    href="/dashboard/usuarios"
                    class="inline-flex items-center text-[15px] font-medium text-neutral-500 hover:text-[#1a3a5a] transition-colors group"
                >
                    <ArrowLeft class="w-5 h-5 mr-1 group-hover:-translate-x-1 transition-transform"/>
                    Volver a Gestión de Personal
                </Link>
            </div>

            <!-- Header -->
            <div class="flex items-center gap-4 mb-8">
                <div class="p-4 rounded-2xl shadow-lg bg-[#1a3a5a]">
                    <UserPlus class="w-8 h-8 text-white" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-3xl font-bold text-neutral-900 tracking-tight">
                        Registrar Usuario
                    </h2>
                    <p class="text-neutral-500">
                        Cuenta de acceso para Director o Encargado del taller
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">

                <!-- Datos personales -->
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <PencilLine class="w-5 h-5 text-[#1a3a5a]"/> Datos del Usuario
                    </CardTitle>


                    <!-- Cédula + Username -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="cedula_identidad">
                                <Hash class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Cédula de Identidad
                            </Label>
                            <Input
                                id="cedula_identidad"
                                v-model="form.cedula_identidad"
                                placeholder="Ej. 12345678"
                                autofocus
                            />
                            <InputError :message="form.errors.cedula_identidad" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="name">
                                <User class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Nombre(s)
                            </Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                placeholder="Ej. Juan Carlos"
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                    </div>

                    <!-- Apellidos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="apellidoPaterno">
                                <User class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Apellido Paterno
                            </Label>
                            <Input id="apellidoPaterno" v-model="form.apellidoPaterno" placeholder="Ej. Pérez"/>
                            <InputError :message="form.errors.apellidoPaterno" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="apellidoMaterno">
                                <User class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Apellido Materno
                            </Label>
                            <Input id="apellidoMaterno" v-model="form.apellidoMaterno" placeholder="Ej. López"/>
                            <InputError :message="form.errors.apellidoMaterno" />
                        </div>
                    </div>

                    <!-- Username + Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="celular" class="flex items-center gap-2">
                                <Phone class="w-4 h-4 text-[#1a3a5a]"/> Celular
                                <span class="text-neutral-400 text-xs font-normal">(opcional)</span>
                            </Label>
                            <Input id="celular" v-model="form.celular" placeholder="Ej. 78945612" />
                            <InputError :message="form.errors.celular" />
                        </div>
                        <!-- Email -->
                        <div class="grid gap-2">
                            <Label for="email">
                                <Mail class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Correo Electrónico
                                <span class="text-neutral-400 font-normal ml-1 text-xs">(opcional)</span>
                            </Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="Ej: user@umsa.bo"
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>

                    <!-- Rol -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="username">
                                <AtSign class="w-4 h-4 text-[#1a3a5a] inline mr-1"/> Nombre de Usuario
                            </Label>
                            <Input id="username" v-model="form.username" placeholder="Nombre corto de acceso"/>
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
                                <option value="">Seleccionar rol </option>
                                <option v-for="r in roles" :key="r" :value="r">
                                    {{ r }}
                                </option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="bg-white p-6 rounded-xl border border-neutral-200 shadow-sm space-y-4">
                    <CardTitle class="text-lg font-semibold text-[#1a3a5a] flex items-center gap-2">
                        <KeyRound class="w-5 h-5 text-[#1a3a5a]"/> Contraseña
                    </CardTitle>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="password">Contraseña</Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                placeholder="Mínimo 8 caracteres"
                            />
                            <InputError :message="form.errors.password" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirmar Contraseña</Label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Repetir contraseña"
                            />
                        </div>
                    </div>

                    <p class="text-xs text-neutral-400 italic">
                        Si olvidaron la contraseña, el administrador puede resetearla desde la lista de usuarios.
                    </p>
                </div>

                <!-- Botones -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <Link
                        href="/dashboard/usuarios"
                        class="flex items-center justify-center h-14 bg-white border border-neutral-200 text-neutral-500 rounded-xl font-semibold text-[18px] hover:bg-neutral-100 transition-all active:scale-95 shadow-sm"
                    >
                        Cancelar
                    </Link>

                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="h-14 bg-[#1a3a5a] text-white rounded-xl font-semibold text-[18px] shadow-lg shadow-blue-900/20 active:scale-95 transition-all w-full flex items-center justify-center gap-3"
                    >
                        <template v-if="form.processing">
                            <Loader2 class="w-5 h-5 animate-spin"/>
                            Guardando...
                        </template>
                        <template v-else>
                            <Save class="w-5 h-5"/>
                            Registrar Usuario
                        </template>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
