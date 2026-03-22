<template>
    <Head title="Dashboard" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col">
            <!-- PAGE HEADER -->
            <div class="mb-5 flex flex-col gap-3 px-4 pt-5 md:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                <p class="text-sm text-surface-500">
                    Quick view of marketing activity, active sequences, and newest realtor
                    relationships.
                </p>
            </div>

            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div class="grid h-auto min-h-0 grid-cols-12 gap-5">
                    <!-- KPI ROW -->
                    <div class="col-span-12">
                        <!-- CHANGED: compact single stacked row -->
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">
                            <Link :href="route('reports.index')" class="block">
                                <div
                                    class="flex items-center gap-3 rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-surface-800 dark:bg-surface-900"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-100 dark:bg-green-500/15"
                                    >
                                        <i
                                            class="pi pi-dollar text-green-600 dark:text-green-300"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-surface-500"
                                        >
                                            Revenue
                                        </p>
                                        <p class="truncate text-lg font-semibold">
                                            {{ totalRevenue }}
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link :href="route('task.index')" class="block">
                                <div
                                    class="flex items-center gap-3 rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-surface-800 dark:bg-surface-900"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/15"
                                    >
                                        <i
                                            class="pi pi-bolt text-emerald-600 dark:text-emerald-300"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-surface-500"
                                        >
                                            Open Tasks
                                        </p>
                                        <p class="truncate text-lg font-semibold">
                                            {{ uncompletedTaskCount }}
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link :href="route('realtor.index')" class="block">
                                <div
                                    class="flex items-center gap-3 rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-surface-800 dark:bg-surface-900"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-500/15"
                                    >
                                        <i
                                            class="pi pi-user-plus text-blue-600 dark:text-blue-300"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-surface-500"
                                        >
                                            New Agents
                                        </p>
                                        <p class="truncate text-lg font-semibold">
                                            {{ realtorCount }}
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link :href="route('realtor.index')" class="block">
                                <div
                                    class="flex items-center gap-3 rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-surface-800 dark:bg-surface-900"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 dark:bg-surface-800"
                                    >
                                        <i
                                            class="pi pi-users text-slate-600 dark:text-surface-300"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-surface-500"
                                        >
                                            Total Agents
                                        </p>
                                        <p class="truncate text-lg font-semibold">
                                            {{ realtorCount }}
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link :href="route('office.index')" class="block">
                                <div
                                    class="flex items-center gap-3 rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-surface-800 dark:bg-surface-900"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-500/15"
                                    >
                                        <i
                                            class="pi pi-building text-indigo-600 dark:text-indigo-300"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-surface-500"
                                        >
                                            Offices
                                        </p>
                                        <p class="truncate text-lg font-semibold">
                                            {{ officeCount }}
                                        </p>
                                    </div>
                                </div>
                            </Link>

                            <Link :href="route('office-visit.index')" class="block">
                                <div
                                    class="flex items-center gap-3 rounded-2xl border border-surface-200 bg-surface-0 px-4 py-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-surface-800 dark:bg-surface-900"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 dark:bg-red-500/15"
                                    >
                                        <i
                                            class="pi pi-check-circle text-red-600 dark:text-red-300"
                                        />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-surface-500"
                                        >
                                            Office Visits
                                        </p>
                                        <p class="truncate text-lg font-semibold">
                                            {{ visitCount }}
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- ACTIVE SEQUENCES -->
                    <div class="col-span-12 min-h-0 lg:col-span-6">
                        <div
                            class="flex h-full min-h-0 flex-col rounded-2xl border border-surface-200 bg-surface-0 p-5 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                        >
                            <div class="mb-4 flex items-start justify-between gap-4">
                                <div>
                                    <h2
                                        class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                    >
                                        Active Sequences
                                    </h2>
                                    <p class="mt-1 text-sm text-surface-500">
                                        Realtors currently progressing through active outreach
                                        sequences.
                                    </p>
                                </div>

                                <Button
                                    icon="pi pi-ellipsis-v"
                                    text
                                    plain
                                    rounded
                                    @click="toggleSequencesMenu"
                                />
                                <Menu
                                    ref="sequencesMenu"
                                    :popup="true"
                                    :model="sequenceMenuItems"
                                />
                            </div>

                            <div class="min-h-0 flex-1">
                                <DataTable
                                    :value="activeSequences"
                                    paginator
                                    :rows="5"
                                    :rowsPerPageOptions="[5, 10, 20, 50]"
                                    scrollable
                                    scrollHeight="flex"
                                    tableStyle="min-width: 40rem"
                                    class="h-full"
                                    @row-click="onSequenceRowClick"
                                    :pt="{
                                        bodyRow: { class: 'cursor-pointer' },
                                    }"
                                >
                                    <template #empty>
                                        <div class="py-8 text-center text-sm text-surface-500">
                                            No active sequences found.
                                        </div>
                                    </template>

                                    <Column header="Realtor" field="realtor.formatted_full_name">
                                        <template #body="{ data }">
                                            <span class="font-medium">
                                                {{ data.realtor?.formatted_full_name || '—' }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column header="Sequence" field="sequence_name" />

                                    <Column header="Next Due" field="next_step_due" />

                                    <Column header="Status">
                                        <template #body="{ data }">
                                            <span
                                                class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                                :class="getSequenceStatusClass(data.status)"
                                            >
                                                {{ data.formatted_status }}
                                            </span>
                                        </template>
                                    </Column>
                                </DataTable>
                            </div>
                        </div>
                    </div>

                    <!-- NEWEST REALTORS -->
                    <div class="col-span-12 min-h-0 lg:col-span-6">
                        <div
                            class="flex h-full min-h-0 flex-col rounded-2xl border border-surface-200 bg-surface-0 p-5 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                        >
                            <div class="mb-4 flex items-start justify-between gap-4">
                                <div>
                                    <h2
                                        class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                    >
                                        Newest Realtors
                                    </h2>
                                    <p class="mt-1 text-sm text-surface-500">
                                        Recently added realtors and their current relationship
                                        status.
                                    </p>
                                </div>

                                <Button
                                    icon="pi pi-ellipsis-v"
                                    text
                                    plain
                                    rounded
                                    @click="toggleRealtorsMenu"
                                />
                                <Menu ref="realtorsMenu" :popup="true" :model="realtorMenuItems" />
                            </div>

                            <div class="min-h-0 flex-1">
                                <DataTable
                                    :value="recentRealtors"
                                    scrollable
                                    scrollHeight="flex"
                                    tableStyle="min-width: 38rem"
                                    class="h-full"
                                    @row-click="onRealtorRowClick"
                                    :pt="{
                                        bodyRow: { class: 'cursor-pointer' },
                                    }"
                                >
                                    <template #empty>
                                        <div class="py-8 text-center text-sm text-surface-500">
                                            No realtors found.
                                        </div>
                                    </template>

                                    <Column class="w-24 !text-center">
                                        <template #body="{ data }">
                                            <Tag
                                                :severity="getTagSeverity(data.status)"
                                                :value="data.status"
                                            />
                                        </template>
                                    </Column>

                                    <Column header="Name" field="formatted_full_name">
                                        <template #body="{ data }">
                                            <span class="font-medium">{{
                                                data.formatted_full_name
                                            }}</span>
                                        </template>
                                    </Column>

                                    <Column header="Created" field="formatted_created_at" />
                                    <Column header="$ Value" field="formatted_value" />
                                </DataTable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import { Head, usePage, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Button, Menu, DataTable, Column, Tag } from 'primevue'

const page = usePage()
const uncompletedTaskCount = ref(page.props.uncompleted_task_count ?? 0)
const realtorCount = ref(page.props.realtor_count ?? 0)
const officeCount = ref(page.props.office_count ?? 0)
const visitCount = ref(page.props.visit_count ?? 0)
const activeSequences = ref(page.props.active_sequences ?? [])
const recentRealtors = ref(page.props.recent_realtors ?? [])
const totalRevenue = ref(page.props.total_revenue ?? '$0')

const sequencesMenu = ref()
const realtorsMenu = ref()

const toggleSequencesMenu = (event) => {
    sequencesMenu.value?.toggle(event)
}

const toggleRealtorsMenu = (event) => {
    realtorsMenu.value?.toggle(event)
}

const sequenceMenuItems = [
    {
        label: 'View All Sequences',
        icon: 'pi pi-sitemap',
        command: () => router.visit(route('sequence.index')),
    },
    {
        label: 'View Realtors',
        icon: 'pi pi-users',
        command: () => router.visit(route('realtor.index')),
    },
]

const realtorMenuItems = [
    {
        label: 'View All Realtors',
        icon: 'pi pi-users',
        command: () => router.visit(route('realtor.index')),
    },
    {
        label: 'Create Realtor',
        icon: 'pi pi-plus-circle',
        command: () => router.visit(route('realtor.create')),
    },
]

const getTagSeverity = (status) => {
    if (status === 'Active') return 'success'
    if (status === 'Inactive') return 'danger'
    if (status === 'Engaged') return 'warn'
    if (status === 'New') return 'info'
    return null
}

const getSequenceStatusClass = (status) => {
    switch (status) {
        case 'not_started':
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300'
        case 'in_progress':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
        case 'completed':
            return 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300'
        case 'cancelled':
            return 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300'
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300'
    }
}

const onSequenceRowClick = (event) => {
    router.visit(
        route('realtor.show', {
            realtor: event.data.realtor_id,
            viewSequence: true,
            sequenceId: event.data.id,
        })
    )
}

const onRealtorRowClick = (event) => {
    router.visit(route('realtor.show', event.data.id));
};
</script>
