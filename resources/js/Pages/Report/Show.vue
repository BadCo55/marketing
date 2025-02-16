<template>
    <Head :title="pageTitle" />
    <Layout>
        <div class="root-container">
            <div class="flex items-center justify-between rounded p-5 mb-5 bg-surface-0 dark:bg-surface-900 dark:text-surface-300 shadow">
                <p class="text-2xl font-bold">
                    {{ report.formatted_type }} Report ({{ report.report_type === 'daily' ? report.formatted_start_date : report.formatted_start_date + '-' + report.formatted_end_date }})
                </p>
                <div class="text-end">
                    <Button
                        :label="isSyncing ? 'Syncing...' : 'Sync Report Data'"
                        :disabled="isSyncing"
                        :icon="isSyncing ? 'pi pi-spin pi-spinner' : 'pi pi-sync'"
                        @click="onSyncData"
                    />
                    <p class="text-xs mt-1">Latest Data: {{ report.formatted_updated_at }}</p>
                </div>
            </div>
            <div class="flex w-full mb-5">
                <Card class="w-full">
                    <template #content>
                        <div class="flex gap-5">
                            <div class="w-full me-5 text-center">
                                <DataTable
                                    :value="inspectionsTableData"
                                    class="p-datatable-sm"
                                >
                                    <!-- Hide default headers -->
                                    <Column header="" headerStyle="display: none">
                                        <template #body="slotProps">
                                            <strong>{{ slotProps.data.label }}</strong>
                                        </template>
                                    </Column>

                                    <Column header="" headerStyle="display: none" class="!text-end">
                                        <template #body="slotProps">
                                            <span>{{ slotProps.data.value }}</span>
                                        </template>
                                    </Column>
                                </DataTable>
                            </div>
                            <div class="w-full ms-5 text-center">
                                <DataTable
                                    :value="marketingTableData"
                                    class="p-datatable-sm"
                                >
                                    <!-- Hide default headers -->
                                    <Column header="" headerStyle="display: none">
                                        <template #body="slotProps">
                                            <strong>{{ slotProps.data.label }}</strong>
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
                </Card>
            </div>
            <div class="flex gap-5 dark:text-surface-300">
                <div class="w-full space-y-5">
                    <Card>
                        <template #title>
                            <div class="flex justify-center">
                                Inspections That Took Place
                            </div>
                            <Divider />
                        </template>
                        <template #content>
                            <div class="flex items-center mb-5">
                                <span class="w-[1rem] h-[1rem] rounded bg-green-500 bg-opacity-50"></span>
                                <span class="ml-2 text-sm font-medium">Existing Realtor</span>
                            </div>
                            <DataTable
                                :value="tableData.occurred"
                                paginator
                                :rows="5"
                                :rowClass="getRowClass"
                            >
                                <Column header="ID" field="id"></Column>
                                <!-- <Column header="Inspector" field="inspector"></Column> -->
                                <Column header="Customer" field="customer"></Column>
                                <Column header="Agent" field="agent"></Column>
                                <!-- <Column header="Paid" field="is_paid"></Column> -->
                                <!-- <Column header="Square Footage" field="sqft"></Column> -->
                                <Column header="Fee" field="total_fee"></Column>
                            </DataTable>
                        </template>
                    </Card>
                    <Card>
                        <template #title>
                            <div class="flex justify-center">
                                Inspections Booked
                            </div>
                            <Divider />
                        </template>
                        <template #content>
                            <div class="flex items-center mb-5">
                                <span class="w-[1rem] h-[1rem] rounded bg-green-500 bg-opacity-50"></span>
                                <span class="ml-2 text-sm font-medium">Existing Realtor</span>
                            </div>
                            <DataTable
                                :value="tableData.scheduled"
                                paginator
                                :rows="5"
                                :rowClass="getRowClass"
                            >
                                <template #empty>
                                    No inspections found.
                                </template>
                                <Column header="ID" field="id"></Column>
                                <!-- <Column header="Inspector" field="inspector"></Column> -->
                                <Column header="Customer" field="customer"></Column>
                                <Column header="Agent" field="agent"></Column>
                                <!-- <Column header="Paid" field="is_paid"></Column> -->
                                <!-- <Column header="Square Footage" field="sqft"></Column> -->
                                <Column header="Fee" field="total_fee"></Column>
                            </DataTable>
                        </template>
                    </Card>
                </div>
                <div class="w-full">
                    <Card>
                        <template #content>
                            <Chart type="bar" :data="chartData" :options="chartOptions" />
                            <div class="mt-5">
                                <DataTable
                                    :value="revenueTableData"
                                    class="p-datatable-sm"
                                >
                                    <!-- Hide default headers -->
                                    <Column header="" headerStyle="display: none">
                                        <template #body="slotProps">
                                            <strong>{{ slotProps.data.label }}</strong>
                                        </template>
                                    </Column>

                                    <Column header="" headerStyle="display: none">
                                        <template #body="slotProps">
                                            <span>{{ slotProps.data.value }}</span>
                                        </template>
                                    </Column>
                                </DataTable>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Chart from 'primevue/chart';
import { Button, Card, DataTable, Column, Divider } from 'primevue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Layout from '@/Layouts/Layout.vue';

const page = usePage();
const report = page.props.report;
const data = page.props.data;
const tableData = data.tabled_data;
// const vonageCalls = page.props.vonage_calls;
const pageTitle = 'Report [' + report.formatted_start_date + '-' + report.formatted_end_date + ']';

const getRowClass = (data) => {
    if (data.row_class) {
        return '!bg-green-500 !bg-opacity-25';
    }
}

const isSyncing = ref(false);

const onSyncData = () => {
    isSyncing.value = true;
    router.post(route('reports.sync', report), {}, {
        onSuccess: () => {
            router.reload({ only: ['report', 'data'] }); // Reload only the report & data
        },
        onFinish: () => isSyncing.value = false
    });
};

const revenueTableData = computed(() => [
    { label: "Revenue Received", value: `$${data.revenue_received.toFixed(2)}` },
    { label: "Scheduled Revenue", value: `$${data.revenue_scheduled.toFixed(2)}` },
    { label: "Passed Due", value: `$${data.revenue_passed_due.toFixed(2)}` },
]);

const inspectionsTableData = computed(() => [
    { label: "# Scheduled/# Paid", value: `${data.scheduled_count}/${data.scheduled_paid}` },
    { label: "# Occurred/# Paid", value: `${data.occurred_count}/${data.occurred_paid}` },
    { label: "Revenue Received", value: `$${data.revenue_received.toFixed(2)}` },
    { label: "Scheduled Revenue", value: `$${data.revenue_scheduled.toFixed(2)}` },
    { label: "Passed Due", value: `$${data.revenue_passed_due.toFixed(2)}` },
]);

const marketingTableData = computed(() => [
    { label: "# Office Visits", value: `${data.office_visit_count}` },
    { label: "# Realtors Created", value: `${data.realtors_created_count}` },
    { label: "# Offices Created", value: `${data.offices_created_count}` },
    { label: "# Completed Tasks", value: `${data.tasks_completed_count}` },
]);

const chartData = computed(() => ({
    labels: ['Received Revenue', 'Scheduled Revenue', 'Passed Due'], // Labels should match the data length
    datasets: [
        {
            label: ['Revenue'],
            data: [
                data.revenue_received || 0,
                data.revenue_scheduled || 0,
                data.revenue_passed_due || 0,
            ],
            backgroundColor: [
                'rgba(249, 115, 22, 0.2)',  // Orange for Received
                'rgba(6, 182, 212, 0.2)',
                'rgba(139, 92, 246, 0.2)'   // Cyan for Scheduled
            ],
            borderColor: [
                'rgb(249, 115, 22)',
                'rgb(6, 182, 212)',
                'rgb(139, 92, 246)'
            ],
            borderWidth: 1
        }
    ]
}));

const chartOptions = computed(() => ({
    responsive: true,
    plugins: {
        legend: {
            labels: {
                color: 'white' // Adjust based on dark mode
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                color: 'white' // Adjust for dark mode
            }
        },
        x: {
            ticks: {
                color: 'white' // Adjust for dark mode
            }
        }
    }
}));

</script>
