<template>
    <Head title="Sequences" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col">
            <!-- PAGE HEADER -->
            <div class="mb-5 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Sequences</h1>
                        <p class="mt-1 text-sm text-surface-500">
                            Manage reusable outreach sequences, review audiences, and open them for details.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link :href="route('sequence.create')">
                            <Button
                                label="Create Sequence"
                                icon="pi pi-plus-circle"
                            />
                        </Link>
                    </div>
                </div>

                <!-- CHANGED: compact summary row -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-surface-500">
                            Total Sequences
                        </p>
                        <p class="mt-1 text-xl font-semibold">{{ sequences.length }}</p>
                    </div>

                    <div class="rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-surface-500">
                            Total Steps
                        </p>
                        <p class="mt-1 text-xl font-semibold">{{ totalStepCount }}</p>
                    </div>

                    <div class="rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-surface-500">
                            View
                        </p>
                        <p class="mt-1 text-xl font-semibold">
                            {{ isLargeBreakpoint ? 'Expanded' : 'Compact' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div
                    class="flex h-full min-h-0 flex-col overflow-hidden rounded-2xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <DataTable
                        v-model:filters="filters"
                        :value="sequences"
                        removableSort
                        stripedRows
                        paginator
                        scrollable
                        :scrollHeight="scrollHeight"
                        :rows="10"
                        :rowsPerPageOptions="[10, 25, 50, 100]"
                        :globalFilterFields="['name', 'target_audience', 'description']"
                        tableStyle="min-width: 60rem"
                        class="h-full"
                        @row-click="onRowClick"
                        :pt="{
                            bodyRow: { class: 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors' }
                        }"
                    >
                        <template #header>
                            <div class="flex flex-col gap-4 p-1">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-xl font-semibold">Sequence Directory</h2>
                                    <p class="text-sm text-surface-500">
                                        Search by sequence name, target audience, or description.
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div class="w-full md:max-w-md">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText
                                                fluid
                                                v-model="filters.global.value"
                                                placeholder="Search sequences..."
                                            />
                                        </IconField>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="text-sm text-surface-500">
                                            {{ sequences.length }} {{ sequences.length === 1 ? 'sequence' : 'sequences' }}
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
                            <div class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                                <i class="pi pi-sitemap text-3xl text-surface-400" />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No sequences found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500">
                                    Create your first sequence to start organizing repeatable outreach workflows.
                                </p>
                                <Link :href="route('sequence.create')">
                                    <Button
                                        label="Create Sequence"
                                        icon="pi pi-plus-circle"
                                    />
                                </Link>
                            </div>
                        </template>

                        <template #loading>
                            Loading sequence data. Please wait.
                        </template>

                        <Column
                            field="name"
                            header="Name"
                            sortable
                            headerClass="w-[16rem]"
                            bodyClass="w-[16rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">{{ data.name }}</p>
                                    <p
                                        v-if="!isLargeBreakpoint"
                                        class="text-sm text-surface-500"
                                    >
                                        {{ data.target_audience || 'No audience' }}
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <Column
                            field="description"
                            header="Description"
                            headerClass="w-[20rem]"
                            bodyClass="w-[20rem]"
                        >
                            <template #body="{ data }">
                                <p class="line-clamp-2 text-sm leading-6 text-surface-600 dark:text-surface-300">
                                    {{ data.description || 'No description provided.' }}
                                </p>
                            </template>
                        </Column>

                        <Column
                            v-if="isLargeBreakpoint"
                            field="target_audience"
                            header="Target Audience"
                            sortable
                            headerClass="w-[12rem]"
                            bodyClass="w-[12rem]"
                        >
                            <template #body="{ data }">
                                <span>{{ data.target_audience || '—' }}</span>
                            </template>
                        </Column>

                        <Column
                            field="step_count"
                            header="Steps"
                            sortable
                            headerClass="w-[8rem]"
                            bodyClass="w-[8rem]"
                        >
                            <template #body="{ data }">
                                <span class="inline-flex min-w-10 items-center justify-center rounded-full bg-surface-100 px-3 py-1 text-sm font-medium dark:bg-surface-800">
                                    {{ data.step_count ?? 0 }}
                                </span>
                            </template>
                        </Column>

                        <!-- CHANGED: subtle chevron instead of view button -->
                        <Column
                            headerClass="w-[4rem]"
                            bodyClass="w-[4rem]"
                        >
                            <template #body>
                                <div class="flex justify-center">
                                    <i class="pi pi-angle-right text-surface-400"></i>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import {
    DataTable,
    Button,
    Column,
    IconField,
    InputText,
    InputIcon,
} from 'primevue';
import { reactive, onMounted, onUnmounted, ref, computed } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';

const page = usePage();
const sequences = page.props.sequences ?? [];

const filters = reactive({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024;
};

onMounted(() => {
    checkBreakpoint();
    window.addEventListener('resize', checkBreakpoint);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});

const scrollHeight = computed(() => 'flex');

const totalStepCount = computed(() => {
    return sequences.reduce((sum, sequence) => sum + (Number(sequence.step_count) || 0), 0);
});

const onClearFilters = () => {
    filters.global.value = null;
};

const onRowClick = (event) => {
    router.visit(route('sequence.show', event.data.id));
};
</script>
