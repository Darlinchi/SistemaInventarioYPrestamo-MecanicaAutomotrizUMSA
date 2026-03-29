<script setup lang="ts">
defineProps<{ columns: string[] }>();
</script>

<template>
    <thead class="bg-[#1a3a5a] text-[13px] font-semibold tracking-wide text-white">
        <tr>
            <th v-for="(col, index) in columns" :key="index"
                :class="[
                    'p-5 whitespace-nowrap',
                    index === 0 ? 'pl-8' : '',
                    index === columns.length - 1 ? 'pr-8 text-right' : ''
                ]">
                {{ col }}
            </th>
        </tr>
    </thead>
</template>
