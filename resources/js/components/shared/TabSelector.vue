<script setup lang="ts">
import * as LucideIcons from 'lucide-vue-next';

interface Tab {
    id: string;
    label: string;
    count?: number; // El "?" lo hace opcional
    icon: any;      // Cambiado a any para aceptar String u Objeto
}

const props = defineProps<{
    tabs: Tab[];
    activeTab: string;
}>();

defineEmits(['update:activeTab']);

// Función inteligente para detectar el icono
const getIcon = (iconSource: any) => {
    if (typeof iconSource === 'string') {
        return (LucideIcons as any)[iconSource];
    }
    return iconSource; // Si ya es un objeto de Lucide, lo devuelve tal cual
};

</script>

<template>
    <div class="flex p-1.5 bg-neutral-100 rounded-2xl w-fit mb-4 border border-neutral-200 shadow-inner gap-3">
        <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="$emit('update:activeTab', tab.id)"
            :class="[
                'flex items-center gap-3 px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300',
                activeTab === tab.id
                    ? 'bg-white text-[#1a3a5a] shadow-lg scale-[1.03] border border-neutral-100'
                    : 'text-neutral-500 hover:text-neutral-800 hover:bg-neutral-200/50'
            ]"
        >
            <component
                :is="getIcon(tab.icon)"
                :class="['w-5 h-5 transition-colors', activeTab === tab.id ? 'text-[#1a3a5a]' : 'text-neutral-400']"
            />

            <span>
                {{ tab.label }}
                <span v-if="tab.count !== undefined" class="ml-1 opacity-60">({{ tab.count }})</span>
            </span>
        </button>
    </div>
</template>
