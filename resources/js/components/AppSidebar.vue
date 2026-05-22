<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar, SidebarContent, SidebarFooter,
    SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import AppLogo from './AppLogo.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import {
    Users, Package, Building2, LayoutDashboard,
    NotebookPen, NotebookText, RefreshCcw, BookMarked,
    FileText, Settings, Key, UsersIcon
} from 'lucide-vue-next';

import userRoutes           from '@/routes/users';
import rolRoutes            from '@/routes/roles';
import itemsRoutes          from '@/routes/items';
import borrowersRoutes      from '@/routes/borrowers';
import subjectRoutes        from '@/routes/subjects';
import loansRoutes          from '@/routes/loans';
import loanReturnRoutes     from '@/routes/loan-returns';
import repositionRoutes     from '@/routes/repositions';
import maintenancesRoutes   from '@/routes/maintenances';
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';
import reportRoutes         from '@/routes/reports';

const page = usePage();

// ── Helpers ───────────────────────────────────────────────────────

// Verifica rol (para acciones que solo aplican a roles base)
const hasRole = (...roles: string[]): boolean => {
    const userRoles = (page.props.auth as any)?.user?.roles ?? [];
    return roles.some(r => userRoles.includes(r));
};

// Verifica permiso — ESTA es la función correcta para el sidebar
// Funciona para cualquier rol, incluidos los nuevos que se creen
const can = (permission: string): boolean => {
    const userPermissions = (page.props.auth as any)?.user?.permissions ?? [];
    return userPermissions.includes(permission);
};

// ── Menú dinámico basado en PERMISOS ─────────────────────────────
// Así, cualquier rol nuevo que tenga los permisos correctos
// automáticamente ve los items correspondientes, sin tocar este archivo.
const mainNavItems = computed((): NavItem[] => {
    const items: NavItem[] = [
        {
            title: 'Panel Principal',
            href:  dashboard(),
            icon:  LayoutDashboard,
        },
    ];

    // ── Usuarios — solo super-admin ve y gestiona usuarios ──────
    if (hasRole('super-admin')) {
        items.push({
            title: 'Gestión de Usuarios',
            href:  userRoutes.index.url(),
            icon:  Users,
        });
        items.push({
            title: 'Gestión de Roles',
            href:  rolRoutes.index.url(),
            icon:  Key,
        });
    }

    // ── Inventario ──────────────────────────────────────────────
    if (can('equipos.ver') || can('herramientas.ver')) {
        items.push({
            title: 'Inventario',
            href:  itemsRoutes.index.url(),
            icon:  Package,
        });
    }

    // ── Préstamos ───────────────────────────────────────────────
    if (can('prestamos.ver')) {
        items.push({
            title: 'Préstamos',
            href:  loansRoutes.index.url(),
            icon:  NotebookPen,
        });
    }

    // ── Devoluciones ─────────────────────────────────────────────
    if (can('prestamos.ver')) {
        items.push({
            title: 'Devoluciones',
            href:  loanReturnRoutes.index.url(),
            icon:  NotebookText,
        });
    }

    // ── Reposiciones ─────────────────────────────────────────────
    if (can('prestamos.ver')) {
        items.push({
            title: 'Reposiciones',
            href:  repositionRoutes.index.url(),
            icon:  RefreshCcw,
        });
    }

    // ── Responsables ─────────────────────────────────────────────
    if (can('prestatarios.ver')) {
        items.push({
            title: 'Responsables',
            href:  borrowersRoutes.index.url(),
            icon:  UsersIcon,
        });
    }

    // ── Materias ─────────────────────────────────────────────────
    if (can('materias.ver')) {
        items.push({
            title: 'Materias',
            href:  subjectRoutes.index.url(),
            icon:  BookMarked,
        });
    }

    // ── Mantenimientos ───────────────────────────────────────────
    if (can('mantenimientos.ver')) {
        items.push({
            title: 'Mantenimientos',
            href:  maintenancesRoutes.index.url(),
            icon:  Settings,
        });
    }

    // ── Empresas de mantenimiento ────────────────────────────────
    if (can('empresas_mant.ver')) {
        items.push({
            title: 'Emp. de Mantenimiento',
            href:  maintenanceCompanyRoutes.index.url(),
            icon:  Building2,
        });
    }

    // ── Reportes ─────────────────────────────────────────────────
    if (can('reportes.ver')) {
        items.push({
            title: 'Reportes',
            href:  reportRoutes.index.url(),
            icon:  FileText,
        });
    }

    return items;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
