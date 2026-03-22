<template>
    <Head title="Offices" />

    <Layout>
        <!-- CHANGED: Rebuilt page to rely on Layout sizing instead of viewport math -->
        <div class="flex h-full min-h-0 flex-col">
            <!-- PAGE HEADER -->
            <div class="mb-5 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Offices</h1>
                        <p class="mt-1 text-sm text-surface-500">
                            Search, review, create, and upload office records.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button
                            label="Upload Office Data"
                            icon="pi pi-file-arrow-up"
                            severity="secondary"
                            outlined
                            @click="isUploadDialogVisible = true"
                        />
                        <Button
                            label="Create Office"
                            icon="pi pi-plus-circle"
                            @click="isAddOfficeModalVisible = true"
                        />
                    </div>
                </div>
            </div>

            <!-- TABLE AREA -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div
                    class="flex h-full min-h-0 flex-col overflow-hidden rounded-2xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <DataTable
                        v-model:filters="filters"
                        :value="offices"
                        removableSort
                        scrollable
                        :scrollHeight="scrollHeight"
                        class="h-full"
                        stripedRows
                        paginator
                        :rows="10"
                        :rowsPerPageOptions="[10, 25, 50, 100]"
                        :globalFilterFields="[
                            'parent_company',
                            'office_name',
                            'street_address',
                            'city',
                            'zip_code',
                        ]"
                        tableStyle="min-width: 72rem"
                        @row-click="onRowClick"
                        :pt="{
                            bodyRow: {
                                class: 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors',
                            },
                        }"
                    >
                        <template #header>
                            <div class="flex flex-col gap-4 p-1">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-xl font-semibold">Office Directory</h2>
                                    <p class="text-sm text-surface-500">
                                        Search by company, office name, address, city, or ZIP code.
                                    </p>
                                </div>

                                <div
                                    class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"
                                >
                                    <div class="w-full md:max-w-md">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText
                                                fluid
                                                v-model="filters.global.value"
                                                placeholder="Search offices..."
                                            />
                                        </IconField>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="text-sm text-surface-500">
                                            {{ offices.length }}
                                            {{ offices.length === 1 ? 'office' : 'offices' }}
                                        </div>

                                        <Button
                                            label="Clear"
                                            severity="secondary"
                                            outlined
                                            @click="onClearFilters"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #empty>
                            <div
                                class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center"
                            >
                                <i class="pi pi-building text-3xl text-surface-400" />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No offices found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500">
                                    Try adjusting your search or add a new office to get started.
                                </p>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <Button
                                        label="Create Office"
                                        icon="pi pi-plus-circle"
                                        @click="isAddOfficeModalVisible = true"
                                    />
                                    <Button
                                        label="Upload Office Data"
                                        icon="pi pi-file-arrow-up"
                                        severity="secondary"
                                        outlined
                                        @click="isUploadDialogVisible = true"
                                    />
                                </div>
                            </div>
                        </template>

                        <template #loading> Loading office data. Please wait. </template>

                        <!-- CHANGED: Image/status column narrowed and cleaned up -->
                        <Column
                            v-if="isLargeBreakpoint"
                            headerClass="w-[7rem]"
                            bodyClass="w-[7rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col items-center gap-2 py-2">
                                    <div
                                        class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <img
                                            v-if="data.image_url"
                                            :src="data.image_url"
                                            class="h-14 w-14 object-cover"
                                            alt="Office"
                                        />
                                        <i v-else class="pi pi-building text-surface-400"></i>
                                    </div>

                                    <Tag
                                        :value="data.status"
                                        :severity="getTagSeverity(data.status)"
                                    />
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: Company made primary column -->
                        <Column
                            field="parent_company"
                            header="Company"
                            sortable
                            headerClass="w-[18rem]"
                            bodyClass="w-[18rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">{{ data.parent_company }}</p>
                                    <p v-if="data.office_name" class="text-sm text-surface-500">
                                        {{ data.office_name }}
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: Phone column reduced -->
                        <Column
                            v-if="isLargeBreakpoint"
                            field="office_phone"
                            header="Phone #"
                            sortable
                            headerClass="w-[11rem]"
                            bodyClass="w-[11rem]"
                        >
                            <template #body="{ data }">
                                {{ data.office_phone || '—' }}
                            </template>
                        </Column>

                        <!-- CHANGED: Address column no longer wastes excessive width -->
                        <Column header="Address" headerClass="w-[18rem]" bodyClass="w-[18rem]">
                            <template #body="{ data }">
                                <div class="flex flex-col gap-0.5">
                                    <p class="font-medium">
                                        {{ data.street_address || '—' }}
                                        <span v-if="data.unit_number">
                                            #{{ data.unit_number }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-surface-600">
                                        {{ data.city || '—' }}, {{ data.state || '—' }}
                                        {{ data.zip_code || '' }}
                                    </p>
                                    <p v-if="data.county" class="text-sm text-surface-500">
                                        {{ data.county }}
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: Added compact metrics column instead of extra wide columns -->
                        <Column
                            v-if="isLargeBreakpoint"
                            header="Summary"
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col gap-1 text-sm">
                                    <p>
                                        <span class="text-surface-500">Agents:</span>
                                        <span class="font-medium">
                                            {{ data.realtor_count ?? 0 }}</span
                                        >
                                    </p>
                                    <p>
                                        <span class="text-surface-500">Active:</span>
                                        <span class="font-medium">
                                            {{ data.active_agents_count ?? 0 }}</span
                                        >
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <!-- CHANGED: Row click instead of view button -->
                        <Column headerClass="w-[4rem]" bodyClass="w-[4rem]">
                            <template #body>
                                <div class="flex justify-center">
                                    <i class="pi pi-angle-right text-surface-400"></i>
                                </div>
                            </template>
                        </Column>

                        <template #paginatorstart>
                            <div class="text-sm text-surface-500">{{ offices.length }} total</div>
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>
    </Layout>

    <Dialog
        header="Add New Office"
        v-model:visible="isAddOfficeModalVisible"
        :style="{ width: '55rem', maxWidth: '95vw' }"
    >
        <NewOfficeForm @updatedOffices="onNewOfficeSubmit" />
    </Dialog>

    <UploadDialog v-model:visible="isUploadDialogVisible" />
</template>

<script setup>
import { Head, usePage, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/Layout.vue'
import NewOfficeForm from '@/Components/NewOfficeForm.vue'
import UploadDialog from './Components/UploadDialog.vue'
import { Button, DataTable, Column, IconField, InputIcon, InputText, Dialog, Tag } from 'primevue'
import { FilterMatchMode } from '@primevue/core/api'
import { ref, onMounted, onUnmounted, computed } from 'vue'

const page = usePage()
const offices = ref(page.props.offices ?? [])
const isAddOfficeModalVisible = ref(false)
const isUploadDialogVisible = ref(false)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

const onNewOfficeSubmit = (data) => {
    offices.value = data
    isAddOfficeModalVisible.value = false
}

const onClearFilters = () => {
    filters.value.global.value = null
}

const isLargeBreakpoint = ref(false)

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024
}

onMounted(() => {
    checkBreakpoint()
    window.addEventListener('resize', checkBreakpoint)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint)
})

const getTagSeverity = (status) => {
    if (status === 'Active') return 'success'
    if (status === 'Inactive') return 'danger'
    if (status === 'Engaged') return 'warn'
    if (status === 'New') return 'info'
    return 'info'
}

const scrollHeight = computed(() => 'flex')

// CHANGED: Row clicks navigate instead of action button
const onRowClick = (event) => {
    router.visit(route('office.show', event.data.id))
}
</script>
