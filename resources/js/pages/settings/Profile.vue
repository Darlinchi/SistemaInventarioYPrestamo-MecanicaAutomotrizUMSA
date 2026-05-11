<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { Form, Head, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Configuración de Perfil',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user as any;
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Perfil" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Información Personal"
                    description="Actualiza tu identidad en el sistema y tu correo de contacto."
                />

                <Form
                    v-bind="ProfileController.update.form()"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="name" class="font-bold text-[#1a3a5a]">Nombre Completo</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full bg-neutral-50"
                            v-model="user.name"
                            required
                            placeholder="Ej: Juan Pérez"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="username" class="font-bold text-[#1a3a5a]">Nombre de Usuario</Label>
                        <Input
                            id="username"
                            class="mt-1 block w-full bg-neutral-50"
                            name="username"
                            :default-value="user.username"
                            required
                            placeholder="Ej: jperez"
                        />
                        <InputError class="mt-2" :message="errors.username" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email" class="font-bold text-[#1a3a5a]">Correo Electrónico</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full bg-neutral-50"
                            name="email"
                            :default-value="user.email"
                            placeholder="correo@umsa.bo"
                        />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            class="bg-[#1a3a5a] hover:bg-[#122a42] px-10"
                        >
                            {{ processing ? 'Guardando...' : 'Guardar Cambios' }}
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="recentlySuccessful" class="text-sm text-green-600 font-medium">
                                Cambios guardados con éxito.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>

            </SettingsLayout>
    </AppLayout>
</template>
