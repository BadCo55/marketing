<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import InputError from '@/Components/InputError.vue'
import { InputText, Button } from 'primevue'

const passwordInput = ref(null)
const currentPasswordInput = ref(null)

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation')
                passwordInput.value.focus()
            }
            if (form.errors.current_password) {
                form.reset('current_password')
                currentPasswordInput.value.focus()
            }
        },
    })
}
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Update Password</h2>

            <p class="mt-1 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-5 space-y-5">
            <div class="flex flex-col gap-5">
                <div class="flex flex-col">
                    <label for="current_password">Current Password</label>
                    <InputText
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        autocomplete="current-password"
                    />
                    <InputError
                        v-if="form.errors.current_password"
                        :message="form.errors.current_password"
                    />
                </div>

                <div class="flex flex-col">
                    <label for="password">New Password</label>
                    <InputText
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                    />
                    <InputError v-if="form.errors.password" :message="form.errors.password" />
                </div>
                <div class="flex flex-col">
                    <label for="password_confirmation">Confirm Password</label>
                    <InputText
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                    />
                    <InputError
                        v-if="form.errors.password_confirmation"
                        :message="form.errors.password_confirmation"
                    />
                </div>
                <div class="flex items-center gap-4">
                    <Button label="Save" :disabled="form.processing" type="submit">Save</Button>
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>
            </div>
        </form>
    </section>
</template>
