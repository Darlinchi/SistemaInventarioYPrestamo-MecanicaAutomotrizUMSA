<script setup lang="ts">
import { Search, XIcon, Filter } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string;         // Para el v-model de búsqueda
    statusValue: string;        // Para el v-model del select
    placeholder?: string;
    options: string[];          // Lista de estados (disponible, prestado, etc.)
    activeTab: 'equipos' | 'herramientas';
}>();

const emit = defineEmits(['update:modelValue', 'update:statusValue']);
</script>

<template>
    <div class="flex flex-wrap items-center gap-4 mb-8">
        <div class="relative w-full md:w-96 group">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <Search class="w-5 h-5 transition-colors text-neutral-400"
                    :class="activeTab === 'equipos' ? 'group-focus-within:text-[#d90000]' : 'group-focus-within:text-[#1a3a5a]'"
                />
            </span>

            <input
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                type="text"
                :placeholder="placeholder || 'Buscar por nombre, marca o serie...'"
                class="pl-10 flex h-12 w-full rounded-2xl border border-neutral-200 bg-white px-4 py-2 text-sm shadow-sm transition-all focus:ring-2 focus:ring-offset-1"
                :class="activeTab === 'equipos' ? 'focus:ring-[#d90000]' : 'focus:ring-[#1a3a5a]'"
            />

            <button v-if="modelValue" @click="$emit('update:modelValue', '')"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-black transition-colors"
            >
                <XIcon class="w-4 h-4"/>
            </button>
        </div>

        <div class="relative min-w-[200px]">
            <select
                :value="statusValue"
                @change="$emit('update:statusValue', ($event.target as HTMLSelectElement).value)"
                class="appearance-none w-full bg-white border border-neutral-200 rounded-2xl text-sm font-bold py-3 px-10 focus:ring-2 focus:ring-offset-1 cursor-pointer transition-all"
                :class="activeTab === 'equipos' ? 'focus:ring-[#d90000] text-neutral-700' : 'focus:ring-[#1a3a5a] text-neutral-700'"
            >
                <option value="">Todos los estados</option>
                <option v-for="estado in options" :key="estado" :value="estado">
                    {{ estado }}
                </option>
            </select>

            <Filter class="absolute left-3.5 top-3.5 w-4 h-4 text-neutral-400 pointer-events-none" />
        </div>
    </div>
</template>
