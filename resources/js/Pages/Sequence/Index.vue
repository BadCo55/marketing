<template>
    <Head title="Sequences" />
    <Layout>
        <div
            class="flex flex-col"
            :style="{
                width: isLargeBreakpoint ? 'calc(100vw - 200px)' : '100vw',
            }"
        >
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
                    <h2 class="text-xl font-medium mb-2 text-center md:text-start">Filters</h2>
                    <div class="flex items-center justify-between flex-col md:flex-row">
                        <div class="flex gap-2 bg-surface-100 dark:bg-surface-800 p-2 rounded w-full md:w-auto">
                            <div class="w-full md:w-auto">
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText fluid v-model="filters['global'].value" placeholder="Keyword Search" />
                                </IconField>
                            </div>
                        </div>
                        <div class="w-full md:w-auto mt-3 md:mt-0">
                            <Link
                                :href="route('sequence.create')"
                            >
                                <Button fluid severity="primary" label="Create Sequence" icon="pi pi-plus-circle" />
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
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { DataTable, Button, Column, IconField, InputText, InputIcon, Select, Checkbox } from 'primevue';
import { reactive, onMounted, onUnmounted, ref } from 'vue';

const page = usePage();
const sequences = page.props.sequences;

const filters = reactive({
    global: { value: null }, // Global filter for all searchable fields
});

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
</script>
