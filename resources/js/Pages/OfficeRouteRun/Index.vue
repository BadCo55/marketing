<template>
    <!-- CHANGED: Added page title -->
    <Head title="Route Runs" />

    <!-- CHANGED: Wrapped page in shared layout -->
    <Layout>
        <!-- CHANGED: Added full-page responsive shell matching the rest of the portal -->
        <div class="flex h-full min-h-0 flex-col">
            <!-- CHANGED: Added page header section -->
            <div class="mb-6 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <!-- CHANGED: Added title and helper text -->
                        <h1 class="text-3xl font-bold tracking-tight">Route Runs</h1>
                        <p class="mt-1 text-sm text-surface-500">
                            Review active, pending, completed, and cancelled route runs across your team.
                        </p>
                    </div>

                    <!-- CHANGED: Added top-right actions -->
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link :href="route('office-route.index')">
                            <Button
                                label="View Routes"
                                icon="pi pi-map"
                                severity="secondary"
                                outlined
                            />
                        </Link>
                    </div>
                </div>

                <!-- CHANGED: Added summary cards matching the rest of the portal -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div
                        class="rounded-xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <p class="mb-1 text-sm text-surface-500">Total Runs</p>
                        <p class="text-2xl font-bold">{{ runs.length }}</p>
                    </div>

                    <div
                        class="rounded-xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <p class="mb-1 text-sm text-surface-500">In Progress</p>
                        <p class="text-2xl font-bold">{{ inProgressCount }}</p>
                    </div>

                    <div
                        class="rounded-xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <p class="mb-1 text-sm text-surface-500">Not Started</p>
                        <p class="text-2xl font-bold">{{ notStartedCount }}</p>
                    </div>

                    <div
                        class="rounded-xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <p class="mb-1 text-sm text-surface-500">Completed</p>
                        <p class="text-2xl font-bold">{{ completedCount }}</p>
                    </div>
                </div>
            </div>

            <!-- CHANGED: Added table shell with proper flex layout -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div
                    class="flex h-full min-h-0 flex-col overflow-hidden rounded-xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <!-- CHANGED: Added DataTable for route runs -->
                    <DataTable
                        v-model:filters="filters"
                        :value="runs"
                        paginator
                        scrollable
                        :scrollHeight="scrollHeight"
                        stripedRows
                        :rows="10"
                        :rowsPerPageOptions="[10, 25, 50, 100]"
                        :globalFilterFields="[
                            'office_route.name',
                            'officeRoute.name',
                            'status',
                            'assigned_to.formatted_full_name',
                            'assignedTo.formatted_full_name',
                            'assignedTo.name',
                            'started_by.formatted_full_name',
                            'startedBy.formatted_full_name',
                            'startedBy.name',
                            'formatted_run_date',
                            'notes',
                        ]"
                        tableStyle="min-width: 84rem"
                        class="h-full"
                        @row-click="onRowClick"
                        :pt="{
                            bodyRow: {
                                class: 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors',
                            },
                        }"
                    >
                        <template #header>
                            <!-- CHANGED: Added header content with search and filter controls -->
                            <div class="flex flex-col gap-4 p-1">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-xl font-semibold">Run Directory</h2>
                                    <p class="text-sm text-surface-500">
                                        Search by route name, assigned user, status, notes, or run date.
                                    </p>
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_auto]"
                                >
                                    <div
                                        class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4"
                                    >
                                        <div class="xl:col-span-2">
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>

                                                <InputText
                                                    fluid
                                                    v-model="filters.global.value"
                                                    placeholder="Search route runs..."
                                                />
                                            </IconField>
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

                                        <div>
                                            <Select
                                                fluid
                                                :options="statusOptions"
                                                v-model="filters.status.value"
                                                optionLabel="label"
                                                optionValue="value"
                                                placeholder="Run status..."
                                                showClear
                                            />
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 sm:flex-row xl:justify-end">
                                        <Button
                                            label="Clear Filters"
                                            severity="secondary"
                                            outlined
                                            @click="onClearFilters"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #empty>
                            <!-- CHANGED: Added polished empty state -->
                            <div
                                class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center"
                            >
                                <i class="pi pi-directions text-3xl text-surface-400" />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No route runs found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500">
                                    Start a route from the route detail page to create the first run.
                                </p>

                                <Link :href="route('office-route.index')">
                                    <Button
                                        label="View Routes"
                                        icon="pi pi-map"
                                    />
                                </Link>
                            </div>
                        </template>

                        <template #loading>
                            <!-- CHANGED: Added loading state -->
                            Loading route run data. Please wait.
                        </template>

                        <!-- CHANGED: Added route name as the main column -->
                        <Column
                            header="Route"
                            field="office_route.name"
                            sortable
                            headerClass="w-[18rem]"
                            bodyClass="w-[18rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">
                                        {{ getRouteName(data) }}
                                    </p>

                                    <p
                                        v-if="getRouteDescription(data)"
                                        class="mt-1 line-clamp-2 text-sm text-surface-500"
                                    >
                                        {{ getRouteDescription(data) }}
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: Added run date column -->
                        <Column
                            header="Run Date"
                            field="formatted_run_date"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                {{ data.formatted_run_date || formatDate(data.run_date) }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added status pill column -->
                        <Column
                            header="Status"
                            field="status"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-sm font-medium text-center"
                                    :class="getStatusPillClass(data.status)"
                                >
                                    {{ formatLabel(data.status) }}
                                </span>
                            </template>
                        </Column>

                        <!-- CHANGED: Added smart destination column so users know where the row click will go -->
                        <Column
                            header="Open"
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                    :class="getDestinationPillClass(data)"
                                >
                                    {{ getDestinationLabel(data) }}
                                </span>
                            </template>
                        </Column>

                        <!-- CHANGED: Added assigned user column -->
                        <Column
                            header="Assigned To"
                            field="assigned_to.id"
                            sortable
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                {{ getAssignedUserName(data) }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added progress column -->
                        <Column
                            header="Progress"
                            headerClass="w-[11rem]"
                            bodyClass="w-[11rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col gap-1">
                                    <span class="font-medium">
                                        {{ Number(data.completed_stop_count ?? 0) }}/{{ Number(data.planned_stop_count ?? 0) }}
                                    </span>

                                    <div class="h-2 overflow-hidden rounded-full bg-surface-200 dark:bg-surface-700">
                                        <div
                                            class="h-full rounded-full bg-primary transition-all"
                                            :style="{ width: `${getProgress(data)}%` }"
                                        />
                                    </div>

                                    <span class="text-xs text-surface-500">
                                        {{ getProgress(data) }}%
                                    </span>
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: Added current stop column -->
                        <Column
                            header="Current Stop"
                            field="current_stop_order"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                {{ data.current_stop_order || '—' }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added started-by column for large screens -->
                        <Column
                            v-if="isLargeBreakpoint"
                            header="Started By"
                            field="started_by.id"
                            sortable
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                {{ getStartedByName(data) }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added created date column for large screens -->
                        <Column
                            v-if="isLargeBreakpoint"
                            header="Created"
                            field="created_at"
                            sortable
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                {{ data.formatted_created_at || formatDate(data.created_at, true) }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added contextual action button column -->
                        <Column
                            header="Action"
                            headerClass="w-[9rem]"
                            bodyClass="w-[9rem]"
                        >
                            <template #body="{ data }">
                                <Button
                                    :label="getDestinationActionLabel(data)"
                                    :icon="getDestinationActionIcon(data)"
                                    size="small"
                                    text
                                    @click.stop="onActionClick(data)"
                                />
                            </template>
                        </Column>

                        <!-- CHANGED: Added subtle chevron column -->
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
                    </DataTable>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
    DataTable,
    Column,
    Button,
    InputText,
    IconField,
    InputIcon,
    Select,
} from 'primevue'
import { FilterMatchMode } from '@primevue/core/api'

const page = usePage()

// CHANGED: Added safe fallbacks for run and user data.
const runs = page.props.office_route_runs ?? []
const users = page.props.users ?? []

// CHANGED: Added responsive breakpoint tracking like other index pages.
const isLargeBreakpoint = ref(false)

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024
}

onMounted(() => {
    // CHANGED: Set initial breakpoint and listen for resize.
    checkBreakpoint()
    window.addEventListener('resize', checkBreakpoint)
})

onUnmounted(() => {
    // CHANGED: Cleaned up resize listener.
    window.removeEventListener('resize', checkBreakpoint)
})

// CHANGED: Added filter state to match the rest of the app.
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS },
    'assigned_to.id': { value: null, matchMode: FilterMatchMode.EQUALS },
})

// CHANGED: Added run status filter options.
const statusOptions = [
    { label: 'Not Started', value: 'not_started' },
    { label: 'In Progress', value: 'in_progress' },
    { label: 'Paused', value: 'paused' },
    { label: 'Completed', value: 'completed' },
    { label: 'Cancelled', value: 'cancelled' },
]

// CHANGED: Added summary metrics.
const inProgressCount = computed(() => {
    return runs.filter((run) => run.status === 'in_progress').length
})

const notStartedCount = computed(() => {
    return runs.filter((run) => run.status === 'not_started').length
})

const completedCount = computed(() => {
    return runs.filter((run) => run.status === 'completed').length
})

const assignedRunCount = computed(() => {
    return runs.filter((run) => !!run.assigned_to || !!run.assignedTo).length
})

// CHANGED: Added flex scroll height to match other index pages.
const scrollHeight = computed(() => 'flex')

// CHANGED: Added route name helper that supports both snake_case and camelCase payloads.
const getRouteName = (run) => {
    return run.office_route?.name || run.officeRoute?.name || 'Unnamed Route'
}

// CHANGED: Added route description helper that supports both snake_case and camelCase payloads.
const getRouteDescription = (run) => {
    return run.office_route?.description || run.officeRoute?.description || ''
}

// CHANGED: Added assigned user name helper.
const getAssignedUserName = (run) => {
    return (
        run.assigned_to?.formatted_full_name ||
        run.assignedTo?.formatted_full_name ||
        run.assignedTo?.name ||
        '—'
    )
}

// CHANGED: Added started-by helper.
const getStartedByName = (run) => {
    return (
        run.started_by?.formatted_full_name ||
        run.startedBy?.formatted_full_name ||
        run.startedBy?.name ||
        '—'
    )
}

// CHANGED: Added helper for status pill styling.
const getStatusPillClass = (status) => {
    switch (status) {
        case 'not_started':
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-200'
        case 'in_progress':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
        case 'paused':
            return 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300'
        case 'completed':
            return 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300'
        case 'cancelled':
            return 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300'
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300'
    }
}

// CHANGED: Added destination helper so row clicks go to the right view based on run status.
const getRunDestination = (run) => {
    switch (run.status) {
        case 'in_progress':
        case 'paused':
            return route('office-run.active', run.id)

        case 'not_started':
        case 'completed':
        case 'cancelled':
        default:
            return route('office-run.show', run.id)
    }
}

// CHANGED: Added destination label helper so the UI explains where the row will open.
const getDestinationLabel = (run) => {
    switch (run.status) {
        case 'in_progress':
            return 'Resume'
        case 'paused':
            return 'Resume'
        case 'not_started':
            return 'Review'
        case 'completed':
            return 'Summary'
        case 'cancelled':
            return 'Summary'
        default:
            return 'Open'
    }
}

// CHANGED: Added destination action label helper for the action button.
const getDestinationActionLabel = (run) => {
    switch (run.status) {
        case 'in_progress':
            return 'Resume'
        case 'paused':
            return 'Resume'
        case 'not_started':
            return 'Open'
        case 'completed':
            return 'View'
        case 'cancelled':
            return 'View'
        default:
            return 'Open'
    }
}

// CHANGED: Added destination action icon helper for the action button.
const getDestinationActionIcon = (run) => {
    switch (run.status) {
        case 'in_progress':
        case 'paused':
            return 'pi pi-play'
        case 'completed':
        case 'cancelled':
            return 'pi pi-eye'
        case 'not_started':
        default:
            return 'pi pi-angle-right'
    }
}

// CHANGED: Added destination pill style helper.
const getDestinationPillClass = (run) => {
    switch (run.status) {
        case 'in_progress':
        case 'paused':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
        case 'completed':
        case 'cancelled':
            return 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300'
        case 'not_started':
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-200'
    }
}

// CHANGED: Added shared label formatter.
const formatLabel = (value) => {
    if (!value) return 'N/A'

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}

// CHANGED: Added date formatter fallback.
const formatDate = (value, includeTime = false) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return value

    if (includeTime) {
        return date.toLocaleString()
    }

    return date.toLocaleDateString()
}

// CHANGED: Added progress helper.
const getProgress = (run) => {
    const planned = Number(run.planned_stop_count ?? 0)
    const completed = Number(run.completed_stop_count ?? 0)

    if (!planned) return 0

    return Math.round((completed / planned) * 100)
}

// CHANGED: Added clear filters helper.
const onClearFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        filters.value[key].value = null
    })
}

// CHANGED: Added row click navigation using the smart destination helper.
const onRowClick = (event) => {
    router.visit(getRunDestination(event.data))
}

// CHANGED: Added explicit action button navigation using the same smart destination helper.
const onActionClick = (run) => {
    router.visit(getRunDestination(run))
}
</script>
