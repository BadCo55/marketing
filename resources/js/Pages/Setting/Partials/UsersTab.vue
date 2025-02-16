<template>
    <DataTable
        :value="localUsers"
    >
        <template #header>
            <div class="flex justify-end">
                <Button label="Add User" icon="pi pi-plus-circle" @click="isCreateUserDialogVisible = true" />
            </div>
        </template>
        <Column v-if="isLargeBreakpoint">
            <template #body="slotProps">
                <div class="flex flex-col items-center gap-2 justify-center">
                    <img :src="slotProps.data.avatar_url" class="w-20 rounded-full" />
                </div>
            </template>
        </Column>
        <Column field="formatted_full_name" header="Name"></Column>
        <Column field="phone_number" header="Phone #"></Column>
        <Column field="email" header="Email"></Column>
        <Column class="w-24 !text-center" alignFrozen="right" :frozen="true">
            <template #body="{ data }">
                <Button icon="pi pi-trash" severity="primary" rounded @click="onDestroy(data.id)"></Button>
            </template>
        </Column>
    </DataTable>
    <CreateUserDialog v-model:visible="isCreateUserDialogVisible" @users="(newUsers) => localUsers = newUsers" />
</template>

<script setup>
import { DataTable, Column, Button, Dialog } from 'primevue';
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import CreateUserDialog from '../Components/CreateUserDialog.vue';
const props = defineProps({
    users: Object,
});
const localUsers = ref(props.users);
const isCreateUserDialogVisible = ref(false);

const onDestroy = (id) => {
    router.delete(route('user.destroy', id), {
        onSuccess: ({ props }) => {
            localUsers.value = props.users;
        }
    });
}


// Detect if the screen is at least a large breakpoint (lg)
const isLargeBreakpoint = ref(false);

const checkBreakpoint = () => {
    isLargeBreakpoint.value = window.innerWidth >= 1024; // lg breakpoint in Tailwind
};
onMounted(() => {
    checkBreakpoint(); // Initial check
    window.addEventListener('resize', checkBreakpoint); // Update on resize
});

// Clean up the event listener on component unmount
onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint);
});
</script>
