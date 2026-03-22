<script setup>
import Layout from '@/Layouts/Layout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Card } from 'primevue';
import UploadAvatar from './Partials/UploadAvatar.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

// CHANGED: Keep page access for user data used in the new profile header / avatar section.
const page = usePage();
</script>

<template>
    <Head title="Profile" />

    <Layout>
        <!-- CHANGED: Replaced root-container layout with a centered responsive page shell. -->
        <div class="mx-auto w-full max-w-6xl px-4 py-5 md:px-6 lg:px-8">
            <!-- CHANGED: Added a proper page header for stronger hierarchy and spacing. -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                    Profile
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Manage your account details, avatar, password, and account settings.
                </p>
            </div>

            <!-- CHANGED: Added a dedicated top profile / avatar card instead of a small floating card. -->
            <Card
                class="mb-5 overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
            >
                <template #content>
                    <!-- CHANGED: Built a responsive header block so the avatar area feels intentional and not cramped. -->
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <!-- CHANGED: Added user identity text next to the avatar upload area. -->
                            <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                {{ page.props.auth.user.first_name }} {{ page.props.auth.user.last_name }}
                            </h2>

                            <!-- CHANGED: Added supporting account metadata. -->
                            <p class="mt-1 break-all text-sm text-surface-500 dark:text-surface-400">
                                {{ page.props.auth.user.email }}
                            </p>

                            <!-- CHANGED: Added helper copy to explain the section. -->
                            <p class="mt-3 max-w-2xl text-sm text-surface-600 dark:text-surface-300">
                                Upload a profile image and keep your account information up to date for the
                                marketing portal.
                            </p>
                        </div>

                        <!-- CHANGED: Wrapped avatar uploader in a styled surface so it has clearer visual structure. -->
                        <div
                            class="rounded-2xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                        >
                            <UploadAvatar :user="page.props.auth.user" />
                        </div>
                    </div>
                </template>
            </Card>

            <!-- CHANGED: Replaced flex layout with a balanced responsive grid for the two main forms. -->
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                <Card
                    class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <template #content>
                        <!-- CHANGED: Added local section heading instead of dropping the form directly into the card. -->
                        <div class="mb-5">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                Profile Information
                            </h2>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Update your name, email address, and account profile details.
                            </p>
                        </div>

                        <!-- CHANGED: Removed restrictive max-w-xl so the form uses the card width properly. -->
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                    </template>
                </Card>

                <Card
                    class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                >
                    <template #content>
                        <!-- CHANGED: Added matching section heading for visual consistency. -->
                        <div class="mb-5">
                            <h2 class="text-lg font-semibold text-surface-900 dark:text-surface-0">
                                Password
                            </h2>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                Choose a strong password and update it regularly to keep your account secure.
                            </p>
                        </div>

                        <!-- CHANGED: Removed restrictive max-w-xl so the password form aligns with the card layout. -->
                        <UpdatePasswordForm />
                    </template>
                </Card>
            </div>

            <!-- CHANGED: Moved delete form into its own separated danger section with better spacing. -->
            <Card
                class="mt-5 overflow-hidden rounded-2xl border border-red-200 shadow-sm dark:border-red-900/60 dark:bg-surface-900"
            >
                <template #content>
                    <!-- CHANGED: Added danger-zone heading to better frame the destructive action. -->
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-red-600 dark:text-red-400">
                            Delete Account
                        </h2>

                        <!-- CHANGED: Added explanatory copy for better UX before destructive action. -->
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                            Permanently delete your account and all associated data. This action cannot be undone.
                        </p>
                    </div>

                    <DeleteUserForm />
                </template>
            </Card>
        </div>
    </Layout>
</template>
