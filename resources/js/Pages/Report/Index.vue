<template>
    <Head title="Reports" />
    <Layout>
        <GetDataDialog v-model:visible="isDialogVisible" @newReports="(newReports) => reports = newReports"  />
        <div
            class="flex flex-col"
            :style="{
                height: 'calc(100vh-80px)',
                width: isLargeBreakpoint ? 'calc(100vw - 200px)' : '100vw'
            }"
        >
            <DataTable
                :value="reports"
                paginator
                scrollable
                :scrollHeight="`calc(100vh - 260px)`"

                showGridlines
                stripedRows
                :rows="10"
                :rowsPerPageOptions="[5, 10, 20, 50]"
            >
                <template #header>
                    <Message v-if="!dailyReportExists" severity="error" icon="pi pi-exclamation-circle" class="mb-3">
                        <div class="flex gap-5 items-center justify-between">
                            <div>
                                <p>Today's report has not been generated.</p>
                            </div>
                            <div>
                                <Button label="Generate Now" @click="generateDailyReport" />
                            </div>
                        </div>
                    </Message>
                    <div class="flex justify-end">
                        <Button label="Generate a Report" @click="isDialogVisible = true" />
                    </div>
                </template>
                <template #empty>
                    No reports found.
                </template>
                <Column field="formatted_type" header="Report Type"></Column>
                <Column field="formatted_start_date" header="Start Date"></Column>
                <Column field="formatted_end_date" header="End Date"></Column>
                <Column class="w-36 !text-center" alignFrozen="right" :frozen="true">
                    <template #body="{ data }">
                        <div class="flex gap-3">
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                rounded
                                @click="deleteReport(data.id)"
                            ></Button>
                            <Link
                                :href="route('reports.show', data.id)"
                            >
                                <Button icon="pi pi-search" severity="info" rounded></Button>
                            </Link>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
        <!-- <div class="p-8">
            <div>
                <Button label="Get Today's Data" @click="isDialogVisible = true" />
            </div>
            <div class="mt-5">
                <DataTable :value="reports">
                    <Column></Column>
                </DataTable>
            </div>
        </div> -->
    </Layout>
</template>

<script setup>
import { Button, DataTable, Column, Message } from 'primevue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import Layout from '@/Layouts/Layout.vue';
import GetDataDialog from './Components/GetDataDialog.vue';

const page = usePage();
const reports = ref(page.props.reports);
const dailyReportExists = ref(page.props.missing_daily_report);
const isDialogVisible = ref(false);

const generateDailyReport = () => {
    router.post(route('reports.generate.today'), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['flash', 'reports'],
        onSuccess: (response) => {
            dailyReportExists.value = true;
            reports.value = response.props.reports;
        },
        onError: (error) => {
            console.log(error)
        }
    });
}

const deleteReport = (id) => {
    if (!confirm("Are you sure you want to delete this report?")) return;

    router.delete(route('reports.destroy', id), {
        only: ['flash', 'reports'],
        onSuccess: (response) => {
            reports.value = response.props.reports;
            console.log("Report deleted successfully");
        },
        onError: (errors) => {
            console.error("Error deleting report:", errors);
        }
    });
};

// Detect if the screen is at least a large breakpoint (lg)
const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024; // lg breakpoint in Tailwind
};

onMounted(() => {
    checkBreakpoint(); // Initial check
    window.addEventListener('resize', checkBreakpoint); // Update on resize
});

// Clean up the event listener on component unmount
onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});

</script>
