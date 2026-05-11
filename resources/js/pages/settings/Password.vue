<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/user-password';
import { Form, Head } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Seguridad',
        href: edit().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Cambiar Contraseña" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Actualizar Contraseña"
                    description="Asegúrate de que tu cuenta utilice una contraseña larga y aleatoria para mantenerla segura."
                />

                <Form
                    v-bind="PasswordController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="[
                        'password',
                        'password_confirmation',
                        'current_password',
                    ]"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="current_password" class="font-bold text-[#1a3a5a]">Contraseña Actual</Label>
                        <Input
                            id="current_password"
                            name="current_password"
                            type="password"
                            class="mt-1 block w-full bg-neutral-50"
                            autocomplete="current-password"
                            placeholder="Introduce tu contraseña actual"
                        />
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password" class="font-bold text-[#1a3a5a]">Nueva Contraseña</Label>
                        <Input
                            id="password"
                            name="password"
                            type="password"
                            class="mt-1 block w-full bg-neutral-50"
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation" class="font-bold text-[#1a3a5a]">Confirmar Nueva Contraseña</Label>
                        <Input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="mt-1 block w-full bg-neutral-50"
                            autocomplete="new-password"
                            placeholder="Repite la nueva contraseña"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            class="bg-[#1a3a5a] hover:bg-[#122a42] px-8 py-2 rounded-xl font-bold uppercase text-xs tracking-widest shadow-md transition-all active:scale-95"
                        >
                            {{ processing ? 'Procesando...' : 'Actualizar Contraseña' }}
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-emerald-600 font-bold"
                            >
                                ¡Contraseña actualizada!
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
