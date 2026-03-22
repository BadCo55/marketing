<template>
    <div class="flex h-full min-h-0 flex-col">
        <DataTable
            :value="localUsers"
            stripedRows
            tableStyle="min-width: 48rem"
            class="w-full"
        >
            <template #header>
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Users</h3>
                        <p class="mt-1 text-sm text-surface-500">
                            Manage portal users and create new accounts.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <div class="text-sm text-surface-500 self-center">
                            {{ localUsers.length }} {{ localUsers.length === 1 ? 'user' : 'users' }}
                        </div>

                        <Button
                            label="Add User"
                            icon="pi pi-plus-circle"
                            @click="isCreateUserDialogVisible = true"
                        />
                    </div>
                </div>
            </template>

            <template #empty>
                <div class="flex min-h-48 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                    <i class="pi pi-users text-3xl text-surface-400" />
                    <p class="font-medium text-surface-700 dark:text-surface-200">
                        No users found.
                    </p>
                    <p class="max-w-md text-sm text-surface-500">
                        Add your first user to start managing access to the portal.
                    </p>
                    <Button
                        label="Add User"
                        icon="pi pi-plus-circle"
                        @click="isCreateUserDialogVisible = true"
                    />
                </div>
            </template>

            <Column
                v-if="isLargeBreakpoint"
                headerClass="w-[7rem]"
                bodyClass="w-[7rem]"
            >
                <template #body="{ data }">
                    <div class="flex justify-center py-2">
                        <div
                            class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-800"
                        >
                            <img
                                v-if="data.avatar_url"
                                :src="data.avatar_url"
                                class="h-14 w-14 object-cover"
                                alt="User avatar"
                            />
                            <i v-else class="pi pi-user text-surface-400"></i>
                        </div>
                    </div>
                </template>
            </Column>

            <Column
                field="formatted_full_name"
                header="Name"
                sortable
                headerClass="w-[16rem]"
                bodyClass="w-[16rem]"
            >
                <template #body="{ data }">
                    <div class="flex flex-col">
                        <p class="font-semibold">{{ data.formatted_full_name }}</p>
                        <p v-if="!isLargeBreakpoint" class="text-sm text-surface-500">
                            {{ data.email || 'No email' }}
                        </p>
                    </div>
                </template>
            </Column>

            <Column
                field="phone_number"
                header="Phone #"
                headerClass="w-[12rem]"
                bodyClass="w-[12rem]"
            >
                <template #body="{ data }">
                    <span>{{ data.phone_number || '—' }}</span>
                </template>
            </Column>

            <Column
                v-if="isLargeBreakpoint"
                field="email"
                header="Email"
                headerClass="w-[18rem]"
                bodyClass="w-[18rem]"
            >
                <template #body="{ data }">
                    <span>{{ data.email || '—' }}</span>
                </template>
            </Column>

            <Column
                headerClass="w-[5rem]"
                bodyClass="w-[5rem] !text-center"
            >
                <template #body="{ data }">
                    <Button
                        icon="pi pi-trash"
                        severity="danger"
                        rounded
                        outlined
                        aria-label="Delete user"
                        @click="onDestroy(data.id)"
                    />
                </template>
            </Column>
        </DataTable>

        <CreateUserDialog
            v-model:visible="isCreateUserDialogVisible"
            @users="onUsersUpdated"
        />
    </div>
</template>

<script setup>
import { DataTable, Column, Button } from 'primevue';
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import CreateUserDialog from '../Components/CreateUserDialog.vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const localUsers = ref(props.users ?? []);
const isCreateUserDialogVisible = ref(false);

const onUsersUpdated = (newUsers) => {
    localUsers.value = newUsers ?? [];
};

const onDestroy = (id) => {
    if (!confirm('Are you sure you want to delete this user?')) return;

    router.delete(route('user.destroy', id), {
        preserveScroll: true,
        onSuccess: ({ props }) => {
            localUsers.value = props.users ?? [];
        },
    });
};

const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024;
};

onMounted(() => {
    checkBreakpoint();
    window.addEventListener('resize', checkBreakpoint);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});
</script>
