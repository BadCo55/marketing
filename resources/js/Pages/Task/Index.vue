<template>
    <Head title="Tasks" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col">
            <div class="mb-6 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Tasks</h1>
                        <p class="mt-1 text-sm text-surface-500">
                            Review, filter, and manage tasks assigned across the portal.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button
                            label="Create Task"
                            icon="pi pi-plus-circle"
                            @click="showTaskDialog"
                        />
                    </div>
                </div>

                <!-- CHANGED: tightened summary cards for mobile and reduced visual bulk -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="mb-1 text-sm text-surface-500">Total Tasks</p>
                        <p class="text-2xl font-bold">{{ props.tasks?.length ?? 0 }}</p>
                    </div>

                    <div class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="mb-1 text-sm text-surface-500">Open</p>
                        <p class="text-2xl font-bold">{{ openTaskCount }}</p>
                    </div>

                    <div class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="mb-1 text-sm text-surface-500">Completed</p>
                        <p class="text-2xl font-bold">{{ completedTaskCount }}</p>
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div class="flex h-full min-h-0 flex-col overflow-hidden rounded-2xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                    <!-- CHANGED: shared filter header kept above both mobile and desktop views -->
                    <div class="border-b border-surface-200 p-4 dark:border-surface-800">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-1">
                                <h2 class="text-xl font-semibold">Task Directory</h2>
                                <p class="text-sm text-surface-500">
                                    Search by keyword, filter by status, or narrow tasks by assigned user.
                                </p>
                            </div>

                            <!-- CHANGED: simplified filter layout so it wraps naturally on mobile -->
                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>

                                        <InputText
                                            fluid
                                            v-model="filters.global.value"
                                            placeholder="Search tasks..."
                                        />
                                    </IconField>
                                </div>

                                <div>
                                    <Select
                                        fluid
                                        :options="statusOptions"
                                        v-model="filters.status.value"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Filter by status..."
                                        showClear
                                    />
                                </div>

                                <div>
                                    <Select
                                        fluid
                                        :options="users"
                                        v-model="filters['assigned_to.id'].value"
                                        optionLabel="formatted_full_name"
                                        optionValue="id"
                                        placeholder="Assigned user..."
                                        showClear
                                    />
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                                <Button
                                    label="Clear Filters"
                                    severity="secondary"
                                    outlined
                                    @click="onClearFilters"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- CHANGED: mobile card list replaces cramped table on small screens -->
                    <div
                        v-if="!isLargeBreakpoint"
                        class="flex-1 overflow-y-auto p-4"
                    >
                        <div
                            v-if="filteredTasks.length"
                            class="flex flex-col gap-3"
                        >
                            <Link
                                v-for="task in filteredTasks"
                                :key="task.id"
                                :href="route('task.show', task.id)"
                                class="block rounded-2xl border border-surface-200 bg-surface-50 p-4 shadow-sm transition hover:border-surface-300 dark:border-surface-700 dark:bg-surface-800 dark:hover:border-surface-600"
                            >
                                <!-- CHANGED: top row for status and due date -->
                                <div class="mb-3 flex items-start justify-between gap-3">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                        :class="getStatusPillClass(task.status)"
                                    >
                                        {{ task.formatted_status }}
                                    </span>

                                    <span
                                        class="text-xs"
                                        :class="{
                                            'text-red-500 font-medium': task.is_passed_due,
                                            'text-orange-500 font-medium': task.is_due_today,
                                            'text-surface-500': !task.is_passed_due && !task.is_due_today,
                                        }"
                                    >
                                        {{ task.formatted_due_date || 'No due date' }}
                                    </span>
                                </div>

                                <!-- CHANGED: task title becomes the primary visual anchor -->
                                <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                    {{ task.title }}
                                </h3>

                                <!-- CHANGED: description shown inline for mobile readability -->
                                <p
                                    v-if="task.formatted_description"
                                    class="mt-1 line-clamp-2 text-sm text-surface-600 dark:text-surface-300"
                                >
                                    {{ task.formatted_description }}
                                </p>

                                <!-- CHANGED: metadata stacked into compact rows -->
                                <div class="mt-4 space-y-2 text-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-surface-500">Assigned To</span>
                                        <span class="text-right text-surface-900 dark:text-surface-100">
                                            {{ task.assigned_to?.formatted_full_name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-surface-500">Created By</span>
                                        <span class="text-right text-surface-900 dark:text-surface-100">
                                            {{ task.created_by?.formatted_full_name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex items-start justify-between gap-3">
                                        <span class="text-surface-500">Created</span>
                                        <span class="text-right text-surface-900 dark:text-surface-100">
                                            {{ task.formatted_created_at || '—' }}
                                        </span>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <!-- CHANGED: dedicated mobile empty state -->
                        <div
                            v-else
                            class="flex min-h-52 flex-col items-center justify-center gap-3 text-center"
                        >
                            <i class="pi pi-check-circle text-3xl text-surface-400" />
                            <p class="font-medium text-surface-700 dark:text-surface-200">No tasks found.</p>
                            <p class="max-w-md text-sm text-surface-500">
                                Try adjusting your filters or create a new task to get started.
                            </p>
                            <Button
                                label="Create Task"
                                icon="pi pi-plus-circle"
                                @click="showTaskDialog"
                            />
                        </div>
                    </div>

                    <!-- CHANGED: desktop DataTable only renders on large screens -->
                    <DataTable
                        v-else
                        v-model:filters="filters"
                        :value="props.tasks"
                        removableSort
                        scrollable
                        :scrollHeight="'flex'"
                        stripedRows
                        paginator
                        :rows="10"
                        :rowsPerPageOptions="[10, 25, 50, 100]"
                        :globalFilterFields="[
                            'status',
                            'formatted_date_created',
                            'formatted_due_date',
                            'title',
                            'formatted_assigned_to',
                            'formatted_created_by'
                        ]"
                        tableStyle="min-width: 78rem"
                        class="h-full"
                    >
                        <template #empty>
                            <div class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                                <i class="pi pi-check-circle text-3xl text-surface-400" />
                                <p class="font-medium text-surface-700 dark:text-surface-200">No tasks found.</p>
                                <p class="max-w-md text-sm text-surface-500">
                                    Try adjusting your filters or create a new task to get started.
                                </p>
                                <Button
                                    label="Create Task"
                                    icon="pi pi-plus-circle"
                                    @click="showTaskDialog"
                                />
                            </div>
                        </template>

                        <Column
                            header="ID"
                            field="id"
                            sortable
                            headerClass="w-[6rem]"
                            bodyClass="w-[6rem]"
                        />

                        <Column
                            header="Status"
                            field="formatted_status"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                    :class="getStatusPillClass(data.status)"
                                >
                                    {{ data.formatted_status }}
                                </span>
                            </template>
                        </Column>

                        <Column
                            header="Date Created"
                            field="formatted_created_at"
                            sortable
                            headerClass="w-[15rem]"
                            bodyClass="w-[15rem]"
                        />

                        <Column
                            header="Due Date"
                            field="formatted_due_date"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <span
                                    :class="{
                                        'text-red-500 font-medium': data.is_passed_due,
                                        'text-orange-500 font-medium': data.is_due_today,
                                    }"
                                >
                                    {{ data.formatted_due_date || '—' }}
                                </span>
                            </template>
                        </Column>

                        <Column
                            header="Title"
                            field="title"
                            sortable
                            headerClass="w-[16rem]"
                            bodyClass="w-[16rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">{{ data.title }}</p>
                                </div>
                            </template>
                        </Column>

                        <Column
                            header="Description"
                            field="formatted_description"
                            headerClass="w-[16rem]"
                            bodyClass="w-[16rem]"
                        />

                        <Column
                            header="Assigned To"
                            field="assigned_to.formatted_full_name"
                            sortable
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                {{ data.assigned_to?.formatted_full_name || '—' }}
                            </template>
                        </Column>

                        <Column
                            header="Created By"
                            field="created_by.formatted_full_name"
                            sortable
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                {{ data.created_by?.formatted_full_name || '—' }}
                            </template>
                        </Column>

                        <Column class="hidden" field="has_due_date"></Column>

                        <Column class="w-20 !text-center" alignFrozen="right" :frozen="true">
                            <template #body="{ data }">
                                <Link :href="route('task.show', data.id)">
                                    <Button
                                        icon="pi pi-search"
                                        severity="primary"
                                        rounded
                                        aria-label="View task"
                                    />
                                </Link>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { DataTable, Column, Button, InputText, IconField, InputIcon, Select } from 'primevue';
import { useAppStore } from '@/Store/appStore';
import { FilterMatchMode } from '@primevue/core/api';

const page = usePage();
const store = useAppStore();
const users = page.props.users ?? [];

const props = defineProps({
    tasks: Array,
});

const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024;
};

onMounted(() => {
    checkBreakpoint();
    window.addEventListener('resize', checkBreakpoint);

    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user');

    if (userId) {
        filters.value['assigned_to.id'].value = Number(userId);
    }
});

onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});

const showTaskDialog = () => {
    store.openTaskDialog();
};

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
    'assigned_to.id': { value: null, matchMode: FilterMatchMode.EQUALS },
});

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const openTaskCount = computed(() => {
    return (props.tasks ?? []).filter(
        (task) => task.status === 'pending' || task.status === 'in_progress'
    ).length;
});

const completedTaskCount = computed(() => {
    return (props.tasks ?? []).filter((task) => task.status === 'completed').length;
});

/* CHANGED: added client-side filtered list for the mobile card layout */
const filteredTasks = computed(() => {
    const tasks = props.tasks ?? [];
    const global = (filters.value.global.value || '').toString().toLowerCase().trim();
    const status = filters.value.status.value;
    const assignedTo = filters.value['assigned_to.id'].value;

    return tasks.filter((task) => {
        const matchesStatus = !status || task.status === status;
        const matchesAssigned = !assignedTo || task.assigned_to?.id === assignedTo;

        const haystack = [
            task.formatted_status,
            task.formatted_created_at,
            task.formatted_due_date,
            task.title,
            task.formatted_description,
            task.assigned_to?.formatted_full_name,
            task.created_by?.formatted_full_name,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase();

        const matchesGlobal = !global || haystack.includes(global);

        return matchesStatus && matchesAssigned && matchesGlobal;
    });
});

const getStatusPillClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300';
        case 'in_progress':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300';
        case 'completed':
            return 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300';
        case 'cancelled':
            return 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300';
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300';
    }
};

const onClearFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        filters.value[key].value = null;
    });
};
</script>
