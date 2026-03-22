<template>
    <Head :title="pageTitle" />
    <Layout>
        <!-- CHANGED: Replaced root-container with a responsive page shell -->
        <div class="mx-auto w-full max-w-7xl px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: Rebuilt the page header into a cleaner responsive card -->
            <div
                class="mb-5 rounded-2xl border border-surface-200 bg-surface-0 p-5 shadow-sm dark:border-surface-800 dark:bg-surface-900"
            >
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <h1 class="text-2xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                            {{ report.formatted_type }} Report
                        </h1>

                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            {{
                                report.report_type === 'daily'
                                    ? report.formatted_start_date
                                    : `${report.formatted_start_date} - ${report.formatted_end_date}`
                            }}
                        </p>

                        <!-- CHANGED: Moved latest sync text into the title area for better hierarchy -->
                        <p class="mt-2 text-xs text-surface-500 dark:text-surface-400">
                            Latest Data: {{ report.formatted_updated_at }}
                        </p>
                    </div>

                    <!-- CHANGED: Sync button block now aligns better on mobile and desktop -->
                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row lg:flex-col lg:items-end">
                        <Button
                            :label="isSyncing ? 'Syncing...' : 'Sync Report Data'"
                            :disabled="isSyncing"
                            :icon="isSyncing ? 'pi pi-spin pi-spinner' : 'pi pi-sync'"
                            @click="onSyncData"
                        />
                    </div>
                </div>
            </div>

            <!-- CHANGED: Replaced the two mini DataTables with stat cards for better readability -->
            <div class="mb-5 grid grid-cols-1 gap-4 xl:grid-cols-3">
                <!-- INSPECTIONS SUMMARY -->
                <Card
                    class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <template #content>
                        <div class="space-y-4">
                            <div>
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                    Inspection Summary
                                </h2>
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    Scheduled, occurred, and paid totals.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div
                                    v-for="item in inspectionsTableData"
                                    :key="item.label"
                                    class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                        {{ item.label }}
                                    </p>
                                    <p class="mt-2 text-lg font-semibold text-surface-900 dark:text-surface-0">
                                        {{ item.value }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- MARKETING SUMMARY -->
                <Card
                    class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <template #content>
                        <div class="space-y-4">
                            <div>
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                    Marketing Summary
                                </h2>
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    Outreach and productivity metrics for the period.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div
                                    v-for="item in marketingTableData"
                                    :key="item.label"
                                    class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                        {{ item.label }}
                                    </p>
                                    <p class="mt-2 text-lg font-semibold text-surface-900 dark:text-surface-0">
                                        {{ item.value }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- REVENUE SUMMARY -->
                <Card
                    class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <template #content>
                        <div class="space-y-4">
                            <div>
                                <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                    Revenue Summary
                                </h2>
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    Revenue performance for the selected report period.
                                </p>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="item in revenueTableData"
                                    :key="item.label"
                                    class="flex items-center justify-between rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">
                                        {{ item.label }}
                                    </span>
                                    <span class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                        {{ item.value }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- CHANGED: Rebuilt lower section into a more balanced responsive grid -->
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-12">
                <!-- LEFT SIDE TABLES -->
                <div class="space-y-5 xl:col-span-8">
                    <Card
                        class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <template #title>
                            <!-- CHANGED: Improved card heading layout -->
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                            Inspections That Took Place
                                        </h2>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Completed inspections during this report period.
                                        </p>
                                    </div>
                                </div>

                                <Divider class="!my-0" />
                            </div>
                        </template>

                        <template #content>
                            <!-- CHANGED: Legend styling improved -->
                            <div class="mb-4 flex items-center">
                                <span class="h-3 w-3 rounded bg-green-500/40"></span>
                                <span class="ml-2 text-sm font-medium text-surface-700 dark:text-surface-300">
                                    Existing Realtor
                                </span>
                            </div>

                            <DataTable
                                :value="tableData.occurred"
                                paginator
                                :rows="5"
                                :rowClass="getRowClass"
                                stripedRows
                                responsiveLayout="scroll"
                            >
                                <template #empty>
                                    No inspections found.
                                </template>

                                <Column header="ID" field="id"></Column>
                                <Column header="Customer" field="customer"></Column>
                                <Column header="Agent" field="agent"></Column>
                                <Column header="Fee" field="total_fee"></Column>
                            </DataTable>
                        </template>
                    </Card>

                    <Card
                        class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <template #title>
                            <!-- CHANGED: Improved second card heading layout -->
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                            Inspections Booked
                                        </h2>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Inspections scheduled during this report period.
                                        </p>
                                    </div>
                                </div>

                                <Divider class="!my-0" />
                            </div>
                        </template>

                        <template #content>
                            <div class="mb-4 flex items-center">
                                <span class="h-3 w-3 rounded bg-green-500/40"></span>
                                <span class="ml-2 text-sm font-medium text-surface-700 dark:text-surface-300">
                                    Existing Realtor
                                </span>
                            </div>

                            <DataTable
                                :value="tableData.scheduled"
                                paginator
                                :rows="5"
                                :rowClass="getRowClass"
                                stripedRows
                                responsiveLayout="scroll"
                            >
                                <template #empty>
                                    No inspections found.
                                </template>

                                <Column header="ID" field="id"></Column>
                                <Column header="Customer" field="customer"></Column>
                                <Column header="Agent" field="agent"></Column>
                                <Column header="Fee" field="total_fee"></Column>
                            </DataTable>
                        </template>
                    </Card>
                </div>

                <!-- RIGHT SIDE CHART -->
                <div class="xl:col-span-4">
                    <Card
                        class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <template #title>
                            <!-- CHANGED: Added chart card header -->
                            <div class="flex flex-col gap-2">
                                <div>
                                    <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                        Revenue Breakdown
                                    </h2>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">
                                        Comparison of received, scheduled, and past due revenue.
                                    </p>
                                </div>

                                <Divider class="!my-0" />
                            </div>
                        </template>

                        <template #content>
                            <!-- CHANGED: Added chart wrapper for better spacing -->
                            <div class="min-h-[320px]">
                                <Chart type="bar" :data="chartData" :options="chartOptions" />
                            </div>

                            <!-- CHANGED: Simplified revenue detail section below chart -->
                            <div class="mt-5 space-y-3">
                                <div
                                    v-for="item in revenueTableData"
                                    :key="item.label"
                                    class="flex items-center justify-between rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">
                                        {{ item.label }}
                                    </span>
                                    <span class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                        {{ item.value }}
                                    </span>
                                </div>
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
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Layout from '@/Layouts/Layout.vue';

const page = usePage();
const report = page.props.report;
const data = page.props.data;
const tableData = data.tabled_data;

// CHANGED: Improved page title formatting for daily vs ranged reports
const pageTitle = computed(() => {
    return report.report_type === 'daily'
        ? `Report [${report.formatted_start_date}]`
        : `Report [${report.formatted_start_date} - ${report.formatted_end_date}]`;
});

// CHANGED: Slightly safer row class helper
const getRowClass = (row) => {
    if (row.row_class) {
        return '!bg-green-500 !bg-opacity-25';
    }
    return '';
};

const isSyncing = ref(false);

// CHANGED: Added local dark mode tracking so chart colors update correctly
const isDarkMode = ref(false);
let mediaQuery = null;

const updateDarkMode = () => {
    isDarkMode.value = document.documentElement.classList.contains('dark');
};

onMounted(() => {
    updateDarkMode();

    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener?.('change', updateDarkMode);

    // CHANGED: Watch class changes on html for app-level theme toggles
    const observer = new MutationObserver(() => updateDarkMode());
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    });

    // CHANGED: Store observer for cleanup on unmount
    window.__reportThemeObserver = observer;
});

onUnmounted(() => {
    mediaQuery?.removeEventListener?.('change', updateDarkMode);

    if (window.__reportThemeObserver) {
        window.__reportThemeObserver.disconnect();
        delete window.__reportThemeObserver;
    }
});

const onSyncData = () => {
    isSyncing.value = true;

    router.post(route('reports.sync', report), {}, {
        onSuccess: () => {
            router.reload({ only: ['report', 'data'] });
        },
        onFinish: () => {
            isSyncing.value = false;
        },
    });
};

// CHANGED: Centralized currency formatter
const currency = (amount) => `$${Number(amount || 0).toFixed(2)}`;

const revenueTableData = computed(() => [
    { label: 'Revenue Received', value: currency(data.revenue_received) },
    { label: 'Scheduled Revenue', value: currency(data.revenue_scheduled) },
    { label: 'Past Due', value: currency(data.revenue_passed_due) },
]);

const inspectionsTableData = computed(() => [
    { label: 'Scheduled / Paid', value: `${data.scheduled_count}/${data.scheduled_paid}` },
    { label: 'Occurred / Paid', value: `${data.occurred_count}/${data.occurred_paid}` },
    { label: 'Revenue Received', value: currency(data.revenue_received) },
    { label: 'Scheduled Revenue', value: currency(data.revenue_scheduled) },
]);

const marketingTableData = computed(() => [
    { label: 'Office Visits', value: `${data.office_visit_count}` },
    { label: 'Realtors Created', value: `${data.realtors_created_count}` },
    { label: 'Offices Created', value: `${data.offices_created_count}` },
    { label: 'Completed Tasks', value: `${data.tasks_completed_count}` },
]);

// CHANGED: Chart data cleaned up
const chartData = computed(() => ({
    labels: ['Received Revenue', 'Scheduled Revenue', 'Past Due'],
    datasets: [
        {
            label: 'Revenue',
            data: [
                data.revenue_received || 0,
                data.revenue_scheduled || 0,
                data.revenue_passed_due || 0,
            ],
            backgroundColor: [
                'rgba(249, 115, 22, 0.2)',
                'rgba(6, 182, 212, 0.2)',
                'rgba(139, 92, 246, 0.2)',
            ],
            borderColor: [
                'rgb(249, 115, 22)',
                'rgb(6, 182, 212)',
                'rgb(139, 92, 246)',
            ],
            borderWidth: 1,
            borderRadius: 8,
        },
    ],
}));

// CHANGED: Chart options now react properly to light/dark mode
const chartOptions = computed(() => {
    const textColor = isDarkMode.value ? '#e5e7eb' : '#374151';
    const mutedTextColor = isDarkMode.value ? '#94a3b8' : '#6b7280';
    const gridColor = isDarkMode.value ? 'rgba(148, 163, 184, 0.18)' : 'rgba(15, 23, 42, 0.08)';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: textColor,
                },
            },
            tooltip: {
                callbacks: {
                    label: (context) => ` ${currency(context.raw)}`,
                },
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: mutedTextColor,
                    callback: (value) => `$${value}`,
                },
                grid: {
                    color: gridColor,
                },
            },
            x: {
                ticks: {
                    color: mutedTextColor,
                },
                grid: {
                    color: gridColor,
                },
            },
        },
    };
});
</script>
