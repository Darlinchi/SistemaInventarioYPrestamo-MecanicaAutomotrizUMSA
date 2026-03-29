<script setup lang="ts">
import { useVModel } from '@vueuse/core'
import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: string
  modelValue?: string | number
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits)
</script>

<template>
    <textarea
        v-model="modelValue"
        data-slot="textarea"
        :class="cn(
            /* Estilo base: Coherente con el Input */
            'flex min-h-24 w-full rounded-xl border border-neutral-200 bg-neutral-50/50 px-4 py-3 text-sm transition-all duration-300 placeholder:text-neutral-400 outline-none disabled:cursor-not-allowed disabled:opacity-50 resize-none',

            /* Foco: Azul Navy Institucional (Igual al Input) */
            'focus:bg-white focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5',

            props.class
        )"
    />
</template>
<!--
<template>
    <textarea
        v-model="modelValue"
        :class="cn('flex min-h-20 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50', props.class)"
    />
</template>
-->
