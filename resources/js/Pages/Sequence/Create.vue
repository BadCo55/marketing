<template>
    <Head title="Create a Sequence" />

    <Layout>
        <!-- CHANGED: Added dark mode text support to the page shell -->
        <div class="mx-auto w-full max-w-7xl px-4 py-5 md:px-6 lg:px-8 text-surface-900 dark:text-surface-0">
            <!-- CHANGED: Added dark mode support to header copy -->
            <div class="mb-6 flex flex-col gap-3">
                <div>
                    <Link :href="route('sequence.index')">
                        <Button
                            label="Back to Sequences"
                            icon="pi pi-arrow-left"
                            severity="secondary"
                            text
                        />
                    </Link>
                </div>

                <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <!-- CHANGED: Added explicit dark mode text color -->
                        <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                            Create a Sequence
                        </h1>

                        <!-- CHANGED: Added dark mode subtitle color -->
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Build a reusable outreach sequence with a target audience and ordered steps.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <Button
                            label="Add Step"
                            icon="pi pi-plus-circle"
                            severity="secondary"
                            outlined
                            type="button"
                            @click="addStep"
                        />
                        <Button
                            label="Submit Sequence"
                            icon="pi pi-check-circle"
                            :loading="form.processing"
                            @click="submitSequence"
                        />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-12 items-start gap-5">
                <!-- CHANGED: Added dark mode styling to sticky summary card -->
                <div class="col-span-12 xl:col-span-4 xl:self-start">
                    <div class="space-y-5 xl:sticky xl:top-8">
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <div class="space-y-6">
                                    <div>
                                        <!-- CHANGED: Added dark mode heading color -->
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Sequence Details
                                        </h2>

                                        <!-- CHANGED: Added dark mode helper copy color -->
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Define the name, audience, and overall purpose of this sequence.
                                        </p>
                                    </div>

                                    <div class="space-y-5">
                                        <div class="flex flex-col">
                                            <!-- CHANGED: Added default light/dark label colors -->
                                            <label
                                                for="name"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.name }"
                                            >
                                                Sequence Name
                                            </label>

                                            <InputText
                                                id="name"
                                                v-model="form.name"
                                                fluid
                                                placeholder="Example: New Realtor Follow-Up"
                                                :invalid="!!form.errors.name"
                                            />

                                            <Message
                                                v-if="form.errors.name"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.name }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <!-- CHANGED: Added default light/dark label colors -->
                                            <label
                                                for="description"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.description }"
                                            >
                                                Description
                                            </label>

                                            <Textarea
                                                id="description"
                                                v-model="form.description"
                                                fluid
                                                rows="5"
                                                autoResize
                                                placeholder="Describe the purpose and expected use of this sequence..."
                                                :invalid="!!form.errors.description"
                                            />

                                            <Message
                                                v-if="form.errors.description"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.description }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <!-- CHANGED: Added default light/dark label colors -->
                                            <label
                                                for="target_audience"
                                                class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                                                :class="{ 'text-red-500': form.errors.target_audience }"
                                            >
                                                Target Audience
                                            </label>

                                            <Select
                                                id="target_audience"
                                                v-model="form.target_audience"
                                                :options="targetAudiences"
                                                fluid
                                                placeholder="Select target audience..."
                                                :invalid="!!form.errors.target_audience"
                                            />

                                            <Message
                                                v-if="form.errors.target_audience"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.target_audience }}
                                            </Message>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added dark mode card styling to summary stat tiles -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Steps
                                            </p>
                                            <p class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                                                {{ form.steps.length }}
                                            </p>
                                        </div>

                                        <div class="rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800">
                                            <p class="mb-1 text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                                Audience
                                            </p>
                                            <p class="text-sm font-semibold text-surface-900 dark:text-surface-0">
                                                {{ form.target_audience || 'Not set' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- CHANGED: Added dark mode divider color -->
                                    <div class="flex flex-col gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
                                        <Button
                                            label="Add Step"
                                            icon="pi pi-plus-circle"
                                            severity="secondary"
                                            outlined
                                            type="button"
                                            @click="addStep"
                                        />

                                        <Button
                                            label="Submit Sequence"
                                            icon="pi pi-check-circle"
                                            :loading="form.processing"
                                            @click="submitSequence"
                                        />
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- CHANGED: Added dark mode styling to steps panel -->
                <div class="col-span-12 min-w-0 xl:col-span-8">
                    <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                        <template #content>
                            <div class="space-y-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <!-- CHANGED: Added explicit dark mode heading color -->
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Steps
                                        </h2>

                                        <!-- CHANGED: Added dark mode subtitle color -->
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Add the actions in the order they should happen in the sequence.
                                        </p>
                                    </div>

                                    <Button
                                        icon="pi pi-plus"
                                        label="Add Step"
                                        type="button"
                                        @click="addStep"
                                    />
                                </div>

                                <!-- CHANGED: Added dark mode empty state styling -->
                                <div
                                    v-if="!form.steps.length"
                                    class="flex min-h-72 flex-col items-center justify-center rounded-2xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <i class="pi pi-sitemap mb-3 text-3xl text-surface-400 dark:text-surface-500"></i>
                                    <p class="font-medium text-surface-700 dark:text-surface-200">
                                        No steps added yet.
                                    </p>
                                    <p class="mt-1 max-w-md text-sm text-surface-500 dark:text-surface-400">
                                        Start by adding the first step in your sequence, such as an email, visit, or phone call.
                                    </p>
                                    <Button
                                        class="mt-4"
                                        label="Add First Step"
                                        icon="pi pi-plus-circle"
                                        type="button"
                                        @click="addStep"
                                    />
                                </div>

                                <!-- CHANGED: Added dark mode styling to each step card -->
                                <div v-else class="space-y-4">
                                    <div
                                        v-for="(step, index) in form.steps"
                                        :key="index"
                                        class="rounded-2xl border border-surface-200 bg-surface-50 p-5 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                                    >
                                        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                                            <div class="min-w-0 flex-1">
                                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                                    <span class="rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                                                        Step {{ index + 1 }}
                                                    </span>

                                                    <!-- CHANGED: Added dark mode badge colors -->
                                                    <span class="rounded-full border border-surface-200 bg-surface-0 px-3 py-1 text-sm text-surface-700 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-200">
                                                        {{ camelToNormal(step.type) || 'No type selected' }}
                                                    </span>

                                                    <!-- CHANGED: Added dark mode badge colors -->
                                                    <span class="rounded-full border border-surface-200 bg-surface-0 px-3 py-1 text-sm text-surface-700 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-200">
                                                        {{ step.interval || 0 }} day(s)
                                                    </span>
                                                </div>

                                                <div class="space-y-3">
                                                    <div>
                                                        <!-- CHANGED: Added dark mode label color -->
                                                        <p class="mb-1 text-sm font-medium text-surface-700 dark:text-surface-200">
                                                            Description
                                                        </p>

                                                        <!-- CHANGED: Added dark mode body copy color -->
                                                        <p class="text-sm leading-6 text-surface-600 dark:text-surface-400">
                                                            {{ step.description || 'No description provided.' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="w-full xl:max-w-md">
                                                <!-- CHANGED: Added dark mode label color -->
                                                <p class="mb-2 text-sm font-medium text-surface-700 dark:text-surface-200">
                                                    Template
                                                </p>

                                                <!-- CHANGED: Added dark mode styling to preview panel -->
                                                <div class="max-h-56 overflow-y-auto rounded-xl border border-surface-200 bg-surface-0 px-4 py-4 text-sm text-surface-800 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-200">
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

                                        <!-- CHANGED: Added dark mode divider color -->
                                        <div class="mt-5 flex flex-col gap-3 border-t border-surface-200 pt-4 dark:border-surface-700 sm:flex-row sm:justify-end">
                                            <Button
                                                label="Edit"
                                                icon="pi pi-pen-to-square"
                                                severity="secondary"
                                                outlined
                                                type="button"
                                                @click="editStep(index)"
                                            />
                                            <Button
                                                label="Delete"
                                                icon="pi pi-trash"
                                                severity="danger"
                                                outlined
                                                type="button"
                                                @click="removeStep(index)"
                                            />
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

    <!-- CHANGED: Added dark mode styling to the step dialog -->
    <Dialog
        v-model:visible="showStepForm"
        modal
        :style="{ width: '72rem', maxWidth: '96vw' }"
        :breakpoints="{ '1280px': '96vw', '960px': '96vw' }"
        :header="editingIndex !== null ? 'Edit Step' : 'Add a New Step'"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' },
            header: { class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-6 py-5 text-surface-900 dark:text-surface-0' },
            content: { class: 'bg-surface-0 dark:bg-surface-900 px-6 py-6 text-surface-900 dark:text-surface-0' }
        }"
    >
        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div class="space-y-5">
                    <div class="flex flex-col">
                        <!-- CHANGED: Added dark mode label color -->
                        <label for="step_type" class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                            Type
                        </label>
                        <Select
                            id="step_type"
                            fluid
                            v-model="stepForm.type"
                            :options="stepTypeOptions"
                            placeholder="Select step type..."
                            option-label="label"
                            option-value="value"
                        />
                    </div>

                    <div class="flex flex-col">
                        <!-- CHANGED: Added dark mode label color -->
                        <label for="interval" class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                            Time Interval from First Step
                        </label>
                        <InputNumber
                            id="interval"
                            fluid
                            v-model="stepForm.interval"
                            placeholder="Enter number of days..."
                        />
                        <Message
                            size="small"
                            severity="secondary"
                            variant="simple"
                            class="mt-2"
                        >
                            Enter the number of days from the start of the sequence.
                        </Message>
                    </div>

                    <div class="flex flex-col">
                        <!-- CHANGED: Added dark mode label color -->
                        <label for="description" class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                            Description
                        </label>
                        <Textarea
                            id="description"
                            fluid
                            v-model="stepForm.description"
                            class="h-48"
                            autoResize
                            placeholder="Describe what should happen in this step..."
                        />
                    </div>
                </div>

                <div class="flex flex-col">
                    <!-- CHANGED: Added dark mode label color -->
                    <label for="template" class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                        Template
                    </label>
                    <Editor
                        id="template"
                        v-model="stepForm.template"
                        editorStyle="height: 400px"
                    />
                </div>
            </div>

            <!-- CHANGED: Added dark mode divider color to dialog footer -->
            <div class="flex flex-col-reverse gap-3 border-t border-surface-200 pt-5 dark:border-surface-800 sm:flex-row sm:justify-end">
                <Button
                    label="Cancel"
                    severity="secondary"
                    outlined
                    type="button"
                    @click="closeStepDialog"
                />
                <Button
                    :label="editingIndex !== null ? 'Save Step' : 'Add Step'"
                    icon="pi pi-check-circle"
                    type="button"
                    @click="submitStep"
                />
            </div>
        </div>
    </Dialog>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { camelToNormal } from '@/utilities/formatString';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Card,
    InputText,
    Textarea,
    Select,
    Button,
    Dialog,
    Message,
    InputNumber,
} from 'primevue';
import Editor from 'primevue/editor';
import { ref } from 'vue';

const showStepForm = ref(false);
const editingIndex = ref(null);

const form = useForm({
    name: '',
    description: '',
    target_audience: '',
    steps: [],
});

const stepForm = useForm({
    type: '',
    interval: null,
    description: '',
    template: '',
});

const targetAudiences = ['Realtor', 'Office', 'Realtor & Office'];

const stepTypeOptions = [
    { label: 'In-Person Visit', value: 'in_person_visit' },
    { label: 'Email', value: 'email' },
    { label: 'Phone Call', value: 'phone_call' },
    { label: 'Text Message', value: 'text_message' },
    { label: 'Task', value: 'task' },
    { label: 'Snail Mail', value: 'snail_mail' },
    { label: 'Social Media Interaction', value: 'social_media_interaction' },
]

// CHANGED: Kept dialog reset logic centralized
const closeStepDialog = () => {
    stepForm.reset();
    editingIndex.value = null;
    showStepForm.value = false;
};

// CHANGED: Reset step form before opening a new step dialog
const addStep = () => {
    editingIndex.value = null;
    stepForm.reset();
    showStepForm.value = true;
};

// CHANGED: Build step payload once and reuse for create/update
const submitStep = () => {
    if (!stepForm.type) return;

    const payload = {
        type: stepForm.type,
        interval: stepForm.interval,
        description: stepForm.description,
        template: stepForm.template,
    };

    if (editingIndex.value !== null) {
        form.steps[editingIndex.value] = payload;
    } else {
        form.steps.push(payload);
    }

    closeStepDialog();
};

// CHANGED: Load step values into the dialog for editing
const editStep = (index) => {
    editingIndex.value = index;
    stepForm.type = form.steps[index].type;
    stepForm.interval = form.steps[index].interval;
    stepForm.description = form.steps[index].description;
    stepForm.template = form.steps[index].template;
    showStepForm.value = true;
};

// CHANGED: Remove step by index
const removeStep = (index) => {
    form.steps.splice(index, 1);
};

// CHANGED: Submit full sequence as before
const submitSequence = () => {
    form.post(route('sequence.store'), {
        onSuccess: () => {},
        onError: (error) => {
            console.log(error);
        },
    });
};
</script>
