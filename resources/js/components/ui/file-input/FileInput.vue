<script setup lang="ts">
import { ref } from 'vue';
import { cn } from '@/lib/utils';

// Definimos las propiedades
const props = defineProps<{
    class?: string;
    accept?: string;
    id?: string;
}>();

// Definimos el evento que recibirá Create.vue
const emit = defineEmits(['change']);

const input = ref<HTMLInputElement | null>(null);

const handleChange = (e: Event) => {
    emit('change', e);
};

// Exponemos el foco por si necesitas activarlo manualmente
defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <div class="grid w-full items-center gap-1.5">
        <input
            :id="id"
            ref="input"
            type="file"
            :accept="accept"
            @change="handleChange"
            :class="cn(
                /* Contenedor principal: Igual al Input de texto */
                'flex h-12 w-full rounded-xl border border-neutral-200 bg-neutral-50/50 px-3 py-2 text-sm transition-all duration-300 outline-none disabled:cursor-not-allowed disabled:opacity-50',

                /* Estilo del Botón Interno (file selector) */
                'file:border-0 file:bg-[#1a3a5a]/10 file:text-[#1a3a5a] file:text-xs file:font-bold file:uppercase file:px-4 file:py-1 file:rounded-lg file:mr-4 file:transition-colors hover:file:bg-[#1a3a5a]/20 hover:file:cursor-pointer',

                /* Foco y Hover */
                'focus:bg-white focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5 text-neutral-500',

                props.class
            )"
        />
    </div>
</template>
<!--
<template>
    <div class="grid w-full items-center gap-1.5">
        <input
            :id="id"
            ref="input"
            type="file"
            :accept="accept"
            @change="handleChange"
            :class="cn(
                'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 hover:file:cursor-pointer',
                props.class
            )"
        />
    </div>
</template>
-->
