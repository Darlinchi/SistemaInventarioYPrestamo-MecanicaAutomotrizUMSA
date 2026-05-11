<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import AppLogo from './AppLogo.vue';

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
// Aqui estan los iconos utilizados
import { Users, Package, Building2, LayoutDashboard, ClipboardList, Settings, FileText, UsersIcon,
    NotebookPen, NotebookText, RefreshCcw, BookMarked
 } from 'lucide-vue-next';
// Rutas del inventario
import itemsRoutes from '@/routes/items';
// Rutas de los responsables
import borrowersRoutes from '@/routes/borrowers';
// Rutas de los materias
import subjectRoutes from '@/routes/subjects';
// Rutas de los prestamos
import loansRoutes from '@/routes/loans';
// Rutas de las devoluciones
import loanReturnRoutes from '@/routes/loan-returns';
// Rutas de reposiciones
import repositionRoutes from '@/routes/repositions';
// Rutas de las mantenimiento
import maintenancesRoutes from '@/routes/maintenances';
// Rutas de las empresas de mantenimiento
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';
// Rutas de los reportes
import reportRoutes from '@/routes/reports';

const page = usePage();

// Helper: verifica si el usuario tiene alguno de los roles indicados
const hasRole = (...roles: string[]): boolean => {
    const userRoles = (page.props.auth as any)?.user?.roles ?? [];
    return roles.some(r => userRoles.includes(r));
};

const mainNavItems = computed((): NavItem[] => {
    const items: NavItem[] = [
        {
            title: 'Panel Principal',
            href: dashboard(),
            icon: LayoutDashboard,
        },
    ];

    const userRoles = (page.props.auth as any)?.user?.roles || [];

    // Se agrego gestionar personal solo para los que son administradores
    if (userRoles.includes('admin')) {
        items.push({
            title: 'Gestionar Personal',
            href: '/dashboard/usuarios',
            icon: Users,
        });
    }

    // Gestionar Personal — solo super-admin
    if (hasRole('super-admin')) {
        items.push({
            title: 'Gestionar Personal',
            href: '/dashboard/usuarios',
            icon: Users,
        });
    }

    // Inventario — super-admin, director (lectura) y encargado (completo)
    // El director ve el mismo enlace pero las acciones de crear/editar
    // están bloqueadas en el backend por los permisos de Spatie.
    if (hasRole('super-admin', 'director', 'encargado')) {
        items.push({
            title: 'Inventario',
            href: itemsRoutes.index.url(),
            icon: Package,
        });
    }

    // Préstamos — super-admin, director (lectura) y encargado (completo)
    if (hasRole('super-admin', 'director', 'encargado')) {
        items.push({
            title: 'Préstamos',
            href: loansRoutes.index.url(),
            icon: NotebookPen,
        });
    }

    // Devoluciones — super-admin, director (lectura) y encargado (completo)
    if (hasRole('super-admin', 'director', 'encargado')) {
        items.push({
            title: 'Devoluciones',
            href: loanReturnRoutes.index.url(),
            icon: NotebookText,
        });
    }

    // Reposiciones — super-admin, director (lectura) y encargado (completo)
    if (hasRole('super-admin', 'director', 'encargado')) {
        items.push({
            title: 'Reposiciones',
            href: repositionRoutes.index.url(),
            icon: RefreshCcw,
        });
    }

    // Prestamistas — super-admin, director (lectura) y encargado (completo)
    if (hasRole('super-admin', 'director', 'encargado')) {
        items.push({
            title: 'Responsables',
            href: borrowersRoutes.index.url(),
            icon: UsersIcon,
        });
    }

    // Materias — super-admin, director (lectura) y encargado (completo)
    if (hasRole('super-admin', 'director', 'encargado')) {
        items.push({
            title: 'Materias',
            href: subjectRoutes.index.url(),
            icon: BookMarked,
        });
    }

    // Mantenimiento — super-admin y encargado (el director no opera esto)
    if (hasRole('super-admin', 'encargado')) {
        items.push({
            title: 'Mantenimientos',
            href: maintenancesRoutes.index.url(),
            icon: Settings,
        });

        items.push({
            title: 'Emp. de Mantenimiento',
            href: maintenanceCompanyRoutes.index.url(),
            icon: Building2,
        });
    }

    // Reportes — todos los roles los ven
    if (hasRole('super-admin', 'director', 'encargado')) {
        items.push({
            title: 'Reportes',
            href: reportRoutes.index.url(),
            icon: FileText,
        });
    }
    return items;
});

/*const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];*/
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
            <!--<NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
