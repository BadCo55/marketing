<template>
    <Dialog header="Create a User" :visible="visible" @update:visible="emit('update:visible')" :style="{ width: '40rem' }">
        <form @submit.prevent="onSubmit">
            <div class="grid grid-cols-2 gap-5">
                <TextInput label="First Name" id="first_name" v-model="form.first_name" :isError="form.errors.first_name ? true : false" :error="form.errors.first_name" />
                <TextInput label="Last Name" id="last_name" v-model="form.last_name" :isError="form.errors.last_name ? true : false" :error="form.errors.last_name" />
                <TextInput label="Email" id="email" v-model="form.email" :isError="form.errors.email ? true : false" :error="form.errors.email" />
                <div class="flex flex-col">
                    <label for="phone_number">Phone #</label>
                    <InputMask
                        id="phone_number"
                        v-model="form.phone_number"
                        :class="{
                            '!border-red-500': form.errors.phone_number,
                            '!border-green-500': form.phone_number && !form.errors.phone_number,
                        }"
                        mask="999-999-9999"
                    />
                    <Message v-if="form.errors.phone_number" variant="simple" severity="error" size="small">{{ form.errors.phone_number }}</Message>
                </div>
                <TextInput label="Password" id="password" v-model="form.password" :isError="form.errors.password ? true : false" :error="form.errors.password" type="password" />
                <TextInput label="Confirm Password" id="password_confirm" v-model="form.password_confirm" :isError="form.errors.password_confirm ? true : false" :error="form.errors.password_confirm" type="password" />
                <div class="flex flex-col">
                    <label for="role">User Role</label>
                    <Select
                        id="role"
                        v-model="form.role"
                        :class="{
                            '!border-red-500': form.errors.role,
                            '!border-green-500': form.role && !form.errors.role,
                        }"
                        :options="options"
                        optionValue="value"
                        optionLabel="label"
                    />
                    <Message v-if="form.errors.role" variant="simple" severity="error" size="small">{{ form.errors.role }}</Message>
                </div>
                <!-- File Upload Section -->
                <div class="flex flex-col">
                    <label for="avatar">Upload Avatar</label>
                    <FileUpload mode="basic" @select="onFileSelect" customUpload class="mt-1" />
                    <Message v-if="form.errors.avatar" variant="simple" severity="error">{{ form.errors.avatar }}</Message>
                </div>
                <div class="inline-flex gap-2">
                    <Checkbox :binary="true" v-model="form.email_password_reset"  />
                    <label for="email_password">Email Password Reset</label>
                </div>
            </div>
            <div class="flex justify-end mt-5 gap-3">
                <Button label="Cancel" severity="secondary" @click="emit('update:visible', false)" />
                <Button label="Submit" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, Button, Checkbox, InputMask, Message, FileUpload, Select } from 'primevue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
const props = defineProps({
    visible: Boolean,
});
const emit = defineEmits([
    'update:visible',
    'users'
]);
const form = useForm({
    first_name: '',
    last_name: '',
    phone_number: '',
    email: '',
    avatar: '',
    role: '',
    password: '',
    password_confirm: '',
    email_password_reset: false,
});
const onFileSelect = (event) => {
    const file = event.files[0];
    if (file) {
        form.avatar = file;
    }
}
const onSubmit = () => {
    form.post(route('user.store'), {
        onSuccess: (response) => {
            form.reset();
            emit('users', response.props.users);
            emit('update:visible', false);
        },
        onError: (error) => {
            console.error(error);
        }
    });
}
const options = [
    { label: 'Admin', value: 'admin' },
    { label: 'User', value: 'user' },
]
</script>
