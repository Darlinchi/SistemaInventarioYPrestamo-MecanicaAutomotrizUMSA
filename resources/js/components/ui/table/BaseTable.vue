<script setup lang="ts">
import { Package } from 'lucide-vue-next';

defineProps<{
    items: any[];
    loading?: boolean;
    emptyText?: string;
}>();
</script>

<template>
    <div class="space-y-4">
        <div v-if="items.length === 0 && !loading"
            class="text-center py-20 bg-white rounded-[2.5rem] border-2 border-dashed border-neutral-200">
            <Package class="w-16 h-16 mx-auto text-neutral-200 mb-4" />
            <p class="text-neutral-400 font-semibold text-sm">
                {{ emptyText || 'No se encontraron registros' }}
            </p>
        </div>

        <div v-else class="bg-white border border-neutral-200 rounded-4xl shadow-sm overflow-hidden transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <slot />
                </table >
            </div>
        </div>
    </div>
</template>
