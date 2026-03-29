<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { useVModel } from "@vueuse/core"
import { cn } from "@/lib/utils"

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes["class"]
}>()

const emits = defineEmits<{
  (e: "update:modelValue", payload: string | number): void
}>()

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
})
</script>

<template>
  <input
    v-model="modelValue"
    data-slot="input"
    :class="cn(
      /* Estilo base del input con tus nuevos colores */
      'flex h-11 w-full rounded-xl border border-neutral-200 bg-neutral-50/50 px-4 py-2 text-sm transition-all duration-300 file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-neutral-400 outline-none disabled:cursor-not-allowed disabled:opacity-50',
      /* Foco en Azul Navy suave */
      'focus:bg-white focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5',
      props.class
    )"
  >
</template>
<!--
<template>
  <input
    v-bind="props"
    :class="cn(
      /* Estilo base del input con tus nuevos colores */
      'flex h-11 w-full rounded-xl border border-neutral-200 bg-neutral-50/50 px-4 py-2 text-sm transition-all duration-300 file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-neutral-400 outline-none disabled:cursor-not-allowed disabled:opacity-50',
      /* Foco en Azul Navy suave */
      'focus:bg-white focus:border-[#1a3a5a] focus:ring-4 focus:ring-[#1a3a5a]/5',
      props.class
    )"
  >
</template>
-->
