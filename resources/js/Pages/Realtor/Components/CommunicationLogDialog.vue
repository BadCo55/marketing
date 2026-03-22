<template>
    <Dialog
        :visible="visible"
        modal
        header="Log Communication"
        :style="{ width: '38rem', maxWidth: '95vw' }"
        :breakpoints="{ '960px': '92vw', '640px': '96vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' },
            header: { class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-6 py-5' },
            content: { class: 'bg-surface-0 dark:bg-surface-900 px-6 py-6 text-surface-900 dark:text-surface-0' },
        }"
        @update:visible="emit('update:visible', false)"
        @hide="onDialogHide"
    >
        <template #header>
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <i class="pi pi-comments text-lg" />
                </div>

                <div>
                    <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-surface-0">
                        Log Communication
                    </h2>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Add a communication entry for this realtor so future follow-up has context.
                    </p>
                </div>
            </div>
        </template>

        <form @submit.prevent="onFormSubmit" class="space-y-6">
            <div>
                <label
                    for="communication_method"
                    class="my-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                >
                    Communication Method
                </label>

                <Select
                    id="communication_method"
                    v-model="form.communication_method"
                    :options="communicationOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select a communication method..."
                    fluid
                    :invalid="!!form.errors.communication_method"
                />

                <Message
                    v-if="form.errors.communication_method"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2"
                >
                    {{ form.errors.communication_method }}
                </Message>
            </div>

            <div>
                <label
                    for="summary"
                    class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                >
                    Summary
                </label>

                <InputText
                    id="summary"
                    v-model="form.summary"
                    fluid
                    placeholder="Example: Introduced DHI and discussed pre-listing inspections"
                    :invalid="!!form.errors.summary"
                />

                <Message
                    v-if="form.errors.summary"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2"
                >
                    {{ form.errors.summary }}
                </Message>
            </div>

            <div>
                <label
                    for="details"
                    class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                >
                    Details
                </label>

                <Textarea
                    id="details"
                    v-model="form.details"
                    fluid
                    rows="5"
                    autoResize
                    placeholder="Add more detail about the conversation, outcome, objections, follow-up items, next step, etc."
                    :invalid="!!form.errors.details"
                />

                <Message
                    v-if="form.errors.details"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-2"
                >
                    {{ form.errors.details }}
                </Message>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-surface-200 pt-5 dark:border-surface-800 sm:flex-row sm:justify-end">
                <Button
                    label="Cancel"
                    type="button"
                    severity="secondary"
                    outlined
                    @click="emit('update:visible', false)"
                />

                <Button
                    label="Save Log"
                    type="submit"
                    icon="pi pi-check-circle"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, Select, InputText, Textarea, Button, Message } from 'primevue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    realtor_id: Number,
    visible: Boolean,
});

const emit = defineEmits(['update:visible', 'realtor']);

const form = useForm({
    realtor_id: props.realtor_id,
    communication_method: '',
    summary: '',
    details: '',
});

const communicationOptions = [
    { label: 'Phone Call', value: 'phone_call' },
    { label: 'Email', value: 'email' },
    { label: 'Text Message', value: 'text_message' },
    { label: 'Social Media Interaction', value: 'social_media' },
    { label: 'Office Visit', value: 'office_visit' },
    { label: 'In-Person Meeting', value: 'in_person_meeting' },
    { label: 'Other', value: 'other' },
];

watch(
    () => props.realtor_id,
    (newValue) => {
        form.realtor_id = newValue;
    }
);

const onDialogHide = () => {
    form.reset();
    form.clearErrors();
    form.realtor_id = props.realtor_id;
};

const onFormSubmit = () => {
    form.post(route('communication-log.store'), {
        only: ['realtor', 'flash'],
        preserveScroll: true,
        onSuccess: (response) => {
            emit('update:visible', false);
            emit('realtor', response.props.realtor);
            form.reset();
            form.clearErrors();
            form.realtor_id = props.realtor_id;
        },
        onError: (error) => {
            console.log(error);
        },
    });
};
</script>
