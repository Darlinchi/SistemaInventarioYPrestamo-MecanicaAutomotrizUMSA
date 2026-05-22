<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

defineProps<{
    href?: string;
    label: string;
}>();
</script>

<template>
    <Link
        :href="href"
        class="group flex items-center gap-2.5 px-5 py-2.5 bg-[#1a3a5a] text-white rounded-xl text-sm font-semibold tracking-wide hover:bg-[#122a42] transition-all duration-300 shadow-md hover:shadow-blue-900/20 active:scale-95"
    >
        <div class="bg-white/10 p-1 rounded-lg group-hover:bg-white/20 transition-colors">
            <Plus class="w-4 h-4 text-white" />
        </div>

        <span>{{ label }}</span>
    </Link>
</template>
