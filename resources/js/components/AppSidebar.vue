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
import { Users, Package, Building2, LayoutDashboard, ClipboardList, Settings, FileText, UsersIcon } from 'lucide-vue-next';
// Rutas del inventario
import itemsRoutes from '@/routes/items';
// Rutas de los prestamistas
import borrowersRoutes from '@/routes/borrowers';
// Rutas de los prestamos
import loansRoutes from '@/routes/loans';
// Rutas de las mantenimiento
import maintenanceRoutes from '@/routes/maintenance';
// Rutas de las empresas de mantenimiento
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';

const page = usePage();

const mainNavItems = computed(() => {
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

    // Se agrego inventario para ambos
    if (userRoles.includes('encargado')) {
        items.push({
            title: 'Inventario',
            href: itemsRoutes.index.url(),
            icon: Package,
        });

        items.push({
            title: 'Préstamos',
            href: loansRoutes.index.url(),
            icon: ClipboardList,
        });

        items.push({
            title: 'Mantenimiento',
            href: maintenanceRoutes.index.url(),
            icon: Settings,
        });

        items.push({
            title: 'Emp. de Mantenimiento',
            href: maintenanceCompanyRoutes.index.url(),
            icon: Building2,
        });

        items.push({
            title: 'Usuarios',
            href: borrowersRoutes.index.url(),
            icon: UsersIcon,
        });

        items.push({
            title: 'Reportes',
            href: '/reportes',
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
