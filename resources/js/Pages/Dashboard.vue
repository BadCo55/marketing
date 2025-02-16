<template>
    <Head title="Dashboard" />
    <Layout>
        <div class="m-8 flex flex-col flex-auto">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="hidden md:block col-span-12 md:col-span-4 lg:col-span-2">
                            <Link
                                :href="route('task.index')"
                            >
                                <div class="p-4 text-center bg-green-500 rounded-xl hover:shadow-lg hover:opacity-80 hover:cursor-pointer">
                                    <span class="inline-flex justify-center items-center bg-green-600 rounded-full mb-4 w-12 h-12 py-2">
                                        <i class="pi pi-dollar !text-xl leading-none text-white" />
                                    </span>
                                    <div class="text-2xl font-medium text-white mb-2">{{ totalRevenue }}</div>
                                    <span class="text-green-100 font-medium">Total Revenue</span>
                                </div>
                            </Link>
                        </div>
                        <div class="hidden md:block col-span-12 md:col-span-4 lg:col-span-2">
                            <Link
                                :href="route('realtor.index')"
                            >
                                <div class="p-4 text-center bg-blue-500 rounded-xl hover:shadow-lg hover:opacity-80 hover:cursor-pointer">
                                    <span class="inline-flex justify-center items-center bg-blue-600 rounded-full mb-4 w-12 h-12 py-2">
                                        <i class="pi pi-user-plus !text-xl leading-none text-white" />
                                    </span>
                                    <div class="text-2xl font-medium text-white mb-2">{{ realtorCount }}</div>
                                    <span class="text-blue-100 font-medium">New Agents</span>
                                </div>
                            </Link>
                        </div>
                        <div class="hidden md:block col-span-12 md:col-span-4 lg:col-span-2">
                            <Link
                                :href="route('task.index')"
                            >
                                <div class="p-4 text-center bg-emerald-500 rounded-xl hover:shadow-lg hover:opacity-80 hover:cursor-pointer">
                                    <span class="inline-flex justify-center items-center bg-emerald-600 rounded-full mb-4 w-12 h-12 py-2">
                                        <i class="pi pi-bolt !text-xl leading-none text-white" />
                                    </span>
                                    <div class="text-2xl font-medium text-white mb-2">{{ uncompletedTaskCount }}</div>
                                    <span class="text-emerald-100 font-medium">Active Agents</span>
                                </div>
                            </Link>
                        </div>
                        <div class="hidden md:block col-span-12 md:col-span-4 lg:col-span-2">
                            <Link
                                :href="route('realtor.index')"
                            >
                                <div class="p-4 text-center bg-slate-500 rounded-xl hover:shadow-lg hover:opacity-80 hover:cursor-pointer">
                                    <span class="inline-flex justify-center items-center bg-slate-600 rounded-full mb-4 w-12 h-12 py-2">
                                        <i class="pi pi-users !text-xl leading-none text-white" />
                                    </span>
                                    <div class="text-2xl font-medium text-white mb-2">{{ realtorCount }}</div>
                                    <span class="text-slate-100 font-medium">Total Agents</span>
                                </div>
                            </Link>
                        </div>
                        <div class="hidden md:block col-span-12 md:col-span-4 lg:col-span-2">
                            <Link
                                :href="route('office.index')"
                            >
                                <div class="p-4 text-center bg-indigo-500 rounded-xl hover:shadow-lg hover:opacity-80 hover:cursor-pointer">
                                    <span class="inline-flex justify-center items-center bg-indigo-600 rounded-full mb-4 w-12 h-12 py-2">
                                        <i class="pi pi-building !text-xl leading-none text-white" />
                                    </span>
                                    <div class="text-2xl font-medium text-white mb-2">{{ officeCount }}</div>
                                    <span class="text-indigo-100 font-medium">Offices</span>
                                </div>
                            </Link>
                        </div>
                        <div class="hidden md:block col-span-12 md:col-span-4 lg:col-span-2">
                            <Link
                                :href="route('office-visit.index')"
                            >
                                <div class="p-4 text-center bg-red-500 rounded-xl hover:shadow-lg hover:opacity-80 hover:cursor-pointer">
                                    <span class="inline-flex justify-center items-center bg-red-600 rounded-full mb-4 w-12 h-12 py-2">
                                        <i class="pi pi-check-circle !text-xl leading-none text-white" />
                                    </span>
                                    <div class="text-2xl font-medium text-white mb-2">{{ visitCount }}</div>
                                    <span class="text-red-100 font-medium">Office Visits</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6">
                    <div class="shadow bg-surface-0 dark:bg-surface-900 rounded-border p-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-medium text-surface-900 dark:text-surface-0">Active Sequences</span>
                            <div class="ml-20">
                                <Button icon="pi pi-ellipsis-v" text plain rounded @click="$refs.menu.toggle($event)" />
                                <Menu ref="menu" :popup="true" :model="items" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <DataTable
                                :value="activeSequences"
                                paginator
                                :rows="5"
                                :rowsPerPageOptions="[5, 10, 20, 50]"
                            >
                                <template #empty>
                                    <p>No active sequences found.</p>
                                </template>
                                <Column header="Realtor" field="realtor.formatted_full_name"></Column>
                                <Column header="Sequence" field="sequence_name"></Column>
                                <Column header="Status" field="formatted_status"></Column>
                                <Column class="w-24 !text-center">
                                    <template #body="{ data }">
                                        <Link
                                            :href="route('realtor.show', { realtor: data.realtor_id, viewSequence: true, sequenceId: data.id })"
                                        >
                                            <Button icon="pi pi-search" severity="primary" rounded></Button>
                                        </Link>
                                    </template>
                                </Column>
                            </DataTable>
                            <!-- <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 md:col-span-4">
                                    <div class="text-center border border-surface rounded-border p-6">
                                        <i class="pi pi-twitter text-5xl text-blue-500" />
                                        <div class="text-surface-900 dark:text-surface-0 text-2xl font-700 my-4 font-bold">12.40K</div>
                                        <span class="font-medium text-surface-600 dark:text-surface-200">Twitter</span>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4 text-center">
                                    <div class="text-center border border-surface rounded-border p-6">
                                        <i class="pi pi-vimeo text-5xl text-blue-500" />
                                        <div class="text-surface-900 dark:text-surface-0 text-2xl font-700 my-4 font-bold">10.20K</div>
                                        <span class="font-medium text-surface-600 dark:text-surface-200">Vimeo</span>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4 text-center">
                                    <div class="text-center border border-surface rounded-border p-6">
                                        <i class="pi pi-facebook text-5xl text-blue-500" />
                                        <div class="text-surface-900 dark:text-surface-0 text-2xl font-700 my-4 font-bold">5.60K</div>
                                        <span class="font-medium text-surface-600 dark:text-surface-200">Facebook</span>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4 text-center">
                                    <div class="text-center border border-surface rounded-border p-6">
                                        <i class="pi pi-discord text-5xl text-indigo-500" />
                                        <div class="text-surface-900 dark:text-surface-0 text-2xl font-700 my-4 font-bold">23.53K</div>
                                        <span class="font-medium text-surface-600 dark:text-surface-200">Discord</span>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4 text-center">
                                    <div class="text-center border border-surface rounded-border p-6">
                                        <i class="pi pi-github text-5xl text-purple-500" />
                                        <div class="text-surface-900 dark:text-surface-0 text-2xl font-700 my-4 font-bold">16.70K</div>
                                        <span class="font-medium text-surface-600 dark:text-surface-200">GitHub</span>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4 text-center">
                                    <div class="text-center border border-surface rounded-border p-6">
                                        <i class="pi pi-google text-5xl text-pink-500" />
                                        <div class="text-surface-900 dark:text-surface-0 text-2xl font-700 my-4 font-bold">16.50K</div>
                                        <span class="font-medium text-surface-600 dark:text-surface-200">Google</span>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                 <div class="col-span-12 lg:col-span-6">
                    <div class="bg-surface-0 dark:bg-surface-900 shadow rounded-border p-6 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-xl font-medium text-surface-900 dark:text-surface-0">Newest Realtors</span>
                            <div class="ml-auto">
                                <Button icon="pi pi-ellipsis-v" text plain rounded @click="$refs.menu.toggle($event)" />
                                <Menu ref="menu" :popup="true" :model="items" />
                            </div>
                        </div>
                        <DataTable
                            :value="recentRealtors"
                        >
                            <template #empty>
                                <p>No realtors found.</p>
                            </template>
                            <Column class="!text-center" alignFrozen="left" :frozen="true">
                                <template #body="{ data }">
                                    <Tag :severity="getTagSeverity(data.status)" :value="data.status"  />
                                </template>
                            </Column>
                            <Column header="Name" field="formatted_full_name"></Column>
                            <Column header="Created" field="formatted_created_at"></Column>
                            <Column header="$ Value" field="formatted_value"></Column>
                            <Column class="w-24 !text-center" alignFrozen="right" :frozen="true">
                                <template #body="{ data }">
                                    <Link
                                        :href="route('realtor.show', data.id)"
                                    >
                                        <Button icon="pi pi-search" severity="primary" rounded></Button>
                                    </Link>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>


    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button, Menu, DataTable, Column, Tag } from 'primevue';

const page = usePage();
const uncompletedTaskCount = ref(page.props.uncompleted_task_count);
const realtorCount = ref(page.props.realtor_count);
const officeCount = ref(page.props.office_count);
const visitCount = ref(page.props.visit_count);
const activeSequences = ref(page.props.active_sequences);
const recentRealtors = ref(page.props.recent_realtors);
const totalRevenue = ref(page.props.total_revenue);

const items = [];

const getTagSeverity = (status) => {
    if (status === 'Active') {
        return 'success';
    } else if (status === 'Inactive') {
        return 'danger';
    } else if (status === 'Engaged') {
        return 'warn';
    } else if (status === 'New') {
        return 'info'
    }
    return null;
}

</script>
