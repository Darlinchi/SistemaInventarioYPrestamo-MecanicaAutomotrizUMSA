<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { PanelLeft } from "lucide-vue-next"
import { cn } from "@/lib/utils"
import { Button } from '@/components/ui/button'
import { useSidebar } from "./utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const { toggleSidebar } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    data-slot="sidebar-trigger"
    variant="ghost"
    size="icon"
    :class="cn('h-10 w-10', props.class)"
    @click="toggleSidebar"
  >
    <PanelLeft class="w-7 h-7 text-[#1a3a5a] stroke-[2.5]" />
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>
