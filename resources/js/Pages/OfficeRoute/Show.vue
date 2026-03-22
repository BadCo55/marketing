<template>
    <Head :title="officeRoute.name || 'Route'" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col px-4 py-5 md:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div class="min-w-0">
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

                        <div class="min-w-0">
                            <h1
                                class="truncate text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0"
                            >
                                {{ officeRoute.name || 'Unnamed Route' }}
                            </h1>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Review route details, assigned user, planning radius, and ordered office stops.
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                        <Link
                            v-if="canStartRouteRun"
                            :href="route('office-run.start', officeRoute.id)"
                        >
                            <Button
                                :label="startRunButtonLabel"
                                :icon="startRunButtonIcon"
                            />
                        </Link>

                        <Link :href="route('office-route.edit', officeRoute.id)">
                            <Button
                                label="Edit Route"
                                icon="pi pi-pen-to-square"
                                severity="secondary"
                                outlined
                            />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1">
                <div class="grid grid-cols-12 items-start gap-5">
                    <!-- Left Rail -->
                    <div class="col-span-12 self-start xl:col-span-4 2xl:col-span-4">
                        <div class="space-y-5 xl:sticky xl:top-6">
                            <Card
                                class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                            >
                                <template #content>
                                    <div class="space-y-6">
                                        <div>
                                            <h2
                                                class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                Route Details
                                            </h2>
                                            <p
                                                class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                Core planning information and route metadata.
                                            </p>
                                        </div>

                                        <div class="space-y-4">
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                >
                                                    <i class="pi pi-user"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        Assigned To
                                                    </p>
                                                    <p
                                                        class="font-medium text-surface-900 dark:text-surface-0"
                                                    >
                                                        {{ officeRoute.assignedTo?.formatted_full_name || officeRoute.assignedTo?.name || 'Unassigned' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                >
                                                    <i class="pi pi-calendar"></i>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        Route Date
                                                    </p>
                                                    <p
                                                        class="font-medium text-surface-900 dark:text-surface-0"
                                                    >
                                                        {{ formattedRouteDate }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                >
                                                    <i class="pi pi-directions-alt"></i>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        Source Type
                                                    </p>
                                                    <p
                                                        class="font-medium text-surface-900 dark:text-surface-0"
                                                    >
                                                        {{ formatLabel(officeRoute.source_type) || '—' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                >
                                                    <i class="pi pi-map"></i>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        Radius
                                                    </p>
                                                    <p
                                                        class="font-medium text-surface-900 dark:text-surface-0"
                                                    >
                                                        {{ formattedRadius }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                >
                                                    <i class="pi pi-file-edit"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p
                                                        class="text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        Description
                                                    </p>
                                                    <p
                                                        class="leading-6"
                                                        :class="
                                                            officeRoute.description
                                                                ? 'text-surface-700 dark:text-surface-300'
                                                                : 'italic text-surface-400 dark:text-surface-500'
                                                        "
                                                    >
                                                        {{ officeRoute.description || 'No description provided.' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <Divider />

                                        <div class="grid grid-cols-2 gap-3">
                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                                >
                                                    Stops
                                                </p>
                                                <p
                                                    class="text-2xl font-bold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ orderedStops.length }}
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                                >
                                                    Status
                                                </p>
                                                <p
                                                    class="text-sm font-semibold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ formatLabel(officeRoute.status) || 'Draft' }}
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                                >
                                                    Est. Drive Time
                                                </p>
                                                <p
                                                    class="text-sm font-semibold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ formattedDriveMinutes }}
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                                >
                                                    Est. Miles
                                                </p>
                                                <p
                                                    class="text-sm font-semibold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ formattedDriveMiles }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            v-if="hasCenterPoint"
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                            >
                                                Center Point
                                            </p>
                                            <p
                                                class="text-sm font-medium text-surface-900 dark:text-surface-0"
                                            >
                                                {{ officeRoute.center_latitude }}, {{ officeRoute.center_longitude }}
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </Card>

                            <Card
                                class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                            >
                                <template #content>
                                    <div class="space-y-3">
                                        <div>
                                            <h3
                                                class="text-base font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                Audit Info
                                            </h3>
                                            <p
                                                class="text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                Route ownership and creation details.
                                            </p>
                                        </div>

                                        <div class="space-y-2 text-sm">
                                            <p class="text-surface-700 dark:text-surface-300">
                                                <span
                                                    class="font-medium text-surface-900 dark:text-surface-100"
                                                >
                                                    Created By:
                                                </span>
                                                {{ officeRoute.createdBy?.formatted_full_name || officeRoute.createdBy?.name || 'Unknown' }}
                                            </p>

                                            <p class="text-surface-700 dark:text-surface-300">
                                                <span
                                                    class="font-medium text-surface-900 dark:text-surface-100"
                                                >
                                                    Route ID:
                                                </span>
                                                {{ officeRoute.id || '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-span-12 min-w-0 xl:col-span-8 2xl:col-span-8">
                        <div class="space-y-5">
                            <Card
                                class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                            >
                                <template #content>
                                    <div class="space-y-5">
                                        <div
                                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                                        >
                                            <div>
                                                <h2
                                                    class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                                >
                                                    Route Stops
                                                </h2>
                                                <p
                                                    class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    Stops shown in their current route order.
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            v-if="!orderedStops.length"
                                            class="flex min-h-52 flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <i
                                                class="pi pi-map-marker mb-3 text-3xl text-surface-400 dark:text-surface-500"
                                            ></i>
                                            <p
                                                class="font-medium text-surface-700 dark:text-surface-200"
                                            >
                                                No stops found.
                                            </p>
                                            <p
                                                class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                This route has not had any offices added yet.
                                            </p>
                                        </div>

                                        <div v-else class="space-y-4">
                                            <div
                                                v-for="stop in orderedStops"
                                                :key="stop.id"
                                                class="rounded-2xl border border-surface-200 bg-surface-50 p-5 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_20rem]">
                                                    <!-- Left: stop info -->
                                                    <div class="min-w-0">
                                                        <div
                                                            class="mb-3 flex flex-wrap items-center gap-2"
                                                        >
                                                            <span
                                                                class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary"
                                                            >
                                                                Stop {{ stop.stop_order }}
                                                            </span>

                                                            <span
                                                                v-if="stop.required"
                                                                class="rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700 dark:bg-red-500/15 dark:text-red-300"
                                                            >
                                                                Required
                                                            </span>

                                                            <span
                                                                class="rounded-full border border-surface-200 bg-surface-0 px-3 py-1 text-sm text-surface-700 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300"
                                                            >
                                                                {{ formatLabel(stop.status) || 'Pending' }}
                                                            </span>
                                                        </div>

                                                        <p
                                                            class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                        >
                                                            {{ stop.office?.parent_company || 'Unknown Office' }}
                                                        </p>

                                                        <p
                                                            v-if="stop.office?.office_name"
                                                            class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                                        >
                                                            {{ stop.office.office_name }}
                                                        </p>

                                                        <p
                                                            class="mt-2 text-sm leading-6 text-surface-700 dark:text-surface-300"
                                                        >
                                                            {{ formatOfficeAddress(stop.office) }}
                                                        </p>

                                                        <p
                                                            v-if="stop.office?.office_phone"
                                                            class="mt-2 text-sm text-surface-700 dark:text-surface-300"
                                                        >
                                                            <span
                                                                class="font-medium text-surface-900 dark:text-surface-100"
                                                            >
                                                                Phone:
                                                            </span>
                                                            {{ stop.office.office_phone }}
                                                        </p>
                                                    </div>

                                                    <!-- Right: timing panel -->
                                                    <div class="min-w-0">
                                                        <div
                                                            class="rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900"
                                                        >
                                                            <div class="space-y-3 text-sm">
                                                                <div>
                                                                    <p
                                                                        class="text-surface-500 dark:text-surface-400"
                                                                    >
                                                                        Scheduled Arrival
                                                                    </p>
                                                                    <p
                                                                        class="font-medium text-surface-900 dark:text-surface-0"
                                                                    >
                                                                        {{ formatDateTime(stop.scheduled_arrival) }}
                                                                    </p>
                                                                </div>

                                                                <div>
                                                                    <p
                                                                        class="text-surface-500 dark:text-surface-400"
                                                                    >
                                                                        Actual Arrival
                                                                    </p>
                                                                    <p
                                                                        class="font-medium text-surface-900 dark:text-surface-0"
                                                                    >
                                                                        {{ formatDateTime(stop.actual_arrival) }}
                                                                    </p>
                                                                </div>

                                                                <div>
                                                                    <p
                                                                        class="text-surface-500 dark:text-surface-400"
                                                                    >
                                                                        Actual Departure
                                                                    </p>
                                                                    <p
                                                                        class="font-medium text-surface-900 dark:text-surface-0"
                                                                    >
                                                                        {{ formatDateTime(stop.actual_departure) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    v-if="stop.notes"
                                                    class="mt-4 rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900"
                                                >
                                                    <p
                                                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    >
                                                        Stop Notes
                                                    </p>
                                                    <p
                                                        class="whitespace-pre-line text-sm leading-6 text-surface-700 dark:text-surface-300"
                                                    >
                                                        {{ stop.notes }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>

                            <Card
                                v-if="officeRoute.notes"
                                class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                            >
                                <template #content>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                        >
                                            Route Notes
                                        </h2>
                                        <p
                                            class="mt-3 whitespace-pre-line leading-6 text-surface-700 dark:text-surface-300"
                                        >
                                            {{ officeRoute.notes }}
                                        </p>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Card, Button, Divider } from 'primevue'

const page = usePage()
const officeRoute = page.props.office_route ?? {}

const orderedStops = computed(() => {
    return [...(officeRoute.stops ?? [])].sort((a, b) => {
        return (a.stop_order ?? 0) - (b.stop_order ?? 0)
    })
})

const formattedRouteDate = computed(() => {
    if (!officeRoute.route_date) return 'Not scheduled'
    return formatDateTime(officeRoute.route_date, false)
})

const formattedRadius = computed(() => {
    if (
        officeRoute.radius_miles === null ||
        officeRoute.radius_miles === undefined ||
        officeRoute.radius_miles === ''
    ) {
        return 'Not set'
    }

    return `${officeRoute.radius_miles} mile(s)`
})

const formattedDriveMinutes = computed(() => {
    const minutes = officeRoute.estimated_total_drive_minutes

    if (minutes === null || minutes === undefined || minutes === '') {
        return 'Not estimated'
    }

    return `${minutes} min`
})

const formattedDriveMiles = computed(() => {
    const miles = officeRoute.estimated_total_drive_miles

    if (miles === null || miles === undefined || miles === '') {
        return 'Not estimated'
    }

    return `${miles} mi`
})

const hasCenterPoint = computed(() => {
    return (
        officeRoute.center_latitude !== null &&
        officeRoute.center_latitude !== undefined &&
        officeRoute.center_longitude !== null &&
        officeRoute.center_longitude !== undefined
    )
})

// CHANGED: Added helper so the Begin Route button only shows when the route can actually be run.
const canStartRouteRun = computed(() => {
    return !!officeRoute.id && orderedStops.value.length > 0
})

// CHANGED: Added button label helper in case you later want to vary the CTA text.
const startRunButtonLabel = computed(() => {
    return 'Begin Route'
})

// CHANGED: Added button icon helper for consistency with the route run flow.
const startRunButtonIcon = computed(() => {
    return 'pi pi-play'
})

const formatLabel = (value) => {
    if (!value) return ''
    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}

const formatOfficeAddress = (office) => {
    if (!office) return 'No address available'

    const unit = office.unit_number ? ` #${office.unit_number}` : ''

    return `${office.street_address || ''}${unit}, ${office.city || ''}, ${office.state || ''} ${office.zip_code || ''}`.trim()
}

const formatDateTime = (value, includeTime = true) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return value

    if (!includeTime) {
        return date.toLocaleDateString()
    }

    return date.toLocaleString()
}
</script>
