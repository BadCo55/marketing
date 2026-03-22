<template>
    <Head :title="sequence.name || 'Sequence'" />

    <Layout>
        <div class="mx-auto w-full max-w-7xl px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: added dark mode text colors to page header -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <div class="mb-2">
                            <Link :href="route('sequence.index')">
                                <Button
                                    label="Back to Sequences"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                            {{ sequence.name }}
                        </h1>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Review the sequence details, step timing, and content templates.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Link :href="route('sequence.edit', sequence.id)">
                            <Button
                                label="Edit Sequence"
                                icon="pi pi-pen-to-square"
                                severity="secondary"
                                outlined
                            />
                        </Link>
                        <Link
                            :href="route('sequence.destroy', sequence.id)"
                            method="delete"
                            as="button"
                        >
                            <Button
                                label="Delete Sequence"
                                icon="pi pi-trash"
                                severity="danger"
                            />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 items-start gap-5">
                <!-- CHANGED: added dark mode card/background/border styling in left rail -->
                <div class="col-span-12 xl:col-span-4 xl:self-start">
                    <div class="space-y-5 xl:sticky xl:top-8">
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-6">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            {{ sequence.name }}
                                        </h2>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Sequence overview and targeting details.
                                        </p>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-start gap-3">
                                            <!-- CHANGED: added dark mode styles to icon chip -->
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-users"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Target Audience
                                                </p>
                                                <p class="font-medium text-surface-900 dark:text-surface-100">
                                                    {{ sequence.target_audience || 'Not specified' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <!-- CHANGED: added dark mode styles to icon chip -->
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300">
                                                <i class="pi pi-file-edit"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-surface-500 dark:text-surface-400">
                                                    Description
                                                </p>
                                                <p class="leading-6 text-surface-700 dark:text-surface-300">
                                                    {{ sequence.description || 'No description provided.' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <Divider />

                                    <div class="grid grid-cols-2 gap-3">
                                        <!-- CHANGED: added dark mode styles to stat cards -->
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Steps
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ steps.length }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Last Interval
                                            </p>
                                            <p class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                                {{ maxInterval }} day(s)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- CHANGED: added dark mode card/background/border styling in right content area -->
                <div class="col-span-12 min-w-0 xl:col-span-8">
                    <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <template #content>
                            <div class="space-y-5">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Sequence Steps
                                        </h2>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">
                                            Steps are shown in the order they should occur.
                                        </p>
                                    </div>
                                </div>

                                <!-- CHANGED: added dark mode styles to empty state -->
                                <div
                                    v-if="!steps.length"
                                    class="flex min-h-72 flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <i class="pi pi-sitemap mb-3 text-3xl text-surface-400 dark:text-surface-500"></i>
                                    <p class="font-medium text-surface-700 dark:text-surface-200">
                                        No steps found.
                                    </p>
                                    <p class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400">
                                        This sequence does not have any steps yet.
                                    </p>
                                </div>

                                <div v-else class="space-y-4">
                                    <div
                                        v-for="step in orderedSteps"
                                        :key="step.id"
                                        class="rounded-2xl border border-surface-200 bg-surface-50 p-5 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                                            <div class="min-w-0 flex-1">
                                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                                    <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                        Step {{ step.step_number }}
                                                    </span>

                                                    <!-- CHANGED: added dark mode styles to metadata pills -->
                                                    <span class="rounded-full border border-surface-200 bg-surface-0 px-3 py-1 text-sm text-surface-700 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-200">
                                                        {{ camelToNormal(step.type) }}
                                                    </span>

                                                    <span class="rounded-full border border-surface-200 bg-surface-0 px-3 py-1 text-sm text-surface-700 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-200">
                                                        {{ step.interval }} day(s)
                                                    </span>
                                                </div>

                                                <div class="space-y-3">
                                                    <div>
                                                        <p class="mb-1 text-sm font-medium text-surface-700 dark:text-surface-200">
                                                            Interval from Initial Contact
                                                        </p>
                                                        <p class="text-sm text-surface-600 dark:text-surface-300">
                                                            {{ step.interval }} day(s)
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-sm font-medium text-surface-700 dark:text-surface-200">
                                                            Description
                                                        </p>
                                                        <p class="text-sm leading-6 text-surface-600 dark:text-surface-300">
                                                            {{ step.description || 'No description provided.' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="w-full xl:max-w-md">
                                                <p class="mb-2 text-sm font-medium text-surface-700 dark:text-surface-200">
                                                    Template
                                                </p>

                                                <!-- CHANGED: added dark mode styles to template preview panel -->
                                                <div class="max-h-64 overflow-y-auto rounded-xl border border-surface-200 bg-surface-0 px-4 py-4 text-sm text-surface-800 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-200">
                                                    <template v-if="step.template">
                                                        <div v-html="step.template"></div>
                                                    </template>
                                                    <template v-else>
                                                        <p class="text-surface-500 dark:text-surface-400">
                                                            No template found.
                                                        </p>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Card, Button, Divider } from 'primevue';
import { camelToNormal } from '@/utilities/formatString';

const page = usePage();
const sequence = ref(page.props.sequence ?? {});

// CHANGED: Added safe fallback for steps
const steps = computed(() => sequence.value.steps ?? []);

// CHANGED: Added ordered steps in case backend order is inconsistent
const orderedSteps = computed(() => {
    return [...steps.value].sort((a, b) => (a.step_number ?? 0) - (b.step_number ?? 0));
});

// CHANGED: Added summary metric for max interval
const maxInterval = computed(() => {
    if (!steps.value.length) return 0;
    return Math.max(...steps.value.map((step) => Number(step.interval) || 0));
});
</script>
