<template>
    <DataTable
        :value="visits"
        paginator
        :rows="5"
        :rowsPerPageOptions="[5, 10, 20, 50]"
    >
        <template #empty>
            <div class="inline-flex gap-5 items-center">
                <p>No visits found</p>
                <Button label="Log One Now" severity="info" />
            </div>
        </template>
        <Column header="Date" field="formatted_visit_date"></Column>
        <Column header="Purpose" field="formatted_purpose"></Column>
        <Column header="Overall Rating">
            <template #body="{ data }">
                <Rating v-model="data.rating" readonly />
            </template>
        </Column>
        <Column header="Interest Level">
            <template #body="{ data }">
                <Rating v-model="data.interest_level" readonly />
            </template>
        </Column>
        <Column header="Follow-Up Needed">
            <template #body="{ data }">
                <div class="flex gap-1 items-center font-medium">
                    <i v-if="data.follow_up_required" class="pi pi-check-circle text-green-500"></i>
                    <i v-if="!data.follow_up_required" class="pi pi-times-circle text-red-500"></i>
                    {{ data.formatted_follow_up_required }}
                </div>
            </template>
        </Column>
        <Column class="w-24 !text-center">
            <template #body="{ data }">
                <Link
                    :href="route('office-visit.show', data.id)"
                >
                    <Button icon="pi pi-search" severity="primary" rounded></Button>
                </Link>
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { DataTable, Column, Rating, Button } from 'primevue';

const props = defineProps({
    office: Object,
});

const visits = props.office.visits;

</script>
