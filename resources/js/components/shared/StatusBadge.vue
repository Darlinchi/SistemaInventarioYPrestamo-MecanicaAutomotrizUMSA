<script setup lang="ts">
const props = defineProps<{
  status: string;
}>();

const getStatusStyles = (status: string) => {
  const s = status.toLowerCase();
  // Configuración de colores institucionalizada
  if (['disponible', 'nuevo', 'bueno', 'activo', 'completado', 'devuelto', 'reparado'].includes(s))
    return 'bg-green-100 text-green-800 border-green-200';
  if (['dañado', 'incompleto', 'extraviado', 'vencido'].includes(s))
    return 'bg-red-100 text-red-800 border-red-200';
  if (['mantenimiento', 'pendiente'].includes(s))
    return 'bg-yellow-100 text-yellow-800 border-yellow-200';
  if (['prestado'].includes(s))
    return 'bg-orange-100 text-orange-800 border-orange-200';
  if (['baja', 'prestado', 'en curso'].includes(s))
    return 'bg-neutral-200 text-neutral-600 border-neutral-300';

  return 'bg-neutral-200 text-neutral-600 border-neutral-300';
};
</script>

<template>
  <span :class="[
    'px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase border tracking-wide inline-flex items-center justify-center',
    getStatusStyles(status)
  ]">
    {{ status }}
  </span>
</template>
