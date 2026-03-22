<template>
    <Head title="Settings" />

    <Layout>
        <div class="flex h-full min-h-0 flex-col">
            <!-- PAGE HEADER -->
            <div class="mb-5 flex flex-col gap-2 px-4 pt-5 md:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight">Settings</h1>
                <p class="text-sm text-surface-500">
                    Manage portal settings, users, and administrative options.
                </p>
            </div>

            <!-- CONTENT -->
            <div class="min-h-0 flex-1 px-4 pb-5 md:px-6 lg:px-8">
                <div class="grid min-h-0 grid-cols-12 gap-5 items-start">
                    <!-- SETTINGS SIDEBAR -->
                    <div class="col-span-12 md:col-span-4 xl:col-span-3">
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800">
                            <template #content>
                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold">Navigation</h2>
                                    <p class="mt-1 text-sm text-surface-500">
                                        Choose a settings section to manage.
                                    </p>
                                </div>

                                <Menu
                                    :model="menuItems"
                                    class="w-full border-0 !bg-transparent p-0"
                                />
                            </template>
                        </Card>
                    </div>

                    <!-- MAIN PANEL -->
                    <div class="col-span-12 min-h-0 md:col-span-8 xl:col-span-9">
                        <Card class="overflow-hidden rounded-2xl border border-surface-200 shadow-sm dark:border-surface-800">
                            <template #content>
                                <div class="mb-5">
                                    <h2 class="text-xl font-semibold">Users</h2>
                                    <p class="mt-1 text-sm text-surface-500">
                                        Manage user accounts, access, and related settings.
                                    </p>
                                </div>

                                <UsersTab :users="users" />
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Menu, Card } from 'primevue';
import { ref } from 'vue';
import UsersTab from './Partials/UsersTab.vue';

const page = usePage();
const users = page.props.users ?? [];

const menuItems = ref([
    {
        label: 'Users',
        icon: 'pi pi-users',
        command: () => {
            // reserved for future settings tab switching
        },
    },
]);
</script>
