<template>
    <Head title="Reports" />

    <Layout>
        <GetDataDialog
            v-model:visible="isDialogVisible"
            @newReports="onNewReports"
        />

        <div class="flex h-full min-h-0 flex-col">
            <!-- PAGE HEADER -->
            <div class="mb-5 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Reports</h1>
                        <p class="mt-1 text-sm text-surface-500">
                            Review generated reports, create new ones, and manage report history.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button
                            label="Generate a Report"
                            icon="pi pi-plus-circle"
                            @click="isDialogVisible = true"
                        />
                    </div>
                </div>
            </div>

            <!-- TABLE SHELL -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div
                    class="flex h-full min-h-0 flex-col overflow-hidden rounded-2xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <DataTable
                        :value="reports"
                        paginator
                        scrollable
                        :scrollHeight="scrollHeight"
                        stripedRows
                        :rows="10"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        tableStyle="min-width: 48rem"
                        class="h-full"
                        @row-click="onRowClick"
                        :pt="{
                            bodyRow: { class: 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors' }
                        }"
                    >
                        <template #header>
                            <div class="flex flex-col gap-4 p-1">
                                <Message
                                    v-if="!dailyReportExists"
                                    severity="warn"
                                    icon="pi pi-exclamation-circle"
                                    :closable="false"
                                >
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-medium">Today's daily report has not been generated.</p>
                                            <p class="text-sm opacity-80">
                                                Generate it now to keep your reporting current.
                                            </p>
                                        </div>

                                        <Button
                                            label="Generate Now"
                                            icon="pi pi-bolt"
                                            @click="generateDailyReport"
                                            :loading="isGeneratingDailyReport"
                                        />
                                    </div>
                                </Message>

                                <div class="flex flex-col gap-1">
                                    <h2 class="text-xl font-semibold">Report Directory</h2>
                                    <p class="text-sm text-surface-500">
                                        Click any row to open a report. Use the delete action only when you want to permanently remove one.
                                    </p>
                                </div>

                                <div class="flex items-center justify-end text-sm text-surface-500">
                                    {{ reports.length }} {{ reports.length === 1 ? 'report' : 'reports' }}
                                </div>
                            </div>
                        </template>

                        <template #empty>
                            <div class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                                <i class="pi pi-chart-bar text-3xl text-surface-400" />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No reports found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500">
                                    Generate your first report to start tracking and reviewing historical data.
                                </p>
                                <Button
                                    label="Generate a Report"
                                    icon="pi pi-plus-circle"
                                    @click="isDialogVisible = true"
                                />
                            </div>
                        </template>

                        <template #loading>
                            Loading report data. Please wait.
                        </template>

                        <Column
                            field="formatted_type"
                            header="Report Type"
                            sortable
                            headerClass="w-[18rem]"
                            bodyClass="w-[18rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">{{ data.formatted_type }}</p>
                                </div>
                            </template>
                        </Column>

                        <Column
                            field="formatted_start_date"
                            header="Start Date"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        />

                        <Column
                            field="formatted_end_date"
                            header="End Date"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        />

                        <!-- CHANGED: subtle chevron column instead of separate view button -->
                        <Column
                            headerClass="w-[4rem]"
                            bodyClass="w-[4rem]"
                        >
                            <template #body>
                                <div class="flex justify-center">
                                    <i class="pi pi-angle-right text-surface-400"></i>
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: keep delete as the only explicit action, but stop row navigation when clicked -->
                        <Column
                            class="w-[5rem] !text-center"
                            alignFrozen="right"
                            :frozen="true"
                        >
                            <template #body="{ data }">
                                <div class="flex items-center justify-center">
                                    <Button
                                        icon="pi pi-trash"
                                        severity="danger"
                                        rounded
                                        text
                                        aria-label="Delete report"
                                        @click.stop="deleteReport(data.id)"
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import { Button, DataTable, Column, Message } from 'primevue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import GetDataDialog from './Components/GetDataDialog.vue';

const page = usePage();

const reports = ref(page.props.reports ?? []);
const dailyReportExists = ref(!page.props.missing_daily_report);
const isDialogVisible = ref(false);
const isGeneratingDailyReport = ref(false);

const onNewReports = (newReports) => {
    reports.value = newReports ?? [];
};

const generateDailyReport = () => {
    isGeneratingDailyReport.value = true;

    router.post(
        route('reports.generate.today'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ['flash', 'reports', 'missing_daily_report'],
            onSuccess: (response) => {
                reports.value = response.props.reports ?? reports.value;
                dailyReportExists.value =
                    response.props.missing_daily_report !== undefined
                        ? !response.props.missing_daily_report
                        : true;
            },
            onError: (error) => {
                console.log(error);
            },
            onFinish: () => {
                isGeneratingDailyReport.value = false;
            },
        }
    );
};

const deleteReport = (id) => {
    if (!confirm('Are you sure you want to delete this report?')) return;

    router.delete(route('reports.destroy', id), {
        preserveScroll: true,
        only: ['flash', 'reports', 'missing_daily_report'],
        onSuccess: (response) => {
            reports.value = response.props.reports ?? reports.value;

            if (response.props.missing_daily_report !== undefined) {
                dailyReportExists.value = !response.props.missing_daily_report;
            }
        },
        onError: (errors) => {
            console.error('Error deleting report:', errors);
        },
    });
};

const onRowClick = (event) => {
    router.visit(route('reports.show', event.data.id));
};

const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024;
};

onMounted(() => {
    checkBreakpoint();
    window.addEventListener('resize', checkBreakpoint);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});

const scrollHeight = computed(() => 'flex');

const latestReportRange = computed(() => {
    if (!reports.value.length) return '—';

    const latest = reports.value[0];

    if (latest?.formatted_start_date && latest?.formatted_end_date) {
        return `${latest.formatted_start_date} - ${latest.formatted_end_date}`;
    }

    return latest?.formatted_end_date || latest?.formatted_start_date || '—';
});
</script>
