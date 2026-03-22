<template>
    <Head :title="`Visit - ${office.parent_company}`" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col">
            <!-- Page Header -->
            <div class="mb-6 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0">
                        <div class="mb-2 flex flex-wrap gap-2">
                            <Link :href="route('office-visit.index')">
                                <Button
                                    label="Back to Visits"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>

                            <Link :href="route('office.show', office.id)">
                                <Button
                                    label="View Office"
                                    icon="pi pi-building"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <h1
                            class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0"
                        >
                            Office Visit
                        </h1>

                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Review the office profile and details recorded for this visit.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Page Body -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div class="grid h-full min-h-0 grid-cols-12 items-start gap-5">
                    <!-- Left Rail -->
                    <div class="col-span-12 xl:col-span-4">
                        <div class="space-y-5 xl:sticky xl:top-6">
                            <Card
                                class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                            >
                                <template #content>
                                    <div class="flex flex-col gap-6">
                                        <div
                                            class="flex flex-col items-center gap-5 lg:flex-row lg:items-start"
                                        >
                                            <div class="flex shrink-0 flex-col items-center gap-4">
                                                <div
                                                    class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-2xl border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-800"
                                                >
                                                    <Image
                                                        v-if="office.image_url"
                                                        :src="office.image_url"
                                                        imageClass="h-32 w-32 object-cover cursor-pointer"
                                                        preview
                                                    />
                                                    <div
                                                        v-else
                                                        class="flex h-full w-full items-center justify-center text-surface-400 dark:text-surface-500"
                                                    >
                                                        <i class="pi pi-building text-3xl" />
                                                    </div>
                                                </div>

                                                <Link
                                                    :href="route('office.show', office.id)"
                                                    class="w-full"
                                                >
                                                    <Button
                                                        label="View Office Details"
                                                        icon="pi pi-eye"
                                                        class="w-full"
                                                        outlined
                                                    />
                                                </Link>
                                            </div>

                                            <div class="min-w-0 flex-1 text-center lg:text-left">
                                                <h2
                                                    class="text-2xl font-bold leading-tight text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ office.parent_company }}
                                                </h2>

                                                <p
                                                    v-if="office.office_name"
                                                    class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    {{ office.office_name }}
                                                </p>

                                                <div class="mt-4 space-y-3 text-sm">
                                                    <div
                                                        v-if="office.office_phone"
                                                        class="flex items-start justify-center gap-3 lg:justify-start"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                        >
                                                            <i class="pi pi-phone" />
                                                        </div>

                                                        <p
                                                            class="text-surface-700 dark:text-surface-300"
                                                        >
                                                            {{ office.office_phone }}
                                                        </p>
                                                    </div>

                                                    <div
                                                        class="flex items-start justify-center gap-3 lg:justify-start"
                                                    >
                                                        <div
                                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-100 text-surface-500 dark:bg-surface-800 dark:text-surface-300"
                                                        >
                                                            <i class="pi pi-map-marker" />
                                                        </div>

                                                        <div
                                                            class="text-surface-700 dark:text-surface-300"
                                                        >
                                                            <p>
                                                                {{ office.street_address }}
                                                                <span v-if="office.unit_number">
                                                                    #{{ office.unit_number }}
                                                                </span>
                                                            </p>
                                                            <p>
                                                                {{ office.city }},
                                                                {{ office.state }}
                                                                {{ office.zip_code }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <Divider />

                                        <div class="grid grid-cols-2 gap-3">
                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                                >
                                                    Visit Date
                                                </p>
                                                <p
                                                    class="text-sm font-semibold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ visit.formatted_visit_date || '—' }}
                                                </p>
                                            </div>

                                            <div
                                                class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                                >
                                                    Duration
                                                </p>
                                                <p
                                                    class="text-sm font-semibold text-surface-900 dark:text-surface-0"
                                                >
                                                    {{ visit.formatted_duration || '—' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <!-- Right Content -->
                    <div class="col-span-12 min-h-0 xl:col-span-8 xl:h-full">
                        <Card
                            class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900 xl:h-full"
                        >
                            <template #content>
                                <div class="space-y-6 xl:h-full xl:overflow-y-auto xl:pr-2">
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-surface-900 dark:text-surface-0"
                                        >
                                            Visit Details
                                        </h2>
                                        <p
                                            class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                        >
                                            Recorded feedback, observations, materials presented,
                                            and follow-up details.
                                        </p>
                                    </div>

                                    <!-- Top Metrics -->
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                            >
                                                Visitor
                                            </p>
                                            <p
                                                class="font-semibold text-surface-900 dark:text-surface-0"
                                            >
                                                {{ visit.created_by?.name || '—' }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-2 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                            >
                                                Overall Rating
                                            </p>
                                            <div class="flex items-center gap-3">
                                                <Rating v-model="visit.rating" readonly />
                                                <span
                                                    class="text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    {{ visit.rating ?? 0 }}/5
                                                </span>
                                            </div>
                                        </div>

                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-2 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                            >
                                                Interest Level
                                            </p>
                                            <div class="flex items-center gap-3">
                                                <Rating v-model="visit.interest_level" readonly />
                                                <span
                                                    class="text-sm text-surface-500 dark:text-surface-400"
                                                >
                                                    {{ visit.interest_level ?? 0 }}/5
                                                </span>
                                            </div>
                                        </div>

                                        <div
                                            class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                            >
                                                Follow-Up
                                            </p>
                                            <span
                                                class="inline-flex rounded-full px-3 py-1 text-sm font-medium"
                                                :class="
                                                    visit.follow_up_required
                                                        ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300'
                                                        : 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300'
                                                "
                                            >
                                                {{ visit.formatted_follow_up_required || '—' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Main Details -->
                                    <div class="grid grid-cols-1 gap-5">
                                        <div
                                            class="rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                            >
                                                Purpose
                                            </p>
                                            <p
                                                class="leading-6 text-surface-700 dark:text-surface-300"
                                            >
                                                {{ visit.formatted_purpose || 'No purpose provided.' }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                            >
                                                Initial Impressions
                                            </p>
                                            <p
                                                class="whitespace-pre-line leading-6 text-surface-700 dark:text-surface-300"
                                            >
                                                {{
                                                    visit.initial_impressions ||
                                                    'No initial impressions provided.'
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                            >
                                                Discussion Topics
                                            </p>
                                            <p
                                                class="whitespace-pre-line leading-6 text-surface-700 dark:text-surface-300"
                                            >
                                                {{
                                                    visit.discussion_topics ||
                                                    'No discussion topics recorded.'
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <p
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                            >
                                                General Notes
                                            </p>
                                            <p
                                                class="whitespace-pre-line leading-6 text-surface-700 dark:text-surface-300"
                                            >
                                                {{ visit.notes || 'No general notes recorded.' }}
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                            <div
                                                class="rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                >
                                                    Materials Presented
                                                </p>

                                                <ul
                                                    v-if="visit.formatted_materials_presented?.length"
                                                    class="space-y-2 text-surface-700 dark:text-surface-300"
                                                >
                                                    <li
                                                        v-for="(material, index) in visit.formatted_materials_presented"
                                                        :key="index"
                                                        class="flex gap-2"
                                                    >
                                                        <span
                                                            class="font-medium text-surface-500 dark:text-surface-400"
                                                        >
                                                            {{ index + 1 }}.
                                                        </span>
                                                        <span>{{ material }}</span>
                                                    </li>
                                                </ul>

                                                <p
                                                    v-else
                                                    class="text-surface-500 dark:text-surface-400"
                                                >
                                                    No materials were recorded.
                                                </p>
                                            </div>

                                            <div
                                                class="self-start rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <p
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                >
                                                    Next Action
                                                </p>
                                                <p
                                                    class="whitespace-pre-line leading-6 text-surface-700 dark:text-surface-300"
                                                >
                                                    {{ visit.next_action || 'No next action recorded.' }}
                                                </p>

                                                <div
                                                    class="mt-4 border-t border-surface-200 pt-4 dark:border-surface-700"
                                                >
                                                    <p
                                                        class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400"
                                                    >
                                                        Next Action Date
                                                    </p>
                                                    <p
                                                        class="font-semibold text-surface-900 dark:text-surface-0"
                                                    >
                                                        {{ visit.formatted_next_action_date || '—' }}
                                                    </p>
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
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import { usePage, Link, Head } from '@inertiajs/vue3'
import { Card, Image, Button, Rating, Divider } from 'primevue'

const page = usePage()
const visit = page.props.visit
const office = visit.office
</script>
