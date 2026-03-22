<template>
    <Dialog
        header="Create a User"
        :visible="visible"
        modal
        :style="{ width: '42rem', maxWidth: '95vw' }"
        :breakpoints="{ '1024px': '80vw', '640px': '96vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' },
            header: { class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4' },
            content: { class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0' },
            footer: { class: 'bg-surface-0 dark:bg-surface-900' }
        }"
        @update:visible="onDialogVisibilityChange"
    >
        <form @submit.prevent="onSubmit" class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                    User Details
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Create a new portal user and optionally upload an avatar.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <TextInput
                    label="First Name"
                    id="first_name"
                    v-model="form.first_name"
                    :isError="!!form.errors.first_name"
                    :error="form.errors.first_name"
                    fluid
                />

                <TextInput
                    label="Last Name"
                    id="last_name"
                    v-model="form.last_name"
                    :isError="!!form.errors.last_name"
                    :error="form.errors.last_name"
                    fluid
                />

                <TextInput
                    label="Email"
                    id="email"
                    v-model="form.email"
                    :isError="!!form.errors.email"
                    :error="form.errors.email"
                    fluid
                />

                <div class="flex flex-col">
                    <label
                        for="phone_number"
                        class="mb-2 text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Phone #
                    </label>
                    <InputMask
                        id="phone_number"
                        v-model="form.phone_number"
                        fluid
                        mask="999-999-9999"
                        placeholder="123-456-7890"
                        :class="{
                            '!border-red-500': form.errors.phone_number,
                            '!border-green-500': form.phone_number && !form.errors.phone_number
                        }"
                    />
                    <Message
                        v-if="form.errors.phone_number"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.phone_number }}
                    </Message>
                </div>

                <TextInput
                    label="Password"
                    id="password"
                    v-model="form.password"
                    :isError="!!form.errors.password"
                    :error="form.errors.password"
                    type="password"
                    fluid
                />

                <TextInput
                    label="Confirm Password"
                    id="password_confirm"
                    v-model="form.password_confirm"
                    :isError="!!form.errors.password_confirm"
                    :error="form.errors.password_confirm"
                    type="password"
                    fluid
                />

                <div class="flex flex-col">
                    <label
                        for="role"
                        class="mb-2 text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        User Role
                    </label>
                    <Select
                        id="role"
                        v-model="form.role"
                        fluid
                        placeholder="Select a role..."
                        :class="{
                            '!border-red-500': form.errors.role,
                            '!border-green-500': form.role && !form.errors.role
                        }"
                        :options="options"
                        optionValue="value"
                        optionLabel="label"
                    />
                    <Message
                        v-if="form.errors.role"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.role }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        for="avatar"
                        class="mb-2 text-sm font-medium text-surface-700 dark:text-surface-200"
                    >
                        Upload Avatar
                    </label>

                    <div class="flex flex-col gap-3">
                        <FileUpload
                            mode="basic"
                            accept="image/*"
                            @select="onFileSelect"
                            customUpload
                            chooseLabel="Choose Avatar"
                            class="w-full"
                        />

                        <div
                            v-if="selectedAvatarName"
                            class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 px-3 py-2 text-sm text-surface-600 dark:text-surface-300"
                        >
                            Selected: {{ selectedAvatarName }}
                        </div>
                    </div>

                    <Message
                        v-if="form.errors.avatar"
                        variant="simple"
                        severity="error"
                        size="small"
                        class="mt-2"
                    >
                        {{ form.errors.avatar }}
                    </Message>
                </div>
            </div>

            <div
                class="rounded-2xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800 px-4 py-4"
            >
                <div class="flex items-start gap-3">
                    <Checkbox
                        inputId="email_password_reset"
                        :binary="true"
                        v-model="form.email_password_reset"
                    />
                    <div>
                        <label
                            for="email_password_reset"
                            class="cursor-pointer text-sm font-medium text-surface-800 dark:text-surface-100"
                        >
                            Email Password Reset
                        </label>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Send the user a password reset email instead of relying only on the manually entered password.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-surface-200 dark:border-surface-800 pt-5">
                <Button
                    label="Cancel"
                    type="button"
                    severity="secondary"
                    outlined
                    @click="closeDialog"
                />
                <Button
                    label="Create User"
                    type="submit"
                    icon="pi pi-check-circle"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, Button, Checkbox, InputMask, Message, FileUpload, Select } from 'primevue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    visible: Boolean,
});

const emit = defineEmits(['update:visible', 'users']);

const selectedAvatarName = ref('');

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

const closeDialog = () => {
    form.clearErrors();
    emit('update:visible', false);
};

const onDialogVisibilityChange = (value) => {
    emit('update:visible', value);
};

const onFileSelect = (event) => {
    const file = event.files[0];
    if (file) {
        form.avatar = file;
        selectedAvatarName.value = file.name;
    }
};

const onSubmit = () => {
    form.post(route('user.store'), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            form.clearErrors();
            selectedAvatarName.value = '';
            emit('users', response.props.users);
            emit('update:visible', false);
        },
        onError: (error) => {
            console.error(error);
        },
    });
};

const options = [
    { label: 'Admin', value: 'admin' },
    { label: 'User', value: 'user' },
];
</script>
