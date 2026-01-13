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
                'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 hover:file:cursor-pointer',
                props.class
            )"
        />
    </div>
</template>
