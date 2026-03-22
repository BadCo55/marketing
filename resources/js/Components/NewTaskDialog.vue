<template>
    <Dialog
        v-model:visible="appStore.taskDialogIsVisible"
        modal
        :style="{ width: '42rem', maxWidth: '95vw' }"
        :breakpoints="{ '1024px': '92vw', '640px': '96vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' },
            header: {
                class: 'border-b border-surface-200 dark:border-surface-800 px-6 py-5 bg-surface-0 dark:bg-surface-900',
            },
            content: {
                class: 'px-6 py-6 bg-surface-0 dark:bg-surface-900 text-surface-900 dark:text-surface-0',
            },
        }"
        @hide="onDialogHide"
    >
        <template #header>
            <div class="flex items-start gap-3">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary"
                >
                    <i class="pi pi-check-square text-lg" />
                </div>

                <div>
                    <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-surface-0">
                        Create a Task
                    </h2>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Add a new task and assign responsibility, priority, and due date.
                    </p>
                </div>
            </div>
        </template>

        <form @submit.prevent="onSubmitTask" class="space-y-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label
                        class="my-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Title
                    </label>
                    <InputText
                        v-model="form.title"
                        fluid
                        placeholder="Enter task title"
                        :class="{ 'p-invalid': form.errors.title }"
                    />
                    <Message
                        v-if="form.errors.title"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.title }}
                    </Message>
                </div>

                <div class="md:col-span-2">
                    <label
                        class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Description
                    </label>
                    <Textarea
                        v-model="form.description"
                        fluid
                        rows="4"
                        autoResize
                        placeholder="Add task details, notes, or instructions"
                        :class="{ 'p-invalid': form.errors.description }"
                    />
                    <Message
                        v-if="form.errors.description"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.description }}
                    </Message>
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Priority
                    </label>
                    <Select
                        v-model="form.priority"
                        fluid
                        :options="priorityOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select priority"
                        :class="{ 'p-invalid': form.errors.priority }"
                    />
                    <Message
                        v-if="form.errors.priority"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.priority }}
                    </Message>
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Assigned To
                    </label>
                    <Select
                        v-model="form.assigned_to"
                        fluid
                        :options="userOptions"
                        optionLabel="fullName"
                        optionValue="id"
                        filter
                        placeholder="Select team member"
                        :class="{ 'p-invalid': form.errors.assigned_to }"
                    />
                    <Message
                        v-if="form.errors.assigned_to"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.assigned_to }}
                    </Message>
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Status
                    </label>
                    <Select
                        v-model="form.status"
                        fluid
                        :options="statusOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select status"
                        :class="{ 'p-invalid': form.errors.status }"
                    />
                    <Message
                        v-if="form.errors.status"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.status }}
                    </Message>
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Due Date
                    </label>
                    <DatePicker
                        v-model="form.due_date"
                        fluid
                        showIcon
                        placeholder="Select due date"
                        :class="{ 'p-invalid': form.errors.due_date }"
                    />
                    <Message
                        v-if="form.errors.due_date"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.due_date }}
                    </Message>
                </div>
            </div>

            <div
                class="flex flex-col-reverse gap-3 border-t border-surface-200 pt-5 dark:border-surface-800 sm:flex-row sm:justify-end"
            >
                <Button
                    type="button"
                    label="Cancel"
                    severity="secondary"
                    outlined
                    @click="appStore.closeTaskDialog()"
                />

                <Button
                    label="Create Task"
                    type="submit"
                    icon="pi pi-check-circle"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, InputText, Textarea, Select, DatePicker, Button, Message } from 'primevue';
import { useAppStore } from '@/Store/appStore';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const appStore = useAppStore();

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['updatedTasks']);

const userOptions = computed(() =>
    props.users.map((user) => ({
        ...user,
        fullName: [user.first_name, user.last_name].filter(Boolean).join(' '),
    }))
);

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const priorityOptions = [
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
];

const form = useForm({
    title: '',
    description: '',
    assigned_to: '',
    due_date: '',
    status: 'pending',
    priority: 'medium',
});

const onDialogHide = () => {
    form.reset();
    form.clearErrors();
};

const onSubmitTask = () => {
    form.post(route('task.store'), {
        only: ['tasks', 'flash'],
        preserveScroll: true,
        onSuccess: (page) => {
            appStore.setTasks(page.props.tasks ?? []);
            appStore.closeTaskDialog();
            form.reset();
            form.clearErrors();
            emit('updatedTasks', page.props.tasks ?? []);
        },
        onError: () => {
            console.log(form.errors);
        },
    });
};
</script>
