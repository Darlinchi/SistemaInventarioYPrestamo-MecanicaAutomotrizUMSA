<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    icon: any;
    variant: 'view' | 'edit' | 'delete' | 'success';
    title?: string;
}>();

const classes = computed(() => {
    const base = "p-2.5 border rounded-xl transition shadow-sm group/btn active:scale-95";
    const variants = {
        view: "bg-white border-neutral-200 hover:bg-green-50 text-green-600",
        edit: "bg-white border-neutral-200 hover:bg-blue-50 text-blue-600",
        delete: "bg-white border-neutral-200 hover:bg-red-50 text-[#d90000]",
        success: "bg-white border-neutral-200 hover:bg-emerald-50 text-emerald-600",
    };
    return `${base} ${variants[props.variant]}`;
});
</script>

<template>
    <button :class="classes" :title="title">
        <component :is="icon" class="w-4.5 h-4.5 transition-transform group-hover/btn:scale-110" />
    </button>
</template>
