<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

// 1. Definimos una interfaz que extienda la original para incluir roles y username
interface AppUser extends User {
    username?: string;
    roles?: string[];
}

interface Props {
    user: AppUser; // Usamos nuestra interfaz extendida
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
// 2. Ajustamos los valores computados para que no fallen
const showAvatar = computed(
    () => props.user.avatar && props.user.avatar !== '',
);
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar" :src="props.user.avatar!" :alt="props.user.username" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(props.user.username || 'U') }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-semibold">{{ props.user?.username || 'Usuario' }}</span>
        
        <span class="truncate text-xs text-muted-foreground">
            {{ props.user?.roles?.[0] || 'Sin Rol' }}
        </span>
    </div>
</template>
