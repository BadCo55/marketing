<template>
    <!-- CHANGED: Added page title -->
    <Head :title="`Start Route Run - ${officeRoute.name || 'Route'}`" />

    <!-- CHANGED: Wrapped page in shared layout -->
    <Layout>
        <!-- CHANGED: Added full-page responsive shell matching the rest of the portal -->
        <div class="px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: Added page header section -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <!-- CHANGED: Added back actions -->
                        <div class="mb-2 flex flex-wrap gap-2">
                            <Link :href="route('office-route.index')">
                                <Button
                                    label="Back to Routes"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>

                            <Link :href="route('office-route.show', officeRoute.id)">
                                <Button
                                    label="View Route"
                                    icon="pi pi-map"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <!-- CHANGED: Added page title and helper text -->
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                                Start Route Run
                            </h1>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Confirm the route details, assign the run, and create a new execution instance from this reusable route.
                            </p>
                        </div>
                    </div>

                    <!-- CHANGED: Added primary page action -->
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button
                            label="Start Route Run"
                            icon="pi pi-play"
                            :loading="form.processing"
                            :disabled="!orderedStops.length"
                            @click="onSubmit"
                        />
                    </div>
                </div>
            </div>

            <!-- CHANGED: Added responsive two-column layout -->
            <div class="grid grid-cols-12 items-start gap-5">
                <!-- CHANGED: Added left summary rail -->
                <div class="col-span-12 xl:col-span-4 xl:self-start">
                    <div class="space-y-5 xl:sticky xl:top-6">
                        <!-- CHANGED: Added route summary card -->
                        <Card class="overflow-hidden rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-6">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Route Summary
                                        </h2>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            This route will be used as the template for the new run.
                                        </p>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-map" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Route
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ officeRoute.name || 'Unnamed Route' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-calendar" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Planned Route Date
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ formattedRouteDate }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-user" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Default Assigned User
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ officeRoute.assignedTo?.formatted_full_name || officeRoute.assignedTo?.name || 'Unassigned' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-directions-alt" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Source Type
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ formatLabel(officeRoute.source_type) || 'Not specified' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <Divider />

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Stops
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ orderedStops.length }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Radius
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ formattedRadius }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="officeRoute.description"
                                        class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <p class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                            Description
                                        </p>
                                        <p class="whitespace-pre-line text-sm leading-6 text-surface-700 dark:text-surface-300">
                                            {{ officeRoute.description }}
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <!-- CHANGED: Added run preview card -->
                        <Card class="rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                            Run Preview
                                        </h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            This new run will snapshot the selected route stops for execution.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="text-xs text-surface-500 dark:text-surface-400">
                                                Run Status
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ formatLabel(form.status) }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="text-xs text-surface-500 dark:text-surface-400">
                                                Planned Stops
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ orderedStops.length }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added quick warning when route has no stops -->
                                    <Message
                                        v-if="!orderedStops.length"
                                        severity="warn"
                                        icon="pi pi-exclamation-triangle"
                                    >
                                        This route has no stops, so a run cannot be started yet.
                                    </Message>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- CHANGED: Added main form column -->
                <div class="col-span-12 min-w-0 xl:col-span-8">
                    <div class="space-y-5">
                        <!-- CHANGED: Added run setup form card -->
                        <Card class="rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <form @submit.prevent="onSubmit" class="space-y-6">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Run Setup
                                        </h2>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Confirm who is running this route, when it should be run, and whether it should begin immediately.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                        <div class="flex flex-col">
                                            <label
                                                for="assigned_to"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.assigned_to }"
                                            >
                                                Assigned To
                                            </label>

                                            <Select
                                                id="assigned_to"
                                                v-model="form.assigned_to"
                                                :options="users"
                                                optionLabel="formatted_full_name"
                                                optionValue="id"
                                                placeholder="Select user..."
                                                fluid
                                                showClear
                                                :invalid="!!form.errors.assigned_to"
                                            />

                                            <Message
                                                v-if="form.errors.assigned_to"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.assigned_to }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <label
                                                for="run_date"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.run_date }"
                                            >
                                                Run Date
                                            </label>

                                            <DatePicker
                                                id="run_date"
                                                v-model="form.run_date"
                                                fluid
                                                showIcon
                                                placeholder="Select date..."
                                                :invalid="!!form.errors.run_date"
                                            />

                                            <Message
                                                v-if="form.errors.run_date"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.run_date }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <label
                                                for="status"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.status }"
                                            >
                                                Initial Status
                                            </label>

                                            <Select
                                                id="status"
                                                v-model="form.status"
                                                :options="statusOptions"
                                                optionLabel="label"
                                                optionValue="value"
                                                placeholder="Select status..."
                                                fluid
                                                :invalid="!!form.errors.status"
                                            />

                                            <Message
                                                v-if="form.errors.status"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.status }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <label
                                                for="started_at"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.started_at }"
                                            >
                                                Start Time
                                            </label>

                                            <DatePicker
                                                id="started_at"
                                                v-model="form.started_at"
                                                fluid
                                                showTime
                                                hourFormat="12"
                                                showIcon
                                                placeholder="Optional start timestamp..."
                                                :invalid="!!form.errors.started_at"
                                            />

                                            <Message
                                                v-if="form.errors.started_at"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.started_at }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col lg:col-span-2">
                                            <label
                                                for="notes"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.notes }"
                                            >
                                                Run Notes
                                            </label>

                                            <Textarea
                                                id="notes"
                                                v-model="form.notes"
                                                rows="5"
                                                autoResize
                                                fluid
                                                placeholder="Add any notes about today’s route, conditions, changes, or execution instructions..."
                                                :invalid="!!form.errors.notes"
                                            />

                                            <Message
                                                v-if="form.errors.notes"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.notes }}
                                            </Message>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added execution options card -->
                                    <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                        <div class="flex items-start gap-3">
                                            <Checkbox
                                                v-model="startImmediately"
                                                binary
                                                inputId="start-immediately"
                                            />

                                            <div class="min-w-0">
                                                <label
                                                    for="start-immediately"
                                                    class="cursor-pointer text-sm font-medium text-surface-900 dark:text-surface-100"
                                                >
                                                    Start immediately
                                                </label>

                                                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                    When enabled, the run will default to in progress and use the current time if no start time is selected.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added preview of stops to be snapshotted -->
                                    <div class="space-y-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                                Stops Included in This Run
                                            </h3>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                These route stops will be copied into the new run in their current order.
                                            </p>
                                        </div>

                                        <div
                                            v-if="!orderedStops.length"
                                            class="flex min-h-40 flex-col items-center justify-center rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <i class="pi pi-map-marker text-2xl text-surface-400 dark:text-surface-500" />
                                            <p class="mt-3 font-medium text-surface-700 dark:text-surface-200">
                                                No stops found for this route.
                                            </p>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                Add stops to the route before starting a run.
                                            </p>
                                        </div>

                                        <div
                                            v-else
                                            class="space-y-3"
                                        >
                                            <div
                                                v-for="stop in orderedStops"
                                                :key="stop.id"
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <div class="mb-2 flex flex-wrap items-center gap-2">
                                                            <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                                Stop {{ stop.stop_order }}
                                                            </span>

                                                            <span
                                                                v-if="stop.required"
                                                                class="rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700 dark:bg-red-500/15 dark:text-red-300"
                                                            >
                                                                Required
                                                            </span>
                                                        </div>

                                                        <p class="font-semibold text-surface-900 dark:text-surface-0">
                                                            {{ stop.office?.parent_company || stop.parent_company || 'Unknown Office' }}
                                                        </p>

                                                        <p
                                                            v-if="stop.office?.office_name || stop.office_name"
                                                            class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                                        >
                                                            {{ stop.office?.office_name || stop.office_name }}
                                                        </p>

                                                        <p class="mt-2 text-sm text-surface-700 dark:text-surface-300">
                                                            {{ formatOfficeAddress(stop.office || stop) }}
                                                        </p>
                                                    </div>

                                                    <div class="text-sm text-surface-500 dark:text-surface-400">
                                                        Priority: {{ stop.priority ?? 3 }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added form footer -->
                                    <div class="flex justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
                                        <Link :href="route('office-route.show', officeRoute.id)">
                                            <Button
                                                label="Cancel"
                                                severity="secondary"
                                                outlined
                                                type="button"
                                            />
                                        </Link>

                                        <Button
                                            label="Start Route Run"
                                            icon="pi pi-play"
                                            type="submit"
                                            :loading="form.processing"
                                            :disabled="!orderedStops.length"
                                        />
                                    </div>
                                </form>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import {
    Card,
    Button,
    Divider,
    Select,
    DatePicker,
    Textarea,
    Message,
    Checkbox,
} from 'primevue';

const page = usePage();

// CHANGED: Added safe fallbacks for route + user data from the controller.
const officeRoute = page.props.office_route ?? {};
const users = page.props.users ?? [];

// CHANGED: Added ordered stops helper so the preview always matches route order.
const orderedStops = computed(() => {
    return [...(officeRoute.stops ?? [])].sort((a, b) => {
        return (a.stop_order ?? 0) - (b.stop_order ?? 0);
    });
});

// CHANGED: Added formatted route date helper.
const formattedRouteDate = computed(() => {
    if (!officeRoute.route_date) return 'Not scheduled';
    return formatDateTime(officeRoute.route_date, false);
});

// CHANGED: Added formatted radius helper.
const formattedRadius = computed(() => {
    if (
        officeRoute.radius_miles === null ||
        officeRoute.radius_miles === undefined ||
        officeRoute.radius_miles === ''
    ) {
        return 'Not set';
    }

    return `${officeRoute.radius_miles} mile(s)`;
});

// CHANGED: Added immediate-start toggle that influences initial status and timestamp.
const startImmediately = ref(false);

// CHANGED: Added form used to create the new route run.
const form = useForm({
    office_route_id: officeRoute.id ?? null,
    assigned_to: officeRoute.assigned_to ?? officeRoute.assignedTo?.id ?? null,
    started_by: null,
    status: 'not_started',
    run_date: officeRoute.route_date ?? null,
    started_at: null,
    start_latitude: officeRoute.center_latitude ?? null,
    start_longitude: officeRoute.center_longitude ?? null,
    notes: '',
});

// CHANGED: Added watcher so checking "start immediately" updates status automatically.
watch(startImmediately, (value) => {
    if (value) {
        form.status = 'in_progress';

        if (!form.started_at) {
            form.started_at = new Date();
        }
    } else if (form.status === 'in_progress') {
        form.status = 'not_started';
    }
});

// CHANGED: Added initial status select options.
const statusOptions = [
    { label: 'Not Started', value: 'not_started' },
    { label: 'In Progress', value: 'in_progress' },
];

// CHANGED: Added submit handler for creating a run from this route.
const onSubmit = () => {
    form.post(route('office-run.store'), {
        preserveScroll: true,
        onError: (error) => {
            console.log(error);
        },
    });
};

// CHANGED: Added shared label formatter.
const formatLabel = (value) => {
    if (!value) return 'N/A';

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};

// CHANGED: Added address formatter for stop preview.
const formatOfficeAddress = (office) => {
    if (!office) return 'No address available';

    const unit = office.unit_number ? ` #${office.unit_number}` : '';

    return `${office.street_address || ''}${unit}, ${office.city || ''}, ${office.state || ''} ${office.zip_code || ''}`.trim();
};

// CHANGED: Added general date/datetime formatter.
const formatDateTime = (value, includeTime = true) => {
    if (!value) return '—';

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) return value;

    if (!includeTime) {
        return date.toLocaleDateString();
    }

    return date.toLocaleString();
};
</script>
