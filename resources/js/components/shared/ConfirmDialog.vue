<script setup lang="ts">
import { Ban, AlertTriangle, Info, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

defineProps<{
    show: boolean;
    title: string;
    message?: string;
    itemName?: string;
    confirmLabel?: string;
    variant?: 'danger' | 'warning' | 'info';
}>();

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-100 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm animate-in fade-in duration-300" @click="$emit('close')"></div>

        <div class="relative bg-white rounded-4xl shadow-2xl max-w-sm w-full p-8 animate-in fade-in zoom-in duration-200">

            <div :class="[
                'flex items-center justify-center w-20 h-20 mx-auto rounded-3xl mb-6 shadow-sm',
                variant === 'danger' ? 'bg-red-50 text-[#d90000]' : 'bg-blue-50 text-[#1a3a5a]'
            ]">
                <Ban v-if="variant === 'danger'" class="w-10 h-10" />
                <Info v-else class="w-10 h-10" />
            </div>

            <div class="text-center space-y-3">
                <h3 class="text-xl font-bold text-neutral-900 leading-tight">
                    {{ title }}
                </h3>
                <p class="text-sm text-neutral-500 leading-relaxed font-medium">
                    {{ message }} <br v-if="itemName">
                    <span v-if="itemName" class="font-bold text-[#d90000] text-base block mt-1">
                        "{{ itemName }}"
                    </span>
                </p>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <Button
                    variant="outline"
                    class="flex-1 rounded-2xl border-neutral-200 text-neutral-500 hover:bg-neutral-50 font-semibold"
                    @click="$emit('close')"
                >
                    Cancelar
                </Button>
                <Button
                    :variant="variant === 'danger' ? 'destructive' : 'default'"
                    class="flex-1 rounded-2xl font-semibold shadow-lg transition-transform active:scale-95"
                    @click="$emit('confirm')"
                >
                    {{ confirmLabel || 'Confirmar' }}
                </Button>
            </div>
        </div>
    </div>
</template>
