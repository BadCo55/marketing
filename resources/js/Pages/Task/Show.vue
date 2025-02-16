<template>
    <Head title="Task Details" />
    <Layout>
        <div class="flex gap-5 p-8">
            <Card class="mb-auto w-[30rem]">
                <template #content>
                    <div class="flex gap-2 mb-2">
                        <Message severity="error" size="small" icon="pi pi-exclamation-circle" v-if="task.is_passed_due && task.status !== 'completed'">
                            <p class="font-medium">PASSED DUE</p>
                        </Message>
                        <Message severity="warn" size="small" icon="pi pi-exclamation-circle" v-if="task.is_due_today && task.status !== 'completed'">
                            <p class="font-medium">DUE TODAY</p>
                        </Message>
                        <Message severity="success" size="small" icon="pi pi-check-circle" v-if="task.status === 'completed'">
                            <p class="font-medium">COMPLETE</p>
                        </Message>
                    </div>
                    <div class="dark:text-surface-300">
                        <h2 class="text-xl font-medium">{{ task.title }}</h2>
                        <p>{{ task.description ? task.description : 'No description found.' }}</p>
                        <div class="grid grid-cols-2 gap-x-2 mt-5">
                            <p class="font-bold">Created By:</p>
                            <p class="text-end">{{ task.created_by.formatted_full_name }}</p>
                            <p class="font-bold">Created At:</p>
                            <p class="text-end">{{ task.formatted_created_at }}</p>
                            <p class="font-bold">Due Date:</p>
                            <p class="text-end">{{ task.formatted_due_date }}</p>
                            <p class="font-bold">Status:</p>
                            <p class="text-end">{{ task.formatted_status }}</p>
                        </div>
                    </div>
                </template>
            </Card>
            <Card class="w-full">
                <template #content>
                    <div class="flex gap-10">
                        <div class="flex flex-col w-96">
                            <label for="">Status</label>
                            <Select v-model="status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status"></Select>
                            <form @submit.prevent="onNoteSubmit">
                                <div class="flex flex-col mt-5">
                                    <label for="">Create a Note</label>
                                    <Textarea v-model="noteForm.note" class="h-36" />
                                </div>
                                <div class="flex justify-end mt-5">
                                    <Button label="Submit" type="submit" />
                                </div>
                            </form>
                        </div>
                        <div class="flex-1 space-y-5 overflow-y-scroll p-5 border dark:border-surface-800 rounded max-h-[40rem]">
                            <div v-for="note in task.notes" :key="note.id" class="dark:bg-surface-800 bg-surface-50 border rounded p-5 dark:text-surface-300">
                                <p class="p-2 dark:bg-surface-900 bg-surface-0 border rounded">{{ note.note }}</p>
                                <div class="flex flex-col items-end mt-3">
                                    <p class="">{{ note.created_by.formatted_full_name }}</p>
                                    <p>{{ note.formatted_created_at }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { usePage, Head, useForm, router } from '@inertiajs/vue3';
import { Card, Message, Select, Textarea, Button } from 'primevue';
import { watch, ref } from 'vue';

const page = usePage();
const task = ref(page.props.task);

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
];

const status = ref(task.value.status);

watch(status, (newVal) => {
    if (newVal) {
        router.put(route('task.update', task.value.id), 
        { 
            request_type: 'status_update',
            status: status.value 
        },
        {
            only: ['task', 'flash'],
            onSuccess: (response) => {
                task.value = response.props.task;
            },
            onError: (error) => {
                console.log(error);
            }
        })
    }
});

const noteForm = useForm({
    note: '',
    task_id: task.value.id,
});

const onNoteSubmit = () => {
    noteForm.post(route('task-note.store'), {
        only: ['task', 'flash'],
        onSuccess: (response) => {
            task.value = response.props.task;
            noteForm.note = '';
        },
        onError: (error) => {
            console.log(error);
        }
    });
}

</script>