<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Bell } from "lucide-vue-next"

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2  border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!--<div class="flex items-center gap-2">
            Botón de Notificaciones
            <button class="relative p-2 rounded-xl hover:bg-neutral-100 transition-all group">
                <Bell class="w-6 h-6 text-[#1a3a5a] group-hover:scale-110 transition-transform" />

                 Badge de conteo
                <span class="absolute top-1 right-1 flex h-4.5 w-4.5 items-center justify-center rounded-full bg-red-600 text-[10px] font-black text-white border-2 border-white shadow-sm">
                    3
                </span>
            </button>

            Opcional: Avatar del usuario
            <div class="w-8 h-8 rounded-full bg-[#1a3a5a] flex items-center justify-center text-white text-xs font-bold ml-2">
                E
            </div>
        </div>
        -->

    </header>
</template>
