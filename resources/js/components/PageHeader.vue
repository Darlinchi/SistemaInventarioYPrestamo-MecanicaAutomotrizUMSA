<script setup lang="ts">
// Definimos los props para el título y la descripción
defineProps<{
    //title: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <div>
            <!--

            <h1 class="text-3xl font-black tracking-tighter text-black mb-2">
                {{ title }}
            </h1>-->
            <p v-if="description" class="text-sm text-neutral-500 font-medium">
                {{ description }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <slot name="action" />
        </div>
    </div>
</template>
