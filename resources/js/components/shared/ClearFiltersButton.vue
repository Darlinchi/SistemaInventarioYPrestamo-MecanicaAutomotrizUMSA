<script setup lang="ts">
import { Eraser } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

defineEmits(['clear']);
</script>

<template>
    <Button
        variant="outline"
        title="Limpiar filtros"
        @click="$emit('clear')"
        class="h-10 px-2 rounded-2xl border-dashed border-neutral-300 text-neutral-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all duration-300 group shadow-sm active:scale-95"
    >
        <Eraser class="w-4 h-4 mr-1 group-hover:rotate-12 transition-transform" />
    </Button>
</template>
