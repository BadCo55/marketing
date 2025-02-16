<template>
    <Head title="Users" />
    <Layout>
        <div class="flex w-full">
            <DataTable 
                class="w-full" 
                v-model:filters="filters"
                :value="users" 
                removableSort 
                stripedRows 
                paginator 
                :rows="10" 
                :rowsPerPageOptions="[10, 25, 50, 100]"
                :globalFilterFields="['']"
            >
                <template #header>
                    <h2 class="text-xl font-medium mb-2">Filters</h2>
                    <div class="flex items-center justify-between">
                        <div class="flex gap-2 bg-surface-100 dark:bg-surface-800 p-2 rounded">
                            <div>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText placeholder="Filter Search" />
                                </IconField>
                            </div>
                            <div class="flex gap-2 items-center">
                                <Select :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Select a status..." />
                                <Select :options="users" optionLabel="name" optionValue="id" placeholder="Select an assigned user..." />
                                <div class="flex items-center gap-1">
                                    <Checkbox :binary="true"  />
                                    <label for="">Has Due Date</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <Button label="Create User" icon="pi pi-plus-circle" @click="isNewUserDialogVisible = true" />
                        </div>
                    </div>
                </template>
                <Column header="Name" field="name"></Column>
                <Column header="Email" field="email"></Column>
                <Column header="Created At" field="created_at"></Column>
                <Column class="w-24 !text-center">
                    <template #body="{ data }">
                        <Link
                            :href="route('user.show', data.id)"
                        >
                            <Button icon="pi pi-search" severity="primary" rounded></Button>
                        </Link>
                    </template>
                </Column>
            </DataTable>
        </div>
    </Layout>
    <Dialog header="Create a User" v-model:visible="isNewUserDialogVisible" class="w-[35rem]">
        <form @submit.prevent="onFormSubmit">
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <FloatLabel variant="in">
                        <InputText :class="{ '!border-red-500': form.errors.first_name }" fluid v-model="form.first_name" id="first_name" />
                        <label for="first_name">First Name</label>
                    </FloatLabel>
                    <Message size="small" variant="simple" severity="error" v-if="form.errors.first_name">{{ form.errors.first_name }}</Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputText :class="{ '!border-red-500': form.errors.last_name }" fluid v-model="form.last_name" id="last_name" />
                        <label for="last_name">Last Name</label>
                    </FloatLabel>
                    <Message size="small" variant="simple" severity="error" v-if="form.errors.last_name">{{ form.errors.last_name }}</Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputMask :class="{ '!border-red-500': form.errors.phone_number }" mask="999-999-9999" fluid v-model="form.phone_number" id="phone_number" />
                        <label for="phone_number">Phone #</label>
                    </FloatLabel>
                    <Message size="small" variant="simple" severity="error" v-if="form.errors.phone_number">{{ form.errors.phone_number }}</Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <InputText :class="{ '!border-red-500': form.errors.email }" fluid v-model="form.email" id="email" />
                        <label for="email">Email</label>
                    </FloatLabel>
                    <Message size="small" variant="simple" severity="error" v-if="form.errors.email">{{ form.errors.email }}</Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <Select :class="{ '!border-red-500': form.errors.role }" :options="roleOptions" optionLabel="label" optionValue="value" fluid v-model="form.role" id="role" />
                        <label for="role">Role</label>
                    </FloatLabel>
                    <Message size="small" variant="simple" severity="error" v-if="form.errors.role">{{ form.errors.role }}</Message>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-5 mt-5">
                <div>
                    <FloatLabel variant="in">
                        <Password :class="{ '!border-red-500': form.errors.password }" fluid v-model="form.password" id="password" />
                        <label for="password">Password</label>
                    </FloatLabel>
                    <Message size="small" variant="simple" severity="error" v-if="form.errors.password">{{ form.errors.password }}</Message>
                </div>
                <div>
                    <FloatLabel variant="in">
                        <Password :class="{ '!border-red-500': form.errors.password_confirm }" fluid v-model="form.password_confirm" id="password_confirm" />
                        <label for="password_confirm">Confirm Password</label>
                    </FloatLabel>
                    <Message size="small" variant="simple" severity="error" v-if="form.errors.password_confirm">{{ form.errors.password_confirm }}</Message>
                </div>
            </div>
            <div class="flex justify-end mt-5">
                <Button label="Submit" icon="pi pi-check-circle" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePage, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { DataTable, Column, InputText, InputIcon, IconField, Select, Checkbox, Button, Dialog, FloatLabel, Message, InputMask, Password } from 'primevue';

const page = usePage();
const users = page.props.users;

/**
 * State variables
 */
const isNewUserDialogVisible = ref(false);

/**
 * Select options
 */
const roleOptions = [
    { value: 'admin', label: 'Admin' },
    { value: 'user', label: 'User' },
];

/**
 * Create user form
 */
const form = useForm({
    first_name: '',
    last_name: '',
    phone_number: '',
    email: '',
    role: '',
    password: '',
    password_confirm: '',
});

/**
 * Submit user form
 */
const onFormSubmit = () => {
    form.post(route('user.store'), {
        onSuccess: () => {

        }, 
        onError: () => {

        },
    })
}

const filters = ref();

</script>