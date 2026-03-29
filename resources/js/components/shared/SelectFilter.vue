<script setup lang="ts">
// Importamos la librería de iconos completa y su tipo
import * as LucideIcons from 'lucide-vue-next';
import { type LucideIcon, Filter, ChevronDown } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    modelValue: string | number;
    label: string;
    options: any[];
    optionValue?: string;
    optionLabel?: string;
    // Simplificamos aquí para evitar el error de unión compleja
    icon?: string | any;
}>();

defineEmits(['update:modelValue']);

// Buscador dinámico de iconos con tipado seguro
const iconComponent = computed(() => {
    // 1. Si no hay icono, el de filtro por defecto
    if (!props.icon) return Filter;

    // 2. Si ya es un objeto (componente), lo devolvemos tal cual
    if (typeof props.icon !== 'string') return props.icon;

    // 3. Si es un string, buscamos en la bolsa de iconos
    // Usamos la búsqueda dinámica pero de forma más simple
    const icon = (LucideIcons as Record<string, any>)[props.icon];

    // 4. Si lo encuentra lo da, si no, el de filtro
    return icon || Filter;
});
</script>

<template>
    <div class="relative w-60 group">
        <component
            :is="iconComponent"
            class="absolute left-2 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 pointer-events-none group-focus-within:text-[#1a3a5a] transition-colors"
        />

        <select
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            class="appearance-none w-full bg-white border border-neutral-200 rounded-2xl text-sm py-3 pl-7 pr-4 outline-none transition-all cursor-pointer focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5 text-neutral-600 shadow-sm hover:border-neutral-300"
        >
            <option value="">{{ label }} (Todos)</option>

            <template v-for="option in options" :key="optionValue ? option[optionValue] : option">
                <option :value="optionValue ? option[optionValue] : option">
                    {{ optionLabel && optionValue
                        ? `${option[optionValue]} - ${option[optionLabel]}`
                        : option
                    }}
                </option>
            </template>
        </select>

        <ChevronDown class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 pointer-events-none transition-transform group-focus-within:rotate-180" />
    </div>
</template>
