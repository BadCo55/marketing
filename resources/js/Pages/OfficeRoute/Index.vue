<template>
    <!-- CHANGED: Added page title -->
    <Head title="Routes" />

    <!-- CHANGED: Wrapped page in shared layout -->
    <Layout>
        <!-- CHANGED: Added full-page responsive shell matching the rest of the portal -->
        <div class="flex h-full min-h-0 flex-col">
            <!-- CHANGED: Added page header section -->
            <div class="mb-6 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <!-- CHANGED: Added title and helper text -->
                        <h1 class="text-3xl font-bold tracking-tight">Routes</h1>
                        <p class="mt-1 text-sm text-surface-500">
                            Review, organize, and manage saved office routes for your team.
                        </p>
                    </div>

                    <!-- CHANGED: Added primary page action -->
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link :href="route('office-route.create')">
                            <Button
                                label="Create Route"
                                icon="pi pi-plus-circle"
                            />
                        </Link>
                    </div>
                </div>

                <!-- CHANGED: Added summary cards like other index pages -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="mb-1 text-sm text-surface-500">Total Routes</p>
                        <p class="text-2xl font-bold">{{ routes.length }}</p>
                    </div>

                    <div class="rounded-xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="mb-1 text-sm text-surface-500">Assigned Routes</p>
                        <p class="text-2xl font-bold">{{ assignedRouteCount }}</p>
                    </div>

                    <div class="rounded-xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="mb-1 text-sm text-surface-500">Scheduled Routes</p>
                        <p class="text-2xl font-bold">{{ scheduledRouteCount }}</p>
                    </div>
                </div>
            </div>

            <!-- CHANGED: Added table shell with proper flex layout -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div class="flex h-full min-h-0 flex-col overflow-hidden rounded-xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                    <!-- CHANGED: Added DataTable for routes -->
                    <DataTable
                        v-model:filters="filters"
                        :value="routes"
                        paginator
                        scrollable
                        :scrollHeight="scrollHeight"
                        stripedRows
                        :rows="10"
                        :rowsPerPageOptions="[10, 25, 50, 100]"
                        :globalFilterFields="[
                            'name',
                            'description',
                            'source_type',
                            'assigned_to.formatted_full_name',
                            'created_by.formatted_full_name',
                            'formatted_route_date'
                        ]"
                        tableStyle="min-width: 72rem"
                        class="h-full"
                        @row-click="onRowClick"
                        :pt="{
                            bodyRow: { class: 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors' }
                        }"
                    >
                        <template #header>
                            <!-- CHANGED: Added header content with search and filter controls -->
                            <div class="flex flex-col gap-4 p-1">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-xl font-semibold">Route Directory</h2>
                                    <p class="text-sm text-surface-500">
                                        Search by route name, assigned user, source type, or date.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1fr)_auto]">
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                                        <div class="xl:col-span-2">
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>

                                                <InputText
                                                    fluid
                                                    v-model="filters.global.value"
                                                    placeholder="Search routes..."
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
                                                :options="sourceTypeOptions"
                                                v-model="filters.source_type.value"
                                                optionLabel="label"
                                                optionValue="value"
                                                placeholder="Source type..."
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
                            <div class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                                <i class="pi pi-directions-alt text-3xl text-surface-400" />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No routes found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500">
                                    Try adjusting your filters or create a new route to get started.
                                </p>

                                <Link :href="route('office-route.create')">
                                    <Button
                                        label="Create Route"
                                        icon="pi pi-plus-circle"
                                    />
                                </Link>
                            </div>
                        </template>

                        <template #loading>
                            <!-- CHANGED: Added loading state -->
                            Loading route data. Please wait.
                        </template>

                        <!-- CHANGED: Added route name as the main column -->
                        <Column
                            header="Route"
                            field="name"
                            sortable
                            headerClass="w-[16rem]"
                            bodyClass="w-[16rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">{{ data.name || 'Unnamed Route' }}</p>

                                    <p
                                        v-if="data.description"
                                        class="mt-1 line-clamp-2 text-sm text-surface-500"
                                    >
                                        {{ data.description }}
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: Added date column -->
                        <Column
                            header="Route Date"
                            field="formatted_route_date"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                {{ data.formatted_route_date || '—' }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added source type badge column -->
                        <Column
                            header="Source"
                            field="source_type"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                    :class="getSourcePillClass(data.source_type)"
                                >
                                    {{ formatLabel(data.source_type) }}
                                </span>
                            </template>
                        </Column>

                        <!-- CHANGED: Added assigned-to column -->
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

                        <!-- CHANGED: Added stop count column -->
                        <Column
                            header="Stops"
                            headerClass="w-[8rem]"
                            bodyClass="w-[8rem]"
                        >
                            <template #body="{ data }">
                                {{ data.stops?.length ?? 0 }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added created-by column for larger screens -->
                        <Column
                            v-if="isLargeBreakpoint"
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

                        <!-- CHANGED: Added created-at column for larger screens -->
                        <Column
                            v-if="isLargeBreakpoint"
                            header="Created"
                            field="created_at"
                            sortable
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                {{ data.formatted_created_at || '—' }}
                            </template>
                        </Column>

                        <!-- CHANGED: Added subtle row action chevron -->
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
import Layout from '@/Layouts/Layout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import {
    DataTable,
    Column,
    Button,
    InputText,
    IconField,
    InputIcon,
    Select,
} from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';

const page = usePage();

// CHANGED: Added safe fallbacks for route and user data
const routes = page.props.office_routes ?? [];
const users = page.props.users ?? [];

// CHANGED: Added responsive breakpoint tracking like other index pages
const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024;
};

onMounted(() => {
    // CHANGED: Set initial breakpoint and listen for resize
    checkBreakpoint();
    window.addEventListener('resize', checkBreakpoint);
});

onUnmounted(() => {
    // CHANGED: Cleaned up resize listener
    window.removeEventListener('resize', checkBreakpoint);
});

// CHANGED: Added filter state for search + assigned user + source type
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    source_type: { value: null, matchMode: FilterMatchMode.EQUALS },
    'assigned_to.id': { value: null, matchMode: FilterMatchMode.EQUALS },
});

// CHANGED: Added source type options used in the filter dropdown
const sourceTypeOptions = [
    { label: 'Manual Selection', value: 'manual' },
    { label: 'Radius Search', value: 'radius' },
    { label: 'CSV Import', value: 'csv' },
    { label: 'Excel Import', value: 'excel' },
];

// CHANGED: Added summary metrics for header cards
const assignedRouteCount = computed(() => {
    return routes.filter((route) => !!route.assigned_to).length;
});

const scheduledRouteCount = computed(() => {
    return routes.filter((route) => !!route.route_date).length;
});

// CHANGED: Added flex scroll height to match other pages
const scrollHeight = computed(() => 'flex');

// CHANGED: Added helper for source pill styling
const getSourcePillClass = (sourceType) => {
    switch (sourceType) {
        case 'manual':
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-200';
        case 'radius':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300';
        case 'csv':
            return 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300';
        case 'excel':
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300';
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300';
    }
};

// CHANGED: Added shared label formatter for source type text
const formatLabel = (value) => {
    if (!value) return 'N/A';

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};

// CHANGED: Added clear filters helper
const onClearFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        filters.value[key].value = null;
    });
};

// CHANGED: Added row click navigation to route show page
const onRowClick = (event) => {
    router.visit(route('office-route.show', event.data.id));
};
</script>
