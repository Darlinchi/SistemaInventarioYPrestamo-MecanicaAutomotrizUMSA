<script setup lang="ts">
import { CircleCheck, XIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    message: string | null | undefined; // Recibe el mensaje directamente de flash.success
}>();

const visible = ref(false);

// Vigilamos si llega un mensaje nuevo para mostrar la alerta
watch(() => props.message, (newMessage) => {
    if (newMessage) {
        visible.value = true;
        // El componente se auto-limpia solo
        setTimeout(() => {
            visible.value = false;
        }, 5000);
    }
}, { immediate: true });
</script>

<template>
    <transition
        enter-active-class="duration-300 ease-out"
        enter-from-class="transform opacity-0 -translate-y-4"
        enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible && message"
            class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-2xl flex items-center justify-between shadow-sm"
        >
            <div class="flex items-center gap-3">
                <CircleCheck class="h-5 w-5 text-green-500"/>
                <p class="text-sm font-bold text-green-800 tracking-tight">
                    {{ message }}
                </p>
            </div>
            <button @click="visible = false" class="hover:scale-110 transition-transform">
                <XIcon class="h-5 w-5 text-green-500"/>
            </button>
        </div>
    </transition>
</template>
