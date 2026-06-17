<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { UserPen, KeyRound, UserCheck, UserX } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import PageHeader from '@/components/PageHeader.vue';
import CreateActionButton from '@/components/CreateActionButton.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gestión de Personal', href: '/dashboard/usuarios' },
];

const props = defineProps<{
    users: Array<{
        id: number;
        name: string;
        username: string;
        celular: string;
        cedula_identidad: string;
        apellidoPaterno: string;
        apellidoMaterno: string;
        email: string;
        activo: boolean;
        roles: string[];
    }>;
}>();

// 👇 Muestra en el confirm exactamente cuál será la nueva contraseña
const resetPassword = (id: number, ci: string, apellido: string) => {
    const passwordMostrar = `${ci}${apellido}`;
    if (confirm(`¿Resetear la contraseña a "${passwordMostrar}"?`)) {
        router.post(`/dashboard/usuarios/${id}/reset-password`, {}, {
            preserveScroll: true,
        });
    }
};

const toggleStatus = (id: number) => {
    router.post(`/dashboard/usuarios/${id}/toggle-status`, {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Gestión de Personal" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <PageHeader
                description="Administración de cuentas de acceso para Directores y Encargados del taller de Mecánica Automotriz."
            >
                <template #action>
                    <CreateActionButton
                        type="button"
                        href="/dashboard/usuarios/create"
                        label="Registrar Usuario"
                    />
                </template>
            </PageHeader>

            <div class="relative bg-white border border-neutral-200 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead class="bg-neutral-50 border-b border-neutral-200 text-[11px] font-black uppercase tracking-widest text-neutral-500">
                        <tr>
                            <th class="p-4 pl-8">C.I.</th>
                            <th class="p-4 pl-8">Nombre Completo</th>
                            <th class="p-4">Usuario / Correo</th>
                            <th class="p-4 pl-8">Celular</th>
                            <th class="p-4">Rol</th>
                            <th class="p-4">Estado</th>
                            <th class="p-4 text-right pr-8">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-sm font-medium">

                        <tr v-if="!users.length">
                            <td colspan="7" class="p-8 text-center text-neutral-400 italic">
                                No hay usuarios registrados.
                            </td>
                        </tr>

                        <tr
                            v-for="user in users"
                            :key="user.id"
                            :class="['hover:bg-neutral-50/50 transition-colors group', !user.activo ? 'opacity-60' : '']"
                        >
                            <td class="p-4 pl-8">
                                <div class="font-bold text-neutral-900">{{ user.cedula_identidad }}</div>
                            </td>

                            <td class="p-4 pl-8">
                                <div class="font-bold text-neutral-900">
                                    {{ user.apellidoPaterno }} {{ user.apellidoMaterno }} {{ user.name }}
                                </div>
                            </td>

                            <td class="p-4">
                                <div class="text-neutral-700 font-medium">{{ user.username }}</div>
                                <div class="text-[11px] text-neutral-400">{{ user.email || '—' }}</div>
                            </td>

                            <td class="p-4 pl-8">
                                <div class="font-bold text-neutral-900">{{ user.celular || '—' }}</div>
                            </td>

                            <td class="p-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md text-[10px] font-black uppercase border border-blue-100">
                                    {{ user.roles[0] || 'Sin Rol' }}
                                </span>
                            </td>

                            <td class="p-4">
                                <span
                                    :class="user.activo
                                        ? 'bg-green-50 text-green-700 border-green-200'
                                        : 'bg-red-50 text-red-600 border-red-200'"
                                    class="px-2 py-1 rounded-full text-[11px] font-black border"
                                >
                                    {{ user.activo ? '● Activo' : '● Inactivo' }}
                                </span>
                            </td>

                            <td class="p-4 text-right pr-8">
                                <div class="flex items-center justify-end gap-1">

                                    <!-- 👇 Pasa CI y apellido para mostrar la contraseña en el confirm -->
                                    <Button
                                        @click="resetPassword(user.id, user.cedula_identidad, user.apellidoPaterno)"
                                        variant="ghost"
                                        size="sm"
                                        :title="`Resetear contraseña a ${user.cedula_identidad}${user.apellidoPaterno}`"
                                        class="hover:bg-amber-50 text-amber-500 border border-transparent hover:border-amber-200"
                                    >
                                        <KeyRound class="w-4 h-4"/>
                                    </Button>

                                    <Link :href="`/dashboard/usuarios/${user.id}/edit`">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            title="Editar usuario"
                                            class="hover:bg-blue-50 text-blue-500 border border-transparent hover:border-blue-200"
                                        >
                                            <UserPen class="w-4 h-4"/>
                                        </Button>
                                    </Link>

                                    <Button
                                        @click="toggleStatus(user.id)"
                                        variant="ghost"
                                        size="sm"
                                        :title="user.activo ? 'Deshabilitar usuario' : 'Habilitar usuario'"
                                        :class="user.activo
                                            ? 'hover:bg-red-50 text-red-500 border border-transparent hover:border-red-200'
                                            : 'hover:bg-emerald-50 text-emerald-500 border border-transparent hover:border-emerald-200'"
                                    >
                                        <UserX v-if="user.activo" class="w-4 h-4"/>
                                        <UserCheck v-else class="w-4 h-4"/>
                                    </Button>

                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
