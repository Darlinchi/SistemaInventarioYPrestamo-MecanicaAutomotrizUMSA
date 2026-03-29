<script setup lang="ts">
import { SquarePen, Trash, Building2, Phone, MapPin, AlignLeft } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import BaseTable from '@/components/table/BaseTable.vue';
import TableHeader from '@/components/table/TableHeader.vue';
import TableAction from '@/components/table/TableAction.vue';
import maintenanceCompanyRoutes from '@/routes/maintenanceCompanies';

defineProps<{
    maintenanceCompanies: any[];
}>();

const emit = defineEmits(['delete']);
</script>

<template>
    <BaseTable :items="maintenanceCompanies" emptyText="No hay empresas de mantenimiento registradas">
        <TableHeader :columns="['NOMBRE', 'CONTACTO', 'UBICACIÓN / DIRECCIÓN', 'DESCRIPCIÓN', 'ACCIONES']" />

        <tbody class="divide-y divide-neutral-100 text-sm">
            <tr v-for="company in maintenanceCompanies" :key="company.id" class="hover:bg-neutral-50/50 transition-colors group">

                <td class="p-4 pl-8">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <Building2 class="w-4 h-4 text-[#1a3a5a]" />
                        </div>
                        <span class="font-bold text-neutral-900">{{ company.nombre_empresa }}</span>
                    </div>
                </td>

                <td class="p-4">
                    <div class="flex items-center gap-2 text-neutral-700">
                        <Phone class="w-3.5 h-3.5 text-neutral-400" />
                        <span class="font-medium">{{ company.telefono || 'Sin teléfono' }}</span>
                    </div>
                </td>

                <td class="p-4">
                    <div class="flex items-start gap-2 max-w-xs text-neutral-600">
                        <MapPin class="w-3.5 h-3.5 text-red-400 shrink-0 mt-0.5" />
                        <span class="leading-tight">{{ company.direccion }}</span>
                    </div>
                </td>

                <td class="p-4">
                    <div class="flex items-center gap-2 text-neutral-500 italic">
                        <AlignLeft class="w-3.5 h-3.5 shrink-0" />
                        <p class="max-w-[250px] truncate text-xs" :title="company.descripcion_empresa">
                            {{ company.descripcion_empresa || 'Sin descripción' }}
                        </p>
                    </div>
                </td>

                <td class="p-4 pr-8 text-right">
                    <div class="flex justify-end gap-2">
                        <Link :href="maintenanceCompanyRoutes.edit.url(company.id)">
                            <TableAction
                                :icon="SquarePen"
                                variant="edit"
                                title="Editar empresa"
                            />
                        </Link>

                        <TableAction
                            :icon="Trash"
                            variant="delete"
                            title="Eliminar empresa"
                            @click="$emit('delete', company.id)"
                        />
                    </div>
                </td>
            </tr>
        </tbody>
    </BaseTable>
</template>
