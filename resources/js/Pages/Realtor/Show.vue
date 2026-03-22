<template>
    <!-- CHANGED: kept safe title binding -->
    <Head :title="`${realtor.first_name} ${realtor.last_name}`" />

    <Layout>
        <!-- CHANGED: removed min-h-full so the Layout main scroll container can control page scrolling correctly -->
        <div class="px-4 py-5 md:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-2">
                            <Link :href="route('realtor.index')">
                                <Button
                                    label="Back to Realtors"
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
                                {{ realtor.first_name }} {{ realtor.last_name }}
                            </h1>

                            <div
                                class="mt-2 flex flex-wrap items-center gap-2 text-sm text-surface-500 dark:text-surface-400"
                            >
                                <Tag
                                    :value="realtor.status || 'Unknown'"
                                    :severity="getTagSeverity(realtor.status)"
                                />
                                <span v-if="realtor.title">{{ camelToNormal(realtor.title) }}</span>
                                <span v-if="realtor.office?.formatted_company_name">
                                    • {{ realtor.office.formatted_company_name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <Button
                            label="Communications"
                            icon="pi pi-comments"
                            severity="secondary"
                            outlined
                            @click="goToTab(3)"
                        />
                        <Button
                            label="Notes"
                            icon="pi pi-file-edit"
                            severity="secondary"
                            outlined
                            @click="goToTab(5)"
                        />
                        <Button label="Sequences" icon="pi pi-directions-alt" @click="goToTab(4)" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 items-start gap-5">
                <div class="col-span-12 self-start xl:col-span-4">
                    <div class="space-y-5 xl:sticky xl:top-5">
                        <Card
                            class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800"
                        >
                            <template #content>
                                <div class="flex flex-col gap-6">
                                    <div
                                        class="flex flex-col items-center gap-5 lg:flex-row lg:items-start"
                                    >
                                        <div class="flex flex-col items-center gap-4">
                                            <div
                                                class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-4 border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <!-- CHANGED: Added preview so clicking the avatar opens a larger lightbox version -->
                                                <!-- CHANGED: Added preview-specific classes so it feels clickable -->
                                                <Image
                                                    v-if="realtor.avatar_url"
                                                    :src="realtor.avatar_url"
                                                    imageClass="w-28 h-28 object-cover cursor-pointer"
                                                    preview
                                                />

                                                <div
                                                    v-else
                                                    class="px-4 text-center text-sm text-surface-400 dark:text-surface-500"
                                                >
                                                    No Avatar
                                                </div>
                                            </div>

                                            <Button
                                                label="Upload Avatar"
                                                icon="pi pi-image"
                                                outlined
                                                class="w-full"
                                                @click="showAvatarDialog = true"
                                            />
                                        </div>

                                        <div class="flex-1 text-center lg:text-left">
                                            <h2
                                                class="text-2xl font-bold leading-tight text-surface-900 dark:text-surface-0"
                                            >
                                                {{ realtor.first_name }} {{ realtor.last_name }}
                                            </h2>

                                            <p
                                                v-if="realtor.title"
                                                class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                {{ camelToNormal(realtor.title) }}
                                            </p>

                                            <div
                                                class="mt-4 space-y-2 text-sm text-surface-700 dark:text-surface-300"
                                            >
                                                <p
                                                    class="break-all"
                                                    :title="realtor.email || 'No email'"
                                                >
                                                    <span
                                                        class="font-medium text-surface-900 dark:text-surface-100"
                                                        >Email:</span
                                                    >
                                                    {{ realtor.email || 'N/A' }}
                                                </p>

                                                <p>
                                                    <span
                                                        class="font-medium text-surface-900 dark:text-surface-100"
                                                        >Phone:</span
                                                    >
                                                    {{ realtor.phone_number || 'N/A' }}
                                                </p>

                                                <p
                                                    v-if="
                                                        realtor.office?.office_name ||
                                                        realtor.office?.parent_company
                                                    "
                                                >
                                                    <span
                                                        class="font-medium text-surface-900 dark:text-surface-100"
                                                        >Office:</span
                                                    >
                                                    {{ realtor.office?.parent_company || 'N/A' }}
                                                    <span v-if="realtor.office?.office_name">
                                                        - {{ realtor.office.office_name }}
                                                    </span>
                                                </p>

                                                <p v-if="realtor.acquired_via">
                                                    <span
                                                        class="font-medium text-surface-900 dark:text-surface-100"
                                                        >Acquired Via:</span
                                                    >
                                                    {{ formatLabel(realtor.acquired_via) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <Divider />

                                    <div class="space-y-3">
                                        <div>
                                            <h3
                                                class="text-base font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                Contact Owner
                                            </h3>
                                            <p
                                                class="text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                Primary internal owner for this relationship.
                                            </p>
                                        </div>

                                        <Select
                                            v-model="contactOwner"
                                            :options="users"
                                            optionLabel="formatted_full_name"
                                            placeholder="Select owner..."
                                            fluid
                                        />
                                    </div>

                                    <div class="space-y-3">
                                        <div>
                                            <h3
                                                class="text-base font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                Quick Actions
                                            </h3>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <Button
                                                label="Personal"
                                                icon="pi pi-user"
                                                severity="secondary"
                                                outlined
                                                @click="goToTab(0)"
                                            />
                                            <Button
                                                label="Professional"
                                                icon="pi pi-briefcase"
                                                severity="secondary"
                                                outlined
                                                @click="goToTab(1)"
                                            />
                                            <Button
                                                label="Metrics"
                                                icon="pi pi-chart-bar"
                                                severity="secondary"
                                                outlined
                                                @click="goToTab(2)"
                                            />
                                            <Button
                                                label="Notes"
                                                icon="pi pi-file-edit"
                                                severity="secondary"
                                                outlined
                                                @click="goToTab(5)"
                                            />
                                        </div>

                                        <Link
                                            v-if="realtor.office?.id"
                                            :href="route('office.show', realtor.office.id)"
                                        >
                                            <Button
                                                label="View Office"
                                                icon="pi pi-building"
                                                class="mt-1 w-full"
                                                outlined
                                            />
                                        </Link>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card
                            class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800"
                        >
                            <template #content>
                                <div class="space-y-4">
                                    <div>
                                        <h3
                                            class="text-base font-semibold text-surface-900 dark:text-surface-0"
                                        >
                                            At a Glance
                                        </h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            A few key relationship metrics.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="text-xs text-surface-500 dark:text-surface-400"
                                            >
                                                Inspections
                                            </p>
                                            <p
                                                class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                {{ previousInspections.length }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="text-xs text-surface-500 dark:text-surface-400"
                                            >
                                                Sequences
                                            </p>
                                            <p
                                                class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                {{ activeSequences.length }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="text-xs text-surface-500 dark:text-surface-400"
                                            >
                                                Notes
                                            </p>
                                            <p
                                                class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                {{ totalNotesCount }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="text-xs text-surface-500 dark:text-surface-400"
                                            >
                                                Pinned Notes
                                            </p>
                                            <p
                                                class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                {{ pinnedNotesCount }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <div class="col-span-12 xl:col-span-8">
                    <Card
                        class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800"
                    >
                        <template #content v-if="!viewSequenceDetails">
                            <Tabs :value="activeTab" scrollable>
                                <TabList>
                                    <Tab v-for="tab in tabs" :key="tab.value" :value="tab.value">
                                        <span class="inline-flex items-center gap-2">
                                            <i :class="tab.icon" />
                                            <span>{{ tab.name }}</span>
                                            <span
                                                v-if="tab.count !== null && tab.count !== undefined"
                                                class="rounded-full bg-surface-100 dark:bg-surface-800 px-2 py-0.5 text-xs text-surface-700 dark:text-surface-200"
                                            >
                                                {{ tab.count }}
                                            </span>
                                        </span>
                                    </Tab>
                                </TabList>

                                <TabPanels>
                                    <TabPanel :value="0">
                                        <PersonalInfoTab
                                            :realtor="realtor"
                                            @realtor="onUpdateRealtor"
                                        />
                                    </TabPanel>

                                    <TabPanel :value="1">
                                        <ProfessionalInfoTab
                                            :realtor="realtor"
                                            @realtor="onUpdateRealtor"
                                        />
                                    </TabPanel>

                                    <TabPanel :value="2">
                                        <div class="mb-6 grid grid-cols-1 gap-5 md:grid-cols-3">
                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    # of Sales
                                                </p>
                                                <p
                                                    class="text-2xl font-bold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ realtor.sale_count ?? 0 }}
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    Average Sale Value
                                                </p>
                                                <p
                                                    class="text-2xl font-bold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ realtor.average_sale_value || 'N/A' }}
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    Total Sale Value
                                                </p>
                                                <p
                                                    class="text-2xl font-bold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ formatCurrency(realtor.value) }}
                                                </p>
                                            </div>
                                        </div>

                                        <DataTable
                                            class="mt-5 w-full"
                                            :value="previousInspections"
                                            stripedRows
                                            paginator
                                            :rows="5"
                                            :rowsPerPageOptions="[5, 10, 25, 50, 100]"
                                        >
                                            <template #empty> No inspections were found. </template>

                                            <template #header>
                                                <div
                                                    class="flex flex-wrap items-center justify-between gap-2"
                                                >
                                                    <span
                                                        class="text-xl font-bold text-surface-900 dark:text-surface-0"
                                                        >Previous Inspections</span
                                                    >
                                                </div>
                                            </template>

                                            <Column
                                                field="inspection_number"
                                                sortable
                                                header="Inspection #"
                                            ></Column>
                                            <Column
                                                field="inspection_date_sort"
                                                sortable
                                                header="Date"
                                            >
                                                <template #body="{ data }">
                                                    {{ data.inspection_date || '—' }}
                                                </template>
                                            </Column>

                                            <Column header="Total Value">
                                                <template #body="slotProps">
                                                    {{ formatCurrency(slotProps.data.total_fee) }}
                                                </template>
                                            </Column>
                                        </DataTable>
                                    </TabPanel>

                                    <TabPanel :value="3">
                                        <CommunicationLogTab
                                            :realtor_id="realtor.id"
                                            :realtor="realtor"
                                            @realtor="onUpdateRealtor"
                                        />
                                    </TabPanel>

                                    <TabPanel :value="4">
                                        <div class="mb-6 grid grid-cols-1 gap-5 xl:grid-cols-3">
                                            <div class="space-y-3">
                                                <div>
                                                    <h3
                                                        class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                                    >
                                                        Add to Sequence
                                                    </h3>
                                                    <p
                                                        class="text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        Select a sequence and assign a start date.
                                                    </p>
                                                </div>

                                                <Select
                                                    fluid
                                                    v-model="selectedSequence"
                                                    :options="sequences"
                                                    optionLabel="name"
                                                    placeholder="Select a sequence..."
                                                />

                                                <Button
                                                    v-if="selectedSequence"
                                                    label="Clear Selection"
                                                    icon="pi pi-times-circle"
                                                    severity="secondary"
                                                    outlined
                                                    @click="selectedSequence = null"
                                                />
                                            </div>

                                            <div v-if="selectedSequence" class="xl:col-span-2">
                                                <Card
                                                    class="rounded-2xl border border-surface-200 bg-surface-50 shadow-none dark:border-surface-700 dark:bg-surface-800"
                                                >
                                                    <template #content>
                                                        <Message
                                                            v-if="
                                                                addToSequenceForm.errors
                                                                    .sequence_id ===
                                                                'The sequence id has already been taken.'
                                                            "
                                                            severity="error"
                                                            class="mb-5"
                                                            icon="pi pi-exclamation-circle"
                                                        >
                                                            This realtor has already been added to
                                                            the selected sequence.
                                                        </Message>

                                                        <form
                                                            @submit.prevent="onAddToSequenceSubmit"
                                                            class="space-y-5"
                                                        >
                                                            <div
                                                                class="flex w-full flex-col md:w-1/2"
                                                            >
                                                                <label
                                                                    class="mb-2 font-medium text-surface-900 dark:text-surface-100"
                                                                >
                                                                    Sequence Start Date
                                                                </label>

                                                                <DatePicker
                                                                    v-model="
                                                                        addToSequenceForm.start_date
                                                                    "
                                                                    placeholder="Select a date..."
                                                                    fluid
                                                                />

                                                                <Message
                                                                    v-if="
                                                                        addToSequenceForm.errors
                                                                            .start_date
                                                                    "
                                                                    severity="error"
                                                                    variant="simple"
                                                                    size="small"
                                                                >
                                                                    {{
                                                                        addToSequenceForm.errors
                                                                            .start_date
                                                                    }}
                                                                </Message>
                                                            </div>

                                                            <div
                                                                class="space-y-3 text-sm text-surface-700 dark:text-surface-300"
                                                            >
                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p
                                                                        class="font-semibold text-surface-900 dark:text-surface-100"
                                                                    >
                                                                        Name
                                                                    </p>
                                                                    <p class="col-span-2">
                                                                        {{ selectedSequence?.name }}
                                                                    </p>
                                                                </div>

                                                                <Divider />

                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p
                                                                        class="font-semibold text-surface-900 dark:text-surface-100"
                                                                    >
                                                                        Target Audience
                                                                    </p>
                                                                    <p class="col-span-2">
                                                                        {{
                                                                            selectedSequence?.target_audience
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <Divider />

                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p
                                                                        class="font-semibold text-surface-900 dark:text-surface-100"
                                                                    >
                                                                        Description
                                                                    </p>
                                                                    <p class="col-span-2">
                                                                        {{
                                                                            selectedSequence?.description ||
                                                                            'No description provided.'
                                                                        }}
                                                                    </p>
                                                                </div>

                                                                <Divider class="!mb-3" />

                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p
                                                                        class="font-semibold text-surface-900 dark:text-surface-100"
                                                                    >
                                                                        Steps
                                                                    </p>

                                                                    <div class="col-span-2">
                                                                        <ul class="space-y-2">
                                                                            <li
                                                                                v-for="step in selectedSequence?.steps"
                                                                                :key="
                                                                                    step.step_number
                                                                                "
                                                                                class="rounded-xl border border-surface-200 bg-white px-3 py-3 dark:border-surface-700 dark:bg-surface-900"
                                                                            >
                                                                                <p
                                                                                    class="font-semibold text-surface-900 dark:text-surface-100"
                                                                                >
                                                                                    Step #{{
                                                                                        step.step_number
                                                                                    }}:
                                                                                    {{ step.type }}
                                                                                </p>
                                                                                <p
                                                                                    class="text-sm text-surface-600 dark:text-surface-400"
                                                                                >
                                                                                    {{
                                                                                        step.description
                                                                                    }}
                                                                                </p>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <div class="flex justify-end pt-3">
                                                                    <Button
                                                                        label="Add Realtor to Sequence"
                                                                        icon="pi pi-plus-circle"
                                                                        type="submit"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </template>
                                                </Card>
                                            </div>
                                        </div>

                                        <DataTable :value="activeSequences">
                                            <template #header>
                                                <h2
                                                    class="text-2xl font-extrabold text-surface-900 dark:text-surface-0"
                                                >
                                                    Active Sequences
                                                </h2>
                                            </template>

                                            <template #empty>No active sequences found.</template>

                                            <Column header="Name" field="sequence_name"></Column>

                                            <Column header="Current Step">
                                                <template #body="{ data }">
                                                    <div>
                                                        <span>{{ data.formatted_status }}</span>
                                                        <span> ({{ data.progress || '0/0' }})</span>
                                                    </div>
                                                </template>
                                            </Column>

                                            <Column
                                                header="Added On"
                                                field="formatted_created_at"
                                            ></Column>

                                            <Column header="Next Step Due By">
                                                <template #body="{ data }">
                                                    {{
                                                        data.formatted_next_step_due ||
                                                        'Not Started'
                                                    }}
                                                </template>
                                            </Column>

                                            <Column>
                                                <template #body="{ data }">
                                                    <div class="flex flex-wrap gap-2">
                                                        <Message
                                                            v-if="data.status === 'not_started'"
                                                            severity="info"
                                                            icon="pi pi-info-circle"
                                                        >
                                                            Not Started
                                                        </Message>

                                                        <Message
                                                            v-if="data.is_passed_due"
                                                            severity="error"
                                                            icon="pi pi-exclamation-triangle"
                                                        >
                                                            Past Due
                                                        </Message>

                                                        <Message
                                                            v-if="data.is_due_today"
                                                            severity="warn"
                                                            icon="pi pi-exclamation-triangle"
                                                        >
                                                            Due Today
                                                        </Message>
                                                    </div>
                                                </template>
                                            </Column>

                                            <Column class="w-24 !text-center">
                                                <template #body="{ data }">
                                                    <Button
                                                        icon="pi pi-search"
                                                        severity="primary"
                                                        rounded
                                                        @click="onViewActiveSequence(data)"
                                                    />
                                                </template>
                                            </Column>
                                        </DataTable>
                                    </TabPanel>

                                    <TabPanel :value="5">
                                        <NotesTab
                                            :users="users"
                                            :realtor="realtor"
                                            @realtor="onUpdateRealtor"
                                        />
                                    </TabPanel>
                                </TabPanels>
                            </Tabs>
                        </template>

                        <template #content v-else>
                            <div class="mb-4 flex flex-col gap-2">
                                <div class="text-sm text-surface-500 dark:text-surface-400">
                                    Sequences / Sequence Details
                                </div>

                                <div
                                    class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"
                                >
                                    <div>
                                        <p
                                            class="text-2xl font-bold text-surface-900 dark:text-surface-0"
                                        >
                                            Sequence Details
                                        </p>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Review progress and log updates for this sequence.
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <!-- CHANGED: added Add Note button and moved note creation to dialog -->
                                        <Button
                                            label="Add Note"
                                            icon="pi pi-plus-circle"
                                            @click="openSequenceNoteDialog"
                                        />
                                        <Button
                                            label="Back to Sequences"
                                            icon="pi pi-arrow-left"
                                            outlined
                                            @click="onBackToMainView"
                                        />
                                    </div>
                                </div>
                            </div>

                            <Divider />

                            <div class="mb-6">
                                <template
                                    v-if="
                                        selectedActiveSequence?.is_due_today ||
                                        selectedActiveSequence?.is_passed_due
                                    "
                                >
                                    <div class="mb-5 flex justify-center">
                                        <Message
                                            v-if="selectedActiveSequence?.is_due_today"
                                            severity="warn"
                                            icon="pi pi-exclamation-triangle"
                                        >
                                            This step is due today.
                                        </Message>

                                        <Message
                                            v-if="selectedActiveSequence?.is_passed_due"
                                            severity="error"
                                            icon="pi pi-exclamation-triangle"
                                        >
                                            This step is past due.
                                        </Message>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="mb-5 flex justify-center">
                                        <Message severity="info" icon="pi pi-info-circle">
                                            This step is due
                                            {{ selectedActiveSequence?.formatted_next_step_due }}
                                        </Message>
                                    </div>
                                </template>

                                <div
                                    class="grid grid-cols-1 gap-4 rounded-2xl border border-surface-200 bg-surface-50 p-5 md:grid-cols-2 dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <div>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Name
                                        </p>
                                        <p
                                            class="font-semibold text-surface-900 dark:text-surface-100"
                                        >
                                            {{ selectedActiveSequence?.sequence_name }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Target Audience
                                        </p>
                                        <p
                                            class="font-semibold text-surface-900 dark:text-surface-100"
                                        >
                                            {{ selectedActiveSequence?.sequence?.target_audience }}
                                        </p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Description
                                        </p>
                                        <p
                                            :class="{
                                                'italic text-surface-400 dark:text-surface-500':
                                                    !selectedActiveSequence?.sequence?.description,
                                                'text-surface-700 dark:text-surface-300':
                                                    selectedActiveSequence?.sequence?.description,
                                            }"
                                        >
                                            {{
                                                selectedActiveSequence?.sequence?.description ||
                                                'No description found'
                                            }}
                                        </p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <p
                                            class="mb-1 text-sm text-surface-500 dark:text-surface-400"
                                        >
                                            Current Step
                                        </p>

                                        <div
                                            class="flex flex-wrap items-center gap-2 text-surface-900 dark:text-surface-100"
                                        >
                                            <span class="font-semibold">
                                                <template
                                                    v-if="
                                                        selectedActiveSequence?.status ===
                                                            'not_started' ||
                                                        selectedActiveSequence?.status ===
                                                            'completed' ||
                                                        selectedActiveSequence?.status ===
                                                            'cancelled'
                                                    "
                                                >
                                                    {{ selectedActiveSequence?.formatted_status }}
                                                </template>

                                                <template v-else>
                                                    {{
                                                        camelToNormal(

                                                            selectedActiveSequence?.sequence?.steps?.find(
                                                                (step) =>
                                                                    step.step_number ===
                                                                    selectedActiveSequence?.current_step
                                                            )?.type
                                                        )
                                                    }}
                                                </template>
                                            </span>

                                            <span
                                                >-
                                                {{
                                                    selectedActiveSequence?.progress || '0/0'
                                                }}</span
                                            >

                                            <Button
                                                v-if="
                                                    selectedActiveSequence?.status !== 'completed'
                                                "
                                                severity="success"
                                                :label="
                                                    selectedActiveSequence?.status === 'not_started'
                                                        ? 'Start Sequence'
                                                        : selectedActiveSequence?.current_step ===
                                                            selectedActiveSequence?.total_steps
                                                          ? 'Complete Sequence'
                                                          : 'Next Step'
                                                "
                                                class="!ms-2"
                                                @click="onNextSequenceStep"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <Divider />

                            <!-- CHANGED: replaced inline sequence note form with a cleaner notes history section -->
                            <div class="space-y-5">
                                <div
                                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <h3
                                            class="text-lg font-semibold text-surface-900 dark:text-surface-0"
                                        >
                                            Sequence Notes
                                        </h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Review previous updates for the current sequence step
                                            and add new notes from the dialog.
                                        </p>
                                    </div>

                                    <Button
                                        label="Add Note"
                                        icon="pi pi-plus-circle"
                                        @click="openSequenceNoteDialog"
                                    />
                                </div>

                                <div
                                    v-if="selectedSequenceStepNotes.length"
                                    class="flex flex-col gap-4"
                                >
                                    <Panel
                                        v-for="note in selectedSequenceStepNotes"
                                        :key="note.id"
                                        :header="formatLabel(note.status)"
                                        class="shadow-sm"
                                    >
                                        <p
                                            class="mb-3 text-sm text-surface-700 dark:text-surface-300"
                                        >
                                            {{ note.status_description }}
                                        </p>

                                        <div
                                            class="flex flex-col gap-3 rounded-xl border border-surface-200 bg-surface-50 px-3 py-3 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <div
                                                class="rounded-lg border border-surface-200 bg-white p-3 dark:border-surface-700 dark:bg-surface-900"
                                            >
                                                <p class="text-surface-800 dark:text-surface-200">
                                                    {{ note.note }}
                                                </p>
                                            </div>

                                            <div
                                                class="flex flex-col gap-1 text-sm text-surface-500 dark:text-surface-400 sm:text-right"
                                            >
                                                <p
                                                    class="font-medium text-surface-700 dark:text-surface-200"
                                                >
                                                    {{ note.created_by?.name || 'Unknown User' }}
                                                </p>
                                                <p>{{ note.created_at }}</p>
                                            </div>
                                        </div>
                                    </Panel>
                                </div>

                                <div
                                    v-else
                                    class="flex min-h-[18rem] flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <i
                                        class="pi pi-file-edit mb-3 text-3xl text-surface-400 dark:text-surface-500"
                                    />
                                    <p class="font-medium text-surface-700 dark:text-surface-200">
                                        No notes found for this step.
                                    </p>
                                    <p
                                        class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400"
                                    >
                                        Add the first sequence note to document progress and next
                                        actions.
                                    </p>
                                    <Button
                                        class="mt-4"
                                        label="Add Note"
                                        icon="pi pi-plus-circle"
                                        @click="openSequenceNoteDialog"
                                    />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>

        <Dialog
            v-model:visible="showAvatarDialog"
            modal
            header="Upload Avatar Image"
            :style="{ width: '25rem', maxWidth: '95vw' }"
            :pt="{
                root: { class: 'overflow-hidden rounded-2xl' },
                header: {
                    class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4',
                },
                content: {
                    class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0',
                },
            }"
        >
            <p class="mb-5 text-surface-700 dark:text-surface-300">
                Click below to upload an avatar image for
                {{ realtor.first_name }} {{ realtor.last_name }}.
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

        <!-- CHANGED: added dedicated dialog for sequence note creation -->
        <Dialog
            v-model:visible="showSequenceNoteDialog"
            modal
            :style="{ width: '42rem', maxWidth: '95vw' }"
            :breakpoints="{ '960px': '92vw', '640px': '96vw' }"
            :pt="{
                root: { class: 'overflow-hidden rounded-2xl' },
                header: {
                    class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-6 py-5 text-surface-900 dark:text-surface-0',
                },
                content: {
                    class: 'bg-surface-0 dark:bg-surface-900 px-6 py-6 text-surface-900 dark:text-surface-0',
                },
            }"
            @hide="resetSequenceNoteForm"
        >
            <!-- CHANGED: custom dialog header for sequence note form -->
            <template #header>
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary"
                    >
                        <i class="pi pi-file-edit text-lg" />
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold leading-tight">Add Sequence Note</h2>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Record an update for this sequence step, set the note status, and
                            optionally choose the next update date.
                        </p>
                    </div>
                </div>
            </template>

            <form @submit.prevent="onNoteSubmit" class="space-y-5">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="flex flex-col">
                        <label
                            for="sequence-note-status"
                            class="mb-2 font-medium text-surface-900 dark:text-surface-100"
                        >
                            Status
                        </label>

                        <Select
                            id="sequence-note-status"
                            v-model="notesForm.status"
                            :options="dynamicStatusOptions"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select an option..."
                            fluid
                            :invalid="!!notesForm.errors.status"
                        />

                        <Message
                            v-if="notesForm.errors.status"
                            severity="error"
                            variant="simple"
                            size="small"
                            class="mt-2"
                        >
                            {{ notesForm.errors.status }}
                        </Message>
                    </div>

                    <div class="flex flex-col">
                        <label
                            for="sequence-note-next-update"
                            class="mb-2 font-medium text-surface-900 dark:text-surface-100"
                        >
                            Next Update By
                        </label>

                        <DatePicker
                            id="sequence-note-next-update"
                            v-model="notesForm.next_update"
                            placeholder="Select a date"
                            fluid
                            :invalid="!!notesForm.errors.next_update"
                        />

                        <Message
                            v-if="notesForm.errors.next_update"
                            severity="error"
                            variant="simple"
                            size="small"
                            class="mt-2"
                        >
                            {{ notesForm.errors.next_update }}
                        </Message>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label
                        for="sequence-note-text"
                        class="mb-2 font-medium text-surface-900 dark:text-surface-100"
                    >
                        Note
                    </label>

                    <Textarea
                        id="sequence-note-text"
                        v-model="notesForm.note"
                        class="h-40"
                        rows="6"
                        autoResize
                        fluid
                        placeholder="Enter notes about the conversation, result, objections, next action, or any useful context..."
                        :invalid="!!notesForm.errors.note"
                    />

                    <Message
                        v-if="notesForm.errors.note"
                        severity="error"
                        variant="simple"
                        size="small"
                        class="mt-2"
                    >
                        {{ notesForm.errors.note }}
                    </Message>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-surface-200 dark:border-surface-800 pt-5 sm:flex-row sm:justify-end"
                >
                    <Button
                        label="Cancel"
                        type="button"
                        severity="secondary"
                        outlined
                        @click="showSequenceNoteDialog = false"
                    />
                    <Button
                        label="Save Note"
                        type="submit"
                        icon="pi pi-check-circle"
                        :loading="notesForm.processing"
                    />
                </div>
            </form>
        </Dialog>

        <Toast />
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import PersonalInfoTab from './Components/PersonalInfoTab.vue'
import NotesTab from './Components/NotesTab.vue'
import CommunicationLogTab from './Components/CommunicationLogTab.vue'
import ProfessionalInfoTab from './Components/ProfessionalInfoTab.vue'
import { usePage, Head, useForm, router, Link } from '@inertiajs/vue3'
import {
    Card,
    Button,
    Dialog,
    FileUpload,
    Image,
    Textarea,
    DataTable,
    Column,
    Select,
    Divider,
    Toast,
    DatePicker,
    Message,
    Panel,
    Tag,
} from 'primevue'
import { ref, watch, onMounted, computed } from 'vue'
import { useToast } from 'primevue'
import Tabs from 'primevue/tabs'
import TabList from 'primevue/tablist'
import Tab from 'primevue/tab'
import TabPanels from 'primevue/tabpanels'
import TabPanel from 'primevue/tabpanel'
import { formatCurrency } from '@/utilities/formatCurrency'
import { camelToNormal } from '@/utilities/formatString'

const toast = useToast()
const page = usePage()

const realtor = ref(page.props.realtor)
const sequences = page.props.sequences ?? []
const users = page.props.users ?? []
const selectedSequence = ref(null)
const showAvatarDialog = ref(false)
const viewSequenceDetails = ref(false)
const contactOwner = ref(users.find((user) => realtor.value.owner?.id === user.id) || null)
const activeTab = ref(0)
const selectedActiveSequence = ref(page.props.selected_sequence ?? null)

// CHANGED: added visibility state for sequence note dialog
const showSequenceNoteDialog = ref(false)

const previousInspections = computed(() => {
    return (realtor.value.previous_inspections ?? []).map((inspection) => {
        const rawDate =
            inspection.inspection_date_raw ||
            inspection.inspection_date_iso ||
            inspection.inspection_date ||
            null

        const parsedDate = rawDate ? new Date(rawDate) : null

        return {
            ...inspection,
            inspection_date_sort:
                parsedDate && !Number.isNaN(parsedDate.getTime())
                    ? parsedDate.getTime()
                    : null,
        }
    })
})
const activeSequences = computed(() => realtor.value.active_sequences ?? [])
const totalNotesCount = computed(() => realtor.value.notes?.length ?? 0)
const pinnedNotesCount = computed(
    () => realtor.value.notes?.filter((note) => note.is_pinned)?.length ?? 0
)

// CHANGED: computed current step notes so the view stays clean and reactive
const selectedSequenceStepNotes = computed(() => {
    const notes = selectedActiveSequence.value?.current_step?.notes ?? []
    return [...notes].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

const tabs = computed(() => [
    { value: 0, name: 'Personal', icon: 'pi pi-user', count: null, urlParam: 'personal' },
    {
        value: 1,
        name: 'Professional',
        icon: 'pi pi-briefcase',
        count: null,
        urlParam: 'professional',
    },
    {
        value: 2,
        name: 'Metrics',
        icon: 'pi pi-chart-bar',
        count: previousInspections.value.length,
        urlParam: 'metrics',
    },
    {
        value: 3,
        name: 'Communications',
        icon: 'pi pi-comments',
        count: realtor.value.communications?.length ?? 0,
        urlParam: 'communications',
    },
    {
        value: 4,
        name: 'Sequences',
        icon: 'pi pi-directions-alt',
        count: activeSequences.value.length,
        urlParam: 'sequences',
    },
    {
        value: 5,
        name: 'Notes',
        icon: 'pi pi-file-edit',
        count: totalNotesCount.value,
        urlParam: 'notes',
    },
])

const dynamicStatusOptions = [
    { label: 'No Response', value: 'no_response' },
    { label: 'Left Voicemail', value: 'left_voicemail' },
    { label: 'Spoke to Contact', value: 'spoke_to_contact' },
    { label: 'Follow Up Needed', value: 'follow_up_needed' },
    { label: 'Completed', value: 'completed' },
]

const notesForm = useForm({
    realtor_id: realtor.value.id,
    sequence_id: '',
    step_id: '',
    status: null,
    next_update: '',
    note: '',
})

const addToSequenceForm = useForm({
    realtor_id: realtor.value.id,
    sequence_id: selectedSequence.value?.id,
    start_date: '',
})

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)

    const tabKey = urlParams.get('tab')
    if (tabKey) {
        const matchedTab = tabs.value.find((tab) => tab.urlParam === tabKey)
        if (matchedTab) {
            activeTab.value = matchedTab.value
        }
    }

    if (urlParams.get('viewSequence') == 1) {
        const sequenceId = Number(urlParams.get('sequenceId'))

        selectedActiveSequence.value =
            activeSequences.value.find((sequence) => sequence.id === sequenceId) || null

        if (selectedActiveSequence.value) {
            viewSequenceDetails.value = true
            activeTab.value = 4
        }
    }
})

watch(activeTab, (newValue) => {
    const activeTabItem = tabs.value.find((tab) => tab.value === newValue)
    if (!activeTabItem) return

    const url = new URL(window.location.href)
    url.searchParams.set('tab', activeTabItem.urlParam)

    if (!viewSequenceDetails.value) {
        url.searchParams.delete('viewSequence')
        url.searchParams.delete('sequenceId')
    }

    window.history.replaceState({}, '', url.toString())
})

watch(contactOwner, async (newVal, oldVal) => {
    if (!newVal || newVal?.id === oldVal?.id) return

    router.put(route('realtor.update', realtor.value.id), {
        request_type: 'update_owner',
        owner: newVal.id,
    })
})

watch(selectedActiveSequence, (newValue) => {
    if (newValue && newValue.id) {
        notesForm.sequence_id = newValue.id
        notesForm.next_update = newValue.due_by ?? ''
        notesForm.step_id = newValue.current_step?.id || ''

        const url = new URL(window.location.href)
        url.searchParams.set('tab', 'sequences')
        url.searchParams.set('viewSequence', '1')
        url.searchParams.set('sequenceId', String(newValue.id))
        window.history.replaceState({}, '', url.toString())
    } else {
        notesForm.sequence_id = ''
        notesForm.step_id = ''
    }
})

const getTagSeverity = (status) => {
    if (status === 'Active') return 'success'
    if (status === 'Inactive') return 'danger'
    if (status === 'Engaged') return 'warn'
    if (status === 'New') return 'info'
    return null
}

const formatLabel = (value) => {
    if (!value) return 'N/A'
    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}

const goToTab = (tabValue) => {
    activeTab.value = tabValue
    viewSequenceDetails.value = false
    selectedActiveSequence.value = null
    // CHANGED: close sequence note dialog if user navigates away
    showSequenceNoteDialog.value = false
}

const onViewActiveSequence = (sequence) => {
    viewSequenceDetails.value = true
    selectedActiveSequence.value = sequence
    activeTab.value = 4
}

// CHANGED: helper to open dialog only when a valid sequence step exists
const openSequenceNoteDialog = () => {
    if (!selectedActiveSequence.value?.id) return

    notesForm.sequence_id = selectedActiveSequence.value.id
    notesForm.step_id = selectedActiveSequence.value.current_step?.id || ''
    notesForm.next_update = selectedActiveSequence.value.due_by ?? ''
    showSequenceNoteDialog.value = true
}

// CHANGED: centralized reset for sequence note dialog
const resetSequenceNoteForm = () => {
    notesForm.reset('status', 'next_update', 'note')
    notesForm.clearErrors()
    notesForm.sequence_id = selectedActiveSequence.value?.id || ''
    notesForm.step_id = selectedActiveSequence.value?.current_step?.id || ''
}

const onBackToMainView = () => {
    viewSequenceDetails.value = false
    selectedActiveSequence.value = null
    notesForm.reset()
    activeTab.value = 4
    // CHANGED: ensure dialog closes when leaving detail mode
    showSequenceNoteDialog.value = false

    const url = new URL(window.location.href)
    url.searchParams.set('tab', 'sequences')
    url.searchParams.delete('viewSequence')
    url.searchParams.delete('sequenceId')
    window.history.replaceState({}, '', url.toString())
}

const onUpload = async (event) => {
    const file = event.files[0]

    if (!file) {
        console.error('No file selected.')
        return
    }

    const formData = new FormData()
    formData.append('avatar', file)

    router.post(route('realtor.upload-avatar', { id: realtor.value.id }), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
        only: ['flash', 'realtor'],
        onSuccess: (response) => {
            realtor.value = response.props.realtor
            showAvatarDialog.value = false

            toast.add({
                severity: 'success',
                summary: 'Avatar Updated',
                detail: 'The avatar was uploaded successfully.',
                life: 3000,
            })
        },
        onError: (error) => {
            console.error(error)
        },
    })
}

const onAddToSequenceSubmit = () => {
    addToSequenceForm.sequence_id = selectedSequence.value?.id || ''

    addToSequenceForm.post(route('realtor-sequence.store'), {
        preserveState: true,
        preserveScroll: true,
        only: ['realtor', 'flash'],
        onSuccess: (response) => {
            realtor.value = response.props.realtor
            addToSequenceForm.reset()
            selectedSequence.value = null

            toast.add({
                severity: 'success',
                summary: 'Sequence Added',
                detail: 'The realtor was added to the selected sequence.',
                life: 3000,
            })
        },
        onError: (error) => {
            console.log(error)
        },
    })
}

const onNextSequenceStep = () => {
    if (!selectedActiveSequence.value?.id) return

    router.put(
        route('realtor-sequence.update', {
            realtor_sequence: selectedActiveSequence.value.id,
        }),
        {
            request_type: 'increment_step',
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['flash', 'realtor'],
            onSuccess: (response) => {
                realtor.value = response.props.realtor

                const sequenceId = selectedActiveSequence.value.id

                if (!activeSequences.value.length) {
                    selectedSequence.value = null
                    selectedActiveSequence.value = null
                    viewSequenceDetails.value = false
                    activeTab.value = 4
                    showSequenceNoteDialog.value = false
                } else {
                    selectedActiveSequence.value =
                        activeSequences.value.find((sequence) => sequence.id === sequenceId) || null
                }

                toast.add({
                    severity: 'success',
                    summary: 'Sequence Updated',
                    detail: 'The sequence step was updated successfully.',
                    life: 3000,
                })
            },
            onError: (error) => {
                console.error(error)
            },
        }
    )
}

const onUpdateRealtor = (updatedRealtor) => {
    realtor.value = updatedRealtor

    contactOwner.value = users.find((user) => user.id === updatedRealtor.owner?.id) || null
}

const onNoteSubmit = () => {
    notesForm.post(route('realtor-sequence-note.store'), {
        preserveState: true,
        preserveScroll: true,
        only: ['flash', 'realtor', 'selected_sequence'],
        onSuccess: (response) => {
            realtor.value = response.props.realtor ?? realtor.value

            if (selectedActiveSequence.value?.id) {
                selectedActiveSequence.value =
                    activeSequences.value.find(
                        (sequence) => sequence.id === selectedActiveSequence.value.id
                    ) ?? selectedActiveSequence.value
            }

            // CHANGED: close dialog and reset form after successful note creation
            resetSequenceNoteForm()
            showSequenceNoteDialog.value = false

            toast.add({
                severity: 'success',
                summary: 'Note Added',
                detail: 'The sequence note was added successfully.',
                life: 3000,
            })
        },
        onError: (error) => {
            console.error(error)
        },
    })
}
</script>
