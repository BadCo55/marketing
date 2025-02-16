<script setup>
import Layout from '@/Layouts/Layout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Card, FileUpload } from 'primevue';
import UploadAvatar from './Partials/UploadAvatar.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
console.log(page.props);
</script>

<template>
    <Head title="Profile" />

    <Layout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Profile
            </h2>
        </template>
        <div class="root-container">
            <div class="flex justify-center mb-5">
                <Card>
                    <template #content>
                        <UploadAvatar :user="page.props.auth.user" />
                    </template>
                </Card>
            </div>
            <div class="flex gap-5 mb-5">
                <Card class="w-full self-start">
                    <template #content>
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </template>
                </Card>
                <Card class="w-full">
                    <template #content>
                        <UpdatePasswordForm class="max-w-xl" />
                    </template>
                </Card>
            </div>
            <div class="flex gap-5">
                <Card class="w-full">
                    <template #content>
                        <DeleteUserForm />
                    </template>
                </Card>
            </div>
        </div>
    </Layout>
</template>
