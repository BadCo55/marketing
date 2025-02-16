<template>
    <Head :title="office.parent_company" />
    <Layout>
        <div class="grid grid-cols-12 gap-5 p-8">
            <Card class="col-span-12 xl:col-span-4 mb-auto items-center">
                <template #content>
                    <div class="flex items-center gap-5">
                        <div class="flex flex-col gap-5">
                            <div class="w-36 overflow-hidden rounded-lg">
                                <Image :src="office.image_url" class="w-36 " />
                            </div>
                            <Button label="Upload Image" @click="showImageDialog = true" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">{{ office.parent_company }}</h2>
                            <p>{{ office.street_address }} {{ office.unit_number ? '#' + office.unit_number : '' }}</p>
                            <p>{{ office.city }}, {{ office.state }} {{ office.zip_code }}</p>
                        </div>
                    </div>
                </template>
            </Card>
            <Card class="col-span-12 xl:col-span-8 mb-auto">
                <template #content>
                    <Tabs
                        scrollable
                        v-model:value="activeTab"
                    >
                        <TabList>
                            <Tab v-for="tab in tabs" :value="tab.value">
                                {{ tab.name }}
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel :value="0">
                                <DetailsTab :office="office" @office="onUpdateOffice" />
                            </TabPanel>
                            <TabPanel :value="1">
                                <DataTable
                                    :value="agentTableData"
                                    class="p-datatable-sm"
                                >
                                    <!-- Hide default headers -->
                                    <Column header="" headerStyle="display: none">
                                        <template #body="slotProps">
                                            <div class="inline-flex items-center gap-2">
                                                <i :class="slotProps.data.class"></i>
                                                <p class="text-lg font-light">{{ slotProps.data.label }}</p>
                                            </div>
                                        </template>
                                    </Column>

                                    <Column header="" headerStyle="display: none" class="!text-end">
                                        <template #body="slotProps">
                                            <span>{{ slotProps.data.value }}</span>
                                        </template>
                                    </Column>
                                </DataTable>
                                <DataTable :value="realtors">
                                    <template #empty>
                                        No agents found!
                                    </template>
                                    <Column class="!text-center">
                                        <template #body="{ data }">
                                            <div class="inline-flex gap-2 items-center">
                                                <i v-if="data.id == office.highest_value_agent.id" class="pi pi-star-fill text-yellow-500"></i>
                                                <Tag :value="data.status" :severity="getTagSeverity(data.status)" />
                                            </div>
                                        </template>
                                    </Column>
                                    <Column field="formatted_full_name" header="Name"></Column>
                                    <Column field="phone_number" header="Phone #"></Column>
                                    <Column field="email" header="Email"></Column>
                                    <Column field="formatted_value" header="Value"></Column>
                                    <Column class="w-12 !text-center">
                                        <template #body="{ data }">
                                            <Link
                                                :href="route('realtor.show', data.id)"
                                            >
                                                <Button icon="pi pi-search" severity="primary" rounded></Button>
                                            </Link>
                                        </template>
                                    </Column>
                                </DataTable>
                            </TabPanel>
                            <!-- <TabPanel :value="2">
                                <p>Marketing Sequence</p>
                            </TabPanel>
                            <TabPanel :value="3">
                                <p>Communication Log</p>
                            </TabPanel> -->
                            <TabPanel :value="2">
                                <VisitTab :office="office" />
                            </TabPanel>
                            <TabPanel :value="3">
                                <PerformanceTab :office="office" />
                            </TabPanel>
                            <TabPanel :value="4">
                                <div class="flex gap-5">
                                    <div class="flex-1">
                                        <form @submit.prevent="onOfficeNoteSubmit">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                                <div class="flex flex-col">
                                                    <label for="" class="font-medium mb-1" :class="{ 'text-red-500': page.props.errors.priority }">Priority Level</label>
                                                    <Select :class="{ '!border-red-500': page.props.errors.priority }" v-model="officeNoteForm.priority" :options="generalNotePriorityLevelOptions" optionLabel="label" optionValue="value" placeholder="Select priority..."></Select>
                                                    <Message v-if="page.props.errors.priority" size="small" severity="error" variant="simple">{{ page.props.errors.priority }}</Message>
                                                </div>
                                                <div class="flex flex-col ms-10">
                                                    <label for="" class="font-medium mb-1">Pinned</label>
                                                    <RadioButtonGroup v-model="officeNoteForm.is_pinned">
                                                        <div class="flex gap-5 mt-2">
                                                            <div class="flex items-center gap-2">
                                                                <RadioButton inputId="pinned-yes" :value="true" />
                                                                <label for="pinned-yes">Yes</label>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <RadioButton inputId="pinned-no" :value="false" />
                                                                <label for="pinned-no">No</label>
                                                            </div>
                                                        </div>
                                                        <Message v-if="page.props.errors.is_pinned" size="small" severity="error" variant="simple">{{ page.props.errors.is_pinned }}</Message>

                                                    </RadioButtonGroup>
                                                </div>
                                                <div class="flex flex-col">
                                                    <label for="" class="font-medium mb-1" :class="{ 'text-red-500': page.props.errors.assigned_to }">Assigned To</label>
                                                    <Select :class="{ '!border-red-500': page.props.errors.assigned_to }" v-model="officeNoteForm.assigned_to" :options="usersWithNobodyOption" optionLabel="name" optionValue="id" placeholder="Select a user..."></Select>
                                                    <Message v-if="page.props.errors.assigned_to" size="small" severity="error" variant="simple">{{ page.props.errors.assigned_to }}</Message>
                                                </div>
                                                <div class="flex flex-col ms-10">
                                                    <label for="" class="font-medium mb-1">Requires Follow-Up</label>
                                                    <RadioButtonGroup v-model="officeNoteForm.requires_follow_up">
                                                        <div class="flex gap-5 mt-2">
                                                            <div class="flex items-center gap-2">
                                                                <RadioButton inputId="followup-yes" :value="true" />
                                                                <label for="followup-yes">Yes</label>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <RadioButton inputId="followup-no" :value="false" />
                                                                <label for="followup-no">No</label>
                                                            </div>
                                                        </div>
                                                        <Message v-if="page.props.errors.requires_follow_up" size="small" severity="error" variant="simple">{{ page.props.errors.requires_follow_up }}</Message>
                                                    </RadioButtonGroup>
                                                </div>
                                            </div>
                                            <div class="flex flex-col mt-5">
                                                <label for="general-note" class="font-medium mb-1" :class="{ 'text-red-500': page.props.errors.note }">Notes</label>
                                                <Textarea :class="{ '!border-red-500': page.props.errors.note }" v-model="officeNoteForm.note" class="h-36" />
                                                <Message v-if="page.props.errors.note" size="small" severity="error" variant="simple">{{ page.props.errors.note }}</Message>
                                            </div>
                                            <div class="flex justify-end mt-5 gap-3">
                                                <!-- <Button label="Cancel" type="button" severity="secondary" /> -->
                                                <Button label="Submit" type="submit" />
                                            </div>
                                        </form>
                                    </div>
                                    <div class="flex-1">
                                        <div v-if="office.notes" class="flex flex-col gap-5 overflow-y-scroll max-h-[600px] border dark:border-surface-900 p-3 rounded-lg">
                                            <div v-for="note in office.notes" :key="note.id" class="bg-surface-50 border dark:border-surface-900 dark:bg-surface-950 rounded-lg p-3">
                                                <div class="flex flex-row justify-between items-center mb-3">
                                                    <p class="inline-flex items-center gap-2">
                                                        <span class="font-medium">Priority Level: </span>
                                                        <span>
                                                            <Message :severity="noteSeverity(note.priority)" size="small">{{ note.priority }}</Message>
                                                        </span>
                                                    </p>
                                                    <div class="inline-flex gap-2" v-if="note.is_pinned">
                                                        <Message severity="success" size="small" icon="pi pi-thumbtack">{{ note.is_pinned ? 'Pinned' : '' }}</Message>
                                                        <Button icon="pi pi-times" @click="onRemovePin(note.id)" />
                                                    </div>
                                                    <div v-else>
                                                        <Button label="Add Pin" @click="onAddPin(note.id)" />
                                                    </div>
                                                </div>
                                                <div class="flex flex-row justify-between items-center mb-3">
                                                    <p class="inline-flex items-center gap-2" v-if="note.requires_follow_up">
                                                        <span class="font-medium">Requires Follow Up: </span>
                                                        <Message severity="info" size="small">{{ note.requires_follow_up ? 'Yes' : 'No' }}</Message>
                                                    </p>
                                                    <p v-if="note.assigned_to" class="inline-flex items-center gap-2">
                                                        <span class="font-medium">Assigned To: </span>
                                                        <span>
                                                            <Message severity="info" size="small">{{ note.assigned_to.name }}</Message>
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="bg-surface-100 border dark:border-surface-900 dark:bg-surface-800 rounded p-2 mb-3">
                                                    {{ note.note }}
                                                </div>
                                                <div class="flex flex-row items-center justify-between">
                                                    <div>
                                                        <Button label="Remove Note" icon="pi pi-trash" @click="onDeleteNote(note.id)" />
                                                    </div>
                                                    <div>
                                                        <p>
                                                            {{ note.created_by.name }}
                                                        </p>
                                                        <p>
                                                            {{ note.formatted_created_at }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="flex flex-col gap-5 max-h-[600px] border dark:border-surface-900 p-3 rounded-lg">
                                            No notes found.
                                        </div>
                                    </div>
                                </div>

                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </template>
            </Card>
        </div>
    </Layout>
    <Dialog v-model:visible="showImageDialog" modal header="Upload Office Image" class="w-[25rem]">
        <p class="mb-5">Click below to upload an office image for {{ office.parent_company }}!</p>
        <FileUpload
            mode="basic"
            @select="onUpload($event)"
            accept="image/*"
            customUpload
            :maxFileSize="1000000"
        >
        </FileUpload>
    </Dialog>

</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import VisitTab from './Components/VisitTab.vue';
import DetailsTab from './Components/DetailsTab.vue';
import PerformanceTab from './Components/PerformanceTab.vue';
import { useAppStore } from '@/Store/appStore';
import { usePage, Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { Card, Button, Image, Dialog, FileUpload, Tabs, TabList, Tab, TabPanel, TabPanels, DataTable, Column, InputText, Select, Textarea, RadioButtonGroup, RadioButton, Message, Tag } from 'primevue';

const page = usePage();
const store = useAppStore();
const office = ref(page.props.office);
const realtors = page.props.office.realtors;
const users = page.props.users;

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

// console.log(office.value);

const agentTableData = computed(() => [
    { class: 'pi pi-star-fill text-yellow-500', label: "Highest Value", value: office.value.highest_value_agent ? office.value.highest_value_agent.formatted_full_name + ' (' + office.value.highest_value_agent.formatted_value + ')' : 'No agents found!' },
    { class: 'pi pi-hashtag text-green-500', label: "Active Agents / Total Agents", value: office.value.active_agents_count + ' / ' + office.value.realtor_count },
    { class: 'pi pi-hashtag text-yellow-500', label: "Engaged Agents", value: office.value.engaged_agents_count },
    { class: 'pi pi-hashtag text-red-500', label: "Inactive Agents", value: office.value.inactive_agents_count },
]);

/**
 * Handle activeTab State
 */
const activeTab = ref(0);
// Generate a unique localStorage key for this office
const localStorageKey = `activeTabForOffice-${office.id}`;

// Load the saved tab index for this office on component mount
onMounted(() => {
    const savedTab = localStorage.getItem(localStorageKey);
    if (savedTab !== null) {
        activeTab.value = parseInt(savedTab, 10); // Convert string to number
    }
});
// Watch for changes to the active tab and save them to localStorage
watch(activeTab, (newValue) => {
    localStorage.setItem(localStorageKey, newValue);
});

const showImageDialog = ref(false);

const usersWithNobodyOption = computed(() => [
    { name: 'Nobody', id: 0 }, // Add the "Nobody" option
    ...users
]);

const generalNotePriorityLevelOptions = [
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
]

const officeNoteForm = useForm({
    priority: 'medium',
    assigned_to: null,
    is_pinned: false,
    requires_follow_up: false,
    note: '',
});


const onOfficeNoteSubmit = () => {
    officeNoteForm.post(route('office-note.store', { office_id: office.id }), {
        preserveState: true,
        only: ['office', 'flash'],
        onSuccess: () => {
            office.notes = page.props.office.notes;
            officeNoteForm.reset();
        },
        onError: (error) => {

        }
    });
}

const noteSeverity = (priorityLevel) => {
    switch (priorityLevel) {
        case 'Low':
            return 'info';
            break;
        case 'Medium':
            return 'warn';
            break;
        case 'High':
            return 'error';
            break;
        default:
            return 'info';
    }

}

const onUpload = async (event) => {
    const file = event.files[0];
    if (!file) {
        console.error("No file selected.");
        return;
    }

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await axios.post(`/office/${office.id}/upload-image`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        office.image = response.data.image_url; // Update image
        showImageDialog.value = false; // Close dialog
        window.location.reload();
    } catch (error) {
        console.error("Upload error:", error.response || error.message);
        alert('Failed to upload image. Please try again.');
    }
};

const sortNotes = () => {
  office.notes.sort((a, b) => {
    // Pinned notes first
    if (a.is_pinned !== b.is_pinned) {
      return b.is_pinned - a.is_pinned; // `true` (1) comes before `false` (0)
    }
    // Newest notes first (compare created_at timestamps)
    return new Date(b.created_at) - new Date(a.created_at);
  });
};

const onRemovePin = (noteId) => {
    router.put(route('office-note.update', { office_note: noteId }),
    { request_type: 'remove_pin', is_pinned: false },
    {
        onSuccess: () => {
            // Find the note and update its `is_pinned` status
            const note = office.notes.find(note => note.id === noteId);
            if (note) {
                note.is_pinned = false;
            }

            // Sort the notes dynamically
            sortNotes();
        },
        onError: (error) => {
            console.error('Error pinning the note', error);
        }
    })
}

const onDeleteNote = (noteId) => {
    router.delete(route('office-note.destroy', { office_note: noteId }), {
        onSuccess: () => {
            const noteIndex = office.notes.findIndex(note => note.id === noteId);
            if (noteIndex !== -1) {
                // Remove the note from the array
                office.notes.splice(noteIndex, 1);
            }
            sortNotes();
        },
        onError: (error) => {
            console.error('Error deleting note:', error);
        }
    })
}

const onAddPin = async (noteId) => {
    router.put(route('office-note.update', { office_note: noteId }),
    { request_type: 'add_pin', is_pinned: true },
    {
        onSuccess: () => {
            // Find the note and update its `is_pinned` status
            const note = office.notes.find(note => note.id === noteId);
            if (note) {
                note.is_pinned = true;
            }

            // Sort the notes dynamically
            sortNotes();
        },
        onError: (error) => {
            console.error('Error pinning the note', error);
        }
    })
}

const tabs = [
    { value: 0, name: 'Location Details' },
    { value: 1, name: 'Agents' },
    // { value: 2, name: 'Marketing Sequence' },
    // { value: 3, name: 'Communication Log' },
    { value: 2, name: 'Visits' },
    { value: 3, name: 'Performance' },
    { value: 4, name: 'Notes' },
];

const onUpdateOffice = (newOffice) => {
    office.value = newOffice;
}


</script>
