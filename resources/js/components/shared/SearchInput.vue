<script setup lang="ts">
import { Search, XIcon } from 'lucide-vue-next';

defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="relative w-96 group">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
            <Search class="w-5 h-5 text-neutral-400 group-focus-within:text-[#1a3a5a] transition-colors" />
        </span>

        <input
            :value="modelValue"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            type="text"
            :placeholder="placeholder || 'Buscar...'"
            class="pl-11 h-12 w-full rounded-2xl border border-neutral-200 bg-white text-sm shadow-sm transition-all outline-none focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5"
        />

        <button
            v-if="modelValue"
            @click="$emit('update:modelValue', '')"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-red-500 transition-colors"
        >
            <XIcon class="w-4 h-4"/>
        </button>
    </div>
</template>
