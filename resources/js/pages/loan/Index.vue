<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Plus, CircleCheck, XIcon } from 'lucide-vue-next';
import loans from '@/routes/loans';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Préstamos',
        href: loans.index.url(),
    },
];
</script>

<template>
    <Head title="Prestamos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Gestión de Préstamos</h1>
                    <p class="text-sm text-neutral-500">Administre préstamos y devoluciones de equipos y herramientas del taller</p>
                </div>
                <div class="flex justify-end mt-4" >
                    <Link :href="loans.create.url()" class="bg-black text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-neutral-800 transition shadow-lg">
                        <Plus class="w-5 h-5"/> Nuevo Préstamo
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
