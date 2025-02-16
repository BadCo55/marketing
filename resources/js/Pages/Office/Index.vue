<template>
    <Head title="Offices" />
    <Layout>
        <DataTable
            v-model:filters="filters"
            :value="offices"
            removableSort
            :scrollHeight="`calc(100vh - 260px)`"
            class="w-full"
            stripedRows
            paginator
            :rows="10"
            :rowsPerPageOptions="[10, 25, 50, 100]"
            :globalFilterFields="['parent_company', 'office_name', 'street_address']"
        >
            <template #header>
                <h2 class="text-xl font-medium mb-2">Filters</h2>
                <div class="flex items-center justify-between">
                    <div class="flex gap-2 bg-surface-100 dark:bg-surface-800 p-2 rounded">
                        <div>
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText
                                    v-model="filters['global'].value"
                                    placeholder="Keyword Search"
                                />
                            </IconField>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button
                            severity="primary"
                            label="Upload Office Data"
                            icon="pi pi-file-arrow-up"
                            @click="isUploadDialogVisible = true"
                        />
                        <Button
                            severity="primary"
                            label="Create Office"
                            @click="isAddOfficeModalVisible = true"
                            icon="pi pi-plus-circle"
                        />
                    </div>
                </div>
            </template>
            <template #empty> No offices found. </template>
            <template #loading> Loading office data. Please wait. </template>
            <!-- <Column field="id" header="ID" sortable class="font-bold"></Column> -->
            <Column v-if="isLargeBreakpoint">
                <template #body="slotProps">
                    <div class="flex flex-col items-center gap-2 justify-center">
                            <img :src="slotProps.data.image_url" class="w-20 rounded-lg" />
                            <Tag :value="slotProps.data.status" :severity="getTagSeverity(slotProps.data.status)" />
                        </div>
                </template>
            </Column>
            <Column field="parent_company" header="Company" sortable></Column>
            <Column field="office_name" header="Office Name" sortable></Column>
            <Column field="office_phone" header="Phone #" sortable></Column>
            <Column header="Address" sortable>
                <template #body="slotProps">
                    <div class="flex flex-col">
                        <p class="font-bold">
                            {{ slotProps.data.street_address }}
                            <span v-if="slotProps.data.unit_number">{{
                                '#' + slotProps.data.unit_number
                            }}</span>
                        </p>
                        <p>
                            {{ slotProps.data.city }}, {{ slotProps.data.state }}
                            {{ slotProps.data.zip_code }}
                        </p>
                        <p>{{ slotProps.data.county }}</p>
                    </div>
                </template>
            </Column>
            <Column class="w-24 !text-center">
                <template #body="{ data }">
                    <Link :href="route('office.show', data.id)">
                        <Button icon="pi pi-search" severity="primary" rounded></Button>
                    </Link>
                </template>
            </Column>
        </DataTable>
    </Layout>
    <!-- Add New Office Modal -->
    <Dialog header="Add New Office" v-model:visible="isAddOfficeModalVisible" class="w-[35rem]">
        <NewOfficeForm @updatedOffices="onNewOfficeSubmit" />
    </Dialog>
    <UploadDialog v-model:visible="isUploadDialogVisible" />
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/Layout.vue'
import NewOfficeForm from '@/Components/NewOfficeForm.vue'
import UploadDialog from './Components/UploadDialog.vue'
import {
    Button,
    DataTable,
    Column,
    IconField,
    InputIcon,
    InputText,
    Dialog,
    Tag
} from 'primevue'
import { FilterMatchMode, FilterOperator } from '@primevue/core/api'
import { ref, onMounted, onUnmounted } from 'vue'

const page = usePage()
const offices = ref(page.props.offices)
const isAddOfficeModalVisible = ref(false)
const isUploadDialogVisible = ref(false)

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    first_name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    last_name: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    phone_number: { value: null, matchMode: FilterMatchMode.CONTAINS },
    email: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'office.parent_company': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'office.office_name': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'office.street_address': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    'office.zip_code': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
})

const onNewOfficeSubmit = (data) => {
    offices.value = data
    isAddOfficeModalVisible.value = false
}

// Detect if the screen is at least a large breakpoint (lg)
const isLargeBreakpoint = ref(false)

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024 // lg breakpoint in Tailwind
}

onMounted(() => {
    checkBreakpoint() // Initial check
    window.addEventListener('resize', checkBreakpoint) // Update on resize
})

// Clean up the event listener on component unmount
onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint)
})

const getTagSeverity = (status) => {
    if (status === 'Active') {
        return 'success';
    } else if (status === 'Inactive') {
        return 'danger';
    } else if (status === 'Engaged') {
        return 'warn';
    } else if (status === 'New') {
        return 'info'
    }
    return 'info';
}
</script>
