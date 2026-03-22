<template>
    <Head :title="`Active Route Run - ${routeRun.office_route?.name || 'Route Run'}`" />

    <Layout>
        <div class="px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: Added page header with stronger route/run context and top actions -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <!-- CHANGED: Added back/view actions -->
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
                                v-if="routeRun.id"
                                :href="route('office-run.show', routeRun.id)"
                            >
                                <Button
                                    label="View Run Details"
                                    icon="pi pi-eye"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <!-- CHANGED: Added route/run title block -->
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                                Active Route Run
                            </h1>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Work through each stop, navigate quickly, call the office, and record the outcome in real time.
                            </p>
                        </div>
                    </div>

                    <!-- CHANGED: Added top-right completion/cancel actions -->
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button
                            label="Complete Run"
                            icon="pi pi-check-circle"
                            severity="success"
                            :disabled="!canCompleteRun || !routeRun.id"
                            :loading="completeRunForm.processing"
                            @click="onCompleteRun"
                        />

                        <Button
                            label="Cancel Run"
                            icon="pi pi-times-circle"
                            severity="danger"
                            outlined
                            :disabled="!routeRun.id"
                            :loading="cancelRunForm.processing"
                            @click="showCancelDialog = true"
                        />
                    </div>
                </div>
            </div>

            <!-- CHANGED: Added responsive two-column layout -->
            <div class="grid grid-cols-12 items-start gap-5">
                <!-- Left Rail -->
                <div class="col-span-12 xl:col-span-4 xl:self-start">
                    <div class="space-y-5 xl:sticky xl:top-6">
                        <!-- CHANGED: Added run summary card -->
                        <Card class="overflow-hidden rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-6">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Run Summary
                                        </h2>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Monitor the current route run and overall progress.
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
                                                    {{ routeRun.office_route?.name || 'Unnamed Route' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-user" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Assigned To
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ routeRun.assignedTo?.formatted_full_name || routeRun.assignedTo?.name || 'Unassigned' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-calendar" />
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
                                                <i class="pi pi-clock" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Started
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-0">
                                                    {{ formatDateTime(routeRun.started_at) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <Divider />

                                    <!-- CHANGED: Added compact progress metric cards -->
                                    <div class="grid grid-cols-2 gap-3">
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
                                                Remaining
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ remainingCount }}
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
                                                Progress
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ progressPercent }}%
                                            </p>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added progress bar -->
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-surface-500 dark:text-surface-400">Route Progress</span>
                                            <span class="font-medium text-surface-900 dark:text-surface-0">
                                                {{ completedCount }}/{{ runStops.length }}
                                            </span>
                                        </div>

                                        <ProgressBar :value="progressPercent" />
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <!-- CHANGED: Reworked current stop card to make the next step very obvious -->
                        <Card class="rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                            Current Stop
                                        </h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Focus on the next actionable stop in the route.
                                        </p>
                                    </div>

                                    <div
                                        v-if="currentStop"
                                        class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950/20"
                                    >
                                        <!-- CHANGED: Added prominent current-step status -->
                                        <div class="mb-3 flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                Stop {{ currentStop.actual_stop_order || currentStop.planned_stop_order || '—' }}
                                            </span>

                                            <span
                                                class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                                :class="getStopStatusPillClass(currentStop.status)"
                                            >
                                                {{ formatLabel(currentStop.status) }}
                                            </span>

                                            <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">
                                                {{ getNextActionLabel(currentStop) }}
                                            </span>
                                        </div>

                                        <p class="font-semibold text-surface-900 dark:text-surface-0">
                                            {{ currentStop.office?.parent_company || currentStop.parent_company || 'Unknown Office' }}
                                        </p>

                                        <p
                                            v-if="currentStop.office?.office_name || currentStop.office_name"
                                            class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                        >
                                            {{ currentStop.office?.office_name || currentStop.office_name }}
                                        </p>

                                        <p class="mt-2 text-sm text-surface-700 dark:text-surface-300">
                                            {{ formatOfficeAddress(currentStop.office || currentStop) }}
                                        </p>

                                        <!-- CHANGED: Added clickable phone line -->
                                        <p
                                            v-if="currentStop.office?.office_phone || currentStop.office_phone"
                                            class="mt-2 text-sm text-surface-700 dark:text-surface-300"
                                        >
                                            <span class="font-medium text-surface-900 dark:text-surface-100">
                                                Phone:
                                            </span>

                                            <a
                                                :href="getPhoneHref(currentStop.office || currentStop)"
                                                class="ml-1 font-medium text-primary hover:underline"
                                            >
                                                {{ currentStop.office?.office_phone || currentStop.office_phone }}
                                            </a>
                                        </p>

                                        <!-- CHANGED: Added real-world field actions first -->
                                        <div class="mt-4 flex flex-col gap-2">
                                            <a
                                                v-if="getNavigationUrl(currentStop)"
                                                :href="getNavigationUrl(currentStop)"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="w-full"
                                            >
                                                <Button
                                                    label="Navigate"
                                                    icon="pi pi-directions-alt"
                                                    class="w-full"
                                                    size="large"
                                                />
                                            </a>

                                            <a
                                                v-if="getPhoneHref(currentStop.office || currentStop)"
                                                :href="getPhoneHref(currentStop.office || currentStop)"
                                                class="w-full"
                                            >
                                                <Button
                                                    label="Call Office"
                                                    icon="pi pi-phone"
                                                    severity="secondary"
                                                    class="w-full"
                                                    size="large"
                                                />
                                            </a>

                                            <Button
                                                v-if="shouldShowArrivePrimary(currentStop)"
                                                label="Arrive at Stop"
                                                icon="pi pi-map-marker"
                                                severity="secondary"
                                                outlined
                                                class="w-full"
                                                @click="onMarkArrived(currentStop)"
                                            />

                                            <Button
                                                v-else-if="shouldShowCompletePrimary(currentStop)"
                                                label="Complete Stop"
                                                icon="pi pi-check"
                                                severity="success"
                                                class="w-full"
                                                @click="onCompleteStop(currentStop)"
                                            />

                                            <Button
                                                v-if="canLogVisit(currentStop)"
                                                label="Log Visit"
                                                icon="pi pi-plus-circle"
                                                severity="secondary"
                                                outlined
                                                class="w-full"
                                                @click="onLogVisit(currentStop)"
                                            />

                                            <Button
                                                v-if="canSkipStop(currentStop)"
                                                label="Skip Stop"
                                                icon="pi pi-forward"
                                                severity="warn"
                                                outlined
                                                class="w-full"
                                                @click="openSkipDialog(currentStop)"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        v-else
                                        class="rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-8 text-center dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <i class="pi pi-check-circle text-2xl text-surface-400 dark:text-surface-500" />
                                        <p class="mt-3 font-medium text-surface-700 dark:text-surface-200">
                                            No active stop remaining.
                                        </p>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            All stops may already be completed, skipped, or cancelled.
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-span-12 min-w-0 xl:col-span-8">
                    <div class="space-y-5">
                        <!-- CHANGED: Reworked stop cards to make navigation, call, and workflow easier -->
                        <Card class="overflow-hidden rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                                Route Stops
                                            </h2>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                Work through each stop in order and record the outcome of the visit.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        v-if="!runStops.length"
                                        class="flex min-h-52 flex-col items-center justify-center rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <i class="pi pi-map-marker text-3xl text-surface-400 dark:text-surface-500" />
                                        <p class="mt-3 font-medium text-surface-700 dark:text-surface-200">
                                            No run stops found.
                                        </p>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            This run does not have any execution stops loaded yet.
                                        </p>
                                    </div>

                                    <div v-else class="space-y-4">
                                        <div
                                            v-for="stop in runStops"
                                            :key="stop.id"
                                            class="rounded-xl border p-5 shadow-sm transition-colors"
                                            :class="getStopCardClass(stop)"
                                        >
                                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                                <div class="min-w-0 flex-1">
                                                    <!-- CHANGED: Added stronger stop header with next-step badge -->
                                                    <div class="mb-3 flex flex-wrap items-center gap-2">
                                                        <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                            Stop {{ stop.actual_stop_order || stop.planned_stop_order || '—' }}
                                                        </span>

                                                        <span
                                                            class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                                            :class="getStopStatusPillClass(stop.status)"
                                                        >
                                                            {{ formatLabel(stop.status) }}
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

                                                        <span
                                                            v-if="getNextActionLabel(stop)"
                                                            class="rounded-full bg-surface-100 px-3 py-1 text-sm font-medium text-surface-700 dark:bg-surface-800 dark:text-surface-200"
                                                        >
                                                            {{ getNextActionLabel(stop) }}
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

                                                    <!-- CHANGED: Made phone clickable in stop content -->
                                                    <p
                                                        v-if="stop.office?.office_phone || stop.office_phone"
                                                        class="mt-2 text-sm text-surface-700 dark:text-surface-300"
                                                    >
                                                        <span class="font-medium text-surface-900 dark:text-surface-100">
                                                            Phone:
                                                        </span>

                                                        <a
                                                            :href="getPhoneHref(stop.office || stop)"
                                                            class="ml-1 font-medium text-primary hover:underline"
                                                        >
                                                            {{ stop.office?.office_phone || stop.office_phone }}
                                                        </a>
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
                                                </div>

                                                <!-- CHANGED: Reworked action panel to prioritize navigate/call -->
                                                <div class="w-full lg:max-w-xs">
                                                    <div class="rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900">
                                                        <div class="space-y-4">
                                                            <div class="space-y-2 text-sm">
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
                                                                        Outcome
                                                                    </p>
                                                                    <p class="font-medium text-surface-900 dark:text-surface-0">
                                                                        {{ stop.outcome || '—' }}
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <Divider />

                                                            <div class="flex flex-col gap-2">
                                                                <a
                                                                    v-if="getNavigationUrl(stop)"
                                                                    :href="getNavigationUrl(stop)"
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    class="w-full"
                                                                >
                                                                    <Button
                                                                        label="Navigate"
                                                                        icon="pi pi-directions-alt"
                                                                        class="w-full"
                                                                        size="small"
                                                                    />
                                                                </a>

                                                                <a
                                                                    v-if="getPhoneHref(stop.office || stop)"
                                                                    :href="getPhoneHref(stop.office || stop)"
                                                                    class="w-full"
                                                                >
                                                                    <Button
                                                                        label="Call Office"
                                                                        icon="pi pi-phone"
                                                                        severity="secondary"
                                                                        class="w-full"
                                                                        size="small"
                                                                    />
                                                                </a>

                                                                <Button
                                                                    v-if="shouldShowArrivePrimary(stop)"
                                                                    label="Arrive at Stop"
                                                                    icon="pi pi-map-marker"
                                                                    severity="secondary"
                                                                    outlined
                                                                    class="w-full"
                                                                    size="small"
                                                                    @click="onMarkArrived(stop)"
                                                                />

                                                                <Button
                                                                    v-if="shouldShowCompletePrimary(stop)"
                                                                    label="Complete Stop"
                                                                    icon="pi pi-check"
                                                                    severity="success"
                                                                    outlined
                                                                    class="w-full"
                                                                    size="small"
                                                                    @click="onCompleteStop(stop)"
                                                                />

                                                                <Button
                                                                    v-if="canLogVisit(stop)"
                                                                    label="Log Visit"
                                                                    icon="pi pi-plus-circle"
                                                                    severity="secondary"
                                                                    outlined
                                                                    class="w-full"
                                                                    size="small"
                                                                    @click="onLogVisit(stop)"
                                                                />

                                                                <Button
                                                                    v-if="canSkipStop(stop)"
                                                                    label="Skip Stop"
                                                                    icon="pi pi-forward"
                                                                    severity="warn"
                                                                    outlined
                                                                    class="w-full"
                                                                    size="small"
                                                                    @click="openSkipDialog(stop)"
                                                                />
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
                    </div>
                </div>
            </div>
        </div>

        <!-- CHANGED: Added skip stop dialog -->
        <Dialog
            v-model:visible="showSkipDialog"
            modal
            header="Skip Stop"
            :style="{ width: '34rem', maxWidth: '95vw' }"
            :pt="{
                root: { class: 'overflow-hidden rounded-xl' },
                header: {
                    class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4 text-surface-900 dark:text-surface-0',
                },
                content: {
                    class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0',
                },
            }"
            @hide="onSkipDialogHide"
        >
            <form @submit.prevent="onSkipStopSubmit" class="space-y-5">
                <div>
                    <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                        Skip This Stop
                    </h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Provide a reason for skipping this stop so the run history stays accurate.
                    </p>
                </div>

                <div class="flex flex-col">
                    <label
                        for="skip_reason"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': skipForm.errors.skip_reason }"
                    >
                        Skip Reason
                    </label>

                    <Textarea
                        id="skip_reason"
                        v-model="skipForm.skip_reason"
                        rows="5"
                        autoResize
                        fluid
                        placeholder="Why is this stop being skipped?"
                        :invalid="!!skipForm.errors.skip_reason"
                    />

                    <Message
                        v-if="skipForm.errors.skip_reason"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2"
                    >
                        {{ skipForm.errors.skip_reason }}
                    </Message>
                </div>

                <div class="flex justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
                    <Button
                        label="Cancel"
                        type="button"
                        severity="secondary"
                        outlined
                        @click="showSkipDialog = false"
                    />

                    <Button
                        label="Skip Stop"
                        type="submit"
                        icon="pi pi-forward"
                        severity="warn"
                        :loading="skipForm.processing"
                    />
                </div>
            </form>
        </Dialog>

        <!-- CHANGED: Added cancel run dialog -->
        <Dialog
            v-model:visible="showCancelDialog"
            modal
            header="Cancel Route Run"
            :style="{ width: '34rem', maxWidth: '95vw' }"
            :pt="{
                root: { class: 'overflow-hidden rounded-xl' },
                header: {
                    class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4 text-surface-900 dark:text-surface-0',
                },
                content: {
                    class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0',
                },
            }"
            @hide="onCancelDialogHide"
        >
            <form @submit.prevent="onCancelRunSubmit" class="space-y-5">
                <div>
                    <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                        Cancel This Route Run
                    </h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Record why this route run is being cancelled.
                    </p>
                </div>

                <div class="flex flex-col">
                    <label
                        for="cancel_reason"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': cancelRunForm.errors.notes }"
                    >
                        Cancellation Reason
                    </label>

                    <Textarea
                        id="cancel_reason"
                        v-model="cancelRunForm.notes"
                        rows="5"
                        autoResize
                        fluid
                        placeholder="Why is this route run being cancelled?"
                        :invalid="!!cancelRunForm.errors.notes"
                    />

                    <Message
                        v-if="cancelRunForm.errors.notes"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2"
                    >
                        {{ cancelRunForm.errors.notes }}
                    </Message>
                </div>

                <div class="flex justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
                    <Button
                        label="Close"
                        type="button"
                        severity="secondary"
                        outlined
                        @click="showCancelDialog = false"
                    />

                    <Button
                        label="Cancel Run"
                        type="submit"
                        icon="pi pi-times-circle"
                        severity="danger"
                        :loading="cancelRunForm.processing"
                    />
                </div>
            </form>
        </Dialog>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { Card, Button, Divider, ProgressBar, Dialog, Textarea, Message } from 'primevue'

const page = usePage()

// CHANGED: Kept route run reactive to incoming Inertia partial reloads.
const routeRun = computed(() => page.props.office_route_run ?? {})

// CHANGED: Added ordered run stops helper.
const runStops = computed(() => {
    return [...(routeRun.value.stops ?? [])].sort((a, b) => {
        const aOrder = a.actual_stop_order ?? a.planned_stop_order ?? 0
        const bOrder = b.actual_stop_order ?? b.planned_stop_order ?? 0
        return aOrder - bOrder
    })
})

// CHANGED: Added current stop helper.
const currentStop = computed(() => {
    return runStops.value.find((stop) =>
        ['pending', 'in_progress', 'arrived'].includes(stop.status)
    ) ?? null
})

// CHANGED: Added summary counts.
const completedCount = computed(() => {
    return runStops.value.filter((stop) => stop.status === 'completed').length
})

const skippedCount = computed(() => {
    return runStops.value.filter((stop) => stop.status === 'skipped').length
})

const remainingCount = computed(() => {
    return runStops.value.filter((stop) =>
        ['pending', 'in_progress', 'arrived'].includes(stop.status)
    ).length
})

const progressPercent = computed(() => {
    if (!runStops.value.length) return 0
    return Math.round((completedCount.value / runStops.value.length) * 100)
})

const canCompleteRun = computed(() => {
    return runStops.value.length > 0 && remainingCount.value === 0
})

// CHANGED: Added formatted run date helper.
const formattedRunDate = computed(() => {
    if (!routeRun.value.run_date) return 'Not scheduled'
    return formatDateTime(routeRun.value.run_date, false)
})

// CHANGED: Added dialog state.
const showSkipDialog = ref(false)
const selectedSkipStop = ref(null)
const showCancelDialog = ref(false)

// CHANGED: Added complete run form.
const completeRunForm = useForm({
    status: 'completed',
    completed_at: null,
    completed_stop_count: 0,
    skipped_stop_count: 0,
})

// CHANGED: Added cancel run form.
const cancelRunForm = useForm({
    status: 'cancelled',
    cancelled_at: null,
    notes: '',
})

// CHANGED: Added skip stop form.
const skipForm = useForm({
    status: 'skipped',
    skipped_at: null,
    skip_reason: '',
})

watch(
    () => routeRun.value,
    (run) => {
        completeRunForm.defaults({
            status: 'completed',
            completed_at: null,
            completed_stop_count: run.completed_stop_count ?? 0,
            skipped_stop_count: run.skipped_stop_count ?? 0,
        })

        cancelRunForm.defaults({
            status: 'cancelled',
            cancelled_at: null,
            notes: '',
        })

        skipForm.defaults({
            status: 'skipped',
            skipped_at: null,
            skip_reason: '',
        })
    },
    { immediate: true }
)

// CHANGED: Added helper to identify current stop.
const isCurrentStop = (stop) => currentStop.value?.id === stop.id

// CHANGED: Added helper to show more obvious next-step text.
const getNextActionLabel = (stop) => {
    if (!stop) return ''

    if (stop.status === 'pending' || stop.status === 'in_progress') {
        return 'Next: Arrive'
    }

    if (stop.status === 'arrived') {
        return 'Next: Complete or Log Visit'
    }

    if (stop.status === 'completed') {
        return 'Done'
    }

    if (stop.status === 'skipped') {
        return 'Skipped'
    }

    if (stop.status === 'cancelled') {
        return 'Cancelled'
    }

    return ''
}

// CHANGED: Added helpers to simplify which primary workflow button should appear.
const shouldShowArrivePrimary = (stop) => ['pending', 'in_progress'].includes(stop?.status)
const shouldShowCompletePrimary = (stop) => ['arrived'].includes(stop?.status)

// CHANGED: Added card class helper by stop status.
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

// CHANGED: Added stop status pill styling.
const getStopStatusPillClass = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300'
        case 'arrived':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
        case 'in_progress':
            return 'bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-300'
        case 'skipped':
            return 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300'
        case 'cancelled':
            return 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300'
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-200'
    }
}

// CHANGED: Added field action availability helpers.
const canMarkArrived = (stop) => ['pending', 'in_progress'].includes(stop.status)
const canLogVisit = (stop) => ['arrived', 'in_progress'].includes(stop.status) && !stop.office_visit_id
const canSkipStop = (stop) => ['pending', 'in_progress', 'arrived'].includes(stop.status)
const canCompleteStop = (stop) => ['arrived', 'in_progress'].includes(stop.status)

// CHANGED: Added safe phone normalization for tel links.
const normalizePhoneNumber = (phone) => {
    if (!phone) return ''
    return String(phone).replace(/[^\d+]/g, '')
}

// CHANGED: Added click-to-call helper.
const getPhoneHref = (office) => {
    const rawPhone = office?.office_phone || office?.phone || ''
    const normalizedPhone = normalizePhoneNumber(rawPhone)

    return normalizedPhone ? `tel:${normalizedPhone}` : null
}

// CHANGED: Added click-to-navigate helper using coordinates first, address fallback second.
const getNavigationUrl = (stop) => {
    const office = stop?.office || stop

    const latitude = office?.latitude
    const longitude = office?.longitude

    if (
        latitude !== null &&
        latitude !== undefined &&
        longitude !== null &&
        longitude !== undefined &&
        latitude !== '' &&
        longitude !== ''
    ) {
        return `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`
    }

    const address = formatOfficeAddress(office)

    if (!address || address === 'No address available') {
        return null
    }

    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`
}

// CHANGED: Added mark-arrived action with partial reload.
const onMarkArrived = (stop) => {
    router.put(
        route('office-run-stop.update', stop.id),
        {
            status: 'arrived',
            arrived_at: new Date().toISOString(),
        },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['office_route_run', 'flash'],
        }
    )
}

// CHANGED: Added complete-stop action with partial reload.
const onCompleteStop = (stop) => {
    router.put(
        route('office-run-stop.update', stop.id),
        {
            status: 'completed',
            completed_at: new Date().toISOString(),
            duration_minutes: calculateDurationMinutes(stop.arrived_at),
        },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['office_route_run', 'flash'],
        }
    )
}

// CHANGED: Added visit logging navigation.
const onLogVisit = (stop) => {
    router.visit(
        route('office-visit.create', {
            office_id: stop.office_id,
            office_route_run_stop_id: stop.id,
            office_route_run_id: routeRun.value.id,
        })
    )
}

// CHANGED: Added skip dialog open handler.
const openSkipDialog = (stop) => {
    selectedSkipStop.value = stop
    skipForm.reset()
    skipForm.clearErrors()
    skipForm.status = 'skipped'
    skipForm.skipped_at = new Date().toISOString()
    skipForm.skip_reason = ''
    showSkipDialog.value = true
}

// CHANGED: Added skip dialog cleanup.
const onSkipDialogHide = () => {
    selectedSkipStop.value = null
    skipForm.reset()
    skipForm.clearErrors()
}

// CHANGED: Added skip stop submit handler.
const onSkipStopSubmit = () => {
    if (!selectedSkipStop.value) return

    skipForm.put(route('office-run-stop.update', selectedSkipStop.value.id), {
        preserveScroll: true,
        preserveState: true,
        only: ['office_route_run', 'flash'],
        onSuccess: () => {
            showSkipDialog.value = false
            onSkipDialogHide()
        },
    })
}

// CHANGED: Added cancel dialog cleanup.
const onCancelDialogHide = () => {
    cancelRunForm.reset()
    cancelRunForm.clearErrors()
    cancelRunForm.status = 'cancelled'
    cancelRunForm.cancelled_at = null
}

// CHANGED: Added complete run handler.
const onCompleteRun = () => {
    if (!routeRun.value.id) return

    completeRunForm.completed_at = new Date().toISOString()
    completeRunForm.completed_stop_count = completedCount.value
    completeRunForm.skipped_stop_count = skippedCount.value

    completeRunForm.put(route('office-run.update', routeRun.value.id), {
        preserveScroll: true,
        preserveState: true,
        only: ['office_route_run', 'flash'],
    })
}

// CHANGED: Added cancel run handler.
const onCancelRunSubmit = () => {
    if (!routeRun.value.id) return

    cancelRunForm.cancelled_at = new Date().toISOString()

    cancelRunForm.put(route('office-run.update', routeRun.value.id), {
        preserveScroll: true,
        preserveState: true,
        only: ['office_route_run', 'flash'],
        onSuccess: () => {
            showCancelDialog.value = false
            onCancelDialogHide()
        },
    })
}

// CHANGED: Added label formatter.
const formatLabel = (value) => {
    if (!value) return 'N/A'

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}

// CHANGED: Added address formatter.
const formatOfficeAddress = (office) => {
    if (!office) return 'No address available'

    const unit = office.unit_number ? ` #${office.unit_number}` : ''

    const streetLine = `${office.street_address || ''}${unit}`.trim()
    const cityStateZip = `${office.city || ''}, ${office.state || ''} ${office.zip_code || ''}`.trim()

    const address = [streetLine, cityStateZip].filter(Boolean).join(', ').trim()

    return address || 'No address available'
}

// CHANGED: Added datetime formatter.
const formatDateTime = (value, includeTime = true) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return value

    if (!includeTime) {
        return date.toLocaleDateString()
    }

    return date.toLocaleString()
}

// CHANGED: Added duration calculator for completed stops.
const calculateDurationMinutes = (arrivedAt) => {
    if (!arrivedAt) return null

    const arrived = new Date(arrivedAt)
    const now = new Date()

    if (Number.isNaN(arrived.getTime())) return null

    return Math.max(1, Math.round((now.getTime() - arrived.getTime()) / 60000))
}
</script>
