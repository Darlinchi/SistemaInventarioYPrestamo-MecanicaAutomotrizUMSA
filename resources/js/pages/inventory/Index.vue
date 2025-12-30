<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Gestión de Usuarios" />

    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold">Inventario</h1>
            <p class="mt-2 text-gray-600">Aquí podrás administrar al inventario del sistema.</p>
            
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                <p>Lista de usuarios (Próximamente...)</p>
            </div>
        </div>
    </AppLayout>
</template>