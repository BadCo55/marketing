<template>
    <Head title="Realtors" />
    <Layout>
        <div
            class="flex flex-col"
            :style="{
                width: isLargeBreakpoint ? 'calc(100vw - 200px)' : '100vw'
            }"
        >
            <DataTable
                v-model:filters="filters"
                :value="realtors"
                paginator
                scrollable
                :scrollHeight="`calc(100vh - 260px)`"
                stripedRows
                :rows="10"
                :rowsPerPageOptions="[10, 25, 50, 100]"
                :globalFilterFields="['first_name', 'last_name', 'phone_number', 'email', 'office.parent_company', 'office.office_name', 'office.street_address', 'office.city', 'office.zip_code']"
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
                                    <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                                </IconField>
                            </div>
                            <!-- <div class="flex gap-2 items-center">
                                <Select :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Select a status..." />
                                <Select :options="users" optionLabel="name" optionValue="id" placeholder="Select an assigned user..." />
                                <div class="flex items-center gap-1">
                                    <Checkbox :binary="true"  />
                                    <label for="">Has Due Date</label>
                                </div>
                            </div> -->
                        </div>
                        <div>
                            <Link
                                :href="route('realtor.create')"
                            >
                                <Button severity="primary" label="Create Realtor" icon="pi pi-plus-circle" />
                            </Link>
                        </div>
                    </div>
                </template>
                <template #empty> No customers found. </template>
                <template #loading> Loading customers data. Please wait. </template>
                <!-- <Column field="id" header="ID" sortable class="font-bold"></Column> -->
                <Column v-if="isLargeBreakpoint">
                    <template #body="slotProps">
                        <div class="flex flex-col items-center gap-2 justify-center">
                            <img :src="slotProps.data.avatar_url" class="w-20 rounded-full" />
                            <Tag :value="slotProps.data.status" :severity="getTagSeverity(slotProps.data.status)" />
                        </div>
                    </template>
                </Column>
                <Column field="formatted_full_name" header="Name" sortable></Column>
                <Column field="phone_number" header="Phone #" sortable></Column>
                <Column field="email" header="Email" sortable v-if="isLargeBreakpoint"></Column>
                <Column header="Office">
                    <template #body="slotProps">
                        <div v-if="slotProps.data.office" class="flex flex-col">
                            <p class="font-bold">{{ slotProps.data.office.parent_company || 'N/A' }}</p>
                            <p v-if="slotProps.data.office.office_name">{{ slotProps.data.office.office_name }}</p>
                            <p>{{ slotProps.data.office.street_address || 'N/A' }} <span v-if="slotProps.data.office.unit_number">#{{ slotProps.data.office.unit_number }}</span></p>
                            <p>{{ slotProps.data.office.city || 'N/A' }}, {{ slotProps.data.office.zip_code || 'N/A' }}</p>
                        </div>
                        <div v-else>
                            <p class="text-gray-500">No office assigned</p>
                        </div>
                    </template>
                </Column>
                <Column field="formatted_value" header="Value" sortable v-if="isLargeBreakpoint"></Column>
                <Column class="w-12 !text-center" alignFrozen="right" :frozen="true">
                    <template #body="{ data }">
                        <Link
                            :href="route('realtor.show', data.id)"
                        >
                            <Button icon="pi pi-search" severity="primary" rounded></Button>
                        </Link>
                    </template>
                </Column>
            </DataTable>
        </div>

    </Layout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { Button, DataTable, Column, IconField, InputIcon, InputText, Tag } from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';
import { ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const realtors = page.props.realtors;

// Detect if the screen is at least a large breakpoint (lg)
const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024; // lg breakpoint in Tailwind
};

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

onMounted(() => {
    checkBreakpoint(); // Initial check
    window.addEventListener('resize', checkBreakpoint); // Update on resize
});

// Clean up the event listener on component unmount
onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});

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
});

</script>

