<template>
    <!-- CHANGED: Added page title for the route edit screen -->
    <Head :title="officeRouteForm.name ? `Edit Route - ${officeRouteForm.name}` : 'Edit Route'" />

    <!-- CHANGED: Wrapped the page in the shared layout -->
    <Layout>
        <!-- CHANGED: Added full-page responsive shell so this page matches the rest of the portal -->
        <div class="flex h-full min-h-0 flex-col px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: Added page header with back navigation and save action -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div class="min-w-0">
                        <!-- CHANGED: Added back button to the route show page -->
                        <div class="mb-2">
                            <Link :href="route('office-route.show', officeRoute.id)">
                                <Button
                                    label="Back to Route"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <!-- CHANGED: Added page title and helper text -->
                        <div>
                            <h1
                                class="truncate text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0"
                            >
                                Edit Route
                            </h1>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Update the route details, assigned user, planning settings, and ordered office stops.
                            </p>
                        </div>
                    </div>

                    <!-- CHANGED: Added top-right action buttons -->
                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                        <Button
                            label="Add Stop"
                            icon="pi pi-plus-circle"
                            severity="secondary"
                            outlined
                            type="button"
                            @click="showAddStopDialog = true"
                        />

                        <Button
                            label="Save Route"
                            icon="pi pi-save"
                            :loading="officeRouteForm.processing"
                            @click="submit"
                        />
                    </div>
                </div>
            </div>

            <!-- CHANGED: Added two-column edit layout -->
            <div class="min-h-0 flex-1">
                <div class="grid grid-cols-12 items-start gap-5">
                    <!-- Left Rail -->
                    <div class="col-span-12 self-start xl:col-span-4 2xl:col-span-4">
                        <div class="space-y-5 xl:sticky xl:top-6">
                            <!-- CHANGED: Added editable route details card -->
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
                                                Update the basic route settings and planning information.
                                            </p>
                                        </div>

                                        <!-- CHANGED: Added editable route fields -->
                                        <div class="space-y-5">
                                            <div class="flex flex-col">
                                                <label
                                                    for="route-name"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': officeRouteForm.errors.name }"
                                                >
                                                    Route Name
                                                </label>

                                                <InputText
                                                    id="route-name"
                                                    v-model="officeRouteForm.name"
                                                    fluid
                                                    placeholder="Enter route name..."
                                                    :invalid="!!officeRouteForm.errors.name"
                                                />

                                                <Message
                                                    v-if="officeRouteForm.errors.name"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ officeRouteForm.errors.name }}
                                                </Message>
                                            </div>

                                            <div class="flex flex-col">
                                                <label
                                                    for="assigned-to"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': officeRouteForm.errors.assigned_to }"
                                                >
                                                    Assigned To
                                                </label>

                                                <Select
                                                    id="assigned-to"
                                                    v-model="officeRouteForm.assigned_to"
                                                    :options="users"
                                                    optionLabel="formatted_full_name"
                                                    optionValue="id"
                                                    placeholder="Select assigned user..."
                                                    fluid
                                                    showClear
                                                    :invalid="!!officeRouteForm.errors.assigned_to"
                                                />

                                                <Message
                                                    v-if="officeRouteForm.errors.assigned_to"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ officeRouteForm.errors.assigned_to }}
                                                </Message>
                                            </div>

                                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                                <div class="flex flex-col">
                                                    <label
                                                        for="route-date"
                                                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                        :class="{ 'text-red-500': officeRouteForm.errors.route_date }"
                                                    >
                                                        Route Date
                                                    </label>

                                                    <DatePicker
                                                        id="route-date"
                                                        v-model="officeRouteForm.route_date"
                                                        dateFormat="mm/dd/yy"
                                                        placeholder="Select a date..."
                                                        fluid
                                                        :invalid="!!officeRouteForm.errors.route_date"
                                                    />

                                                    <Message
                                                        v-if="officeRouteForm.errors.route_date"
                                                        severity="error"
                                                        variant="simple"
                                                        size="small"
                                                        class="mt-2"
                                                    >
                                                        {{ officeRouteForm.errors.route_date }}
                                                    </Message>
                                                </div>

                                                <div class="flex flex-col">
                                                    <label
                                                        for="route-status"
                                                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                        :class="{ 'text-red-500': officeRouteForm.errors.status }"
                                                    >
                                                        Status
                                                    </label>

                                                    <Select
                                                        id="route-status"
                                                        v-model="officeRouteForm.status"
                                                        :options="statusOptions"
                                                        optionLabel="label"
                                                        optionValue="value"
                                                        placeholder="Select status..."
                                                        fluid
                                                        :invalid="!!officeRouteForm.errors.status"
                                                    />

                                                    <Message
                                                        v-if="officeRouteForm.errors.status"
                                                        severity="error"
                                                        variant="simple"
                                                        size="small"
                                                        class="mt-2"
                                                    >
                                                        {{ officeRouteForm.errors.status }}
                                                    </Message>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                                <div class="flex flex-col">
                                                    <label
                                                        for="source-type"
                                                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                        :class="{ 'text-red-500': officeRouteForm.errors.source_type }"
                                                    >
                                                        Source Type
                                                    </label>

                                                    <Select
                                                        id="source-type"
                                                        v-model="officeRouteForm.source_type"
                                                        :options="sourceTypeOptions"
                                                        optionLabel="label"
                                                        optionValue="value"
                                                        placeholder="Select source type..."
                                                        fluid
                                                        showClear
                                                        :invalid="!!officeRouteForm.errors.source_type"
                                                    />

                                                    <Message
                                                        v-if="officeRouteForm.errors.source_type"
                                                        severity="error"
                                                        variant="simple"
                                                        size="small"
                                                        class="mt-2"
                                                    >
                                                        {{ officeRouteForm.errors.source_type }}
                                                    </Message>
                                                </div>

                                                <div class="flex flex-col">
                                                    <label
                                                        for="radius-miles"
                                                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                        :class="{ 'text-red-500': officeRouteForm.errors.radius_miles }"
                                                    >
                                                        Radius (miles)
                                                    </label>

                                                    <InputNumber
                                                        id="radius-miles"
                                                        v-model="officeRouteForm.radius_miles"
                                                        :min="0"
                                                        :step="1"
                                                        fluid
                                                        :invalid="!!officeRouteForm.errors.radius_miles"
                                                    />

                                                    <Message
                                                        v-if="officeRouteForm.errors.radius_miles"
                                                        severity="error"
                                                        variant="simple"
                                                        size="small"
                                                        class="mt-2"
                                                    >
                                                        {{ officeRouteForm.errors.radius_miles }}
                                                    </Message>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                                <div class="flex flex-col">
                                                    <label
                                                        for="center-latitude"
                                                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                        :class="{ 'text-red-500': officeRouteForm.errors.center_latitude }"
                                                    >
                                                        Center Latitude
                                                    </label>

                                                    <InputNumber
                                                        id="center-latitude"
                                                        v-model="officeRouteForm.center_latitude"
                                                        :minFractionDigits="0"
                                                        :maxFractionDigits="7"
                                                        fluid
                                                        :invalid="!!officeRouteForm.errors.center_latitude"
                                                    />

                                                    <Message
                                                        v-if="officeRouteForm.errors.center_latitude"
                                                        severity="error"
                                                        variant="simple"
                                                        size="small"
                                                        class="mt-2"
                                                    >
                                                        {{ officeRouteForm.errors.center_latitude }}
                                                    </Message>
                                                </div>

                                                <div class="flex flex-col">
                                                    <label
                                                        for="center-longitude"
                                                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                        :class="{ 'text-red-500': officeRouteForm.errors.center_longitude }"
                                                    >
                                                        Center Longitude
                                                    </label>

                                                    <InputNumber
                                                        id="center-longitude"
                                                        v-model="officeRouteForm.center_longitude"
                                                        :minFractionDigits="0"
                                                        :maxFractionDigits="7"
                                                        fluid
                                                        :invalid="!!officeRouteForm.errors.center_longitude"
                                                    />

                                                    <Message
                                                        v-if="officeRouteForm.errors.center_longitude"
                                                        severity="error"
                                                        variant="simple"
                                                        size="small"
                                                        class="mt-2"
                                                    >
                                                        {{ officeRouteForm.errors.center_longitude }}
                                                    </Message>
                                                </div>
                                            </div>

                                            <div class="flex flex-col">
                                                <label
                                                    for="route-description"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': officeRouteForm.errors.description }"
                                                >
                                                    Description
                                                </label>

                                                <Textarea
                                                    id="route-description"
                                                    v-model="officeRouteForm.description"
                                                    rows="5"
                                                    autoResize
                                                    fluid
                                                    placeholder="Describe this route..."
                                                    :invalid="!!officeRouteForm.errors.description"
                                                />

                                                <Message
                                                    v-if="officeRouteForm.errors.description"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ officeRouteForm.errors.description }}
                                                </Message>
                                            </div>

                                            <div class="flex flex-col">
                                                <label
                                                    for="route-notes"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': officeRouteForm.errors.notes }"
                                                >
                                                    Notes
                                                </label>

                                                <Textarea
                                                    id="route-notes"
                                                    v-model="officeRouteForm.notes"
                                                    rows="5"
                                                    autoResize
                                                    fluid
                                                    placeholder="Add route notes..."
                                                    :invalid="!!officeRouteForm.errors.notes"
                                                />

                                                <Message
                                                    v-if="officeRouteForm.errors.notes"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ officeRouteForm.errors.notes }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>

                            <!-- CHANGED: Added summary card for quick route stats -->
                            <Card
                                class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                            >
                                <template #content>
                                    <div class="space-y-4">
                                        <div>
                                            <h3
                                                class="text-base font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                Summary
                                            </h3>
                                            <p
                                                class="text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                Quick overview of this route while editing.
                                            </p>
                                        </div>

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
                                                    {{ officeRouteForm.stops.length }}
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
                                                    {{ formatLabel(officeRouteForm.status) || 'Draft' }}
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
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-span-12 min-w-0 xl:col-span-8 2xl:col-span-8">
                        <div class="space-y-5">
                            <!-- CHANGED: Added stop management card -->
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
                                                    Reorder stops, edit stop details, and remove offices from this route.
                                                </p>
                                            </div>

                                            <Button
                                                label="Add Stop"
                                                icon="pi pi-plus"
                                                severity="secondary"
                                                outlined
                                                @click="showAddStopDialog = true"
                                            />
                                        </div>

                                        <!-- CHANGED: Added empty state -->
                                        <div
                                            v-if="!officeRouteForm.stops.length"
                                            class="flex min-h-52 flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <i
                                                class="pi pi-map-marker mb-3 text-3xl text-surface-400 dark:text-surface-500"
                                            ></i>
                                            <p
                                                class="font-medium text-surface-700 dark:text-surface-200"
                                            >
                                                No stops added yet.
                                            </p>
                                            <p
                                                class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                Add offices to this route to build the stop order.
                                            </p>

                                            <div class="mt-4">
                                                <Button
                                                    label="Add First Stop"
                                                    icon="pi pi-plus"
                                                    @click="showAddStopDialog = true"
                                                />
                                            </div>
                                        </div>

                                        <!-- CHANGED: Added editable stop cards -->
                                        <div v-else class="space-y-4">
                                            <div
                                                v-for="(stop, index) in officeRouteForm.stops"
                                                :key="stop.local_key"
                                                class="rounded-2xl border border-surface-200 bg-surface-50 p-5 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <div class="space-y-5">
                                                    <!-- CHANGED: Added stop header with move/delete controls -->
                                                    <div
                                                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                                                    >
                                                        <div class="flex min-w-0 items-center gap-3">
                                                            <span
                                                                class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary"
                                                            >
                                                                Stop {{ index + 1 }}
                                                            </span>

                                                            <span
                                                                class="text-sm text-surface-500 dark:text-surface-400"
                                                            >
                                                                {{ stop.office?.parent_company || stop.parent_company || 'Unknown Office' }}
                                                            </span>
                                                        </div>

                                                        <div class="flex shrink-0 items-center gap-2">
                                                            <Button
                                                                icon="pi pi-arrow-up"
                                                                severity="secondary"
                                                                text
                                                                :disabled="index === 0"
                                                                @click="moveStopUp(index)"
                                                            />

                                                            <Button
                                                                icon="pi pi-arrow-down"
                                                                severity="secondary"
                                                                text
                                                                :disabled="index === officeRouteForm.stops.length - 1"
                                                                @click="moveStopDown(index)"
                                                            />

                                                            <Button
                                                                icon="pi pi-trash"
                                                                severity="danger"
                                                                text
                                                                @click="removeStop(index)"
                                                            />
                                                        </div>
                                                    </div>

                                                    <!-- CHANGED: Added editable stop fields -->
                                                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                                        <div class="flex flex-col">
                                                            <label
                                                                :for="`stop-order-${stop.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                            >
                                                                Stop Order
                                                            </label>

                                                            <InputNumber
                                                                :id="`stop-order-${stop.local_key}`"
                                                                v-model="stop.stop_order"
                                                                :min="1"
                                                                :step="1"
                                                                fluid
                                                            />
                                                        </div>

                                                        <div class="flex flex-col">
                                                            <label
                                                                :for="`stop-status-${stop.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                            >
                                                                Status
                                                            </label>

                                                            <Select
                                                                :id="`stop-status-${stop.local_key}`"
                                                                v-model="stop.status"
                                                                :options="stopStatusOptions"
                                                                optionLabel="label"
                                                                optionValue="value"
                                                                placeholder="Select status..."
                                                                fluid
                                                            />
                                                        </div>

                                                        <div class="flex flex-col">
                                                            <label
                                                                :for="`stop-scheduled-arrival-${stop.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                            >
                                                                Scheduled Arrival
                                                            </label>

                                                            <DatePicker
                                                                :id="`stop-scheduled-arrival-${stop.local_key}`"
                                                                v-model="stop.scheduled_arrival"
                                                                showTime
                                                                hourFormat="12"
                                                                fluid
                                                            />
                                                        </div>

                                                        <div class="flex flex-col">
                                                            <label
                                                                :for="`stop-office-name-${stop.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                            >
                                                                Office
                                                            </label>

                                                            <InputText
                                                                :id="`stop-office-name-${stop.local_key}`"
                                                                :modelValue="getStopOfficeLabel(stop)"
                                                                fluid
                                                                disabled
                                                            />
                                                        </div>

                                                        <div
                                                            class="flex flex-col lg:col-span-2"
                                                        >
                                                            <label
                                                                :for="`stop-notes-${stop.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                            >
                                                                Stop Notes
                                                            </label>

                                                            <Textarea
                                                                :id="`stop-notes-${stop.local_key}`"
                                                                v-model="stop.notes"
                                                                rows="4"
                                                                autoResize
                                                                fluid
                                                                placeholder="Add notes for this stop..."
                                                            />
                                                        </div>
                                                    </div>

                                                    <!-- CHANGED: Added office reference panel -->
                                                    <div
                                                        class="rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900"
                                                    >
                                                        <div class="space-y-2 text-sm">
                                                            <p
                                                                class="font-semibold text-surface-900 dark:text-surface-100"
                                                            >
                                                                {{ stop.office?.parent_company || stop.parent_company || 'Unknown Office' }}
                                                            </p>

                                                            <p
                                                                v-if="stop.office?.office_name || stop.office_name"
                                                                class="text-surface-500 dark:text-surface-400"
                                                            >
                                                                {{ stop.office?.office_name || stop.office_name }}
                                                            </p>

                                                            <p
                                                                class="text-surface-700 dark:text-surface-300"
                                                            >
                                                                {{ formatOfficeAddress(stop.office || stop) }}
                                                            </p>

                                                            <p
                                                                v-if="stop.office?.office_phone || stop.office_phone"
                                                                class="text-surface-700 dark:text-surface-300"
                                                            >
                                                                <span class="font-medium text-surface-900 dark:text-surface-100">
                                                                    Phone:
                                                                </span>
                                                                {{ stop.office?.office_phone || stop.office_phone }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CHANGED: Added footer actions -->
                                        <div
                                            class="flex justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800"
                                        >
                                            <Link :href="route('office-route.show', officeRoute.id)">
                                                <Button
                                                    label="Cancel"
                                                    severity="secondary"
                                                    outlined
                                                    type="button"
                                                />
                                            </Link>

                                            <Button
                                                label="Save Route"
                                                icon="pi pi-save"
                                                :loading="officeRouteForm.processing"
                                                @click="submit"
                                            />
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHANGED: Added dialog for adding an office to the route -->
        <Dialog
            v-model:visible="showAddStopDialog"
            modal
            header="Add Route Stop"
            :style="{ width: '42rem', maxWidth: '95vw' }"
            :pt="{
                root: { class: 'overflow-hidden rounded-2xl' },
                header: {
                    class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4 text-surface-900 dark:text-surface-0',
                },
                content: {
                    class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0',
                },
            }"
            @hide="onAddStopDialogHide"
        >
            <div class="space-y-5">
                <div>
                    <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                        Add Office to Route
                    </h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Search for an office and add it as the next stop in this route.
                    </p>
                </div>

                <div class="flex flex-col">
                    <label
                        for="office-search"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        Office
                    </label>

                    <AutoComplete
                        id="office-search"
                        v-model="selectedOffice"
                        :suggestions="filteredOffices"
                        dropdown
                        fluid
                        :optionLabel="getOfficeAutocompleteLabel"
                        placeholder="Search by company, office name, address, city, or ZIP..."
                        @complete="filterOffices"
                    />
                </div>

                <div
                    v-if="selectedOffice && typeof selectedOffice === 'object'"
                    class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                >
                    <p class="font-semibold text-surface-900 dark:text-surface-100">
                        {{ selectedOffice.parent_company || 'Unknown Office' }}
                    </p>

                    <p
                        v-if="selectedOffice.office_name"
                        class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                    >
                        {{ selectedOffice.office_name }}
                    </p>

                    <p class="mt-2 text-sm text-surface-700 dark:text-surface-300">
                        {{ formatOfficeAddress(selectedOffice) }}
                    </p>
                </div>

                <div
                    class="flex justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800"
                >
                    <Button
                        label="Cancel"
                        type="button"
                        severity="secondary"
                        outlined
                        @click="showAddStopDialog = false"
                    />

                    <Button
                        label="Add Stop"
                        icon="pi pi-plus"
                        :disabled="!selectedOffice || typeof selectedOffice !== 'object'"
                        @click="addSelectedOfficeAsStop"
                    />
                </div>
            </div>
        </Dialog>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import {
    AutoComplete,
    Button,
    Card,
    DatePicker,
    Dialog,
    Divider,
    InputNumber,
    InputText,
    Message,
    Select,
    Textarea,
} from 'primevue'

const page = usePage()

// CHANGED: Added safe fallbacks for incoming props needed on the edit page.
const officeRoute = page.props.office_route ?? {}
const users = page.props.users ?? []
const offices = page.props.offices ?? []

// CHANGED: Added stop normalizer so each stop has a stable local key for editing.
const buildStop = (stop = {}, index = 0) => {
    return {
        id: stop.id ?? null,
        local_key: stop.id ?? `new-stop-${Date.now()}-${index}-${Math.random().toString(36).slice(2, 8)}`,
        office_id: stop.office_id ?? stop.office?.id ?? null,
        stop_order: stop.stop_order ?? index + 1,
        status: stop.status ?? 'pending',
        required: Boolean(stop.required ?? false),
        scheduled_arrival: stop.scheduled_arrival ?? null,
        actual_arrival: stop.actual_arrival ?? null,
        actual_departure: stop.actual_departure ?? null,
        notes: stop.notes ?? '',
        parent_company: stop.parent_company ?? stop.office?.parent_company ?? null,
        office_name: stop.office_name ?? stop.office?.office_name ?? null,
        street_address: stop.street_address ?? stop.office?.street_address ?? null,
        unit_number: stop.unit_number ?? stop.office?.unit_number ?? null,
        city: stop.city ?? stop.office?.city ?? null,
        state: stop.state ?? stop.office?.state ?? null,
        zip_code: stop.zip_code ?? stop.office?.zip_code ?? null,
        office_phone: stop.office_phone ?? stop.office?.office_phone ?? null,
        office: stop.office ?? null,
    }
}

// CHANGED: Added form state for editing the route and its stops.
const officeRouteForm = useForm({
    name: officeRoute.name ?? '',
    assigned_to: officeRoute.assigned_to ?? officeRoute.assignedTo?.id ?? null,
    route_date: officeRoute.route_date ?? null,
    source_type: officeRoute.source_type ?? '',
    radius_miles: officeRoute.radius_miles ?? null,
    center_latitude: officeRoute.center_latitude ?? null,
    center_longitude: officeRoute.center_longitude ?? null,
    status: officeRoute.status ?? 'draft',
    description: officeRoute.description ?? '',
    notes: officeRoute.notes ?? '',
    stops: (officeRoute.stops ?? [])
        .slice()
        .sort((a, b) => (a.stop_order ?? 0) - (b.stop_order ?? 0))
        .map((stop, index) => buildStop(stop, index)),
})

// CHANGED: Added options for route-level status selection.
const statusOptions = [
    { label: 'Draft', value: 'draft' },
    { label: 'Ready', value: 'ready' },
    { label: 'Active', value: 'active' },
    { label: 'Archived', value: 'archived' },
]

// CHANGED: Added options for source type selection.
const sourceTypeOptions = [
    { label: 'Manual', value: 'manual' },
    { label: 'Radius Search', value: 'radius_search' },
    { label: 'Imported', value: 'imported' },
]

// CHANGED: Added options for stop status selection.
const stopStatusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'Ready', value: 'ready' },
    { label: 'Skipped', value: 'skipped' },
    { label: 'Cancelled', value: 'cancelled' },
]

// CHANGED: Added computed display helpers for the summary cards.
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

// CHANGED: Added dialog and autocomplete state for adding stops.
const showAddStopDialog = ref(false)
const selectedOffice = ref(null)
const filteredOffices = ref([])

// CHANGED: Added autocomplete search helper for offices.
const filterOffices = (event) => {
    const query = (event.query || '').toLowerCase().trim()

    filteredOffices.value = offices.filter((officeItem) => {
        const haystack = [
            officeItem.parent_company,
            officeItem.office_name,
            officeItem.street_address,
            officeItem.city,
            officeItem.zip_code,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase()

        return haystack.includes(query)
    })
}

// CHANGED: Added autocomplete label helper.
const getOfficeAutocompleteLabel = (officeItem) => {
    const unitPart = officeItem.unit_number ? ` #${officeItem.unit_number}` : ''
    return officeItem.office_name
        ? `${officeItem.parent_company} - ${officeItem.office_name} (${officeItem.street_address || ''}${unitPart})`
        : `${officeItem.parent_company} (${officeItem.street_address || ''}${unitPart})`
}

// CHANGED: Added helper to show the selected office name on each stop card.
const getStopOfficeLabel = (stop) => {
    const company = stop.office?.parent_company || stop.parent_company || 'Unknown Office'
    const officeName = stop.office?.office_name || stop.office_name

    return officeName ? `${company} - ${officeName}` : company
}

// CHANGED: Added method to add the selected office as a new route stop.
const addSelectedOfficeAsStop = () => {
    if (!selectedOffice.value || typeof selectedOffice.value !== 'object') return

    officeRouteForm.stops.push(
        buildStop(
            {
                office_id: selectedOffice.value.id,
                stop_order: officeRouteForm.stops.length + 1,
                status: 'pending',
                required: false,
                scheduled_arrival: null,
                notes: '',
                parent_company: selectedOffice.value.parent_company,
                office_name: selectedOffice.value.office_name,
                street_address: selectedOffice.value.street_address,
                unit_number: selectedOffice.value.unit_number,
                city: selectedOffice.value.city,
                state: selectedOffice.value.state,
                zip_code: selectedOffice.value.zip_code,
                office_phone: selectedOffice.value.office_phone,
                office: selectedOffice.value,
            },
            officeRouteForm.stops.length
        )
    )

    normalizeStopOrder()
    onAddStopDialogHide()
    showAddStopDialog.value = false
}

// CHANGED: Added dialog cleanup helper.
const onAddStopDialogHide = () => {
    selectedOffice.value = null
    filteredOffices.value = []
}

// CHANGED: Added helper to remove a stop from the route.
const removeStop = (index) => {
    officeRouteForm.stops.splice(index, 1)
    normalizeStopOrder()
}

// CHANGED: Added helper to move a stop upward.
const moveStopUp = (index) => {
    if (index <= 0) return

    const stop = officeRouteForm.stops[index]
    officeRouteForm.stops.splice(index, 1)
    officeRouteForm.stops.splice(index - 1, 0, stop)

    normalizeStopOrder()
}

// CHANGED: Added helper to move a stop downward.
const moveStopDown = (index) => {
    if (index >= officeRouteForm.stops.length - 1) return

    const stop = officeRouteForm.stops[index]
    officeRouteForm.stops.splice(index, 1)
    officeRouteForm.stops.splice(index + 1, 0, stop)

    normalizeStopOrder()
}

// CHANGED: Added helper to keep stop_order aligned with the visible order.
const normalizeStopOrder = () => {
    officeRouteForm.stops = officeRouteForm.stops.map((stop, index) => ({
        ...stop,
        stop_order: index + 1,
    }))
}

// CHANGED: Added shared label formatter.
const formatLabel = (value) => {
    if (!value) return ''

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}

// CHANGED: Added shared address formatter for office display.
const formatOfficeAddress = (officeItem) => {
    if (!officeItem) return 'No address available'

    const unit = officeItem.unit_number ? ` #${officeItem.unit_number}` : ''

    return `${officeItem.street_address || ''}${unit}, ${officeItem.city || ''}, ${officeItem.state || ''} ${officeItem.zip_code || ''}`.trim()
}

// CHANGED: Added shared date formatter for display helpers.
const formatDateTime = (value, includeTime = true) => {
    if (!value) return '—'

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) return value

    if (!includeTime) {
        return date.toLocaleDateString()
    }

    return date.toLocaleString()
}

// CHANGED: Added submit handler for updating the route.
const submit = () => {
    normalizeStopOrder()

    officeRouteForm.put(route('office-route.update', officeRoute.id), {
        preserveScroll: true,
    })
}
</script>
