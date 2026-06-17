<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { KeyRound, ShieldCheck } from 'lucide-vue-next';

const form = useForm({
    password:              '',
    password_confirmation: '',
});

function submit() {
    form.post('/dashboard/cambiar-contrasena', {  // 👈 agrega /dashboard/
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Cambiar Contraseña" />
    <AuthLayout
        title="Cambia tu contraseña"
        description="Por seguridad, debes establecer una nueva contraseña antes de continuar."
    >
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 mb-6">
            <ShieldCheck class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" />
            <div>
                <p class="text-sm font-bold text-amber-800">Primer inicio de sesión</p>
                <p class="text-xs text-amber-700 mt-0.5">
                    Tu contraseña actual es temporal. Crea una nueva con al menos
                    <span class="font-bold">8 caracteres</span>,
                    <span class="font-bold">una mayúscula</span> y
                    <span class="font-bold">un número</span>.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-5">
            <div class="grid gap-2">
                <Label for="password">
                    <KeyRound class="w-4 h-4 inline mr-1 text-[#1a3a5a]" />
                    Nueva Contraseña
                </Label>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    placeholder="Mínimo 8 caracteres, 1 mayúscula y 1 número"
                    required
                    autofocus
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirmar Nueva Contraseña</Label>
                <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    placeholder="Repite la contraseña"
                    required
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                :disabled="form.processing || !form.password"
                class="w-full mt-2 bg-[#1a3a5a] text-white h-11 rounded-xl font-semibold"
            >
                {{ form.processing ? 'Guardando...' : 'Establecer nueva contraseña' }}
            </Button>
        </form>
    </AuthLayout>
</template>
