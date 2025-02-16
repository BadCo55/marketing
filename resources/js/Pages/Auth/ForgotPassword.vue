<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { InputText, Button, Card, Message } from 'primevue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />
        <Card class="w-[25rem]">
            <template #content>
                <div class="mb-4 text-sm dark:text-surface-500">
                    Forgot your password? No problem. Just let us know your email
                    address and we will email you a password reset link that will allow
                    you to choose a new one.
                </div>
        
                <div
                    v-if="status"
                    class="mb-4 text-sm font-medium text-green-600"
                >
                    {{ status }}
                </div>
        
                <form @submit.prevent="submit">
                    <div>
                        <label for="email" class="dark:text-surface-500">Email</label>
        
                        <InputText
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <Message variant="simple" size="small" severity="error" v-if="form.errors.email">{{ form.errors.email }}</Message>
                    </div>
        
                    <div class="mt-4 flex items-center justify-end">
                        <Button
                            class="!text-uppercase"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            type="submit"
                        >
                            Email Password Reset Link
                        </Button>
                    </div>
                </form>
            </template>
        </Card>
    </GuestLayout>
</template>
