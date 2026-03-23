<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- CHANGED: Made the outer wrapper responsive with padding and full-width behavior -->
        <div class="w-full px-4 sm:px-6">
            <!-- CHANGED: Replaced fixed width with responsive max-width and full width -->
            <Card class="mx-auto w-full max-w-3xl">
                <template #title>
                    <!-- CHANGED: Improved header spacing and responsive text sizing -->
                    <div class="mb-5 flex justify-center text-center">
                        <h1 class="text-2xl font-medium dark:text-surface-600 sm:text-3xl">
                            Create an Account
                        </h1>
                    </div>
                </template>

                <template #content>
                    <form @submit.prevent="submit">
                        <!-- CHANGED: Stack fields on mobile, two columns on medium+ screens -->
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <InputText
                                    id="first_name"
                                    v-model="form.first_name"
                                    fluid
                                    placeholder="First Name"
                                    :class="{ '!border-red-500': form.errors.first_name }"
                                />
                                <Message
                                    v-if="form.errors.first_name"
                                    variant="simple"
                                    size="small"
                                    severity="error"
                                >
                                    {{ form.errors.first_name }}
                                </Message>
                            </div>

                            <div>
                                <InputText
                                    id="last_name"
                                    v-model="form.last_name"
                                    fluid
                                    placeholder="Last Name"
                                    :class="{ '!border-red-500': form.errors.last_name }"
                                />
                                <Message
                                    v-if="form.errors.last_name"
                                    variant="simple"
                                    size="small"
                                    severity="error"
                                >
                                    {{ form.errors.last_name }}
                                </Message>
                            </div>

                            <div>
                                <InputMask
                                    id="phone_number"
                                    v-model="form.phone_number"
                                    mask="999-999-9999"
                                    fluid
                                    placeholder="Phone #"
                                    :class="{ '!border-red-500': form.errors.phone_number }"
                                />
                                <Message
                                    v-if="form.errors.phone_number"
                                    variant="simple"
                                    size="small"
                                    severity="error"
                                >
                                    {{ form.errors.phone_number }}
                                </Message>
                            </div>

                            <div>
                                <InputText
                                    id="email"
                                    v-model="form.email"
                                    fluid
                                    placeholder="Email"
                                    :class="{ '!border-red-500': form.errors.email }"
                                />
                                <Message
                                    v-if="form.errors.email"
                                    variant="simple"
                                    size="small"
                                    severity="error"
                                >
                                    {{ form.errors.email }}
                                </Message>
                            </div>

                            <div>
                                <Password
                                    id="password"
                                    v-model="form.password"
                                    fluid
                                    toggleMask
                                    :feedback="false"
                                    placeholder="Password"
                                    :class="{ '!border-red-500': form.errors.password }"
                                />
                                <Message
                                    v-if="form.errors.password"
                                    variant="simple"
                                    size="small"
                                    severity="error"
                                >
                                    {{ form.errors.password }}
                                </Message>
                            </div>

                            <div>
                                <Password
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    fluid
                                    toggleMask
                                    :feedback="false"
                                    placeholder="Confirm Password"
                                    :class="{
                                        '!border-red-500': form.errors.password_confirmation,
                                    }"
                                />
                                <Message
                                    v-if="form.errors.password_confirmation"
                                    variant="simple"
                                    size="small"
                                    severity="error"
                                >
                                    {{ form.errors.password_confirmation }}
                                </Message>
                            </div>
                        </div>

                        <!-- CHANGED: Made footer stack on mobile and align right on larger screens -->
                        <div
                            class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end"
                        >
                            <Link
                                :href="route('login')"
                                class="text-center text-sm text-surface-600 underline hover:text-surface-900 dark:text-surface-500 dark:hover:text-surface-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:text-left"
                            >
                                Already registered?
                            </Link>

                            <!-- CHANGED: Removed duplicate click handler and made button full-width on mobile -->
                            <Button
                                label="Register"
                                type="submit"
                                :loading="form.processing"
                                class="w-full sm:w-auto"
                            />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Message, InputText, Password, Button, Card, InputMask } from 'primevue'

const form = useForm({
    first_name: '',
    last_name: '',
    phone_number: '',
    email: '',
    role: 'user',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>
