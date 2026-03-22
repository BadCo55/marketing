<template>
    <Head :title="sequenceForm.name ? `Edit Sequence - ${sequenceForm.name}` : 'Edit Sequence'" />

    <Layout>
        <!-- CHANGED: Added full-page responsive shell to match the rest of the portal -->
        <div class="flex h-full min-h-0 flex-col px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: Added page header with back action and save action -->
            <div class="mb-6 flex flex-col gap-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div class="min-w-0">
                        <!-- CHANGED: Added back navigation -->
                        <div class="mb-2">
                            <Link :href="route('sequence.show', sequence.id)">
                                <Button
                                    label="Back to Sequence"
                                    icon="pi pi-arrow-left"
                                    severity="secondary"
                                    text
                                />
                            </Link>
                        </div>

                        <!-- CHANGED: Added page title and helper text -->
                        <div>
                            <h1 class="truncate text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                                Edit Sequence
                            </h1>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Update the sequence details and manage the ordered follow-up steps.
                            </p>
                        </div>
                    </div>

                    <!-- CHANGED: Added top-right save action -->
                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                        <Button
                            label="Save Sequence"
                            icon="pi pi-save"
                            :loading="sequenceForm.processing"
                            @click="submit"
                        />
                    </div>
                </div>
            </div>

            <!-- CHANGED: Added responsive two-column editing layout -->
            <div class="min-h-0 flex-1">
                <div class="grid grid-cols-12 items-start gap-5">
                    <!-- CHANGED: Added left rail for sequence details -->
                    <div class="col-span-12 self-start xl:col-span-4 2xl:col-span-4">
                        <div class="space-y-5 xl:sticky xl:top-6">
                            <!-- CHANGED: Added main details card -->
                            <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                                <template #content>
                                    <div class="space-y-6">
                                        <div>
                                            <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                                Sequence Details
                                            </h2>
                                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                Set the basic information for this sequence.
                                            </p>
                                        </div>

                                        <!-- CHANGED: Added editable sequence fields -->
                                        <div class="space-y-5">
                                            <div class="flex flex-col">
                                                <label
                                                    for="sequence-name"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': sequenceForm.errors.name }"
                                                >
                                                    Name
                                                </label>

                                                <InputText
                                                    id="sequence-name"
                                                    v-model="sequenceForm.name"
                                                    fluid
                                                    placeholder="Enter sequence name..."
                                                    :invalid="!!sequenceForm.errors.name"
                                                />

                                                <Message
                                                    v-if="sequenceForm.errors.name"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ sequenceForm.errors.name }}
                                                </Message>
                                            </div>

                                            <div class="flex flex-col">
                                                <label
                                                    for="target-audience"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': sequenceForm.errors.target_audience }"
                                                >
                                                    Target Audience
                                                </label>

                                                <InputText
                                                    id="target-audience"
                                                    v-model="sequenceForm.target_audience"
                                                    fluid
                                                    placeholder="Ex: Realtors, Homeowners, Investors..."
                                                    :invalid="!!sequenceForm.errors.target_audience"
                                                />

                                                <Message
                                                    v-if="sequenceForm.errors.target_audience"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ sequenceForm.errors.target_audience }}
                                                </Message>
                                            </div>

                                            <div class="flex flex-col">
                                                <label
                                                    for="sequence-description"
                                                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                    :class="{ 'text-red-500': sequenceForm.errors.description }"
                                                >
                                                    Description
                                                </label>

                                                <Textarea
                                                    id="sequence-description"
                                                    v-model="sequenceForm.description"
                                                    rows="6"
                                                    autoResize
                                                    fluid
                                                    placeholder="Describe what this sequence is for..."
                                                    :invalid="!!sequenceForm.errors.description"
                                                />

                                                <Message
                                                    v-if="sequenceForm.errors.description"
                                                    severity="error"
                                                    variant="simple"
                                                    size="small"
                                                    class="mt-2"
                                                >
                                                    {{ sequenceForm.errors.description }}
                                                </Message>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>

                            <!-- CHANGED: Added summary card for quick stats -->
                            <Card class="rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                                <template #content>
                                    <div class="space-y-4">
                                        <div>
                                            <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                                Summary
                                            </h3>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                                Quick overview of the current sequence structure.
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                                <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                    Steps
                                                </p>
                                                <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                    {{ sequenceForm.steps.length }}
                                                </p>
                                            </div>

                                            <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                                <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                    Audience
                                                </p>
                                                <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                    {{ sequenceForm.target_audience || 'Not set' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <!-- CHANGED: Added main content area for managing steps -->
                    <div class="col-span-12 min-w-0 xl:col-span-8 2xl:col-span-8">
                        <div class="space-y-5">
                            <!-- CHANGED: Added steps editor card -->
                            <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                                <template #content>
                                    <div class="space-y-5">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                                    Sequence Steps
                                                </h2>
                                                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                                    Edit each step in the order it should occur.
                                                </p>
                                            </div>

                                            <!-- CHANGED: Added add-step action -->
                                            <Button
                                                label="Add Step"
                                                icon="pi pi-plus"
                                                severity="secondary"
                                                outlined
                                                @click="addStep"
                                            />
                                        </div>

                                        <!-- CHANGED: Added empty state when no steps exist -->
                                        <div
                                            v-if="!sequenceForm.steps.length"
                                            class="flex min-h-52 flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <i class="pi pi-list mb-3 text-3xl text-surface-400 dark:text-surface-500"></i>
                                            <p class="font-medium text-surface-700 dark:text-surface-200">
                                                No steps added yet.
                                            </p>
                                            <p class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400">
                                                Add the first step to begin building this sequence.
                                            </p>

                                            <div class="mt-4">
                                                <Button
                                                    label="Add First Step"
                                                    icon="pi pi-plus"
                                                    @click="addStep"
                                                />
                                            </div>
                                        </div>

                                        <!-- CHANGED: Added editable step list -->
                                        <div v-else class="space-y-4">
                                            <div
                                                v-for="(step, index) in sequenceForm.steps"
                                                :key="step.local_key"
                                                class="rounded-2xl border border-surface-200 bg-surface-50 p-5 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                                            >
                                                <div class="space-y-5">
                                                    <!-- CHANGED: Added step header with reorder and delete controls -->
                                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                        <div class="flex min-w-0 items-center gap-3">
                                                            <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                                Step {{ index + 1 }}
                                                            </span>

                                                            <span class="text-sm text-surface-500 dark:text-surface-400">
                                                                {{ formatStepType(step.type) || 'New Step' }}
                                                            </span>
                                                        </div>

                                                        <div class="flex shrink-0 items-center gap-2">
                                                            <Button
                                                                icon="pi pi-arrow-up"
                                                                severity="secondary"
                                                                text
                                                                :disabled="index === 0"
                                                                @click="moveStepUp(index)"
                                                            />

                                                            <Button
                                                                icon="pi pi-arrow-down"
                                                                severity="secondary"
                                                                text
                                                                :disabled="index === sequenceForm.steps.length - 1"
                                                                @click="moveStepDown(index)"
                                                            />

                                                            <Button
                                                                icon="pi pi-trash"
                                                                severity="danger"
                                                                text
                                                                @click="removeStep(index)"
                                                            />
                                                        </div>
                                                    </div>

                                                    <!-- CHANGED: Added editable fields for each step -->
                                                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                                                        <div class="flex min-w-0 flex-col">
                                                            <label
                                                                :for="`step-type-${step.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                                :class="{ 'text-red-500': getStepError(index, 'type') }"
                                                            >
                                                                Type
                                                            </label>

                                                            <Select
                                                                :id="`step-type-${step.local_key}`"
                                                                v-model="step.type"
                                                                :options="stepTypeOptions"
                                                                optionLabel="label"
                                                                optionValue="value"
                                                                placeholder="Select step type..."
                                                                fluid
                                                                :invalid="!!getStepError(index, 'type')"
                                                            />

                                                            <Message
                                                                v-if="getStepError(index, 'type')"
                                                                severity="error"
                                                                variant="simple"
                                                                size="small"
                                                                class="mt-2"
                                                            >
                                                                {{ getStepError(index, 'type') }}
                                                            </Message>
                                                        </div>

                                                        <div class="flex min-w-0 flex-col">
                                                            <label
                                                                :for="`step-interval-${step.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                                :class="{ 'text-red-500': getStepError(index, 'interval') }"
                                                            >
                                                                Interval (days)
                                                            </label>

                                                            <InputNumber
                                                                :id="`step-interval-${step.local_key}`"
                                                                v-model="step.interval"
                                                                :min="0"
                                                                :step="1"
                                                                fluid
                                                                :invalid="!!getStepError(index, 'interval')"
                                                            />

                                                            <Message
                                                                v-if="getStepError(index, 'interval')"
                                                                severity="error"
                                                                variant="simple"
                                                                size="small"
                                                                class="mt-2"
                                                            >
                                                                {{ getStepError(index, 'interval') }}
                                                            </Message>
                                                        </div>

                                                        <div class="flex min-w-0 flex-col lg:col-span-2">
                                                            <label
                                                                :for="`step-description-${step.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                                :class="{ 'text-red-500': getStepError(index, 'description') }"
                                                            >
                                                                Description
                                                            </label>

                                                            <Textarea
                                                                :id="`step-description-${step.local_key}`"
                                                                v-model="step.description"
                                                                rows="3"
                                                                autoResize
                                                                fluid
                                                                placeholder="Internal description of this step..."
                                                                :invalid="!!getStepError(index, 'description')"
                                                            />

                                                            <Message
                                                                v-if="getStepError(index, 'description')"
                                                                severity="error"
                                                                variant="simple"
                                                                size="small"
                                                                class="mt-2"
                                                            >
                                                                {{ getStepError(index, 'description') }}
                                                            </Message>
                                                        </div>

                                                        <!-- CHANGED: Wrapped editor in a constrained container so it cannot overflow the card -->
                                                        <div class="flex min-w-0 flex-col lg:col-span-2">
                                                            <label
                                                                :for="`step-template-${step.local_key}`"
                                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                                :class="{ 'text-red-500': getStepError(index, 'template') }"
                                                            >
                                                                Template / Content
                                                            </label>

                                                            <div
                                                                class="sequence-editor-wrapper min-w-0 overflow-hidden rounded-xl border"
                                                                :class="
                                                                    getStepError(index, 'template')
                                                                        ? 'border-red-300 dark:border-red-700'
                                                                        : 'border-surface-200 dark:border-surface-700'
                                                                "
                                                            >
                                                                <Editor
                                                                    :id="`step-template-${step.local_key}`"
                                                                    v-model="step.template"
                                                                    editorStyle="height: 240px"
                                                                />
                                                            </div>

                                                            <Message
                                                                v-if="getStepError(index, 'template')"
                                                                severity="error"
                                                                variant="simple"
                                                                size="small"
                                                                class="mt-2"
                                                            >
                                                                {{ getStepError(index, 'template') }}
                                                            </Message>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CHANGED: Added footer actions -->
                                        <div class="flex justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
                                            <Link :href="route('sequence.show', sequence.id)">
                                                <Button
                                                    label="Cancel"
                                                    severity="secondary"
                                                    outlined
                                                    type="button"
                                                />
                                            </Link>

                                            <Button
                                                label="Save Sequence"
                                                icon="pi pi-save"
                                                :loading="sequenceForm.processing"
                                                @click="submit"
                                            />
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
import Layout from '@/Layouts/Layout.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { Button, Card, Divider, InputNumber, InputText, Message, Select, Textarea } from 'primevue'
import Editor from 'primevue/editor'

const page = usePage()

// CHANGED: Added safe fallback for the loaded sequence payload.
const sequence = page.props.sequence ?? {}

// CHANGED: Added normalized local step builder for stable front-end editing.
const buildStep = (step = {}, index = 0) => {
    return {
        id: step.id ?? null,
        local_key: step.id ?? `new-${Date.now()}-${index}-${Math.random().toString(36).slice(2, 8)}`,
        type: step.type ?? '',
        interval: step.interval ?? 0,
        description: step.description ?? '',
        template: step.template ?? '',
        step_number: step.step_number ?? index + 1,
    }
}

// CHANGED: Added edit form with sequence details and editable steps.
const sequenceForm = useForm({
    name: sequence.name ?? '',
    description: sequence.description ?? '',
    target_audience: sequence.target_audience ?? '',
    steps: (sequence.steps ?? []).map((step, index) => buildStep(step, index)),
})

// CHANGED: Added step type options for the editor.
const stepTypeOptions = [
    { label: 'In-Person Visit', value: 'in_person_visit' },
    { label: 'Email', value: 'email' },
    { label: 'Phone Call', value: 'phone_call' },
    { label: 'Text Message', value: 'text_message' },
    { label: 'Task', value: 'task' },
    { label: 'Snail Mail', value: 'snail_mail' },
    { label: 'Social Media Interaction', value: 'social_media_interaction' },
]

// CHANGED: Added helper to create a new blank step.
const addStep = () => {
    sequenceForm.steps.push(
        buildStep(
            {
                type: '',
                interval: 0,
                description: '',
                template: '',
            },
            sequenceForm.steps.length
        )
    )

    normalizeStepNumbers()
}

// CHANGED: Added helper to remove a step.
const removeStep = (index) => {
    sequenceForm.steps.splice(index, 1)
    normalizeStepNumbers()
}

// CHANGED: Added helper to move a step upward.
const moveStepUp = (index) => {
    if (index <= 0) return

    const step = sequenceForm.steps[index]
    sequenceForm.steps.splice(index, 1)
    sequenceForm.steps.splice(index - 1, 0, step)

    normalizeStepNumbers()
}

// CHANGED: Added helper to move a step downward.
const moveStepDown = (index) => {
    if (index >= sequenceForm.steps.length - 1) return

    const step = sequenceForm.steps[index]
    sequenceForm.steps.splice(index, 1)
    sequenceForm.steps.splice(index + 1, 0, step)

    normalizeStepNumbers()
}

// CHANGED: Added helper to keep step_number aligned to the visible order.
const normalizeStepNumbers = () => {
    sequenceForm.steps = sequenceForm.steps.map((step, index) => ({
        ...step,
        step_number: index + 1,
    }))
}

// CHANGED: Added helper for nested step validation messages.
const getStepError = (index, field) => {
    return sequenceForm.errors[`steps.${index}.${field}`]
}

// CHANGED: Added label formatter for step type display.
const formatStepType = (value) => {
    if (!value) return ''

    return String(value)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}

// CHANGED: Added submit handler for saving the edited sequence.
const submit = () => {
    normalizeStepNumbers()

    sequenceForm.put(route('sequence.update', sequence.id), {
        preserveScroll: true,
    })
}
</script>

<style scoped>
/* CHANGED: Constrained the PrimeVue editor so it cannot overflow its parent card */
.sequence-editor-wrapper {
    width: 100%;
    max-width: 100%;
}

.sequence-editor-wrapper :deep(.p-editor-container) {
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.sequence-editor-wrapper :deep(.ql-toolbar) {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border: 0;
    border-bottom: 1px solid var(--p-content-border-color, #e5e7eb);
}

.sequence-editor-wrapper :deep(.ql-container) {
    width: 100%;
    max-width: 100%;
    border: 0;
}

.sequence-editor-wrapper :deep(.ql-editor) {
    min-height: 240px;
    word-break: break-word;
    overflow-wrap: anywhere;
}

.sequence-editor-wrapper :deep(.ql-picker),
.sequence-editor-wrapper :deep(.ql-formats),
.sequence-editor-wrapper :deep(button),
.sequence-editor-wrapper :deep(span) {
    max-width: 100%;
}
</style>
