<script setup lang="ts">
import { defineProps } from 'vue'

interface PayRun {
    taxYear?: string,
    period?: string,
}

interface Employer {
    id: string,
    name: string,
    logoUrl?: string,
    crn?: string,
    employeeCount?: number,
    currentPayRun?: PayRun,
    dateSynced?: string,
}

interface Props {
    employer?: Employer,
}

const props = defineProps<Props>()
</script>

<template>
    <a
        v-if="employer"
        :href="`/admin/staff-management/benefits/employers/${employer.id}`"
        :title="`Go to pay runs of ${employer.name}`"
        class="grid grid-cols-6 border-b border-solid border-gray-200 no-underline hover:bg-gray-200"
    >
        <div class="flex items-center col-span-2 whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-indigo-800 sm:pl-6">
            <div class="object-cover object-center w-6 h-6 rounded-full overflow-hidden mb-0">
                <img
                    :src="employer.logoUrl"
                    class="w-full h-full"
                >
            </div>
            <span style="margin-bottom:0">{{ employer.name }}</span>
        </div>
        <div class="flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ employer.crn ? employer.crn : '-' }}</div>
        <div class="flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">-</div>
        <div class="flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">-</div>
        <div class="flex items-center whitespace-nowrap px-3 py-4 text-sm text-gray-500">-</div>
    </a>
</template>