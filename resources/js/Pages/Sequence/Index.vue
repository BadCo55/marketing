<template>
    <Head title="Sequences" />
    <Layout>
        <DataTable
            :value="sequences"
            removableSort
            stripedRows
            showGridlines
            :scrollHeight="`calc(100vh - 260px)`"
            paginator
            :filters="filters"
            :rows="10"
            :rowsPerPageOptions="[10, 25, 50, 100]"
            :globalFilterFields="['name', 'target_audience', 'description']"
        >
            <template #header>
                <div class="flex justify-end">

                </div>
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
                                <label for="" class="dark:text-surface-400">Has Due Date</label>
                            </div>
                        </div> -->
                    </div>
                    <div>
                        <Link
                            :href="route('sequence.create')"
                        >
                            <Button severity="primary" label="Create Sequence" icon="pi pi-plus-circle" />
                        </Link>
                    </div>
                </div>
            </template>
            <template #empty> No sequences found. </template>
            <template #loading> Loading sequence data. Please wait. </template>
            <Column field="name" header="Name" sortable></Column>
            <Column field="description" header="Description"></Column>
            <Column field="target_audience" header="Target Audience" sortable></Column>
            <Column field="step_count" header="# of Steps"></Column>
            <Column class="w-24 !text-center">
                <template #body="{ data }">
                    <Link
                        :href="route('sequence.show', data.id)"
                    >
                        <Button icon="pi pi-search" severity="primary" rounded></Button>
                    </Link>
                </template>
            </Column>
        </DataTable>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { DataTable, Button, Column, IconField, InputText, InputIcon, Select, Checkbox } from 'primevue';
import { reactive } from 'vue';

const page = usePage();
const sequences = page.props.sequences;

const filters = reactive({
    global: { value: null }, // Global filter for all searchable fields
});
</script>
