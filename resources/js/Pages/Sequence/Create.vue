<template>
    <Head title="Create a Sequence" />
    <Layout>
        <div class="flex gap-5 p-8">
            <div>
                <Card class="flex w-[25rem] gap-5">
                    <template #content>
                        <div class="grid grid-cols-1 gap-8">
                            <div class="my-3">
                                <FloatLabel>
                                    <InputText :invalid="page.props.errors.name" fluid v-model="form.name" id="name" />
                                    <label for="name">Sequence Name</label>
                                </FloatLabel>
                                <small class="text-red-500" v-if="page.props.errors.name">{{ page.props.errors.name }}</small>
                            </div>
                            <div>
                                <FloatLabel>
                                    <Textarea :invalid="page.props.errors.description" fluid v-model="form.description" id="description" />
                                    <label for="description">Description</label>
                                </FloatLabel>
                                <small class="text-red-500" v-if="page.props.errors.description">{{ page.props.errors.description }}</small>
                            </div>
                            <div>
                                <FloatLabel>
                                    <Select :invalid="page.props.errors.target_audience" fluid v-model="form.target_audience" id="target_audience" :options="targetAudiences" />
                                    <label for="target_audience">Target Audience</label>
                                </FloatLabel>
                                <small class="text-red-500" v-if="page.props.errors.target_audience">{{ page.props.errors.target_audience }}</small>
                            </div>
                        </div>
                    </template>
                </Card>
                <div class="flex mt-5">
                    <Button fluid label="Submit Sequence" icon="pi pi-check-circle" @click="submitSequence" />
                </div>
            </div>
            <Card class="flex w-full">
                <template #content>
                    <h3 class="text-xl font-bold mb-3">Steps</h3>
                    <div v-for="(step, index) in form.steps" :key="index" class="mb-3">
                        <div class="bg-surface-50 dark:bg-surface-800 rounded-lg p-2 shadow">
                            <div class="flex gap-5">
                                <div class="w-full">
                                    <p><strong>Step {{ index + 1 }}:</strong> {{ step.type }}</p>
                                    <p><strong>Interval: </strong> {{ step.interval }} day(s)</p>
                                    <p><strong>Description: </strong>{{ step.description }}</p>
                                </div>
                                <div class="w-full">
                                    <p class="mb-2"><strong>Template</strong></p>
                                    <div class="bg-surface-0 border dark:bg-surface-900 rounded-lg px-5 py-3 max-h-48 overflow-y-scroll">
                                        <template v-if="step.template">
                                            <div v-html="step.template"></div>
                                        </template>
                                        <template v-else>No template found</template>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-5 bg-surface-0 p-3 border rounded-lg w-fit ml-auto">
                                <Button label="Edit" icon="pi pi-pen-to-square" @click="editStep" />
                                <Button label="Delete" icon="pi pi-trash" @click="removeStep" />
                            </div>
                        </div>
                    </div>
                    <Button icon="pi pi-plus" label="Add Step" class="mt-3" @click="addStep" />
                </template>
            </Card>
        </div>
    </Layout>
    <Dialog class="w-[50%] h-[80%]" v-model:visible="showStepForm" header="Add a New Step">
        <div class="flex gap-5">
            <!-- Left Column -->
            <div class="flex-1 space-y-5">
                <div class="flex flex-col">
                    <label for="step_type" class="mb-1 ms-2">Type</label>
                    <Select id="step_type" fluid v-model="stepForm.type" :options="stepTypeOptions" />
                </div>
                <div class="flex flex-col">
                    <label for="interval" class="mb-1 ms-2">Time Interval from First Step</label>
                    <InputNumber id="interval" fluid v-model="stepForm.interval" />
                    <Message size="small" severity="secondary" variant="simple" class="ms-2">Enter in # of days.</Message>
                </div>
                <div class="flex flex-col">
                    <label for="description" class="mb-1 ms-2">Description</label>
                    <Textarea id="description" fluid v-model="stepForm.description" class="h-48" />
                </div>
            </div>
            <!-- Right Column -->
            <div class="flex-1 flex flex-col">
                <label for="template" class="mb-1 ms-2">Template</label>
                <Editor id="template" v-model="stepForm.template" editorStyle="height: 400px" />
            </div>
        </div>
        <div class="flex justify-end mt-10">
            <Button label="Submit" @click="submitStep" />
        </div>
    </Dialog>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import { Card, InputText, FloatLabel, Textarea, Select, Button, Dialog, Message, InputNumber } from 'primevue';
import Editor from 'primevue/editor';
import { ref } from 'vue';

const page = usePage();
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
    interval: '',
    description: '',
    template: '',
});

const targetAudiences = [
    'Realtor',
    'Office',
    'Realtor & Office'
];

const stepTypeOptions = [
    'In-Person Visit',
    'Email',
    'Phone Call',
    'Text Message',
    'Snail Mail',
    'Social Media Interaction'
];

const addStep = () => {
    editingIndex.value = null; // Ensure we're not editing
    stepForm.value = { type: '', interval: '', description: '', template: '' };
    showStepForm.value = true;
};

const submitStep = () => {
    if (editingIndex.value !== null) {
        // Update existing step
        form.steps[editingIndex.value] = {
            type: stepForm.type,
            interval: stepForm.interval,
            description: stepForm.description,
            template: stepForm.template,
        };

    } else {
        // Add a new step
        form.steps.push({
            type: stepForm.type,
            interval: stepForm.interval,
            description: stepForm.description,
            template: stepForm.template,
        });

    }
    // Reset form and close dialog
    // stepForm.value = { type: '', interval: '', description: '', template: '' };
    stepForm.reset();
    editingIndex.value = null;
    showStepForm.value = false;
};

const editStep = (index) => {
    editingIndex.value = index; // Track the index
    stepForm.value = { ...form.steps[index] }; // Load step data into stepForm
    showStepForm.value = true; // Open the dialog
};

const removeStep = (index) => {
    form.steps.splice(index, 1); // Remove the step from the array
};

const submitSequence = () => {
    form.post(route('sequence.store'), {
        onSuccess: () => {

        },
        onError: (error) => {
            console.log(error);
        }
    })
};
</script>
