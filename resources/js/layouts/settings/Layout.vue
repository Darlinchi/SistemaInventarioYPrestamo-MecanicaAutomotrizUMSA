<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
// Importamos las rutas necesarias
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
// Importamos iconos para que el menú lateral se vea más profesional
import { User, KeyRound, ShieldCheck } from 'lucide-vue-next';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Perfil',
        href: editProfile(),
        icon: User,
    },
    {
        title: 'Seguridad y Contraseña',
        href: editPassword(),
        icon: KeyRound,
    },
    {
        title: 'Doble Factor (2FA)',
        href: show(),
        icon: ShieldCheck,
    },
];

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Configuración de la Cuenta"
            description="Administra tu información personal y los ajustes de seguridad de tu acceso."
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12 mt-6">
            <aside class="w-full lg:w-64">
                <nav class="flex flex-col space-y-1">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start gap-3 px-4 py-2 rounded-xl transition-all',
                            urlIsActive(item.href, currentPath)
                                ? 'bg-[#1a3a5a]/10 text-[#1a3a5a] font-bold shadow-sm'
                                : 'text-neutral-500 hover:bg-neutral-100',
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-3xl">
                <section class="max-w-2xl space-y-12 animate-in fade-in slide-in-from-bottom-2 duration-500">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
