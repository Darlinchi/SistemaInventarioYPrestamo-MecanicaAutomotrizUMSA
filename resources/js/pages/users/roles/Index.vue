<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Shield, ShieldCheck, ShieldAlert, Plus, Pencil,
    Trash2, Save, X, Users, CheckSquare, Square, Loader2
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface Role {
    id: number;
    name: string;
    permissions: string[];
    users_count: number;
}

const props = defineProps<{
    roles: Role[];
    permissionGroups: Record<string, string[]>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gestión de Personal', href: '/dashboard/usuarios' },
    { title: 'Roles y Permisos', href: '#' },
];

// ── Estado UI ──────────────────────────────────────────────────────
const editingRole  = ref<Role | null>(null);
const showCreate   = ref(false);

// ── Formulario crear rol ───────────────────────────────────────────
const createForm = useForm({
    name:        '',
    permissions: [] as string[],
});

function submitCreate() {
    createForm.post('/dashboard/roles', {
        preserveScroll: true,
        onSuccess: () => { showCreate.value = false; createForm.reset(); },
    });
}

// ── Formulario editar rol ──────────────────────────────────────────
const editForm = useForm({
    name:        '',
    permissions: [] as string[],
});

function abrirEdicion(role: Role) {
    editingRole.value = role;
    editForm.name        = role.name;
    editForm.permissions = [...role.permissions];
    editForm.clearErrors();
}

function submitEdit() {
    if (!editingRole.value) return;
    editForm.put(`/dashboard/roles/${editingRole.value.id}`, {
        preserveScroll: true,
        onSuccess: () => { editingRole.value = null; editForm.reset(); },
    });
}

function cancelEdit() {
    editingRole.value = null;
    editForm.reset();
}

// ── Eliminar rol ───────────────────────────────────────────────────
function deleteRole(role: Role) {
    if (!confirm(`¿Eliminar el rol "${role.name}"? Esta acción no se puede deshacer.`)) return;
    router.delete(`/dashboard/roles/${role.id}`, { preserveScroll: true });
}

// ── Toggle permiso en un form ──────────────────────────────────────
function togglePermission(form: any, perm: string) {
    const idx = form.permissions.indexOf(perm);
    if (idx === -1) form.permissions.push(perm);
    else form.permissions.splice(idx, 1);
}

function toggleAll(form: any, perms: string[]) {
    const allSelected = perms.every((p: string) => form.permissions.includes(p));
    if (allSelected) {
        form.permissions = form.permissions.filter((p: string) => !perms.includes(p));
    } else {
        perms.forEach((p: string) => { if (!form.permissions.includes(p)) form.permissions.push(p); });
    }
}

// ── Módulos en español ─────────────────────────────────────────────
const moduloLabel: Record<string, string> = {
    usuarios:      'Usuarios',
    roles:         'Roles',
    prestatarios:  'Prestatarios',
    materias:      'Materias',
    herramientas:  'Herramientas',
    equipos:       'Equipos',
    prestamos:     'Préstamos',
    mantenimientos:'Mantenimientos',
    empresas_mant: 'Empresas Mant.',
    reportes:      'Reportes',
    configuracion: 'Configuración',
};

// ── Colores por rol ────────────────────────────────────────────────
function rolColor(name: string) {
    if (name === 'super-admin') return 'bg-red-50 text-red-700 border-red-200';
    if (name === 'director')    return 'bg-blue-50 text-blue-700 border-blue-200';
    if (name === 'encargado')   return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    return 'bg-neutral-50 text-neutral-700 border-neutral-200';
}

const rolesBase = ['super-admin', 'director', 'encargado'];
</script>

<template>
    <Head title="Roles y Permisos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-7xl mx-auto">

            <PageHeader description="Administra los roles del sistema y sus permisos de acceso.">
                <template #action>
                    <Button @click="showCreate = !showCreate"
                        class="flex items-center gap-2 bg-[#1a3a5a] text-white hover:bg-[#122a42]">
                        <Plus class="w-4 h-4"/>
                        Nuevo Rol
                    </Button>
                </template>
            </PageHeader>

            <!-- ── Formulario crear rol ── -->
            <div v-if="showCreate"
                class="mb-6 bg-white border border-blue-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-black text-[#1a3a5a] uppercase tracking-widest flex items-center gap-2">
                    <Plus class="w-4 h-4"/> Crear Nuevo Rol
                </h3>

                <div class="grid gap-2 max-w-xs">
                    <label class="text-xs font-black text-neutral-500 uppercase tracking-widest">Nombre del Rol</label>
                    <Input v-model="createForm.name" placeholder="Ej: supervisor" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs">{{ createForm.errors.name }}</p>
                </div>

                <!-- Permisos agrupados -->
                <div class="space-y-3">
                    <p class="text-xs font-black text-neutral-500 uppercase tracking-widest">Permisos</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        <div v-for="(perms, modulo) in permissionGroups" :key="modulo"
                            class="border border-neutral-200 rounded-xl p-3 bg-neutral-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-black text-[#1a3a5a] uppercase">
                                    {{ moduloLabel[modulo] ?? modulo }}
                                </span>
                                <button type="button" @click="toggleAll(createForm, perms)"
                                    class="text-[10px] font-bold text-blue-600 hover:text-blue-800">
                                    {{ perms.every((p: string) => createForm.permissions.includes(p)) ? 'Quitar todo' : 'Todo' }}
                                </button>
                            </div>
                            <div class="space-y-1">
                                <label v-for="perm in perms" :key="perm"
                                    class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox"
                                        :checked="createForm.permissions.includes(perm)"
                                        @change="togglePermission(createForm, perm)"
                                        class="rounded border-neutral-300 text-[#1a3a5a]"/>
                                    <span class="text-[11px] text-neutral-600 group-hover:text-neutral-900">
                                        {{ perm.split('.')[1] }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <Button @click="showCreate = false" variant="outline">Cancelar</Button>
                    <Button @click="submitCreate" :disabled="createForm.processing"
                        class="bg-[#1a3a5a] text-white flex items-center gap-2">
                        <Loader2 v-if="createForm.processing" class="w-4 h-4 animate-spin"/>
                        <Save v-else class="w-4 h-4"/>
                        {{ createForm.processing ? 'Guardando...' : 'Crear Rol' }}
                    </Button>
                </div>
            </div>

            <!-- ── Lista de roles ── -->
            <div class="space-y-4">
                <div v-for="role in roles" :key="role.id"
                    class="bg-white border border-neutral-200 rounded-2xl shadow-sm overflow-hidden">

                    <!-- Header del rol -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-100">
                        <div class="flex items-center gap-3">
                            <ShieldCheck v-if="role.name === 'super-admin'" class="w-5 h-5 text-red-600"/>
                            <Shield v-else class="w-5 h-5 text-[#1a3a5a]"/>
                            <div>
                                <span :class="rolColor(role.name)"
                                    class="px-3 py-1 rounded-full text-xs font-black border uppercase tracking-wider">
                                    {{ role.name }}
                                </span>
                                <span class="ml-3 text-xs text-neutral-400 font-medium">
                                    {{ role.users_count }} usuario(s) · {{ role.permissions.length }} permiso(s)
                                </span>
                            </div>
                        </div>

                        <!-- Acciones del rol -->
                        <div class="flex items-center gap-2"
                            v-if="role.name !== 'super-admin'">
                            <button @click="abrirEdicion(role)"
                                class="p-1.5 text-neutral-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                title="Editar permisos">
                                <Pencil class="w-4 h-4"/>
                            </button>
                            <button @click="deleteRole(role)"
                                :disabled="role.users_count > 0"
                                class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition disabled:opacity-30 disabled:cursor-not-allowed"
                                :title="role.users_count > 0 ? 'Tiene usuarios asignados' : 'Eliminar rol'">
                                <Trash2 class="w-4 h-4"/>
                            </button>
                        </div>
                        <span v-else class="text-xs text-neutral-300 italic">Protegido</span>
                    </div>

                    <!-- Vista de permisos del rol (solo lectura si no está editando) -->
                    <div v-if="editingRole?.id !== role.id" class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5">
                            <span v-for="perm in role.permissions" :key="perm"
                                class="px-2 py-0.5 bg-neutral-100 text-neutral-600 rounded-md text-[10px] font-mono border border-neutral-200">
                                {{ perm }}
                            </span>
                            <span v-if="role.permissions.length === 0"
                                class="text-xs text-neutral-400 italic">Sin permisos asignados</span>
                        </div>
                    </div>

                    <!-- Formulario editar rol -->
                    <div v-if="editingRole?.id === role.id" class="px-6 py-4 space-y-4 bg-blue-50/30">
                        <div class="grid gap-2 max-w-xs">
                            <label class="text-xs font-black text-neutral-500 uppercase tracking-widest">Nombre del Rol</label>
                            <Input v-model="editForm.name" />
                            <p v-if="editForm.errors.name" class="text-red-500 text-xs">{{ editForm.errors.name }}</p>
                        </div>

                        <div class="space-y-3">
                            <p class="text-xs font-black text-neutral-500 uppercase tracking-widest">Permisos asignados</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                <div v-for="(perms, modulo) in permissionGroups" :key="modulo"
                                    class="border border-neutral-200 rounded-xl p-3 bg-white">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black text-[#1a3a5a] uppercase">
                                            {{ moduloLabel[modulo] ?? modulo }}
                                        </span>
                                        <button type="button" @click="toggleAll(editForm, perms)"
                                            class="text-[10px] font-bold text-blue-600 hover:text-blue-800">
                                            {{ perms.every((p: string) => editForm.permissions.includes(p)) ? 'Quitar' : 'Todo' }}
                                        </button>
                                    </div>
                                    <div class="space-y-1">
                                        <label v-for="perm in perms" :key="perm"
                                            class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox"
                                                :checked="editForm.permissions.includes(perm)"
                                                @change="togglePermission(editForm, perm)"
                                                class="rounded border-neutral-300 text-[#1a3a5a]"/>
                                            <span class="text-[11px] text-neutral-600 group-hover:text-neutral-900">
                                                {{ perm.split('.')[1] }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <Button @click="cancelEdit" variant="outline" class="flex items-center gap-2">
                                Cancelar
                            </Button>
                            <Button @click="submitEdit" :disabled="editForm.processing"
                                class="bg-[#1a3a5a] text-white flex items-center gap-2">
                                <Loader2 v-if="editForm.processing" class="w-4 h-4 animate-spin"/>
                                <Save v-else class="w-4 h-4"/>
                                {{ editForm.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
