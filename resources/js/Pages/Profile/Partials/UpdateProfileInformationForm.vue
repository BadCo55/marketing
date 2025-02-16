<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { InputText, Button } from 'primevue';
import InputError from '@/Components/InputError.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    first_name: user.first_name,
    last_name: user.last_name,
    email: user.email,
    phone_number: user.phone_number,
});

const onSubmit = () => {
    form.patch(route('profile.update'), {
        onSuccess: (response) => {
            console.log(response);
        },
        onError: (error) => {
            console.error(error);
        }
    })
}

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="onSubmit"
            class="mt-5 space-y-5"
        >
            <div class="grid grid-cols-2 gap-5">
                <div class="flex flex-col">
                    <label for="name">First Name</label>
                    <InputText
                        v-model="form.first_name"
                        placeholder="First Name"
                    />
                    <InputError v-if="form.errors.first_name" :message="form.errors.first_name" />
                </div>
                <div class="flex flex-col">
                    <label for="name">Last Name</label>
                    <InputText
                        v-model="form.last_name"
                        placeholder="Last Name"
                    />
                    <InputError v-if="form.errors.last_name" :message="form.errors.last_name" />
                </div>
                <div class="flex flex-col">
                    <label for="phone_number">Phone #</label>
                    <InputText
                        v-model="form.phone_number"
                        placeholder="Phon Number"
                    />
                    <InputError v-if="form.errors.phone_number" :message="form.errors.phone_number" />
                </div>
                <div class="flex flex-col">
                    <label for="name">Email</label>
                    <InputText
                        v-model="form.email"
                        placeholder="Email"
                    />
                    <InputError v-if="form.errors.email" :message="form.errors.email" />
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing" label="Save" type="submit" />

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
