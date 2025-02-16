<template>
    <GuestLayout>
        <Head title="Register" />
        <Card class="w-[35rem]">
            <template #title>
                <div class="flex mb-5 justify-center">
                    <h1 class="dark:text-surface-600 font-medium text-3xl">Create an Account</h1>
                </div>
            </template>
            <template #content>
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <InputText fluid placeholder="First Name" v-model="form.first_name" id="first_name" :class="{ '!border-red-500': form.errors.first_name }" />
                            <Message variant="simple" size="small" severity="error" v-if="form.errors.first_name">{{ form.errors.first_name }}</Message>
                        </div>
                        <div>
                            <InputText fluid placeholder="Last Name" v-model="form.last_name" id="last_name" :class="{ '!border-red-500': form.errors.last_name }" />
                            <Message variant="simple" size="small" severity="error" v-if="form.errors.last_name">{{ form.errors.last_name }}</Message>
                        </div>
                        <div>
                            <InputMask mask="999-999-9999" fluid placeholder="Phone #" v-model="form.phone_number" id="phone_number" :class="{ '!border-red-500': form.errors.phone_number }" />
                            <Message variant="simple" size="small" severity="error" v-if="form.errors.phone_number">{{ form.errors.phone_number }}</Message>
                        </div>
                        <div>
                            <InputText fluid placeholder="Email" v-model="form.email" id="email" :class="{ '!border-red-500': form.errors.email }" />
                            <Message variant="simple" size="small" severity="error" v-if="form.errors.email">{{ form.errors.email }}</Message>
                        </div>
                        <div>
                            <Password fluid placeholder="Password" v-model="form.password" id="password" :class="{ '!border-red-500': form.errors.password }" />
                            <Message variant="simple" size="small" severity="error" v-if="form.errors.password">{{ form.errors.password }}</Message>
                        </div>
                        <div>
                            <Password fluid placeholder="Confirm Password" v-model="form.password_confirmation" id="password_confirmation" :class="{ '!border-red-500': form.errors.password_confirmation }" />
                            <Message variant="simple" size="small" severity="error" v-if="form.errors.password_confirmation">{{ form.errors.password_confirmation }}</Message>
                        </div>
                    </div>
                    <div class="mt-5 flex items-center justify-end gap-2">
                        <Link
                            :href="route('login')"
                            class="rounded-md text-sm text-surface-600 underline hover:text-surface-900 dark:text-surface-500 dark:hover:text-surface-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Already registered?
                        </Link>
                        <Button label="Register" type="submit" @click="submit" />
                    </div>
        
                    <!-- <div class="mt-4 flex items-center justify-end">
        
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Register
                        </PrimaryButton>
                    </div> -->
                </form>
            </template>
        </Card>
    </GuestLayout>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Message, InputText, Password, Button, Card, InputMask } from 'primevue';
const form = useForm({
    first_name: '',
    last_name: '',
    phone_number: '',
    email: '',
    role: 'user',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

