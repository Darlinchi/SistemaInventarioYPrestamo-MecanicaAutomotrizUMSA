// ❌ Lógica de fecha básica, repetida en múltiples componentes probablemente
const formatDate = (dateString: string | null) => {
    if (!dateString) return '---';
    return dateString.split('T')[0];
};

// ✅ Centraliza esto en un composable o helper
// /composables/useDate.ts
export const formatDate = (d: string | null) =>
    d ? new Date(d).toLocaleDateString('es-BO') : '---';
