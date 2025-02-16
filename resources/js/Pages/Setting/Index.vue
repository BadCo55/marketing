<template>
    <Head title="Settings" />
    <Layout>
        <div class="root-container">
            <div class="flex flex-col lg:flex-row gap-5">
                <Menu :model="menuItems" class="self-start" />
                <Card class="w-full">
                    <template #content>
                        <UsersTab :users="users" />
                    </template>
                </Card>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Menu, Card } from 'primevue';
import { ref, onMounted, onUnmounted } from 'vue';
import UsersTab from './Partials/UsersTab.vue';

const page = usePage();
const users = page.props.users;
const menuItems = ref([
    { label: 'Users', icon: 'pi pi-users' }
]);


</script>
