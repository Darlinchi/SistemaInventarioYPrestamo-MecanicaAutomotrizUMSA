<script setup lang="ts">
import { Package, Image as ImageIcon } from 'lucide-vue-next';

defineProps<{
    items: any[];
    headers: string[]; // Títulos de las columnas
}>();
</script>

<template>
    <div class="space-y-4">
        <div v-if="items.length === 0" class="text-center py-20 bg-white rounded-[2.5rem] border-2 border-dashed border-neutral-200">
            <Package class="w-16 h-16 mx-auto text-neutral-200 mb-4" />
            <p class="text-neutral-400 font-bold text-sm tracking-widest">NO SE ENCONTRARON RESULTADOS</p>
        </div>

        <div v-else class="bg-white border border-neutral-200 rounded-2xl shadow-xl shadow-blue-900/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#1a3a5a] text-[13px] font-semibold tracking-wider text-white">
                        <tr>
                            <th v-for="header in headers" :key="header" class="p-5 first:pl-8 last:pr-8 last:text-right">
                                {{ header.toUpperCase() }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-neutral-100">
                        <tr v-for="(item, index) in items" :key="index" class="hover:bg-neutral-50/50 group transition-colors">
                            <slot :item="item"></slot>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
