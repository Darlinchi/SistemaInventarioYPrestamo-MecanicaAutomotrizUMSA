<script setup lang="ts">
import { Calendar } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

defineProps<{
    modelValue: string;
    label?: string;
}>();

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="relative min-w-40 group">
        <Calendar class="absolute left-2 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 pointer-events-none group-focus-within:text-[#1a3a5a] transition-colors" />

        <input
            type="date"
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            class="appearance-none w-full bg-white border border-neutral-200 rounded-2xl text-sm py-3 pl-7 pr-4 outline-none transition-all cursor-pointer focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5 text-neutral-600 shadow-sm"
        />

        <span v-if="label" class="absolute -top-6 left-2 text-[10px] text-neutral-400 uppercase font-bold tracking-tighter opacity-0 group-hover:opacity-100 transition-opacity">
            {{ label }}
        </span>
    </div>
</template>
