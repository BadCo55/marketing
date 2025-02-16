<template>
    <div>
        <div class="w-full me-5 text-center">
            <DataTable
                :value="tableData"
                class="p-datatable-sm"
            >
                <!-- Hide default headers -->
                <Column header="" headerStyle="display: none">
                    <template #body="slotProps">
                        <div class="inline-flex items-center gap-2">
                            <i :class="slotProps.data.class"></i>
                            <p class="text-lg font-light">{{ slotProps.data.label }}</p>
                        </div>
                    </template>
                </Column>

                <Column header="" headerStyle="display: none" class="!text-end">
                    <template #body="slotProps">
                        <span>{{ slotProps.data.value }}</span>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>

<script setup>
import { DataTable, Column } from 'primevue';
import { computed } from 'vue';

const props = defineProps({
    office: Object,
});

const tableData = computed(() => [
    { class: 'pi pi-hashtag text-yellow-500', label: "Inspections Scheduled", value: props.office.total_inspections_scheduled },
    { class: 'pi pi-dollar text-green-500', label: "Revenue Received", value: props.office.total_revenue_received },
    { class: 'pi pi-hashtag text-yellow-500', label: "Active Agents / Total Agents", value: `${props.office.active_agents_count}/${props.office.realtor_count}` },
    { class: 'pi pi-hashtag text-yellow-500', label: "Inactive Agents", value: props.office.inactive_agents_count },
    { class: 'pi pi-calendar text-blue-500', label: "Last Inspection Booked", value: props.office.latest_inspection_date ? props.office.latest_inspection_date : 'No inspections found' },
]);
</script>

