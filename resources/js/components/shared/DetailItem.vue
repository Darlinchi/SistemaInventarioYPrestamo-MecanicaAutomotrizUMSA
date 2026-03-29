<script setup lang="ts">
// Importamos todos los posibles iconos que uses en tus fichas técnicas
import * as LucideIcons from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    icon: string;    // Nombre del icono (ej: 'Hash', 'Package')
    label: string;   // Etiqueta (ej: 'Número de Serie')
    value: string | number | null | undefined; // El dato real
}>();

// Buscamos el componente del icono dinámicamente
const iconComponent = computed(() => {
    return (LucideIcons as any)[props.icon];
});

</script>
<template>
    <div class="flex items-center gap-4 p-2 group hover:bg-neutral-50 rounded-2xl transition-colors">
        <div class="p-2.5 bg-[#1a3a5a]/5 rounded-xl group-hover:bg-white group-hover:shadow-sm transition-all">
            <component
                :is="iconComponent"
                v-if="iconComponent"
                class="w-5 h-5 text-[#1a3a5a]"
            />
        </div>

        <div class="flex flex-col">
            <span class="text-[12px] font-semibold text-neutral-600 tracking-wider leading-none mb-1">
                {{ label }}
            </span>
            <span class="text-sm font-semibold text-neutral-800 wrap-break-words">
                {{ value || 'No registrado' }}
            </span>
        </div>
    </div>
</template>
