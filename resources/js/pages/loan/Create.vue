<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import loans from '@/routes/loans';
import { router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Prestamos',
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
                    <h1 class="text-3xl font-black tracking-tighter uppercase text-black">Para crear</h1>
                    <p class="text-sm text-neutral-500">Administre prestamos y devoluciones de equipos y herramientas del taller</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
