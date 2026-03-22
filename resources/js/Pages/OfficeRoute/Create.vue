<template>
    <Head title="Create Route" />

    <Layout>
        <!-- CHANGED: Added full-page responsive container matching the rest of the portal -->
        <div class="mx-auto w-full max-w-7xl px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: Added structured page header with back action and primary CTA -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <!-- CHANGED: Added back navigation -->
                        <div class="mb-2">
                            <Link :href="route('office-route.index')">
                                <Button
                                    label="Back to Routes"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <!-- CHANGED: Added page title and helper copy -->
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                                Create Route
                            </h1>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Build a new office route, define the coverage area, and organize selected stops.
                            </p>
                        </div>
                    </div>

                    <!-- CHANGED: Added primary page actions -->
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button
                            label="Optimize Order"
                            icon="pi pi-directions-alt"
                            severity="secondary"
                            outlined
                            type="button"
                            :disabled="form.stops.length < 2"
                            @click="onOptimizeStops"
                        />
                        <Button
                            label="Create Route"
                            icon="pi pi-check-circle"
                            type="button"
                            :loading="form.processing"
                            @click="onSubmit"
                        />
                    </div>
                </div>
            </div>

            <!-- CHANGED: Added responsive two-column layout consistent with other show/create pages -->
            <div class="grid grid-cols-12 items-start gap-5">
                <!-- CHANGED: Added left summary rail -->
                <div class="col-span-12 xl:col-span-4 xl:self-start">
                    <div class="space-y-5 xl:sticky xl:top-6">
                        <!-- CHANGED: Added route details card -->
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-6">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Route Details
                                        </h2>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Define the route name, driver, date, source, and planning area.
                                        </p>
                                    </div>

                                    <!-- CHANGED: Added route form fields -->
                                    <div class="space-y-5">
                                        <div class="flex flex-col">
                                            <label
                                                for="name"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.name }"
                                            >
                                                Route Name
                                            </label>
                                            <InputText
                                                id="name"
                                                v-model="form.name"
                                                fluid
                                                placeholder="Example: Broward Tuesday Route"
                                                :invalid="!!form.errors.name"
                                            />
                                            <Message
                                                v-if="form.errors.name"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.name }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <label
                                                for="description"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.description }"
                                            >
                                                Description
                                            </label>
                                            <Textarea
                                                id="description"
                                                v-model="form.description"
                                                fluid
                                                rows="4"
                                                autoResize
                                                placeholder="Add route notes, territory goals, or planning context..."
                                                :invalid="!!form.errors.description"
                                            />
                                            <Message
                                                v-if="form.errors.description"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.description }}
                                            </Message>
                                        </div>

                                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
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
                                                    for="route_date"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': form.errors.route_date }"
                                                >
                                                    Route Date
                                                </label>
                                                <DatePicker
                                                    id="route_date"
                                                    v-model="form.route_date"
                                                    fluid
                                                    showIcon
                                                    placeholder="Select date..."
                                                    :invalid="!!form.errors.route_date"
                                                />
                                                <Message
                                                    v-if="form.errors.route_date"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ form.errors.route_date }}
                                                </Message>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                            <div class="flex flex-col">
                                                <label
                                                    for="source_type"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': form.errors.source_type }"
                                                >
                                                    Source Type
                                                </label>
                                                <Select
                                                    id="source_type"
                                                    v-model="form.source_type"
                                                    :options="sourceTypeOptions"
                                                    optionLabel="label"
                                                    optionValue="value"
                                                    placeholder="Select source..."
                                                    fluid
                                                    :invalid="!!form.errors.source_type"
                                                />
                                                <Message
                                                    v-if="form.errors.source_type"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ form.errors.source_type }}
                                                </Message>
                                            </div>

                                            <div class="flex flex-col">
                                                <label
                                                    for="radius_miles"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': form.errors.radius_miles }"
                                                >
                                                    Radius (Miles)
                                                </label>
                                                <InputNumber
                                                    id="radius_miles"
                                                    v-model="form.radius_miles"
                                                    fluid
                                                    :min="1"
                                                    :max="100"
                                                    placeholder="Enter radius..."
                                                    :invalid="!!form.errors.radius_miles"
                                                />
                                                <Message
                                                    v-if="form.errors.radius_miles"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ form.errors.radius_miles }}
                                                </Message>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                            <div class="flex flex-col">
                                                <label
                                                    for="center_latitude"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': form.errors.center_latitude }"
                                                >
                                                    Center Latitude
                                                </label>
                                                <InputNumber
                                                    id="center_latitude"
                                                    v-model="form.center_latitude"
                                                    fluid
                                                    :minFractionDigits="4"
                                                    :maxFractionDigits="7"
                                                    placeholder="Latitude..."
                                                    :invalid="!!form.errors.center_latitude"
                                                />
                                                <Message
                                                    v-if="form.errors.center_latitude"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ form.errors.center_latitude }}
                                                </Message>
                                            </div>

                                            <div class="flex flex-col">
                                                <label
                                                    for="center_longitude"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': form.errors.center_longitude }"
                                                >
                                                    Center Longitude
                                                </label>
                                                <InputNumber
                                                    id="center_longitude"
                                                    v-model="form.center_longitude"
                                                    fluid
                                                    :minFractionDigits="4"
                                                    :maxFractionDigits="7"
                                                    placeholder="Longitude..."
                                                    :invalid="!!form.errors.center_longitude"
                                                />
                                                <Message
                                                    v-if="form.errors.center_longitude"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ form.errors.center_longitude }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added compact summary cards -->
                                    <div class="grid grid-cols-2 gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Stops
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ form.stops.length }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Source
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ selectedSourceLabel }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- CHANGED: Added main planning column -->
                <div class="col-span-12 min-w-0 xl:col-span-8">
                    <div class="space-y-5">
                        <!-- CHANGED: Added map placeholder / selection workspace -->
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                                Route Planner Map
                                            </h2>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                This area can later show the office map, radius selection, and available offices inside the selected territory.
                                            </p>
                                        </div>

                                        <Button
                                            label="Use Current Offices in Radius"
                                            icon="pi pi-map"
                                            severity="secondary"
                                            outlined
                                            type="button"
                                            @click="onMockLoadOffices"
                                        />
                                    </div>

                                    <!-- CHANGED: Added large placeholder map block for future map integration -->
                                    <RadiusMap
                                        :offices="offices"
                                        :center-latitude="form.center_latitude"
                                        :center-longitude="form.center_longitude"
                                        :radius-miles="form.radius_miles"
                                        :selected-office-ids="selectedOfficeIds"
                                        @update:centerLatitude="form.center_latitude = $event"
                                        @update:centerLongitude="form.center_longitude = $event"
                                        @update:radiusMiles="form.radius_miles = $event"
                                        @update:selectedOfficeIds="onUpdateSelectedOfficeIds"
                                        @update:selectedOffices="onUpdateSelectedOffices"
                                    />
                                </div>
                            </template>
                        </Card>

                        <!-- CHANGED: Added selected stops card -->
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                                Selected Stops
                                            </h2>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                Review and organize the offices currently included in this route.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added empty state for stops -->
                                    <div
                                        v-if="!form.stops.length"
                                        class="flex min-h-52 flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <i class="pi pi-directions-alt mb-3 text-3xl text-surface-400 dark:text-surface-500"></i>
                                        <p class="font-medium text-surface-700 dark:text-surface-100">
                                            No stops added yet.
                                        </p>
                                        <p class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400">
                                            Add offices from the map or import source, then optimize the route order.
                                        </p>
                                    </div>

                                    <!-- CHANGED: Added stop list cards -->
                                    <div v-else class="space-y-4">
                                        <div
                                            v-for="(stop, index) in form.stops"
                                            :key="stop.office_id ?? index"
                                            class="rounded-2xl border border-surface-200 bg-surface-50 p-4 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                                        <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                            Stop {{ index + 1 }}
                                                        </span>

                                                        <span
                                                            v-if="stop.required"
                                                            class="rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700 dark:bg-red-500/15 dark:text-red-300"
                                                        >
                                                            Required
                                                        </span>
                                                    </div>

                                                    <p class="font-semibold text-surface-900 dark:text-surface-0">
                                                        {{ stop.parent_company || 'Unknown Office' }}
                                                    </p>

                                                    <p
                                                        v-if="stop.office_name"
                                                        class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        {{ stop.office_name }}
                                                    </p>

                                                    <p class="mt-2 text-sm text-surface-700 dark:text-surface-300">
                                                        {{ formatStopAddress(stop) }}
                                                    </p>
                                                </div>

                                                <div class="flex flex-wrap gap-2">
                                                    <Button
                                                        label="Move Up"
                                                        icon="pi pi-arrow-up"
                                                        severity="secondary"
                                                        outlined
                                                        size="small"
                                                        type="button"
                                                        :disabled="index === 0"
                                                        @click="moveStopUp(index)"
                                                    />
                                                    <Button
                                                        label="Move Down"
                                                        icon="pi pi-arrow-down"
                                                        severity="secondary"
                                                        outlined
                                                        size="small"
                                                        type="button"
                                                        :disabled="index === form.stops.length - 1"
                                                        @click="moveStopDown(index)"
                                                    />
                                                    <Button
                                                        label="Remove"
                                                        icon="pi pi-trash"
                                                        severity="danger"
                                                        outlined
                                                        size="small"
                                                        type="button"
                                                        @click="removeStop(index)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
import { computed } from 'vue';
import {
    Card,
    Button,
    InputText,
    InputNumber,
    Textarea,
    Select,
    DatePicker,
    Message,
} from 'primevue';
import RadiusMap from './Components/RadiusMap.vue';

const page = usePage();

// CHANGED: Added safe fallbacks for page props expected by this route create page.
const users = page.props.users ?? [];
const offices = page.props.offices ?? [];

// CHANGED: Added source type options for route creation.
const sourceTypeOptions = [
    { label: 'Manual Selection', value: 'manual' },
    { label: 'Radius Search', value: 'radius' },
    { label: 'CSV Import', value: 'csv' },
    { label: 'Excel Import', value: 'excel' },
];

// CHANGED: Added main route creation form.
const form = useForm({
    name: '',
    description: '',
    assigned_to: null,
    route_date: null,
    source_type: 'manual',
    center_latitude: null,
    center_longitude: null,
    radius_miles: 10,
    stops: [],
});

// CHANGED: Added computed label helper for the summary card.
const selectedSourceLabel = computed(() => {
    return (
        sourceTypeOptions.find((option) => option.value === form.source_type)?.label ??
        'Not selected'
    );
});

// CHANGED: Sync selected IDs → stops (lightweight)
const onUpdateSelectedOfficeIds = (ids) => {
    const existingStops = form.stops;

    // Keep only selected ones
    const filtered = existingStops.filter((stop) =>
        ids.includes(stop.office_id)
    );

    form.stops = filtered.map((stop, index) => ({
        ...stop,
        stop_order: index + 1,
    }));
};

// CHANGED: Build stops from selected offices coming from map
const onUpdateSelectedOffices = (offices) => {
    form.stops = offices.map((office, index) => ({
        office_id: office.id,
        stop_order: index + 1,
        parent_company: office.parent_company,
        office_name: office.office_name,
        street_address: office.street_address,
        unit_number: office.unit_number,
        city: office.city,
        state: office.state,
        zip_code: office.zip_code,
        latitude: office.latitude,
        longitude: office.longitude,
        office_phone: office.office_phone,
        required: false,
    }));
}; 

// CHANGED: Track selected office IDs separately from stops
const selectedOfficeIds = computed(() =>
    form.stops.map((stop) => stop.office_id)
);

// CHANGED: Added mock office loader so the page is usable while map/radius logic is still being built.
const onMockLoadOffices = () => {
    if (form.stops.length || !offices.length) return;

    form.stops = offices.slice(0, 5).map((office, index) => ({
        office_id: office.id,
        stop_order: index + 1,
        parent_company: office.parent_company,
        office_name: office.office_name,
        street_address: office.street_address,
        unit_number: office.unit_number,
        city: office.city,
        state: office.state,
        zip_code: office.zip_code,
        latitude: office.latitude,
        longitude: office.longitude,
        office_phone: office.office_phone,
        required: false,
    }));
};

// CHANGED: Added helper to format stop addresses consistently.
const formatStopAddress = (stop) => {
    const unit = stop.unit_number ? ` #${stop.unit_number}` : '';
    return `${stop.street_address || ''}${unit}, ${stop.city || ''}, ${stop.state || ''} ${stop.zip_code || ''}`.trim();
};

// CHANGED: Added stop reordering helpers for manual organization before optimization is built.
const moveStopUp = (index) => {
    if (index <= 0) return;

    const updatedStops = [...form.stops];
    [updatedStops[index - 1], updatedStops[index]] = [updatedStops[index], updatedStops[index - 1]];

    form.stops = updatedStops.map((stop, stopIndex) => ({
        ...stop,
        stop_order: stopIndex + 1,
    }));
};

// CHANGED: Added downward stop reorder helper.
const moveStopDown = (index) => {
    if (index >= form.stops.length - 1) return;

    const updatedStops = [...form.stops];
    [updatedStops[index], updatedStops[index + 1]] = [updatedStops[index + 1], updatedStops[index]];

    form.stops = updatedStops.map((stop, stopIndex) => ({
        ...stop,
        stop_order: stopIndex + 1,
    }));
};

// CHANGED: Added stop removal helper.
const removeStop = (index) => {
    const updatedStops = [...form.stops];
    updatedStops.splice(index, 1);

    form.stops = updatedStops.map((stop, stopIndex) => ({
        ...stop,
        stop_order: stopIndex + 1,
    }));
};

// CHANGED: Added placeholder optimize action so UI flow is ready before routing logic exists.
const onOptimizeStops = () => {
    form.stops = [...form.stops].map((stop, index) => ({
        ...stop,
        stop_order: index + 1,
    }));
};

// CHANGED: Added submit handler for the new route create page.
const onSubmit = () => {
    form.post(route('office-route.store'), {
        preserveScroll: true,
        onError: (error) => {
            console.log(error);
        },
    });
};
</script>
