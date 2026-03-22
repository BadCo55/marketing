<template>
    <Head title="Office Visits" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col">
            <!-- PAGE HEADER -->
            <div class="mb-5 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Office Visits</h1>
                        <p class="mt-1 text-sm text-surface-500">
                            Search, review, and manage visit history across all offices.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link :href="route('office-visit.create')">
                            <Button
                                label="Schedule a Visit"
                                icon="pi pi-calendar"
                                severity="secondary"
                                outlined
                                disabled
                            />
                        </Link>

                        <Link :href="route('office-visit.create')">
                            <Button
                                label="Log a New Visit"
                                icon="pi pi-map-marker"
                            />
                        </Link>
                    </div>
                </div>

                <!-- CHANGED: Added compact summary row -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-surface-500">Total Visits</p>
                        <p class="mt-1 text-xl font-semibold">{{ visits.length }}</p>
                    </div>

                    <div class="rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-surface-500">Follow-Up Required</p>
                        <p class="mt-1 text-xl font-semibold">{{ followUpCount }}</p>
                    </div>

                    <div class="rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <p class="text-xs font-medium uppercase tracking-wide text-surface-500">Users Logged</p>
                        <p class="mt-1 text-xl font-semibold">{{ users.length }}</p>
                    </div>
                </div>
            </div>

            <!-- TABLE SHELL -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div
                    class="flex h-full min-h-0 flex-col overflow-hidden rounded-2xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <DataTable
                        v-model:filters="filters"
                        :value="visits"
                        paginator
                        scrollable
                        :scrollHeight="scrollHeight"
                        stripedRows
                        :rows="10"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        :globalFilterFields="[
                            'office.parent_company',
                            'office.office_name',
                            'created_by.name',
                            'formatted_visit_date',
                            'formatted_purpose',
                            'formatted_duration',
                            'formatted_follow_up_required',
                            'formatted_next_action_date'
                        ]"
                        tableStyle="min-width: 70rem"
                        class="h-full"
                        @row-click="onRowClick"
                        :pt="{
                            bodyRow: { class: 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors' }
                        }"
                    >
                        <template #header>
                            <div class="flex flex-col gap-4 p-1">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-xl font-semibold">Visit Directory</h2>
                                    <p class="text-sm text-surface-500">
                                        Filter by keyword, visit owner, or whether follow-up is required.
                                    </p>
                                </div>

                                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:min-w-[48rem]">
                                        <div>
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>
                                                <InputText
                                                    fluid
                                                    v-model="filters.global.value"
                                                    placeholder="Search visits..."
                                                />
                                            </IconField>
                                        </div>

                                        <div>
                                            <Select
                                                fluid
                                                :options="users"
                                                optionLabel="name"
                                                optionValue="name"
                                                placeholder="Filter by user..."
                                                v-model="filters.created_by_name.value"
                                                showClear
                                            />
                                        </div>

                                        <div class="flex h-full items-center gap-3 rounded-xl border border-surface-200 bg-surface-50 px-4 py-2.5 dark:border-surface-700 dark:bg-surface-800">
                                            <Checkbox
                                                inputId="follow-up-checkbox"
                                                v-model="filters.follow_up_required.value"
                                                binary
                                            />
                                            <label
                                                for="follow-up-checkbox"
                                                class="text-sm text-surface-700 dark:text-surface-300"
                                            >
                                                Follow-Up Required
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="text-sm text-surface-500">
                                            {{ visits.length }} {{ visits.length === 1 ? 'visit' : 'visits' }}
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
                                <i class="pi pi-map-marker text-3xl text-surface-400" />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No visits found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500">
                                    Try adjusting your filters or log a new office visit to get started.
                                </p>

                                <Link :href="route('office-visit.create')">
                                    <Button
                                        label="Log a New Visit"
                                        icon="pi pi-plus-circle"
                                    />
                                </Link>
                            </div>
                        </template>

                        <template #loading>
                            Loading visit data. Please wait.
                        </template>

                        <Column
                            header="Date"
                            field="formatted_visit_date"
                            sortable
                            headerClass="w-[9rem]"
                            bodyClass="w-[9rem]"
                        />

                        <Column
                            header="Office"
                            sortable
                            headerClass="w-[16rem]"
                            bodyClass="w-[16rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">{{ data.office?.parent_company || '—' }}</p>
                                    <p
                                        v-if="data.office?.office_name"
                                        class="text-sm text-surface-500"
                                    >
                                        {{ data.office.office_name }}
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <Column
                            v-if="isLargeBreakpoint"
                            header="Created By"
                            field="created_by.name"
                            sortable
                            headerClass="w-[11rem]"
                            bodyClass="w-[11rem]"
                        >
                            <template #body="{ data }">
                                <span>{{ data.created_by?.name || '—' }}</span>
                            </template>
                        </Column>

                        <Column
                            v-if="isLargeBreakpoint"
                            header="Purpose"
                            field="formatted_purpose"
                            sortable
                            headerClass="w-[11rem]"
                            bodyClass="w-[11rem]"
                        />

                        <Column
                            v-if="isLargeBreakpoint"
                            header="Duration"
                            field="formatted_duration"
                            sortable
                            headerClass="w-[8rem]"
                            bodyClass="w-[8rem]"
                        />

                        <Column
                            header="Rating"
                            field="rating"
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <div class="inline-flex items-center gap-2">
                                    <Rating v-model="data.rating" readonly />
                                    <span class="text-sm text-surface-500">{{ data.rating ?? 0 }}</span>
                                </div>
                            </template>
                        </Column>

                        <Column
                            header="Interest"
                            field="interest_level"
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <div class="inline-flex items-center gap-2">
                                    <Rating v-model="data.interest_level" readonly />
                                    <span class="text-sm text-surface-500">{{ data.interest_level ?? 0 }}</span>
                                </div>
                            </template>
                        </Column>

                        <Column
                            header="Follow-Up"
                            field="follow_up_required"
                            sortable
                            headerClass="w-[11rem]"
                            bodyClass="w-[11rem]"
                        >
                            <template #body="{ data }">
                                <div
                                    class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium"
                                    :class="
                                        data.follow_up_required
                                            ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300'
                                            : 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300'
                                    "
                                >
                                    <i
                                        class="pi"
                                        :class="data.follow_up_required ? 'pi-check-circle' : 'pi-times-circle'"
                                    ></i>
                                    {{ data.formatted_follow_up_required }}
                                </div>
                            </template>
                        </Column>

                        <Column
                            header="Follow-Up Due"
                            field="formatted_next_action_date"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <span>{{ data.formatted_next_action_date || '—' }}</span>
                            </template>
                        </Column>

                        <!-- CHANGED: Removed view button, kept subtle chevron -->
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
import {
    Button,
    DataTable,
    Column,
    IconField,
    InputText,
    InputIcon,
    Checkbox,
    Rating,
    Select,
} from 'primevue';
import { FilterMatchMode } from '@primevue/core/api';
import { Link, Head, usePage, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

const page = usePage();
const visits = ref(page.props.visits ?? []);
const users = page.props.users ?? [];

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

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    follow_up_required: { value: null, matchMode: FilterMatchMode.EQUALS },

    // CHANGED: Use a distinct key instead of created_by object
    created_by_name: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const followUpCount = computed(() => {
    return visits.value.filter((visit) => visit.follow_up_required).length;
});

const scrollHeight = computed(() => 'flex');

const onClearFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        filters.value[key].value = null;
    });
};

const onRowClick = (event) => {
    router.visit(route('office-visit.show', event.data.id));
};
</script>
