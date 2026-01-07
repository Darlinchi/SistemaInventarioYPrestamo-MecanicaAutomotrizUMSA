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
import { Users, Package, BookOpen, Folder, LayoutDashboard, ClipboardList, Settings, FileText } from 'lucide-vue-next';
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
            href: '/dashboard/inventario',
            icon: Package,
        });

        items.push({
            title: 'Préstamos',
            href: '/prestamos',
            icon: ClipboardList,
        });

        items.push({
            title: 'Mantenimiento',
            href: '/mantenimiento',
            icon: Settings,
        });

        items.push({
            title: 'Reportes',
            href: '/reportes',
            icon: FileText,
        });
    }
    return items;
});

const footerNavItems: NavItem[] = [
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
];
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
