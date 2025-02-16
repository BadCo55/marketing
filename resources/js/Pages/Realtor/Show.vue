<template>
    <Head :title="realtor.first_name + ' ' + realtor.last_name" />
    <Layout>
        <div class="root-container">
            <div class="grid grid-cols-12 gap-5">
                <div class="col-span-12 xl:col-span-4">
                    <Card class="mb-5 flex justify-center">
                        <template #content>
                            <div class="flex flex-col lg:flex-row items-center gap-5">
                                <div class="flex flex-col gap-5">
                                    <div class="rounded-full w-36 h-36 overflow-hidden">
                                        <Image :src="realtor.avatar_url" class="w-36 h-36 rounded-full" />
                                    </div>
                                    <Button label="Upload Avatar" @click="showAvatarDialog = true" />
                                </div>
                                <div>
                                    <Tag :value="realtor.status" class="mb-1" :severity="getTagSeverity(realtor.status)" />
                                    <h2 class="text-2xl font-bold">{{ realtor.first_name }} {{ realtor.last_name }}</h2>
                                    <p class="break-all text-xs" :title="realtor.email">{{ realtor.email }}</p>
                                    <p>{{ realtor.phone_number }}</p>
                                </div>
                            </div>
                        </template>
                    </Card>
                    <Card class="self-start w-full">
                        <template #content>
                            <div class="flex flex-col">
                                <label for="" class="font-medium text-lg">Contact Owner</label>
                                <Select :options="users" optionLabel="formatted_full_name" v-model="contactOwner"></Select>
                            </div>
                        </template>
                    </Card>
                </div>
                <div class="col-span-12 xl:col-span-8">
                    <Card class="mb-auto">
                        <template #content v-if="!viewSequenceDetails">
                            <Tabs :value="activeTab" scrollable>
                                <TabList>
                                    <Tab v-for="tab in tabs" :value="tab.value">{{ tab.name }}</Tab>
                                </TabList>
                                <TabPanels>
                                    <!-- PERSONAL INFORMATION -->
                                    <TabPanel :value="0">
                                        <PersonalInfoTab :realtor="realtor" />
                                    </TabPanel>
                                    <!-- PROFESSIONAL INFORMATION -->
                                    <TabPanel :value="1">
                                        <ProfessionalInfoTab :realtor="realtor" />
                                    </TabPanel>
                                    <TabPanel :value="2">
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                                            <div class="flex flex-col">
                                                <label># of Sales</label>
                                                <InputText v-model="realtor.sale_count" disabled :value="realtor.sale_count" />
                                            </div>
                                            <div class="flex flex-col">
                                                <label>Average Sale Value</label>
                                                <InputText v-model="realtor.average_sale_value" disabled :value="realtor.average_sale_value" />
                                            </div>
                                            <div class="flex flex-col">
                                                <label>Total Sale Value</label>
                                                <InputText v-model="realtor.value" disabled :value="realtor.value" />
                                            </div>
                                            <!-- <div class="flex flex-col">
                                                <label>Client Volume</label>
                                                <InputText />
                                            </div> -->
                                        </div>
                                        <DataTable
                                            class="w-full mt-5"
                                            :value="previousInspections"
                                            stripedRows
                                            paginator
                                            :rows="5"
                                            :rowsPerPageOptions="[5, 10, 25, 50, 100]"
                                        >
                                            <template #empty>
                                                No inspections were found.
                                            </template>
                                            <template #header>
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <span class="text-xl font-bold">Previous Inspections</span>
                                                </div>
                                            </template>
                                            <Column field="inspection_number" sortable header="Inspection #"></Column>
                                            <Column field="inspection_date" sortable header="Date"></Column>
                                            <Column header="Total Value">
                                                <template #body="slotProps">
                                                    ${{ Number(slotProps.data.total_fee).toFixed(2) }}
                                                </template>
                                            </Column>
                                        </DataTable>
                                    </TabPanel>
                                    <TabPanel :value="3">
                                        <CommunicationLogTab :realtor_id="realtor.id" @realtor="onUpdateRealtor(realtor)" :realtor="realtor" />
                                    </TabPanel>
                                    <TabPanel :value="4">
                                        <div class="flex gap-5 mb-5">
                                            <div class="w-1/3 space-y-3">
                                                <Select fluid v-model="selectedSequence" :options="sequences" optionLabel="name" placeholder="Select a Sequence to Add" />
                                                <Button v-if="selectedSequence" label="Cancel" icon="pi pi-times-circle" @click="selectedSequence = null" />
                                            </div>
                                            <div v-if="selectedSequence" class="w-2/3">
                                                <Card class="dark:!bg-surface-800 !bg-surface-100">
                                                    <template #content>
                                                        <Message severity="error" v-if="addToSequenceForm.errors.sequence_id === 'The sequence id has already been taken.'" class="mb-5" icon="pi pi-exclamation-circle">
                                                            This realtor has already been added to the selected sequence!
                                                        </Message>
                                                        <form @submit.prevent="onAddToSequenceSubmit">
                                                            <div class="flex flex-col w-1/2 mb-5">
                                                                <label for="">Sequence Start Date</label>
                                                                <DatePicker placeholder="Select a date..." v-model="addToSequenceForm.start_date"  />
                                                                <Message severity="error" variant="simple" size="small" v-if="addToSequenceForm.errors.start_date">{{ addToSequenceForm.errors.start_date }}</Message>
                                                            </div>
                                                            <div class="space-y-1">
                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p class="font-bold">Name:</p>
                                                                    <p class="col-span-2">{{ selectedSequence?.name }}</p>
                                                                </div>
                                                                <Divider />
                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p class="font-bold">Target Audience:</p>
                                                                    <p class="col-span-2">{{ selectedSequence?.target_audience }}</p>
                                                                </div>
                                                                <Divider />
                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p class="font-bold">Description:</p>
                                                                    <p class="col-span-2">{{ selectedSequence?.description }}</p>
                                                                </div>
                                                                <Divider class="!mb-3" />
                                                                <div class="grid grid-cols-3 gap-5">
                                                                    <p class="font-bold">Steps:</p>
                                                                    <div class="col-span-2">
                                                                        <ul class="space-y-1">
                                                                            <li class="dark:bg-surface-900 bg-surface-200 rounded-lg px-3 py-2" v-for="step in selectedSequence?.steps" :key="step.step_number">
                                                                                <p class="font-bold">Step #{{ step.step_number }}: {{ step.type }}</p>
                                                                                <p class="font-light">
                                                                                    {{ step.description }}
                                                                                </p>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="flex justify-end mt-5 pt-5">
                                                                    <Button label="Add Realtor to this Sequence" icon="pi pi-plus-circle" type="submit" />
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </template>
                                                </Card>
                                            </div>
                                        </div>
                                        <DataTable :value="activeSequences" >
                                            <template #header>
                                                <h2 class="text-2xl font-extrabold">Active Sequences</h2>
                                            </template>
                                            <template #empty>No active sequences found.</template>
                                            <Column header="Name" field="sequence_name"></Column>
                                            <Column
                                                header="Current Step"
                                            >
                                                <template #body="{ data }">
                                                    <div>
                                                        <span>{{ data.formatted_status }}</span>
                                                        <span> ({{ data.progress || '0/0' }})</span>
                                                    </div>
                                                </template>
                                            </Column>
                                            <Column header="Added On" field="formatted_created_at"></Column>
                                            <Column header="Next Step Due By">
                                                <template #body="{ data }">
                                                    {{ data.formatted_next_step_due || 'Not Started' }}
                                                </template>
                                            </Column>
                                            <Column>
                                                <template #body="{ data }">
                                                    <div class="flex">
                                                        <Message v-if="data.status === 'not_started'" severity="info" icon="pi pi-info-circle">Not Started</Message>
                                                        <Message v-if="data.is_passed_due" severity="error" icon="pi pi-exclamation-triangle">Passed Due</Message>
                                                        <Message v-if="data.is_due_today" severity="warn" icon="pi pi-exclamation-triangle">Due Today</Message>
                                                    </div>
                                                </template>
                                            </Column>
                                            <Column class="w-24 !text-center">
                                                <template #body="{ data }">
                                                        <Button icon="pi pi-search" severity="primary" rounded @click="onViewActiveSequence(data)"></Button>
                                                </template>
                                            </Column>
                                        </DataTable>
                                    </TabPanel>
                                    <TabPanel :value="5">
                                        <NotesTab :users="users" :realtor="realtor" @realtor="onUpdateRealtor(realtor)" />
                                    </TabPanel>
                                </TabPanels>
                            </Tabs>
                        </template>
                        <template #content v-else>
                            <div class="flex justify-between mb-3">
                                <p class="text-2xl font-bold">Sequence Details</p>
                                <Button label="Back" icon="pi pi-arrow-left" @click="onBackToMainView" />
                            </div>
                            <Divider />
                            <div class="mb-5">
                                <template v-if="selectedActiveSequence.is_due_today || selectedActiveSequence.is_passed_due">
                                    <div class="flex justify-center mb-5">
                                        <Message v-if="selectedActiveSequence.is_due_today" severity="warn" icon="pi pi-exclamation-triangle">This step is due today!</Message>
                                        <Message v-if="selectedActiveSequence.is_passed_due" severity="error" icon="pi pi-exclamation-triangle">This step is passed due!</Message>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flex justify-center mb-5">
                                        <Message severity="info" icon="pi pi-info-circle">This step is due {{ selectedActiveSequence.formatted_next_step_due }}</Message>
                                    </div>
                                </template>
                                <div class="flex justify-center gap-5">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold">Name:</td>
                                                <td class="ps-5">{{ selectedActiveSequence.sequence_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">Description:</td>
                                                <td
                                                    class="ps-5"
                                                    :class="{ 'text-gray-500 opacity-75': !selectedActiveSequence.sequence.description }"
                                                >
                                                    {{ selectedActiveSequence.sequence.description ? selectedActiveSequence.sequence.description : 'No description found' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">Target Audience:</td>
                                                <td class="ps-5">{{ selectedActiveSequence.sequence.target_audience }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">Current Step:</td>
                                                <td class="ps-5">
                                                    <span v-if="selectedActiveSequence.status === 'not_started' || selectedActiveSequence.status === 'completed' || selectedActiveSequence.status === 'cancelled'">
                                                        {{ selectedActiveSequence.formatted_status }}
                                                    </span>
                                                    <span v-else>
                                                        {{ selectedActiveSequence.sequence.steps.find((step) => step.step_number === selectedActiveSequence.current_step).type }}
                                                    </span>
                                                    - {{ selectedActiveSequence?.progress || '0/0' }}
                                                    <Button
                                                        v-if="selectedActiveSequence.status !== 'completed'"
                                                        severity="success"
                                                        :label="selectedActiveSequence.status === 'not_started' ? 'Start Sequence' : selectedActiveSequence.current_step === selectedActiveSequence.total_steps ? 'Complete Sequence' : 'Next Step'"
                                                        class="!ms-2"
                                                        @click="onNextSequenceStep"
                                                    />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold"></td>
                                                <td class="ps-5">
                                                    {{ selectedActiveSequence.current_step?.description || '' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <Divider />
                            <div class="flex gap-5">
                                <div class="flex-1">
                                    <form @submit.prevent="onNoteSubmit">
                                        <div class="grid grid-cols-2 gap-5 mb-5">
                                            <div class="flex flex-col">
                                                <label for="status" class="font-medium">Status</label>
                                                <Select :options="dynamicStatusOptions" optionLabel="label" v-model="notesForm.status" placeholder="Select an option..." />
                                            </div>
                                            <div class="flex flex-col">
                                                <label for="rating" class="font-medium">Next Update By</label>
                                                <DatePicker v-model="notesForm.next_update" placeholder="Select a date" />
                                            </div>
                                        </div>
                                        <label for="notes" class="mb-1 font-medium">Enter Notes Here</label>
                                        <Textarea fluid id="notes" v-model="notesForm.note" class="h-36" />
                                        <Message v-if="page.props.errors.note" severity="error" variant="simple" size="small">{{ page.props.errors.note }}</Message>

                                        <Button label="Submit" type="submit" icon="pi pi-check-circle" class="float-end" />
                                    </form>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-xl mb-2">Previous Notes</p>
                                    <div class="flex flex-col gap-5">
                                        <Panel
                                            v-for="note in selectedActiveSequence.current_step?.notes"
                                            :key="note.id"
                                            :header="note.status"
                                            class="shadow"
                                        >
                                            <p class="text-sm mb-3">{{ note.status_description }}</p>
                                            <div class="flex flex-col gap-3 bg-surface-50 border rounded py-3 px-2">
                                                <div class="bg-surface-0 rounded p-2 border">
                                                    <p>{{ note.note }}</p>
                                                </div>
                                                <p class="text-end">
                                                    -{{ note.created_by.name }} <br>
                                                    {{ note.created_at }}
                                                </p>
                                            </div>
                                        </Panel>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
        <Dialog v-model:visible="showAvatarDialog" modal header="Upload Avatar Image" class="w-[25rem]">
            <p class="mb-5">Click below to upload an avatar image for {{ realtor.first_name }} {{ realtor.last_name }}!</p>
            <FileUpload
                mode="basic"
                @select="onUpload($event)"
                accept="image/*"
                customUpload
                :maxFileSize="1000000"
            >
            </FileUpload>
        </Dialog>
        <Toast />
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import PersonalInfoTab from './Components/PersonalInfoTab.vue';
import NotesTab from './Components/NotesTab.vue';
import CommunicationLogTab from './Components/CommunicationLogTab.vue';
import ProfessionalInfoTab from './Components/ProfessionalInfoTab.vue';
import { usePage, Head, useForm, router } from '@inertiajs/vue3';
import { Card, Button, Dialog, FileUpload, Image, InputText, Textarea, DataTable, Column, Select, Divider, Toast, Rating, DatePicker, Message, Panel, Tag } from 'primevue';
import { ref, watch, onMounted } from 'vue';
import { useToast } from 'primevue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

const toast = useToast();
const page = usePage();
const realtor = ref(page.props.realtor);
const sequences = page.props.sequences;
const users = page.props.users;
const activeSequences = ref(realtor.value.active_sequences);
const previousInspections = realtor.value.previous_inspections;
const selectedSequence = ref(null);
const showAvatarDialog = ref(false);
const viewSequenceDetails = ref(false);
const contactOwner = ref(users.find(user => realtor.value.owner?.id === user.id) || null);
const activeTab = ref(0);


onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('viewSequence') == 1) {
        const sequenceId = Number(urlParams.get('sequenceId'));

        selectedActiveSequence.value = activeSequences.value.find(sequence => sequence.id === sequenceId);
        viewSequenceDetails.value = true;
        activeTab.value = 4;
    }
});

watch(contactOwner, async (newVal) => {
    if (newVal) {
        router.put(
            route('realtor.update', realtor.value.id),
            {
                request_type: 'update_owner',
                owner: newVal.id
            },
        );
    }
});

const selectedActiveSequence = ref(page.props.selected_sequence);

watch(selectedActiveSequence, (newValue) => {
    if (newValue && newValue.id) {
        notesForm.sequence_id = newValue.id;
        notesForm.next_update = newValue.due_by;
        notesForm.step_id = newValue.current_step?.id;
    } else {
        notesForm.sequence_id = '';
    }
});

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

const notesForm = useForm({
    realtor_id: realtor.id,
    sequence_id: '',
    step_id: '',
    status: null,
    next_update: '',
    note: '',
});

const tabs = [
    { value: 0, name: 'Personal Info', urlParam: 'personal-info' },
    { value: 1, name: 'Professional Info', urlParam: 'professional-info' },
    { value: 2, name: 'Metrics', urlParam: 'metrics' },
    { value: 3, name: 'Communication Log', urlParam: 'communication-log' },
    { value: 4, name: 'Sequences', urlParam: 'sequences' },
    { value: 5, name: 'Notes', urlParam: 'general-notes' },
];

const onViewActiveSequence = (sequence) => {
    viewSequenceDetails.value = true;
    selectedActiveSequence.value = sequence;
}

const onBackToMainView = () => {
    viewSequenceDetails.value = false;
    selectedActiveSequence.value = null;
    notesForm.reset();
    activeTab.value = 4;
}

const onUpload = async (event) => {
    const file = event.files[0];
    if (!file) {
        console.error("No file selected.");
        return;
    }
    const formData = new FormData();
    formData.append('avatar', file);
    router.post(route('realtor.upload-avatar', {id: realtor.value.id}),
        formData,
        {
            headers: {
                'Content-Type': 'multipart/form-data', // Explicitly set the content type
            },
            only: ['flash', 'realtor'],
            onSuccess: (response) => {
                realtor.value = response.props.realtor; // Update avatar
                showAvatarDialog.value = false; // Close dialog
            },
            onError: (error) => {
                console.error(error);
            }
        }
    )
};

const addToSequenceForm = useForm({
    realtor_id: realtor.value.id,
    sequence_id: selectedSequence.value?.id,
    start_date: ''
});
const onAddToSequenceSubmit = () => {
    addToSequenceForm.sequence_id = selectedSequence.value.id;
    addToSequenceForm.post(route('realtor-sequence.store'),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['realtor', 'flash'],
            onSuccess: (response) => {
                activeSequences.value = response.props.realtor.active_sequences;
                addToSequenceForm.reset();
                selectedSequence.value = null;
            },
            onError: (error) => {
                console.log(error);
            }
        }
    )
}


const onNextSequenceStep = () => {
    router.put(route('realtor-sequence.update', { realtor_sequence: selectedActiveSequence.value.id }),
        {
            request_type: 'increment_step',
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['flash', 'realtor'],
            onSuccess: (response) => {
                realtor.value = page.props.realtor;
                const sequenceId = selectedActiveSequence.value.id;
                if (realtor.value.active_sequences.length === 0) {
                    activeSequences.value = null;
                    selectedSequence.value = null;
                    selectedActiveSequence.value = null;
                    viewSequenceDetails.value = false;
                    activeTab.value = 4;
                } else {
                    selectedActiveSequence.value = realtor.value.active_sequences.find((sequence) => sequence.id === sequenceId);
                }
                // selectedActiveSequence.value = response.props.sequence;
            },
            onError: (error) => {
                console.error(error);
            }
        }
    )
};

const onUpdateRealtor = (realtor) => {
    realtor.value = realtor;
}
</script>

