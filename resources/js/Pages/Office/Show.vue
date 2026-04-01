<template>
    <Head :title="office.parent_company" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col">
            <div class="mb-6 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-2">
                            <Link :href="route('office.index')">
                                <Button
                                    label="Back to Offices"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <div>
                            <h1
                                class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0"
                            >
                                {{ office.parent_company }}
                            </h1>
                            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                                {{ fullAddress }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <Button
                            label="Agents"
                            icon="pi pi-users"
                            severity="secondary"
                            outlined
                            @click="goToTab(1)"
                        />
                        <Button
                            label="Visits"
                            icon="pi pi-map-marker"
                            severity="secondary"
                            outlined
                            @click="goToTab(2)"
                        />
                        <Button label="Notes" icon="pi pi-file-edit" @click="goToTab(4)" />
                        <Button label="Delete Office" icon="pi pi-trash" @click="onDeleteOffice" />
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div class="grid h-full min-h-0 grid-cols-12 gap-5 items-start">
                    <div class="col-span-12 xl:col-span-4">
                        <div class="space-y-5 xl:sticky xl:top-6">
                            <Card
                                class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                            >
                                <template #content>
                                    <div class="flex flex-col gap-6">
                                        <div
                                            class="flex flex-col items-center gap-5 lg:flex-row lg:items-start"
                                        >
                                            <div class="flex flex-col items-center gap-4">
                                                <div
                                                    class="group relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-2xl border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-800"
                                                >
                                                    <Image
                                                        v-if="office.image_url"
                                                        :src="office.image_url"
                                                        imageClass="h-32 w-32 object-cover cursor-zoom-in"
                                                        preview
                                                    />

                                                    <div
                                                        v-if="office.image_url"
                                                        class="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition-all duration-200 group-hover:bg-black/20 group-hover:opacity-100"
                                                    ></div>

                                                    <div
                                                        v-else
                                                        class="flex h-full w-full items-center justify-center text-surface-400 dark:text-surface-500"
                                                    >
                                                        <i class="pi pi-building text-3xl" />
                                                    </div>
                                                </div>

                                                <Button
                                                    label="Upload Image"
                                                    icon="pi pi-image"
                                                    outlined
                                                    class="w-full"
                                                    @click="showImageDialog = true"
                                                />
                                            </div>

                                            <div class="min-w-0 flex-1 text-center lg:text-left">
                                                <h2
                                                    class="text-2xl font-bold leading-tight text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ office.parent_company }}
                                                </h2>
                                                <p>{{ office.office_name }}</p>

                                                <div class="mt-4 space-y-3 text-sm">
                                                    <div
                                                        class="flex items-start justify-center gap-3 lg:justify-start"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                        >
                                                            <i class="pi pi-map-marker"></i>
                                                        </div>

                                                        <p
                                                            class="leading-5 text-surface-700 dark:text-surface-300"
                                                        >
                                                            {{ fullAddress }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        v-if="office.office_phone"
                                                        class="flex items-start justify-center gap-3 lg:justify-start"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                        >
                                                            <i class="pi pi-phone"></i>
                                                        </div>

                                                        <p
                                                            class="text-surface-700 dark:text-surface-300"
                                                        >
                                                            {{ office.office_phone }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        v-if="office.county"
                                                        class="flex items-start justify-center gap-3 lg:justify-start"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                        >
                                                            <i class="pi pi-globe"></i>
                                                        </div>

                                                        <p
                                                            class="text-surface-700 dark:text-surface-300"
                                                        >
                                                            {{ office.county }} County
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <Divider />

                                        <div>
                                            <h3
                                                class="mb-3 text-base font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                At a Glance
                                            </h3>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div
                                                    class="flex items-center gap-3 rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                                >
                                                    <i class="pi pi-users text-green-500"></i>
                                                    <div>
                                                        <p
                                                            class="text-xs text-surface-500 dark:text-surface-400"
                                                        >
                                                            Total Agents
                                                        </p>
                                                        <p
                                                            class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                        >
                                                            {{ office.realtor_count ?? 0 }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center gap-3 rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                                >
                                                    <i class="pi pi-user-plus text-blue-500"></i>
                                                    <div>
                                                        <p
                                                            class="text-xs text-surface-500 dark:text-surface-400"
                                                        >
                                                            Active Agents
                                                        </p>
                                                        <p
                                                            class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                        >
                                                            {{ office.active_agents_count ?? 0 }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center gap-3 rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                                >
                                                    <i class="pi pi-user-edit text-yellow-500"></i>
                                                    <div>
                                                        <p
                                                            class="text-xs text-surface-500 dark:text-surface-400"
                                                        >
                                                            Engaged
                                                        </p>
                                                        <p
                                                            class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                        >
                                                            {{ office.engaged_agents_count ?? 0 }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center gap-3 rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                                >
                                                    <i class="pi pi-user-minus text-red-500"></i>
                                                    <div>
                                                        <p
                                                            class="text-xs text-surface-500 dark:text-surface-400"
                                                        >
                                                            Inactive
                                                        </p>
                                                        <p
                                                            class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                        >
                                                            {{ office.inactive_agents_count ?? 0 }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-start gap-3 rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-500/15 dark:text-yellow-300"
                                            >
                                                <i class="pi pi-star-fill"></i>
                                            </div>

                                            <div>
                                                <p
                                                    class="text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    Highest Value Agent
                                                </p>
                                                <p
                                                    class="font-semibold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{
                                                        office.highest_value_agent
                                                            ? `${office.highest_value_agent.formatted_full_name} (${office.highest_value_agent.formatted_value})`
                                                            : 'No agents found'
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <div class="col-span-12 min-h-0 xl:col-span-8 xl:h-full">
                        <Card
                            class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900 xl:h-full"
                        >
                            <template #content>
                                <div class="xl:flex xl:h-full xl:min-h-0 xl:flex-col">
                                    <Tabs
                                        v-model:value="activeTab"
                                        scrollable
                                        class="xl:flex xl:h-full xl:min-h-0 xl:flex-col"
                                    >
                                        <TabList>
                                            <Tab v-for="tab in tabs" :key="tab.value" :value="tab.value">
                                                <span class="inline-flex items-center gap-2">
                                                    <i :class="tab.icon"></i>
                                                    <span>{{ tab.name }}</span>
                                                    <span
                                                        v-if="tab.count !== null && tab.count !== undefined"
                                                        class="rounded-full bg-surface-100 px-2 py-0.5 text-xs text-surface-700 dark:bg-surface-800 dark:text-surface-200"
                                                    >
                                                        {{ tab.count }}
                                                    </span>
                                                </span>
                                            </Tab>
                                        </TabList>

                                        <TabPanels class="xl:min-h-0 xl:flex-1">
                                            <TabPanel
                                                :value="0"
                                                class="xl:h-full xl:min-h-0 xl:overflow-y-auto"
                                            >
                                                <DetailsTab :office="office" @office="onUpdateOffice" />
                                            </TabPanel>

                                            <TabPanel
                                                :value="1"
                                                class="xl:h-full xl:min-h-0 xl:overflow-y-auto"
                                            >
                                                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                                                    <div
                                                        v-for="item in agentStatCards"
                                                        :key="item.label"
                                                        class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                                    >
                                                        <div class="mb-2 inline-flex items-center gap-2">
                                                            <i :class="item.class"></i>
                                                            <p
                                                                class="text-sm text-surface-500 dark:text-surface-400"
                                                            >
                                                                {{ item.label }}
                                                            </p>
                                                        </div>
                                                        <p
                                                            class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                        >
                                                            {{ item.value }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <DataTable :value="realtors">
                                                    <template #empty> No agents found. </template>

                                                    <template #header>
                                                        <div
                                                            class="flex items-center justify-between gap-3"
                                                        >
                                                            <div>
                                                                <h3
                                                                    class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                                                >
                                                                    Agents
                                                                </h3>
                                                                <p
                                                                    class="text-sm text-surface-500 dark:text-surface-400"
                                                                >
                                                                    Agents currently associated with this
                                                                    office.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <Column class="!text-center">
                                                        <template #body="{ data }">
                                                            <div class="inline-flex items-center gap-2">
                                                                <i
                                                                    v-if="
                                                                        office.highest_value_agent &&
                                                                        data.id ===
                                                                            office.highest_value_agent.id
                                                                    "
                                                                    class="pi pi-star-fill text-yellow-500"
                                                                ></i>
                                                                <Tag
                                                                    :value="data.status"
                                                                    :severity="getTagSeverity(data.status)"
                                                                />
                                                            </div>
                                                        </template>
                                                    </Column>

                                                    <Column
                                                        field="formatted_full_name"
                                                        header="Name"
                                                    ></Column>
                                                    <Column field="phone_number" header="Phone #"></Column>
                                                    <Column field="email" header="Email"></Column>
                                                    <Column field="formatted_value" header="Value"></Column>

                                                    <Column class="w-12 !text-center">
                                                        <template #body="{ data }">
                                                            <Link :href="route('realtor.show', data.id)">
                                                                <Button
                                                                    icon="pi pi-search"
                                                                    severity="primary"
                                                                    rounded
                                                                />
                                                            </Link>
                                                        </template>
                                                    </Column>
                                                </DataTable>
                                            </TabPanel>

                                            <TabPanel
                                                :value="2"
                                                class="xl:h-full xl:min-h-0 xl:overflow-y-auto"
                                            >
                                                <VisitTab :office="office" />
                                            </TabPanel>

                                            <TabPanel
                                                :value="3"
                                                class="xl:h-full xl:min-h-0 xl:overflow-y-auto"
                                            >
                                                <PerformanceTab :office="office" />
                                            </TabPanel>

                                            <TabPanel
                                                :value="4"
                                                class="xl:h-full xl:min-h-0 xl:overflow-y-auto"
                                            >
                                                <div class="space-y-5">
                                                    <div
                                                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                                                    >
                                                        <div>
                                                            <h3
                                                                class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                            >
                                                                Office Notes
                                                            </h3>
                                                            <p
                                                                class="text-sm text-surface-500 dark:text-surface-400"
                                                            >
                                                                Pinned notes appear first, followed by
                                                                newest entries.
                                                            </p>
                                                        </div>

                                                        <Button
                                                            label="Create Note"
                                                            icon="pi pi-plus-circle"
                                                            @click="showCreateNoteDialog = true"
                                                        />
                                                    </div>

                                                    <div
                                                        v-if="sortedOfficeNotes.length"
                                                        class="flex max-h-[700px] flex-col gap-4 overflow-y-auto rounded-2xl border border-surface-200 p-1 dark:border-surface-700"
                                                    >
                                                        <div
                                                            v-for="note in sortedOfficeNotes"
                                                            :key="note.id"
                                                            class="rounded-2xl border border-surface-200 bg-surface-50 p-4 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                                                        >
                                                            <div
                                                                class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                                                            >
                                                                <div
                                                                    class="flex flex-wrap items-center gap-2"
                                                                >
                                                                    <Message
                                                                        v-if="note.is_pinned"
                                                                        severity="success"
                                                                        size="small"
                                                                        icon="pi pi-thumbtack"
                                                                    >
                                                                        Pinned
                                                                    </Message>

                                                                    <Message
                                                                        :severity="
                                                                            noteSeverity(note.priority)
                                                                        "
                                                                        size="small"
                                                                    >
                                                                        {{ note.priority }}
                                                                    </Message>

                                                                    <Message
                                                                        v-if="note.requires_follow_up"
                                                                        severity="info"
                                                                        size="small"
                                                                    >
                                                                        Follow Up Required
                                                                    </Message>

                                                                    <Message
                                                                        v-if="note.assigned_to"
                                                                        severity="secondary"
                                                                        size="small"
                                                                    >
                                                                        Assigned:
                                                                        {{ note.assigned_to.name }}
                                                                    </Message>
                                                                </div>

                                                                <div class="flex items-center gap-2">
                                                                    <Button
                                                                        v-if="note.is_pinned"
                                                                        icon="pi pi-times"
                                                                        severity="secondary"
                                                                        text
                                                                        rounded
                                                                        @click="onRemovePin(note.id)"
                                                                    />
                                                                    <Button
                                                                        v-else
                                                                        label="Pin"
                                                                        icon="pi pi-thumbtack"
                                                                        severity="secondary"
                                                                        outlined
                                                                        size="small"
                                                                        @click="onAddPin(note.id)"
                                                                    />
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900"
                                                            >
                                                                <div
                                                                    class="whitespace-pre-line text-sm leading-6 text-surface-800 dark:text-surface-200"
                                                                >
                                                                    {{ note.note }}
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
                                                            >
                                                                <div>
                                                                    <Button
                                                                        label="Remove Note"
                                                                        icon="pi pi-trash"
                                                                        severity="danger"
                                                                        outlined
                                                                        size="small"
                                                                        @click="onDeleteNote(note.id)"
                                                                    />
                                                                </div>

                                                                <div
                                                                    class="text-sm text-surface-500 dark:text-surface-400 sm:text-right"
                                                                >
                                                                    <p
                                                                        class="font-medium text-surface-700 dark:text-surface-200"
                                                                    >
                                                                        {{ note.created_by.name }}
                                                                    </p>
                                                                    <p>{{ note.formatted_created_at }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        v-else
                                                        class="flex min-h-52 flex-col items-center justify-center rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                                    >
                                                        <i
                                                            class="pi pi-file-edit mb-3 text-2xl text-surface-400 dark:text-surface-500"
                                                        />
                                                        <p
                                                            class="font-medium text-surface-700 dark:text-surface-200"
                                                        >
                                                            No notes found.
                                                        </p>
                                                        <p
                                                            class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                                        >
                                                            Create the first note for this office.
                                                        </p>
                                                        <Button
                                                            label="Create Note"
                                                            icon="pi pi-plus-circle"
                                                            class="mt-4"
                                                            @click="showCreateNoteDialog = true"
                                                        />
                                                    </div>
                                                </div>
                                            </TabPanel>
                                        </TabPanels>
                                    </Tabs>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </Layout>

    <Dialog
        v-model:visible="showImageDialog"
        modal
        header="Upload Office Image"
        :style="{ width: '25rem', maxWidth: '95vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' },
            header: {
                class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4 text-surface-900 dark:text-surface-0',
            },
            content: {
                class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0',
            },
        }"
    >
        <p class="mb-5 text-surface-700 dark:text-surface-300">
            Click below to upload an office image for {{ office.parent_company }}.
        </p>

        <FileUpload
            mode="basic"
            @select="onUpload($event)"
            accept="image/*"
            customUpload
            :maxFileSize="1000000"
            chooseLabel="Choose Image"
        />
    </Dialog>

    <Dialog
        v-model:visible="showCreateNoteDialog"
        modal
        header="Create Office Note"
        :style="{ width: '42rem', maxWidth: '95vw' }"
        :breakpoints="{ '960px': '92vw', '640px': '96vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' },
            header: {
                class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4 text-surface-900 dark:text-surface-0',
            },
            content: {
                class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0',
            },
        }"
        @hide="onNoteDialogHide"
    >
        <form @submit.prevent="onOfficeNoteSubmit" class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                    Create Note
                </h3>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Add internal notes, assignments, and follow-up reminders for this office.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="flex flex-col">
                    <label
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': officeNoteForm.errors.priority }"
                    >
                        Priority Level
                    </label>
                    <Select
                        v-model="officeNoteForm.priority"
                        :options="generalNotePriorityLevelOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select priority..."
                        fluid
                        :class="{ '!border-red-500': officeNoteForm.errors.priority }"
                    />
                    <Message
                        v-if="officeNoteForm.errors.priority"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ officeNoteForm.errors.priority }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': officeNoteForm.errors.assigned_to }"
                    >
                        Assigned To
                    </label>
                    <Select
                        v-model="officeNoteForm.assigned_to"
                        :options="usersWithNobodyOption"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Select a user..."
                        fluid
                        :class="{ '!border-red-500': officeNoteForm.errors.assigned_to }"
                    />
                    <Message
                        v-if="officeNoteForm.errors.assigned_to"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ officeNoteForm.errors.assigned_to }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                        Pinned
                    </label>
                    <div
                        class="rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800"
                    >
                        <RadioButtonGroup v-model="officeNoteForm.is_pinned">
                            <div class="flex gap-5">
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="office-pinned-yes" :value="true" />
                                    <label
                                        for="office-pinned-yes"
                                        class="text-surface-700 dark:text-surface-300"
                                        >Yes</label
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="office-pinned-no" :value="false" />
                                    <label
                                        for="office-pinned-no"
                                        class="text-surface-700 dark:text-surface-300"
                                        >No</label
                                    >
                                </div>
                            </div>
                        </RadioButtonGroup>
                    </div>
                    <Message
                        v-if="officeNoteForm.errors.is_pinned"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ officeNoteForm.errors.is_pinned }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                        Requires Follow-Up
                    </label>
                    <div
                        class="rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800"
                    >
                        <RadioButtonGroup v-model="officeNoteForm.requires_follow_up">
                            <div class="flex gap-5">
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="office-followup-yes" :value="true" />
                                    <label
                                        for="office-followup-yes"
                                        class="text-surface-700 dark:text-surface-300"
                                        >Yes</label
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="office-followup-no" :value="false" />
                                    <label
                                        for="office-followup-no"
                                        class="text-surface-700 dark:text-surface-300"
                                        >No</label
                                    >
                                </div>
                            </div>
                        </RadioButtonGroup>
                    </div>
                    <Message
                        v-if="officeNoteForm.errors.requires_follow_up"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ officeNoteForm.errors.requires_follow_up }}
                    </Message>
                </div>
            </div>

            <div class="flex flex-col">
                <label
                    for="office-note"
                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                    :class="{ 'text-red-500': officeNoteForm.errors.note }"
                >
                    Note
                </label>
                <Textarea
                    id="office-note"
                    v-model="officeNoteForm.note"
                    fluid
                    rows="6"
                    autoResize
                    class="h-36"
                    :class="{ '!border-red-500': officeNoteForm.errors.note }"
                />
                <Message
                    v-if="officeNoteForm.errors.note"
                    size="small"
                    severity="error"
                    variant="simple"
                    class="mt-2"
                >
                    {{ officeNoteForm.errors.note }}
                </Message>
            </div>

            <div
                class="flex justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800"
            >
                <Button
                    label="Cancel"
                    type="button"
                    severity="secondary"
                    outlined
                    @click="showCreateNoteDialog = false"
                />
                <Button
                    label="Create Note"
                    type="submit"
                    icon="pi pi-check-circle"
                    :loading="officeNoteForm.processing"
                />
            </div>
        </form>
    </Dialog>

    <ConfirmDialog />
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import VisitTab from './Components/VisitTab.vue'
import DetailsTab from './Components/DetailsTab.vue'
import PerformanceTab from './Components/PerformanceTab.vue'
import { usePage, Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'
import {
    Card,
    Button,
    Image,
    Dialog,
    FileUpload,
    Tabs,
    TabList,
    Tab,
    TabPanel,
    TabPanels,
    DataTable,
    Column,
    Select,
    Textarea,
    RadioButtonGroup,
    RadioButton,
    Message,
    Tag,
    Divider,
    useConfirm,
    ConfirmDialog
} from 'primevue'
import axios from 'axios'

const page = usePage()
const office = ref(page.props.office)
const users = page.props.users ?? []
const showImageDialog = ref(false)
const showCreateNoteDialog = ref(false)
const confirm = useConfirm();

const realtors = computed(() => office.value.realtors ?? [])

const fullAddress = computed(() => {
    const unit = office.value.unit_number ? ` #${office.value.unit_number}` : ''
    return `${office.value.street_address || ''}${unit}, ${office.value.city || ''}, ${office.value.state || ''} ${office.value.zip_code || ''}`.trim()
})

const getTagSeverity = (status) => {
    if (status === 'Active') return 'success'
    if (status === 'Inactive') return 'danger'
    if (status === 'Engaged') return 'warn'
    if (status === 'New') return 'info'
    return null
}

const agentStatCards = computed(() => [
    {
        class: 'pi pi-star-fill text-yellow-500',
        label: 'Highest Value Agent',
        value: office.value.highest_value_agent
            ? `${office.value.highest_value_agent.formatted_full_name} (${office.value.highest_value_agent.formatted_value})`
            : 'No agents found',
    },
    {
        class: 'pi pi-users text-green-500',
        label: 'Active / Total Agents',
        value: `${office.value.active_agents_count ?? 0} / ${office.value.realtor_count ?? 0}`,
    },
    {
        class: 'pi pi-user-edit text-yellow-500',
        label: 'Engaged Agents',
        value: office.value.engaged_agents_count ?? 0,
    },
    {
        class: 'pi pi-user-minus text-red-500',
        label: 'Inactive Agents',
        value: office.value.inactive_agents_count ?? 0,
    },
])

const activeTab = ref(0)
const localStorageKey = computed(() => `activeTabForOffice-${office.value.id}`)

onMounted(() => {
    const savedTab = localStorage.getItem(localStorageKey.value)
    if (savedTab !== null) {
        activeTab.value = parseInt(savedTab, 10)
    }
})

watch(activeTab, (newValue) => {
    localStorage.setItem(localStorageKey.value, newValue)
})

const goToTab = (tabValue) => {
    activeTab.value = tabValue
}

const usersWithNobodyOption = computed(() => [{ name: 'Nobody', id: 0 }, ...users])

const generalNotePriorityLevelOptions = [
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
]

const getDefaultOfficeNoteState = () => ({
    priority: 'medium',
    assigned_to: null,
    is_pinned: false,
    requires_follow_up: false,
    note: '',
})

const officeNoteForm = useForm(getDefaultOfficeNoteState())

const resetOfficeNoteForm = () => {
    officeNoteForm.defaults(getDefaultOfficeNoteState())
    officeNoteForm.reset()
    officeNoteForm.clearErrors()
}

const onNoteDialogHide = () => {
    resetOfficeNoteForm()
}

const onOfficeNoteSubmit = () => {
    officeNoteForm.post(route('office-note.store', { office_id: office.value.id }), {
        preserveState: true,
        preserveScroll: true,
        only: ['office', 'flash'],
        onSuccess: (response) => {
            office.value = response.props.office
            resetOfficeNoteForm()
            showCreateNoteDialog.value = false
        },
        onError: () => {},
    })
}

const noteSeverity = (priorityLevel) => {
    switch (priorityLevel) {
        case 'Low':
        case 'low':
            return 'info'
        case 'Medium':
        case 'medium':
            return 'warn'
        case 'High':
        case 'high':
            return 'danger'
        default:
            return 'info'
    }
}

const sortedOfficeNotes = computed(() => {
    const notes = [...(office.value.notes ?? [])]

    return notes.sort((a, b) => {
        if (a.is_pinned !== b.is_pinned) {
            return Number(b.is_pinned) - Number(a.is_pinned)
        }
        return new Date(b.created_at) - new Date(a.created_at)
    })
})

const onUpload = async (event) => {
    const file = event.files[0]
    if (!file) {
        console.error('No file selected.')
        return
    }

    const formData = new FormData()
    formData.append('image', file)

    try {
        const response = await axios.post(`/office/${office.value.id}/upload-image`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })

        office.value.image_url = response.data.image_url
        showImageDialog.value = false
    } catch (error) {
        console.error('Upload error:', error.response || error.message)
        alert('Failed to upload image. Please try again.')
    }
}

const onRemovePin = (noteId) => {
    router.put(
        route('office-note.update', { office_note: noteId }),
        { request_type: 'remove_pin', is_pinned: false },
        {
            preserveScroll: true,
            onSuccess: () => {
                const note = office.value.notes.find((n) => n.id === noteId)
                if (note) note.is_pinned = false
            },
            onError: (error) => {
                console.error('Error removing pin:', error)
            },
        }
    )
}

const onDeleteNote = (noteId) => {
    router.delete(route('office-note.destroy', { office_note: noteId }), {
        preserveScroll: true,
        onSuccess: () => {
            const noteIndex = office.value.notes.findIndex((n) => n.id === noteId)
            if (noteIndex !== -1) {
                office.value.notes.splice(noteIndex, 1)
            }
        },
        onError: (error) => {
            console.error('Error deleting note:', error)
        },
    })
}

const onAddPin = (noteId) => {
    router.put(
        route('office-note.update', { office_note: noteId }),
        { request_type: 'add_pin', is_pinned: true },
        {
            preserveScroll: true,
            onSuccess: () => {
                const note = office.value.notes.find((n) => n.id === noteId)
                if (note) note.is_pinned = true
            },
            onError: (error) => {
                console.error('Error pinning the note:', error)
            },
        }
    )
}

const tabs = computed(() => [
    { value: 0, name: 'Location Details', icon: 'pi pi-building', count: null },
    { value: 1, name: 'Agents', icon: 'pi pi-users', count: realtors.value.length },
    { value: 2, name: 'Visits', icon: 'pi pi-map-marker', count: null },
    { value: 3, name: 'Performance', icon: 'pi pi-chart-bar', count: null },
    { value: 4, name: 'Notes', icon: 'pi pi-file-edit', count: office.value.notes?.length ?? 0 },
])

const onUpdateOffice = (newOffice) => {
    office.value = newOffice
}

const confirmDeleteOffice = () => {
    return new Promise((resolve) => {
        confirm.require({
            header: 'Delete Office',
            message: `Are you sure you want to delete ${office.value.parent_company}${office.value.office_name ? ` - ${office.value.office_name}` : ''}? This action cannot be undone.`,
            icon: 'pi pi-exclamation-triangle',
            rejectLabel: 'Cancel',
            acceptLabel: 'Delete',
            rejectProps: {
                severity: 'secondary',
                outlined: true,
            },
            acceptProps: {
                severity: 'danger',
            },
            accept: () => resolve(true),
            reject: () => resolve(false),
            onHide: () => resolve(false),
        })
    })
}

// CHANGED: awaitable delete flow
const onDeleteOffice = async () => {
    const confirmed = await confirmDeleteOffice()

    if (!confirmed) return

    router.delete(route('office.destroy', office.value.id), {
        preserveScroll: true,
    })
}


</script>
