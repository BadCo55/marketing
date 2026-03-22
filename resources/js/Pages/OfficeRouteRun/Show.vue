<template>
    <Head :title="routeRun?.office_route?.name ? `Run - ${routeRun.office_route.name}` : 'Route Run'" />

    <Layout>
        <div class="w-full px-4 py-5 md:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-2 flex flex-wrap gap-2">
                            <Link :href="route('office-run.index')">
                                <Button
                                    label="Back to Route Runs"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>

                            <Link
                                v-if="routeRun.office_route_id || routeRun.office_route_run?.id"
                                :href="route('office-route.show', routeRun.office_route_id || routeRun.office_route_run?.id)"
                            >
                                <Button
                                    label="View Route"
                                    icon="pi pi-map"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                                {{ routeRun.office_route?.name || 'Unnamed Route Run' }}
                            </h1>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Review run details, execution progress, assigned user, and the outcome of each stop.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link
                            v-if="showActiveButton"
                            :href="route('office-run.active', routeRun.id)"
                        >
                            <Button
                                :label="activeButtonLabel"
                                :icon="activeButtonIcon"
                            />
                        </Link>

                        <Link
                            v-if="canResumeFromRoute"
                            :href="route('office-run.start', routeRun.office_route_id || routeRun.office_route_run?.id)"
                        >
                            <Button
                                label="Start New Run From Route"
                                icon="pi pi-plus-circle"
                                severity="secondary"
                                outlined
                            />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 items-start gap-5">
                <div class="col-span-12 xl:col-span-4 xl:self-start">
                    <div class="space-y-5 xl:sticky xl:top-6">
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-6">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Run Details
                                        </h2>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Core execution information and run-level metadata.
                                        </p>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-tag"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Status
                                                </p>
                                                <div class="mt-1">
                                                    <span
                                                        class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                                        :class="getStatusPillClass(routeRun.status)"
                                                    >
                                                        {{ formatLabel(routeRun.status) || 'Not Started' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-user"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Assigned To
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ assignedUserName }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-calendar"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Run Date
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ formattedRunDate }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-play-circle"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Started At
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ formatDateTime(routeRun.started_at) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-check-circle"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Completed At
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ formatDateTime(routeRun.completed_at) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-stop-circle"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Cancelled At
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ formatDateTime(routeRun.cancelled_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <Divider />

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Planned Stops
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ plannedStopCount }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Completed
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ completedCount }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Skipped
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ skippedCount }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Cancelled
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ cancelledCount }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-surface-500 dark:text-surface-400">
                                                Progress
                                            </span>
                                            <span class="font-medium text-surface-900 dark:text-surface-0">
                                                {{ progressPercent }}%
                                            </span>
                                        </div>

                                        <ProgressBar :value="progressPercent" />
                                    </div>

                                    <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                        <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                            Current Stop
                                        </p>
                                        <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                            {{ currentStopLabel }}
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-3">
                                    <div>
                                        <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                            Audit Info
                                        </h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Run ownership and audit trail.
                                        </p>
                                    </div>

                                    <div class="space-y-2 text-sm">
                                        <p class="text-surface-700 dark:text-surface-300">
                                            <span class="font-medium text-surface-900 dark:text-surface-100">Created By:</span>
                                            {{ createdByName }}
                                        </p>

                                        <p class="text-surface-700 dark:text-surface-300">
                                            <span class="font-medium text-surface-900 dark:text-surface-100">Started By:</span>
                                            {{ startedByName }}
                                        </p>

                                        <p class="text-surface-700 dark:text-surface-300">
                                            <span class="font-medium text-surface-900 dark:text-surface-100">Completed By:</span>
                                            {{ completedByName }}
                                        </p>

                                        <p class="text-surface-700 dark:text-surface-300">
                                            <span class="font-medium text-surface-900 dark:text-surface-100">Run ID:</span>
                                            {{ routeRun.id || '—' }}
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-3">
                                    <div>
                                        <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                            Execution Metrics
                                        </h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Actual performance figures captured for this run.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-3">
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Actual Drive Time
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ formattedActualDriveMinutes }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Actual Duration
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ formattedActualDurationMinutes }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Actual Drive Miles
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ formattedActualDriveMiles }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <div class="col-span-12 min-w-0 xl:col-span-8">
                    <div class="space-y-5">
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                                Run Stops
                                            </h2>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                Stops shown in execution order with their status and recorded outcome.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="!orderedStops.length"
                                        class="flex min-h-52 flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <i class="pi pi-map-marker mb-3 text-3xl text-surface-400 dark:text-surface-500"></i>
                                        <p class="font-medium text-surface-700 dark:text-surface-200">
                                            No run stops found.
                                        </p>
                                        <p class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400">
                                            This run does not have any execution stops loaded yet.
                                        </p>
                                    </div>

                                    <div v-else class="space-y-4">
                                        <div
                                            v-for="stop in orderedStops"
                                            :key="stop.id"
                                            class="rounded-2xl border p-5 shadow-sm"
                                            :class="getStopCardClass(stop)"
                                        >
                                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <div class="mb-3 flex flex-wrap items-center gap-2">
                                                        <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                            Stop {{ stop.actual_stop_order || stop.planned_stop_order || '—' }}
                                                        </span>

                                                        <span
                                                            class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                                            :class="getStatusPillClass(stop.status)"
                                                        >
                                                            {{ formatLabel(stop.status) || 'Pending' }}
                                                        </span>

                                                        <span
                                                            v-if="stop.required"
                                                            class="rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700 dark:bg-red-500/15 dark:text-red-300"
                                                        >
                                                            Required
                                                        </span>

                                                        <span
                                                            v-if="isCurrentStop(stop)"
                                                            class="rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-300"
                                                        >
                                                            Current
                                                        </span>
                                                    </div>

                                                    <p class="text-lg font-semibold text-surface-900 dark:text-surface-0">
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

                                                    <p
                                                        v-if="stop.office?.office_phone || stop.office_phone"
                                                        class="mt-2 text-sm text-surface-700 dark:text-surface-300"
                                                    >
                                                        <span class="font-medium text-surface-900 dark:text-surface-100">Phone:</span>
                                                        {{ stop.office?.office_phone || stop.office_phone }}
                                                    </p>

                                                    <div
                                                        v-if="stop.notes"
                                                        class="mt-4 rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900"
                                                    >
                                                        <p class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                            Stop Notes
                                                        </p>
                                                        <p class="whitespace-pre-line text-sm leading-6 text-surface-700 dark:text-surface-300">
                                                            {{ stop.notes }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        v-if="stop.skip_reason"
                                                        class="mt-4 rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-950/20"
                                                    >
                                                        <p class="mb-2 text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                                            Skip Reason
                                                        </p>
                                                        <p class="whitespace-pre-line text-sm leading-6 text-yellow-800 dark:text-yellow-200">
                                                            {{ stop.skip_reason }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        v-if="stop.cancel_reason"
                                                        class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/20"
                                                    >
                                                        <p class="mb-2 text-sm font-medium text-red-800 dark:text-red-300">
                                                            Cancel Reason
                                                        </p>
                                                        <p class="whitespace-pre-line text-sm leading-6 text-red-800 dark:text-red-200">
                                                            {{ stop.cancel_reason }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="w-full lg:max-w-xs">
                                                    <div class="rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900">
                                                        <div class="space-y-3 text-sm">
                                                            <div>
                                                                <p class="text-surface-500 dark:text-surface-400">
                                                                    Started
                                                                </p>
                                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                                    {{ formatDateTime(stop.started_at) }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <p class="text-surface-500 dark:text-surface-400">
                                                                    Arrived
                                                                </p>
                                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                                    {{ formatDateTime(stop.arrived_at) }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <p class="text-surface-500 dark:text-surface-400">
                                                                    Completed
                                                                </p>
                                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                                    {{ formatDateTime(stop.completed_at) }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <p class="text-surface-500 dark:text-surface-400">
                                                                    Duration
                                                                </p>
                                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                                    {{ formatMinutes(stop.duration_minutes) }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <p class="text-surface-500 dark:text-surface-400">
                                                                    Outcome
                                                                </p>
                                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                                    {{ stop.outcome || '—' }}
                                                                </p>
                                                            </div>

                                                            <div>
                                                                <p class="text-surface-500 dark:text-surface-400">
                                                                    Office Visit
                                                                </p>
                                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                                    {{ stop.office_visit_id ? `Linked (#${stop.office_visit_id})` : 'Not linked' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card
                            v-if="routeRun.notes"
                            class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                        >
                            <template #content>
                                <div>
                                    <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                        Run Notes
                                    </h2>
                                    <p class="mt-3 whitespace-pre-line leading-6 text-surface-700 dark:text-surface-300">
                                        {{ routeRun.notes }}
                                    </p>
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
import Layout from '@/Layouts/Layout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Card, Button, Divider, ProgressBar } from 'primevue'

const page = usePage()
const routeRun = page.props.office_route_run ?? {}

console.log(routeRun.office_route);

const orderedStops = computed(() => {
    return [...(routeRun.ordered_stops ?? [])].sort((a, b) => {
        const aOrder = a.actual_stop_order ?? a.planned_stop_order ?? 0
        const bOrder = b.actual_stop_order ?? b.planned_stop_order ?? 0
        return aOrder - bOrder
    })
})

const plannedStopCount = computed(() => {
    return Number(routeRun.planned_stop_count ?? orderedStops.value.length ?? 0)
})

const completedCount = computed(() => {
    return orderedStops.value.filter((stop) => stop.status === 'completed').length
})

const skippedCount = computed(() => {
    return orderedStops.value.filter((stop) => stop.status === 'skipped').length
})

const cancelledCount = computed(() => {
    return orderedStops.value.filter((stop) => stop.status === 'cancelled').length
})

const progressPercent = computed(() => {
    if (!plannedStopCount.value) return 0
    return Math.round((completedCount.value / plannedStopCount.value) * 100)
})

const currentStop = computed(() => {
    return orderedStops.value.find((stop) =>
        ['pending', 'in_progress', 'arrived'].includes(stop.status)
    ) ?? null
})

const currentStopLabel = computed(() => {
    if (!currentStop.value) return 'No active stop'

    return `${currentStop.value.actual_stop_order || currentStop.value.planned_stop_order || '—'} - ${currentStop.value.office?.parent_company || currentStop.value.parent_company || 'Unknown Office'}`
})

const formattedRunDate = computed(() => {
    if (!routeRun.run_date) return 'Not scheduled'
    return formatDateTime(routeRun.run_date, false)
})

const assignedUserName = computed(() => {
    return (
        routeRun.assigned_to?.formatted_full_name ||
        routeRun.assignedTo?.formatted_full_name ||
        routeRun.assignedTo?.name ||
        'Unassigned'
    )
})

const createdByName = computed(() => {
    return (
        routeRun.created_by?.formatted_full_name ||
        routeRun.createdBy?.formatted_full_name ||
        routeRun.createdBy?.name ||
        'Unknown'
    )
})

const startedByName = computed(() => {
    return (
        routeRun.started_by?.formatted_full_name ||
        routeRun.startedBy?.formatted_full_name ||
        routeRun.startedBy?.name ||
        'Unknown'
    )
})

const completedByName = computed(() => {
    return (
        routeRun.completed_by?.formatted_full_name ||
        routeRun.completedBy?.formatted_full_name ||
        routeRun.completedBy?.name ||
        'Unknown'
    )
})

const formattedActualDriveMinutes = computed(() => {
    return formatMinutes(routeRun.actual_total_drive_minutes)
})

const formattedActualDurationMinutes = computed(() => {
    return formatMinutes(routeRun.actual_total_duration_minutes)
})

const formattedActualDriveMiles = computed(() => {
    if (
        routeRun.actual_total_drive_miles === null ||
        routeRun.actual_total_drive_miles === undefined ||
        routeRun.actual_total_drive_miles === ''
    ) {
        return 'Not recorded'
    }

    return `${routeRun.actual_total_drive_miles} mi`
})

const showActiveButton = computed(() => {
    return !!routeRun.id && ['not_started', 'in_progress', 'paused'].includes(routeRun.status)
})

const activeButtonLabel = computed(() => {
    if (routeRun.status === 'not_started') return 'Begin Route'
    return 'Resume Route'
})

const activeButtonIcon = computed(() => {
    if (routeRun.status === 'not_started') return 'pi pi-play'
    return 'pi pi-directions'
})

const canResumeFromRoute = computed(() => {
    return !!(routeRun.office_route_id || routeRun.office_route_run?.id)
})

const isCurrentStop = (stop) => {
    return currentStop.value?.id === stop.id
}

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
        case 'arrived':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
        case 'skipped':
            return 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300'
        case 'cancelled':
            return 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300'
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300'
    }
}

const getStopCardClass = (stop) => {
    if (isCurrentStop(stop)) {
        return 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950/20'
    }

    switch (stop.status) {
        case 'completed':
            return 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950/20'
        case 'skipped':
            return 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950/20'
        case 'cancelled':
            return 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950/20'
        default:
            return 'border-surface-200 bg-surface-50 dark:border-surface-700 dark:bg-surface-800'
    }
}

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

const formatMinutes = (value) => {
    if (value === null || value === undefined || value === '') {
        return 'Not recorded'
    }

    return `${value} min`
}
</script>
