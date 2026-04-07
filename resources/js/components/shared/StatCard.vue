<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineProps<{
    title: string;
    value: number | string;
    icon: any;
    colorClass?: string;
    description?: string;
}>();
</script>

<template>
    <Card class="rounded-[2.5rem] border-none shadow-sm hover:shadow-xl transition-all group overflow-hidden relative bg-white">
        <CardHeader class="flex flex-row items-center justify-between">
            <CardTitle class="text-[11px] font-black text-neutral-500 uppercase tracking-widest">
                {{ title }}
            </CardTitle>
            <div :class="['p-3 rounded-2xl transition-colors bg-neutral-50', colorClass]">
                <component :is="icon" class="w-5 h-5" />
            </div>
        </CardHeader>
        <CardContent>
            <div class="text-4xl font-black text-neutral-800">{{ value }}</div>
            <p v-if="description" class="text-[11px] text-neutral-500 font-bold mt-1">
                {{ description }}
            </p>
        </CardContent>
        <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-current opacity-20" :class="colorClass?.split(' ')[0]"></div>
    </Card>
</template>
