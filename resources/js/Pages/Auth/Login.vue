<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { InputText, Card, Password, Checkbox, Button, Message } from 'primevue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
        <Card class="w-[20rem]">
            <template #content>
                <form @submit.prevent="submit">
                    <div>
                        <label for="email" class="dark:text-surface-500">Email</label>
        
                        <InputText
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            autofocus
                            autocomplete="username"
                        />
                        <Message variant="simple" size="small" severity="error" v-if="form.errors.email">{{ form.errors.email }}</Message>
                    </div>
        
                    <div class="mt-4">
                        <label for="password" class="dark:text-surface-500">Password</label>

                        <Password
                            :feedback="false"
                            id="password"
                            class="mt-1 block w-full"
                            fluid
                            v-model="form.password"
                            autocomplete="current-password"
                        />
                        <Message variant="simple" size="small" severity="error" v-if="form.errors.password">{{ form.errors.password }}</Message>
                    </div>
        
                    <div class="mt-4 block">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model="form.remember" :binary="true" />
                            <span class="ms-2 text-sm text-surface-500"
                                >Remember me</span
                            >
                        </label>
                    </div>
        
                    <div class="mt-4 flex items-center justify-end">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="rounded-md text-sm text-surface-500 underline hover:text-surface-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Forgot your password?
                        </Link>
        
                        <Button
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            type="submit"
                        >
                            Log in
                        </Button>
                    </div>
                </form>
            </template>
        </Card>
    </GuestLayout>
</template>
