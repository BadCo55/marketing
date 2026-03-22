<template>
    <div class="space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">
                    Visits
                </h3>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Review office visit history, ratings, interest levels, and follow-up needs.
                </p>
            </div>

            <Link :href="route('office-visit.create', { office_id: office.id })">
                <Button
                    label="Log Visit"
                    icon="pi pi-plus-circle"
                />
            </Link>
        </div>

        <div class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-700 dark:bg-surface-900">
            <DataTable
                :value="visits"
                paginator
                :rows="5"
                :rowsPerPageOptions="[5, 10, 20, 50]"
                stripedRows
                class="w-full"
                @row-click="onRowClick"
                :pt="{
                    bodyRow: {
                        class: 'cursor-pointer transition-colors hover:bg-surface-100 dark:hover:bg-surface-800'
                    }
                }"
            >
                <template #empty>
                    <div class="flex min-h-40 flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800">
                        <i class="pi pi-map-marker text-2xl text-surface-400 dark:text-surface-500"></i>
                        <p class="font-medium text-surface-700 dark:text-surface-100">No visits found.</p>
                        <p class="max-w-md text-sm text-surface-500 dark:text-surface-400">
                            Start tracking office outreach by logging the first visit for this location.
                        </p>
                        <Link :href="route('office-visit.create', { office_id: office.id })">
                            <Button
                                label="Log One Now"
                                icon="pi pi-plus-circle"
                                severity="info"
                            />
                        </Link>
                    </div>
                </template>

                <template #header>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h4 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                Visit History
                            </h4>
                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                {{ visits.length }} {{ visits.length === 1 ? 'visit' : 'visits' }} recorded
                            </p>
                        </div>
                    </div>
                </template>

                <Column header="Date" field="formatted_visit_date"></Column>
                <Column header="Purpose" field="formatted_purpose"></Column>

                <Column header="Rating">
                    <template #body="{ data }">
                        <div class="inline-flex items-center gap-3">
                            <Rating v-model="data.rating" readonly />
                            <span class="text-sm text-surface-500 dark:text-surface-400">
                                {{ data.rating ?? 0 }}/5
                            </span>
                        </div>
                    </template>
                </Column>

                <Column header="Interest Level">
                    <template #body="{ data }">
                        <div class="inline-flex items-center gap-3">
                            <Rating v-model="data.interest_level" readonly />
                            <span class="text-sm text-surface-500 dark:text-surface-400">
                                {{ data.interest_level ?? 0 }}/5
                            </span>
                        </div>
                    </template>
                </Column>

                <Column header="Follow-Up Needed">
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
                                v-if="data.follow_up_required"
                                class="pi pi-check-circle"
                            ></i>
                            <i
                                v-else
                                class="pi pi-times-circle"
                            ></i>

                            {{ data.formatted_follow_up_required }}
                        </div>
                    </template>
                </Column>

                <Column
                    headerClass="w-[4rem]"
                    bodyClass="w-[4rem]"
                >
                    <template #body>
                        <div class="flex justify-center">
                            <i class="pi pi-angle-right text-surface-400 dark:text-surface-500"></i>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { DataTable, Column, Rating, Button } from 'primevue';

const props = defineProps({
    office: Object,
});

const visits = props.office?.visits ?? [];

const onRowClick = (event) => {
    router.visit(route('office-visit.show', event.data.id));
};
</script>
