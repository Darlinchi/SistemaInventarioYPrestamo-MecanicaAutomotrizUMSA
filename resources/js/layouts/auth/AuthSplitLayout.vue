<script setup lang="ts">
import { home } from '@/routes';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0 bg-white"
    >
        <div
            class="relative hidden h-full flex-col bg-white p-10 text-black lg:flex border-r border-neutral-200"
        >
            <
            <div class="absolute inset-0 bg-white" />
            
            <Link
                :href="home()"
                class="relative z-20 flex items-center text-lg font-semibold tracking-tight text-black"
            >
                <div class="flex flex-col items-center space-y-4 text-center w-full">
                    <!-- En qui esta el titulo del sistema y el logo de la carrera -->
                    <span class="text-2xl uppercase tracking-wider">{{ name }}</span>
                    <AppLogoIcon class="mr-2 size-8 fill-current text-white"/>
                </div>
            </Link>
            <div v-if="quote" class="flex flex-col items-center relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <!-- En message se puso el nombre de la carrera -->
                    <p class="text-xl">&ldquo;{{ quote.message }}&rdquo;</p>
                    <footer class="text-sm text-neutral-300">
                        {{ quote.author }}
                    </footer>
                </blockquote>
            </div>
        </div>
        
        <div class="h-full lg:p-8 bg-neutral-50 flex items-center justify-center">
            <!--<div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]"
            >-->
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[380px] bg-white p-8 rounded-2xl shadow-sm border border-neutral-100"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
