<template>
    <Head title="Realtors" />

    <Layout>
        <!-- CHANGED: kept the page as a full-height flex column so the desktop table can fill available space -->
        <div class="flex h-full min-h-0 flex-col">
            <!-- CHANGED: kept the page header but tightened structure and left it shared across mobile + desktop layouts -->
            <div class="mb-6 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1
                            class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0"
                        >
                            Realtors
                        </h1>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Search, review, and manage realtor relationships across your marketing
                            portal.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link :href="route('realtor.create')">
                            <Button label="Create Realtor" icon="pi pi-plus-circle" />
                        </Link>
                    </div>
                </div>

                <!-- CHANGED: kept summary cards, which already stack well on smaller screens -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div
                        class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <p class="mb-1 text-sm text-surface-500 dark:text-surface-400">
                            Total Realtors
                        </p>
                        <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                            {{ realtors.length }}
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <p class="mb-1 text-sm text-surface-500 dark:text-surface-400">
                            Active / Engaged
                        </p>
                        <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                            {{ activeOrEngagedCount }}
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                    >
                        <p class="mb-1 text-sm text-surface-500 dark:text-surface-400">
                            With Office Assigned
                        </p>
                        <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                            {{ assignedOfficeCount }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- CHANGED: converted the content shell into a card-like container that works for both mobile cards and desktop table -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div
                    class="flex h-full min-h-0 flex-col overflow-hidden rounded-2xl border border-surface-200 bg-surface-0 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <!-- CHANGED: moved the header/filter UI outside the desktop-only DataTable so mobile can reuse it -->
                    <div class="border-b border-surface-200 p-4 dark:border-surface-800">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-1">
                                <h2
                                    class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                >
                                    Realtor Directory
                                </h2>
                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                    Search by name, phone, email, or office information.
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
                                            placeholder="Search realtors..."
                                        />
                                    </IconField>
                                </div>

                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <Button
                                        label="Clear Search"
                                        severity="secondary"
                                        outlined
                                        @click="onClearFilters"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CHANGED: added a dedicated mobile card layout instead of forcing the table to be responsive on small screens -->
                    <div v-if="!isLargeBreakpoint" class="min-h-0 flex-1 overflow-y-auto p-4">
                        <template v-if="filteredRealtors.length">
                            <div class="flex flex-col gap-3">
                                <Link
                                    v-for="realtor in filteredRealtors"
                                    :key="realtor.id"
                                    :href="route('realtor.show', realtor.id)"
                                    class="block rounded-2xl border border-surface-200 bg-surface-50 p-4 shadow-sm transition-colors hover:border-surface-300 hover:bg-surface-100/70 dark:border-surface-700 dark:bg-surface-800 dark:hover:border-surface-600 dark:hover:bg-surface-800/80"
                                >
                                    <div class="flex items-start gap-4">
                                        <!-- CHANGED: added a compact avatar/status rail for mobile cards -->
                                        <div class="flex shrink-0 flex-col items-center gap-2">
                                            <div
                                                class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-900"
                                            >
                                                <img
                                                    v-if="realtor.avatar_url"
                                                    :src="realtor.avatar_url"
                                                    class="h-14 w-14 object-cover"
                                                    alt="Realtor avatar"
                                                />
                                                <i
                                                    v-else
                                                    class="pi pi-user text-surface-400 dark:text-surface-500"
                                                ></i>
                                            </div>

                                            <Tag
                                                :value="realtor.status"
                                                :severity="getTagSeverity(realtor.status)"
                                            />
                                        </div>

                                        <!-- CHANGED: card body now prioritizes the fields that matter most on mobile -->
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <h3
                                                        class="truncate text-base font-semibold text-surface-900 dark:text-surface-0"
                                                    >
                                                        {{ realtor.formatted_full_name }}
                                                    </h3>

                                                    <p
                                                        class="mt-1 text-sm text-surface-600 dark:text-surface-300"
                                                    >
                                                        {{
                                                            realtor.office?.parent_company ||
                                                            'No office assigned'
                                                        }}
                                                    </p>

                                                    <p
                                                        v-if="realtor.office?.office_name"
                                                        class="text-sm text-surface-500 dark:text-surface-400"
                                                    >
                                                        {{ realtor.office.office_name }}
                                                    </p>
                                                </div>

                                                <div
                                                    v-if="realtor.formatted_value"
                                                    class="shrink-0 rounded-full border border-surface-200 bg-surface-0 px-3 py-1 text-sm font-medium text-surface-700 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-200"
                                                >
                                                    {{ realtor.formatted_value }}
                                                </div>
                                            </div>

                                            <!-- CHANGED: added condensed contact + office metadata rows for mobile readability -->
                                            <div class="mt-4 space-y-2 text-sm">
                                                <div
                                                    class="flex items-center gap-2 text-surface-600 dark:text-surface-300"
                                                >
                                                    <i
                                                        class="pi pi-phone text-xs text-surface-400 dark:text-surface-500"
                                                    ></i>
                                                    <span>{{
                                                        realtor.phone_number || 'No phone'
                                                    }}</span>
                                                </div>

                                                <div
                                                    class="flex items-center gap-2 text-surface-600 dark:text-surface-300"
                                                >
                                                    <i
                                                        class="pi pi-envelope text-xs text-surface-400 dark:text-surface-500"
                                                    ></i>
                                                    <span class="truncate">{{
                                                        realtor.email || 'No email'
                                                    }}</span>
                                                </div>

                                                <div
                                                    v-if="
                                                        realtor.office?.city ||
                                                        realtor.office?.zip_code
                                                    "
                                                    class="flex items-center gap-2 text-surface-500 dark:text-surface-400"
                                                >
                                                    <i
                                                        class="pi pi-building text-xs text-surface-400 dark:text-surface-500"
                                                    ></i>
                                                    <span>
                                                        {{ realtor.office?.city || '—' }}
                                                        <span v-if="realtor.office?.zip_code">
                                                            {{ realtor.office.zip_code }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- CHANGED: added a clear mobile CTA instead of tiny action icons -->
                                            <div class="mt-4">
                                                <Button
                                                    label="View Realtor"
                                                    icon="pi pi-search"
                                                    class="w-full"
                                                    severity="primary"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </template>

                        <!-- CHANGED: added mobile-specific empty state because the DataTable empty slot no longer handles mobile -->
                        <template v-else>
                            <div
                                class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center"
                            >
                                <i
                                    class="pi pi-user text-3xl text-surface-400 dark:text-surface-500"
                                />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No realtors found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500 dark:text-surface-400">
                                    Try adjusting your search or create a new realtor to get
                                    started.
                                </p>
                                <Link :href="route('realtor.create')">
                                    <Button label="Create Realtor" icon="pi pi-plus-circle" />
                                </Link>
                            </div>
                        </template>
                    </div>

                    <!-- CHANGED: desktop keeps the DataTable, but only renders on large screens -->
                    <DataTable
                        v-else
                        v-model:filters="filters"
                        :value="realtors"
                        paginator
                        scrollable
                        :scrollHeight="scrollHeight"
                        stripedRows
                        :rows="10"
                        :rowsPerPageOptions="[10, 25, 50, 100]"
                        :globalFilterFields="[
                            'first_name',
                            'last_name',
                            'phone_number',
                            'email',
                            'office.parent_company',
                            'office.office_name',
                            'office.street_address',
                            'office.city',
                            'office.zip_code',
                        ]"
                        tableStyle="min-width: 78rem"
                        class="h-full"
                        @row-click="onRowClick"
                        :pt="{
                            bodyRow: {
                                class: 'cursor-pointer hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors',
                            },
                        }"
                    >
                        <template #empty>
                            <div
                                class="flex min-h-52 flex-col items-center justify-center gap-3 px-6 py-10 text-center"
                            >
                                <i
                                    class="pi pi-user text-3xl text-surface-400 dark:text-surface-500"
                                />
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    No realtors found.
                                </p>
                                <p class="max-w-md text-sm text-surface-500 dark:text-surface-400">
                                    Try adjusting your search or create a new realtor to get
                                    started.
                                </p>
                                <Link :href="route('realtor.create')">
                                    <Button label="Create Realtor" icon="pi pi-plus-circle" />
                                </Link>
                            </div>
                        </template>

                        <template #loading> Loading realtor data. Please wait. </template>

                        <Column headerClass="w-[9rem]" bodyClass="w-[9rem]">
                            <template #body="slotProps">
                                <div class="flex flex-col items-center justify-center gap-2 py-2">
                                    <div
                                        class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <img
                                            v-if="slotProps.data.avatar_url"
                                            :src="slotProps.data.avatar_url"
                                            class="h-16 w-16 object-cover"
                                            alt="Realtor avatar"
                                        />
                                        <i
                                            v-else
                                            class="pi pi-user text-surface-400 dark:text-surface-500"
                                        ></i>
                                    </div>

                                    <Tag
                                        :value="slotProps.data.status"
                                        :severity="getTagSeverity(slotProps.data.status)"
                                    />
                                </div>
                            </template>
                        </Column>

                        <Column
                            field="formatted_full_name"
                            header="Name"
                            sortable
                            headerClass="w-[16rem]"
                            bodyClass="w-[16rem]"
                        >
                            <template #body="{ data }">
                                <div class="flex flex-col">
                                    <p class="font-semibold">{{ data.formatted_full_name }}</p>
                                </div>
                            </template>
                        </Column>

                        <Column
                            field="phone_number"
                            header="Phone #"
                            sortable
                            headerClass="w-[11rem]"
                            bodyClass="w-[11rem]"
                        >
                            <template #body="{ data }">
                                <span>{{ data.phone_number || '—' }}</span>
                            </template>
                        </Column>

                        <Column
                            field="email"
                            header="Email"
                            sortable
                            headerClass="w-[16rem]"
                            bodyClass="w-[16rem]"
                        >
                            <template #body="{ data }">
                                <span>{{ data.email || '—' }}</span>
                            </template>
                        </Column>

                        <Column header="Office" headerClass="w-[20rem]" bodyClass="w-[20rem]">
                            <template #body="slotProps">
                                <div v-if="slotProps.data.office" class="flex flex-col gap-0.5">
                                    <p class="font-semibold">
                                        {{ slotProps.data.office.parent_company || 'N/A' }}
                                    </p>
                                    <p
                                        v-if="slotProps.data.office.office_name"
                                        class="text-sm text-surface-600 dark:text-surface-300"
                                    >
                                        {{ slotProps.data.office.office_name }}
                                    </p>
                                    <p class="text-sm text-surface-600 dark:text-surface-300">
                                        {{ slotProps.data.office.street_address || 'N/A' }}
                                        <span v-if="slotProps.data.office.unit_number">
                                            #{{ slotProps.data.office.unit_number }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">
                                        {{ slotProps.data.office.city || 'N/A' }},
                                        {{ slotProps.data.office.zip_code || 'N/A' }}
                                    </p>
                                </div>

                                <div v-else>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">
                                        No office assigned
                                    </p>
                                </div>
                            </template>
                        </Column>

                        <Column
                            field="formatted_value"
                            header="Value"
                            sortable
                            headerClass="w-[10rem]"
                            bodyClass="w-[10rem]"
                        >
                            <template #body="{ data }">
                                <span>{{ data.formatted_value || '—' }}</span>
                            </template>
                        </Column>

                        <Column
                            headerClass="w-[4rem]"
                            bodyClass="w-[4rem]"
                            alignFrozen="right"
                            :frozen="true"
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
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/Layout.vue'
import { Button, DataTable, Column, IconField, InputIcon, InputText, Tag } from 'primevue'
import { FilterMatchMode } from '@primevue/core/api'
import { ref, onMounted, onUnmounted, computed } from 'vue'

const page = usePage()

// CHANGED: kept safe fallback for realtor data
const realtors = page.props.realtors ?? []

// CHANGED: breakpoint is now used to switch between desktop DataTable and mobile card list
const isLargeBreakpoint = ref(false)

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024
}

const getTagSeverity = (status) => {
    if (status === 'Active') return 'success'
    if (status === 'Inactive') return 'danger'
    if (status === 'Engaged') return 'warn'
    if (status === 'New') return 'info'
    return 'info'
}

onMounted(() => {
    checkBreakpoint()
    window.addEventListener('resize', checkBreakpoint)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint)
})

// CHANGED: kept filter structure simple since the page only exposes global search right now
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

// CHANGED: added mobile-friendly computed results so cards can respect the same search bar as the table
const filteredRealtors = computed(() => {
    const query = (filters.value.global.value || '').toString().toLowerCase().trim()

    if (!query) return realtors

    return realtors.filter((realtor) => {
        const haystack = [
            realtor.first_name,
            realtor.last_name,
            realtor.formatted_full_name,
            realtor.phone_number,
            realtor.email,
            realtor.office?.parent_company,
            realtor.office?.office_name,
            realtor.office?.street_address,
            realtor.office?.city,
            realtor.office?.zip_code,
        ]
            .filter(Boolean)
            .join(' ')
            .toLowerCase()

        return haystack.includes(query)
    })
})

// CHANGED: kept summary metrics for the header cards
const activeOrEngagedCount = computed(() => {
    return realtors.filter((realtor) => realtor.status === 'Active' || realtor.status === 'Engaged')
        .length
})

const assignedOfficeCount = computed(() => {
    return realtors.filter((realtor) => !!realtor.office).length
})

// CHANGED: desktop DataTable still uses flex scrolling inside the Layout content region
const scrollHeight = computed(() => 'flex')

// CHANGED: clear helper now resets both desktop and mobile search views because they share the same filter state
const onClearFilters = () => {
    filters.value.global.value = null
}

const onRowClick = (event) => {
    router.visit(route('realtor.show', event.data.id)) // CHANGED: clicking a desktop row now opens the realtor
}
</script>
