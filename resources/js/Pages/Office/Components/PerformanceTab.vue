<template>
    <div class="space-y-5">
        <div>
            <h3 class="text-xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">
                Performance
            </h3>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                Key office performance metrics and recent activity.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="item in metricCards"
                :key="item.label"
                :class="[
                    'rounded-2xl border border-surface-200 bg-surface-0 p-5 shadow-sm dark:border-surface-700 dark:bg-surface-900',
                    item.label === 'Last Inspection Booked' ? 'md:col-span-2' : ''
                ]"
            >
                <div class="mb-3 inline-flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-100 dark:bg-surface-800">
                        <i :class="item.class"></i>
                    </div>
                    <p class="text-sm font-medium text-surface-500 dark:text-surface-400">
                        {{ item.label }}
                    </p>
                </div>

                <p class="text-2xl font-semibold leading-tight text-surface-900 dark:text-surface-0">
                    {{ item.value }}
                </p>

                <p
                    v-if="item.helper"
                    class="mt-2 text-sm text-surface-500 dark:text-surface-400"
                >
                    {{ item.helper }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatCurrency } from '@/utilities/formatCurrency';

const props = defineProps({
    office: Object,
});

const metricCards = computed(() => [
    {
        class: 'pi pi-hashtag text-yellow-500',
        label: 'Inspections Scheduled',
        value: props.office.total_inspections_scheduled ?? 0,
    },
    {
        class: 'pi pi-dollar text-green-500',
        label: 'Revenue Received',
        value: formatCurrency(props.office.total_revenue_received ?? 0),
    },
    {
        class: 'pi pi-users text-blue-500',
        label: 'Active Agents / Total Agents',
        value: `${props.office.active_agents_count ?? 0} / ${props.office.realtor_count ?? 0}`,
    },
    {
        class: 'pi pi-user-minus text-red-500',
        label: 'Inactive Agents',
        value: props.office.inactive_agents_count ?? 0,
    },
    {
        class: 'pi pi-calendar text-cyan-500',
        label: 'Last Inspection Booked',
        value: props.office.latest_inspection_date || 'No inspections found',
        helper: props.office.latest_inspection_date ? '' : 'No inspection activity recorded yet.',
        isWide: true,
    },
]);
</script>
