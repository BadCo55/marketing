<template>
    <Head title="Tasks" />
    <Layout>
        <div
            class="flex flex-col"
            :style="{
                height: 'calc(100vh - 80px)',
                width: isLargeBreakpoint ? 'calc(100vw - 200px)' : '100vw'
            }"
        >
            <DataTable
                v-model:filters="filters"
                :value="props.tasks"
                removableSort
                scrollable
                :scrollHeight="`calc(100vh - 260px)`"
                stripedRows
                showGridlines
                paginator
                :rows="10"
                :rowsPerPageOptions="[10, 25, 50, 100]"
                :globalFilterFields="['status', 'formatted_date_created', 'formatted_due_date', 'title', 'formatted_assigned_to', 'formatted_created_by']"
            >
                <template #header>
                    <h2 class="text-xl font-medium mb-2">Filters</h2>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col lg:flex-row gap-2 bg-surface-100 dark:bg-surface-800 p-2 rounded">
                            <div>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText fluid v-model="filters['global'].value" placeholder="Filter Search" />
                                </IconField>
                            </div>
                            <div class="flex flex-col lg:flex-row gap-2 items-start lg:items-center">
                                <Select fluid :options="statusOptions" v-model="filters['status'].value" optionLabel="label" optionValue="value" placeholder="Select a status..." />
                                <Select fluid :options="users" v-model="filters['assigned_to.id'].value" optionLabel="formatted_full_name" optionValue="id" placeholder="Select an assigned user..." />
                            </div>
                            <div class="flex items-center">
                                <Button label="Clear Filters" @click="onClearFilters" />
                            </div>
                        </div>
                        <div>
                            <Button label="Create Task" icon="pi pi-plus-circle" @click="showTaskDialog" />
                        </div>
                    </div>
                </template>
                <template #empty> No tasks found. </template>
                <Column header="ID" v-if="isLargeBreakpoint" field="id"></Column>
                <Column header="Status" field="formatted_status"></Column>
                <Column header="Date Created" v-if="isLargeBreakpoint" field="formatted_created_at"></Column>
                <Column header="Due Date" field="formatted_due_date"></Column>
                <Column header="Title" field="title"></Column>
                <Column header="Description" v-if="isLargeBreakpoint" field="formatted_description"></Column>
                <Column header="Assigned To" field="assigned_to.formatted_full_name"></Column>
                <Column header="Created By" v-if="isLargeBreakpoint" field="created_by.formatted_full_name"></Column>
                <Column class="hidden" field="has_due_date"></Column>
                <Column class="w-24 !text-center" alignFrozen="right" :frozen="true">
                    <template #body="{ data }">
                        <Link
                            :href="route('task.show', data.id)"
                        >
                            <Button icon="pi pi-search" severity="primary" rounded></Button>
                        </Link>
                    </template>
                </Column>
            </DataTable>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { DataTable, Column, Button, InputText, IconField, InputIcon, Select, Checkbox } from 'primevue';
import { useAppStore } from '@/Store/appStore';
import { FilterMatchMode } from '@primevue/core/api';


const page = usePage();
const store = useAppStore();
const users = page.props.users;

// Detect if the screen is at least a large breakpoint (lg)
const isLargeBreakpoint = ref(false);
const isLargeScreen = ref(window.innerWidth >= 1024);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024; // lg breakpoint in Tailwind
};

onMounted(() => {
    checkBreakpoint(); // Initial check
    window.addEventListener('resize', checkBreakpoint); // Update on resize

    // Check if user is in url params
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user');

    if (userId) {
        filters.value['assigned_to.id'].value = Number(userId); // Ensure it’s a number
    }

});

// Clean up the event listener on component unmount
onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});

const props = defineProps({
    tasks: Array,
});

const showTaskDialog = () => {
    store.openTaskDialog();
}

/**
 * Handle data table filtering
 */
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
    'assigned_to.id': { value: null, matchMode: FilterMatchMode.EQUALS },
});


/**
 * Select input options
 */
const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const onClearFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        filters.value[key].value = null; // Reset each filter value to null
    });
};

</script>
