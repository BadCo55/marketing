<template>
    <Head :title="task.title || 'Task Details'" />

    <Layout>
        <!-- CHANGED: Simplified page shell so the page reads like a detail view instead of a dashboard -->
        <div class="flex h-full min-h-0 flex-col">
            <!-- CHANGED: Kept a lighter page header with status pills on the right -->
            <div class="mb-5 flex flex-col gap-4 px-4 pt-5 md:px-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                            Task Details
                        </h1>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Review task details, update status, and track internal notes.
                        </p>
                    </div>

                    <!-- CHANGED: Status indicators remain in the header for quick scanning -->
                    <div class="flex flex-wrap gap-2">
                        <Message
                            v-if="task.is_passed_due && task.status !== 'completed'"
                            severity="error"
                            size="small"
                            icon="pi pi-exclamation-circle"
                        >
                            <span class="font-medium">Past Due</span>
                        </Message>

                        <Message
                            v-if="task.is_due_today && task.status !== 'completed'"
                            severity="warn"
                            size="small"
                            icon="pi pi-exclamation-circle"
                        >
                            <span class="font-medium">Due Today</span>
                        </Message>

                        <Message
                            v-if="task.status === 'completed'"
                            severity="success"
                            size="small"
                            icon="pi pi-check-circle"
                        >
                            <span class="font-medium">Completed</span>
                        </Message>

                        <Message
                            v-if="task.status === 'cancelled'"
                            severity="secondary"
                            size="small"
                            icon="pi pi-times-circle"
                        >
                            <span class="font-medium">Cancelled</span>
                        </Message>
                    </div>
                </div>
            </div>

            <!-- CHANGED: Main content keeps a narrow left rail and one stacked right content column -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div class="grid h-full min-h-0 grid-cols-12 gap-5 items-start">
                    <!-- CHANGED: Left rail is now compact and only holds summary info -->
                    <div class="col-span-12 xl:col-span-3 xl:self-start">
                        <div class="space-y-5 xl:sticky xl:top-5">
                            <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800">
                                <template #content>
                                    <!-- CHANGED: Reduced visual weight and kept this card metadata-focused -->
                                    <div class="space-y-4">
                                        <!-- CHANGED: Compact title only in left rail -->
                                        <div>
                                            <h2 class="text-lg font-semibold leading-tight text-surface-900 dark:text-surface-0">
                                                {{ camelToNormal(task.title) }}
                                            </h2>
                                        </div>

                                        <!-- CHANGED: Compact metadata stack with icons -->
                                        <div class="space-y-2 rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-800 dark:bg-surface-900">
                                            <div class="flex items-start gap-3 rounded-lg px-2 py-2">
                                                <div class="mt-0.5 text-surface-500 dark:text-surface-400">
                                                    <i class="pi pi-user text-sm"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                        Created By
                                                    </p>
                                                    <p class="truncate text-sm font-medium text-surface-900 dark:text-surface-100">
                                                        {{ task.created_by?.formatted_full_name || '—' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3 rounded-lg px-2 py-2">
                                                <div class="mt-0.5 text-surface-500 dark:text-surface-400">
                                                    <i class="pi pi-calendar text-sm"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                        Created At
                                                    </p>
                                                    <p class="text-sm text-surface-700 dark:text-surface-300">
                                                        {{ task.formatted_created_at || '—' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3 rounded-lg px-2 py-2">
                                                <div class="mt-0.5 text-surface-500 dark:text-surface-400">
                                                    <i class="pi pi-clock text-sm"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                        Due Date
                                                    </p>
                                                    <p
                                                        class="text-sm"
                                                        :class="{
                                                            'font-medium text-red-500': task.is_passed_due && task.status !== 'completed',
                                                            'font-medium text-orange-500': task.is_due_today && task.status !== 'completed',
                                                            'text-surface-700 dark:text-surface-300': !task.is_passed_due && !task.is_due_today,
                                                        }"
                                                    >
                                                        {{ task.formatted_due_date || 'No due date' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex items-start gap-3 rounded-lg px-2 py-2">
                                                <div class="mt-0.5 text-surface-500 dark:text-surface-400">
                                                    <i class="pi pi-flag text-sm"></i>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                        Status
                                                    </p>
                                                    <div class="mt-1">
                                                        <span
                                                            class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                                                            :class="getStatusPillClass(task.status)"
                                                        >
                                                            {{ task.formatted_status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CHANGED: Small summary card for note count -->
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-3 dark:border-surface-800 dark:bg-surface-900">
                                            <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Notes
                                            </p>
                                            <p class="mt-1 text-lg font-semibold text-surface-900 dark:text-surface-0">
                                                {{ task.notes?.length || 0 }}
                                            </p>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                Internal updates for this task.
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <!-- CHANGED: Right column is now one stacked content flow instead of a 2-card side-by-side layout -->
                    <div class="col-span-12 min-h-0 xl:col-span-9">
                        <div class="flex min-h-0 flex-col gap-5">
                            <!-- CHANGED: New overview/actions card combines task description + status + note composer -->
                            <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800">
                                <template #content>
                                    <div class="space-y-6">
                                        <!-- CHANGED: Full task description moved into the main content area -->
                                        <div class="space-y-3">
                                            <div>
                                                <h3 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                                    Overview
                                                </h3>
                                                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                    Core task details and the latest internal updates.
                                                </p>
                                            </div>

                                            <div class="rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-800 dark:bg-surface-900">
                                                <h4 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                                    {{ camelToNormal(task.title) }}
                                                </h4>
                                                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-surface-700 dark:text-surface-300">
                                                    {{ task.description || 'No description found.' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- CHANGED: Status + note composer live together in a single workflow section -->
                                        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[16rem_minmax(0,1fr)]">
                                            <div class="space-y-3">
                                                <div>
                                                    <h4 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                                        Task Status
                                                    </h4>
                                                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                        Update the current progress of this task.
                                                    </p>
                                                </div>

                                                <Select
                                                    v-model="status"
                                                    :options="statusOptions"
                                                    optionLabel="label"
                                                    optionValue="value"
                                                    placeholder="Select status..."
                                                    fluid
                                                />
                                            </div>

                                            <form @submit.prevent="onNoteSubmit" class="space-y-4">
                                                <div>
                                                    <h4 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                                        Add Internal Note
                                                    </h4>
                                                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                        Record progress, blockers, decisions, or next steps.
                                                    </p>
                                                </div>

                                                <div class="flex flex-col">
                                                    <Textarea
                                                        v-model="noteForm.note"
                                                        rows="5"
                                                        autoResize
                                                        fluid
                                                        class="min-h-[10rem]"
                                                        placeholder="Add an internal update or note..."
                                                        :invalid="!!noteForm.errors.note"
                                                    />

                                                    <!-- CHANGED: Validation message moved directly under textarea -->
                                                    <small
                                                        v-if="noteForm.errors.note"
                                                        class="mt-2 text-red-500"
                                                    >
                                                        {{ noteForm.errors.note }}
                                                    </small>
                                                </div>

                                                <div class="flex justify-end">
                                                    <Button
                                                        label="Submit Note"
                                                        type="submit"
                                                        icon="pi pi-check-circle"
                                                        :loading="noteForm.processing"
                                                    />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
                            </Card>

                            <!-- CHANGED: Notes are now full-width and more timeline-like -->
                            <Card class="flex min-h-0 flex-col overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800">
                                <template #content>
                                    <div class="flex h-full min-h-0 flex-col">
                                        <div class="mb-4">
                                            <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                                Task Notes
                                            </h3>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                Internal history and updates related to this task.
                                            </p>
                                        </div>

                                        <div
                                            v-if="task.notes?.length"
                                            class="min-h-0 flex-1 overflow-y-auto rounded-2xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-800 dark:bg-surface-900"
                                        >
                                            <!-- CHANGED: Timeline-like vertical stack -->
                                            <div class="relative flex flex-col gap-4">
                                                <div
                                                    v-for="note in sortedNotes"
                                                    :key="note.id"
                                                    class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-800 dark:bg-surface-800"
                                                >
                                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                        <div>
                                                            <p class="font-medium text-surface-900 dark:text-surface-100">
                                                                {{ note.created_by?.formatted_full_name || '—' }}
                                                            </p>
                                                            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                                                {{ note.formatted_created_at }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-900">
                                                        <p class="whitespace-pre-line text-sm leading-6 text-surface-800 dark:text-surface-200">
                                                            {{ note.note }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            v-else
                                            class="flex min-h-[18rem] flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-800 dark:bg-surface-900"
                                        >
                                            <i class="pi pi-file-edit mb-3 text-3xl text-surface-400" />
                                            <p class="font-medium text-surface-700 dark:text-surface-200">
                                                No notes found.
                                            </p>
                                            <p class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400">
                                                Add the first note to document progress on this task.
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { camelToNormal } from '@/utilities/formatString';
import { usePage, Head, useForm, router } from '@inertiajs/vue3';
import { Card, Message, Select, Textarea, Button } from 'primevue';
import { watch, ref, computed } from 'vue';

const page = usePage();
const task = ref(page.props.task);

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const status = ref(task.value.status);

// CHANGED: Kept status pill helper for summary rail
const getStatusPillClass = (statusValue) => {
    switch (statusValue) {
        case 'pending':
            return 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-300';
        case 'in_progress':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300';
        case 'completed':
            return 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-300';
        case 'cancelled':
            return 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-300';
        default:
            return 'bg-surface-100 text-surface-700 dark:bg-surface-800 dark:text-surface-300';
    }
};

// CHANGED: Sort notes newest first for a cleaner activity flow
const sortedNotes = computed(() => {
    return [...(task.value.notes || [])].sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at);
    });
});

// CHANGED: Status watcher stays the same but supports the new stacked layout
watch(status, (newVal, oldVal) => {
    if (!newVal || newVal === oldVal) return;

    router.put(
        route('task.update', task.value.id),
        {
            request_type: 'status_update',
            status: status.value,
        },
        {
            only: ['task', 'flash'],
            preserveScroll: true,
            onSuccess: (response) => {
                task.value = response.props.task;
                status.value = response.props.task.status;
            },
            onError: (error) => {
                console.log(error);
            },
        }
    );
});

const noteForm = useForm({
    note: '',
    task_id: task.value.id,
});

// CHANGED: Note submission unchanged functionally, fits the new full-width notes workflow
const onNoteSubmit = () => {
    noteForm.post(route('task-note.store'), {
        only: ['task', 'flash'],
        preserveScroll: true,
        onSuccess: (response) => {
            task.value = response.props.task;
            noteForm.reset();
        },
        onError: (error) => {
            console.log(error);
        },
    });
};
</script>
