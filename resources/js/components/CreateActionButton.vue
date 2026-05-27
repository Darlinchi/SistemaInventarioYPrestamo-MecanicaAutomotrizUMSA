<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
// Importamos tanto Plus como FileDown de lucide-vue-next
import { Plus, FileDown } from 'lucide-vue-next';

defineProps<{
    href?: string;
    label: string;
    // Nueva propiedad opcional para elegir el icono ('create' o 'download')
    iconType?: 'create' | 'download';
}>();
</script>

<template>
    <!-- Si viene un href, actúa como enlace de Inertia -->
    <Link
        v-if="href"
        :href="href"
        class="group flex items-center gap-2.5 px-5 py-2.5 bg-[#1a3a5a] text-white rounded-xl text-sm font-semibold tracking-wide hover:bg-[#122a42] transition-all duration-300 shadow-md hover:shadow-blue-900/20 active:scale-95"
    >
        <div class="bg-white/10 p-1 rounded-lg group-hover:bg-white/20 transition-colors">
            <!-- Condicional dinámica para el icono -->
            <FileDown v-if="iconType === 'download'" class="w-4 h-4 text-white" />
            <Plus v-else class="w-4 h-4 text-white" />
        </div>

        <span>{{ label }}</span>
    </Link>

    <!-- Si NO viene un href, actúa como un botón plano nativo (como lo usamos en reportes) -->
    <button
        v-else
        type="button"
        class="group flex items-center gap-2.5 px-5 py-2.5 bg-[#1a3a5a] text-white rounded-xl text-sm font-semibold tracking-wide hover:bg-[#122a42] transition-all duration-300 shadow-md hover:shadow-blue-900/20 active:scale-95 outline-none"
    >
        <div class="bg-white/10 p-1 rounded-lg group-hover:bg-white/20 transition-colors">
            <!-- Condicional dinámica para el icono -->
            <FileDown v-if="iconType === 'download'" class="w-4 h-4 text-white" />
            <Plus v-else class="w-4 h-4 text-white" />
        </div>

        <span>{{ label }}</span>
    </button>
</template>
