<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    title: string;
    value: string | number;
    variant?: 'blue' | 'green' | 'orange' | 'neutral';
}>();

const colorClass = computed(() => {
    switch (props.variant) {
        case 'blue': return 'text-blue-600';
        case 'green': return 'text-emerald-600';
        case 'orange': return 'text-orange-500';
        default: return 'text-neutral-900';
    }
});
</script>

<template>
    <div class="bg-white p-6 rounded-4xl border border-neutral-200 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between min-h-[140px]">
        <h3 class="text-[13px] font-black uppercase tracking-widest text-neutral-500">
            {{ title }}
        </h3>
        <div :class="['text-4xl font-black tracking-tighter mt-2', colorClass]">
            {{ value }}
        </div>
    </div>
</template>
