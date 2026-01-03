<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img 
        src="/images/logo-carrera.png" 
        alt="Logo Carrera de Mecánica" 
        class="h-100 w-auto object-contain"
    />
</template>
