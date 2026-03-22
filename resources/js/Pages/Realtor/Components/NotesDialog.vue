<template>
    <Dialog
        :visible="visible"
        modal
        :style="{ width: '42rem', maxWidth: '95vw' }"
        :breakpoints="{ '960px': '92vw', '640px': '96vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' },
            header: {
                class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-6 py-5 text-surface-900 dark:text-surface-0',
            },
            content: {
                class: 'bg-surface-0 dark:bg-surface-900 px-6 py-6 text-surface-900 dark:text-surface-0',
            },
        }"
        @update:visible="emit('update:visible', false)"
        @hide="onDialogHide"
    >
        <template #header>
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <i class="pi pi-file-edit text-lg" />
                </div>

                <div>
                    <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-surface-0">
                        Create Note
                    </h2>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Add an internal note, assign ownership, and mark whether it needs follow-up.
                    </p>
                </div>
            </div>
        </template>

        <form @submit.prevent="onGeneralNoteSubmit" class="mt-2 space-y-6">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="flex flex-col">
                    <label
                        for="priority"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.priority }"
                    >
                        Priority Level
                    </label>
                    <Select
                        id="priority"
                        v-model="form.priority"
                        fluid
                        :class="{ '!border-red-500': form.errors.priority }"
                        :options="generalNotePriorityLevelOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select priority..."
                    />
                    <Message
                        v-if="form.errors.priority"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.priority }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.is_pinned }"
                    >
                        Pinned
                    </label>
                    <div class="rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800">
                        <RadioButtonGroup v-model="form.is_pinned">
                            <div class="flex flex-wrap gap-5">
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="pinned-yes" :value="true" />
                                    <label for="pinned-yes" class="text-surface-700 dark:text-surface-300">Yes</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="pinned-no" :value="false" />
                                    <label for="pinned-no" class="text-surface-700 dark:text-surface-300">No</label>
                                </div>
                            </div>
                        </RadioButtonGroup>
                    </div>
                    <Message
                        v-if="form.errors.is_pinned"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.is_pinned }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        for="assigned_to"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.assigned_to }"
                    >
                        Assigned To
                    </label>
                    <Select
                        id="assigned_to"
                        v-model="form.assigned_to"
                        fluid
                        filter
                        :class="{ '!border-red-500': form.errors.assigned_to }"
                        :options="usersWithNobodyOption"
                        optionLabel="formatted_full_name"
                        optionValue="id"
                        placeholder="Select a user..."
                    />
                    <Message
                        v-if="form.errors.assigned_to"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.assigned_to }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.requires_follow_up }"
                    >
                        Requires Follow-Up
                    </label>
                    <div class="rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800">
                        <RadioButtonGroup v-model="form.requires_follow_up">
                            <div class="flex flex-wrap gap-5">
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="followup-yes" :value="true" />
                                    <label for="followup-yes" class="text-surface-700 dark:text-surface-300">Yes</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <RadioButton inputId="followup-no" :value="false" />
                                    <label for="followup-no" class="text-surface-700 dark:text-surface-300">No</label>
                                </div>
                            </div>
                        </RadioButtonGroup>
                    </div>
                    <Message
                        v-if="form.errors.requires_follow_up"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.requires_follow_up }}
                    </Message>
                </div>
            </div>

            <div class="flex flex-col">
                <label
                    for="general-note"
                    class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                    :class="{ 'text-red-500': form.errors.note }"
                >
                    Note
                </label>
                <Textarea
                    id="general-note"
                    v-model="form.note"
                    fluid
                    rows="6"
                    autoResize
                    :class="{ '!border-red-500': form.errors.note }"
                    placeholder="Enter the note, context, follow-up details, reminders, or anything the team should know..."
                />
                <Message
                    v-if="form.errors.note"
                    size="small"
                    severity="error"
                    variant="simple"
                    class="mt-2"
                >
                    {{ form.errors.note }}
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
                    label="Create Note"
                    type="submit"
                    icon="pi pi-check-circle"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import {
    Dialog,
    Select,
    Textarea,
    Button,
    Message,
    RadioButtonGroup,
    RadioButton,
} from 'primevue'
import { useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { generalNotePriorityLevelOptions, usersWithNobodyOption } from '@/constants'

const emit = defineEmits(['update:visible', 'realtor'])

const props = defineProps({
    visible: Boolean,
    realtor: Object,
})

const realtor = ref(props.realtor)

const form = useForm({
    priority: 'medium',
    is_pinned: false,
    assigned_to: null,
    requires_follow_up: false,
    note: '',
})

watch(
    () => props.realtor,
    (newRealtor) => {
        realtor.value = newRealtor
    },
    { deep: true }
)

const onDialogHide = () => {
    form.reset()
    form.clearErrors()
}

const onGeneralNoteSubmit = () => {
    form.post(route('realtor-note.store', { realtor_id: props.realtor.id }), {
        preserveState: true,
        preserveScroll: true,
        only: ['realtor', 'flash'],
        onSuccess: (response) => {
            realtor.value = response.props.realtor
            form.reset()
            form.clearErrors()
            emit('update:visible', false)
            emit('realtor', realtor.value)
        },
        onError: (error) => {
            console.log(error)
        },
    })
}
</script>
