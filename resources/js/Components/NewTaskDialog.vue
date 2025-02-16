<template>
    <Dialog header="Create a Task" v-model:visible="appStore.taskDialogIsVisible" class="w-[35rem]" @hide="form.reset()">
        <form @submit.prevent="onSubmitTask">
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <InputText :class="{ '!border-red-500': form.errors.title }" fluid placeholder="Title" v-model="form.title"  />
                    <Message variant="simple" severity="error" size="small" v-if="form.errors.title">{{ form.errors.title }}</Message>
                </div>
                <div>
                    <Textarea :class="{ '!border-red-500': form.errors.description }" fluid placeholder="Description" v-model="form.description" />
                    <Message variant="simple" severity="error" size="small" v-if="form.errors.description">{{ form.errors.description }}</Message>
                </div>
                <div>
                    <Select :class="{ '!border-red-500': form.errors.priority }" fluid :options="priorityOptions" optionLabel="label" optionValue="value" placeholder="priority" v-model="form.priority" />
                    <Message variant="simple" severity="error" size="small" v-if="form.errors.priority">{{ form.errors.priority }}</Message>
                </div>
                <div>
                    <Select :class="{ '!border-red-500': form.errors.assigned_to }" fluid :options="userOptions" optionLabel="fullName" optionValue="id" placeholder="Assigned to..." v-model="form.assigned_to" />
                    <Message variant="simple" severity="error" size="small" v-if="form.errors.assigned_to">{{ form.errors.assigned_to }}</Message>
                </div>
                <div>
                    <Select :class="{ '!border-red-500': form.errors.status }" fluid :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" v-model="form.status" />
                    <Message variant="simple" severity="error" size="small" v-if="form.errors.status">{{ form.errors.status }}</Message>
                </div>
                <div>
                    <DatePicker :class="{ '!border-red-500': form.errors.due_date }" fluid placeholder="Due Date" v-model="form.due_date" />
                    <Message variant="simple" severity="error" size="small" v-if="form.errors.due_date">{{ form.errors.due_date }}</Message>
                </div>
            </div>
            <div class="flex justify-end mt-5">
                <Button label="Submit" type="submit" icon="pi pi-check-circle" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, InputText, Textarea, Select, DatePicker, Button, Message } from 'primevue';
import { useAppStore } from '@/Store/appStore';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const appStore = useAppStore();
const props = defineProps({
    users: Array,
});

const emit = defineEmits(['updatedTasks']);

// Create a computed property for user options with full names
const userOptions = computed(() =>
    props.users.map((user) => ({
        ...user,
        fullName: `${user.first_name} ${user.last_name}`,
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

const onSubmitTask = () => {
    // appStore.closeTaskDialog();
    form.post(route('task.store'), {
        only: ['tasks', 'flash'],
        onSuccess: (response) => {
            appStore.setTasks(response.props.data);
            appStore.closeTaskDialog();
        },
        onError: (error) => {
            console.log(form.errors);
        },
    });
}
</script>
