<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { disable, enable, show } from '@/routes/two-factor';
import { BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { onUnmounted, ref } from 'vue';

interface Props {
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Autenticación de Dos Factores',
        href: show.url(),
    },
];

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => {
    clearTwoFactorAuthData();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Seguridad - 2FA" />
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Autenticación de Dos Factores"
                    description="Administra la configuración de seguridad adicional para tu cuenta institucional."
                />

                <div
                    v-if="!twoFactorEnabled"
                    class="flex flex-col items-start justify-start space-y-4"
                >
                    <Badge variant="destructive" class="font-black uppercase tracking-widest px-3">Desactivado</Badge>

                    <p class="text-sm text-muted-foreground leading-relaxed">
                        Cuando habilitas la autenticación de dos factores, se te solicitará un código PIN seguro
                        durante el inicio de sesión. Puedes obtener este código desde una aplicación
                        compatible con TOTP (como Google Authenticator) en tu teléfono móvil.
                    </p>

                    <div>
                        <Button
                            v-if="hasSetupData"
                            @click="showSetupModal = true"
                            class="bg-[#1a3a5a] hover:bg-[#122a42] font-bold"
                        >
                            <ShieldCheck class="w-4 h-4 mr-2" /> Continuar Configuración
                        </Button>
                        <Form
                            v-else
                            v-bind="enable.form()"
                            @success="showSetupModal = true"
                            #default="{ processing }"
                        >
                            <Button
                                type="submit"
                                :disabled="processing"
                                class="bg-[#1a3a5a] hover:bg-[#122a42] font-bold"
                            >
                                <ShieldCheck class="w-4 h-4 mr-2" /> Activar Seguridad 2FA
                            </Button>
                        </Form>
                    </div>
                </div>

                <div
                    v-else
                    class="flex flex-col items-start justify-start space-y-4"
                >
                    <Badge variant="default" class="bg-emerald-500 font-black uppercase tracking-widest px-3">Activado</Badge>

                    <p class="text-sm text-muted-foreground leading-relaxed">
                        La autenticación de dos factores está activa. Ahora tu cuenta es más segura.
                        Durante el inicio de sesión, deberás introducir el PIN generado por la aplicación en tu teléfono.
                    </p>

                    <TwoFactorRecoveryCodes />

                    <div class="relative inline">
                        <Form v-bind="disable.form()" #default="{ processing }">
                            <Button
                                variant="destructive"
                                type="submit"
                                :disabled="processing"
                                class="font-bold"
                            >
                                <ShieldBan class="w-4 h-4 mr-2" />
                                Desactivar 2FA
                            </Button>
                        </Form>
                    </div>
                </div>

                <TwoFactorSetupModal
                    v-model:isOpen="showSetupModal"
                    :requiresConfirmation="requiresConfirmation"
                    :twoFactorEnabled="twoFactorEnabled"
                />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
