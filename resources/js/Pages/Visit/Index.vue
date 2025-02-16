<template>
    <Head title="Office Visits" />
    <Layout>
        <div
            class="flex flex-col"
            :style="{
                height: 'calc(100vh - 80px)',
                width: isLargeBreakpoint ? 'calc(100vw - 200px)' : '100vw'
            }"
        >
            <DataTable
                v-model:filters="filters"
                :value="visits"
                paginator
                scrollable
                :scrollHeight="`calc(100vh - 260px)`"
                showGridlines
                stripedRows
                :rows="10"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                :globalFilterFields="['created_by', 'formatted_visit_date', 'formatted_purpose', 'rating', 'interest_level', 'formatted_follow_up_required', 'formatted_next_action_date']"
            >
                <template #header>
                    <h2 class="text-xl font-medium mb-2">Filters</h2>
                    <div class="flex items-center justify-between">
                        <div class="flex gap-2 bg-surface-100 dark:bg-surface-800 p-2 rounded">
                            <IconField>
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                            </IconField>
                            <div class="flex">
                                <Select :options="users" optionLabel="name" optionValue="name" placeholder="Select a user..." v-model="filters['created_by']" />
                            </div>
                            <div class="flex items-center gap-2">
                                <Checkbox v-model="filters['follow_up_required'].value" binary />
                                <label for="follow-up-checkbox" class="dark:text-surface-400">Follow-Up Required</label>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                :href="route('office-visit.create')"
                            >
                                <Button disabled label="Schedule a Visit" icon="pi pi-calendar" />
                            </Link>
                            <Link
                                :href="route('office-visit.create')"
                            >
                                <Button label="Log a New Visit" icon="pi pi-map-marker" />
                            </Link>
                        </div>
                    </div>
                </template>
                <template #empty>
                    No visits found.
                </template>
                <Column header="ID" field="id"></Column>
                <Column header="Date" field="formatted_visit_date"></Column>
                <Column header="Office">
                    <template #body="{ data }">
                        {{ data.office.parent_company }} <span v-if="data.office.office_name">({{ data.office.office_name }})</span>
                    </template>
                </Column>
                <Column v-if="isLargeBreakpoint" header="Created By" field="created_by.name"></Column>
                <Column v-if="isLargeBreakpoint" header="Purpose" field="formatted_purpose"></Column>
                <Column v-if="isLargeBreakpoint" header="Duration" field="formatted_duration"></Column>
                <Column header="Overall Rating" field="rating">
                    <template #body="{ data }">
                        <Rating v-model="data.rating" readonly />
                    </template>
                </Column>
                <Column header="Interest Level" field="interest_level">
                    <template #body="{ data }">
                        <Rating v-model="data.interest_level" readonly />
                    </template>
                </Column>
                <Column header="Follow-Up Required" field="follow_up_required">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <i
                                class="pi"
                                :class="data.follow_up_required ? 'pi-check-circle text-green-500' : 'pi-times-circle text-red-500'"
                            ></i>
                            {{ data.formatted_follow_up_required }}
                        </div>
                    </template>
                </Column>
                <Column header="Follow-Up Due" field="formatted_next_action_date"></Column>
                <Column alignFrozen="right" :frozen="true">
                    <template #body="{ data }">
                        <Link
                            :href="route('office-visit.show', data.id)"
                        >
                            <Button icon="pi pi-search" rounded />
                        </Link>
                    </template>
                </Column>
            </DataTable>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Button, DataTable, Column, IconField, InputText, InputIcon, Checkbox, Rating, Select } from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';
import { Link, Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const visits = page.props.visits;
const users = page.props.users;

// Detect if the screen is at least a large breakpoint (lg)
const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024; // lg breakpoint in Tailwind
};

onMounted(() => {
    checkBreakpoint(); // Initial check
    window.addEventListener('resize', checkBreakpoint); // Update on resize
});

// Clean up the event listener on component unmount
onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});

/**
 * Filter visits via search
 */
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    follow_up_required: { value: null, matchMode: FilterMatchMode.EQUALS },
    created_by: { value: null, matchMode: FilterMatchMode.EQUALS },
});

</script>
