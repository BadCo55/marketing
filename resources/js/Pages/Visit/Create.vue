<template>
    <Head title="Log a Visit" />
    <Dialog header="Add New Office" v-model:visible="isNewOfficeDialogVisible" class="w-[35rem]">
        <NewOfficeForm  />
    </Dialog>
    <Layout>
        <div v-if="!isOfficeSelected" class="flex justify-center mx-auto p-8">
            <Card class="w-full lg:w-1/3">
                <template #content>
                    <form @submit.prevent="onOfficeVisitSubmit">
                        <div class="flex flex-col items-center gap-5 w-full">
                            <div class="flex flex-col w-full">
                                <AutoComplete 
                                    fluid
                                    :suggestions="filteredOffices" 
                                    v-model="form.office" 
                                    dropdown
                                    @complete="filterOffices"
                                    :optionLabel="getLabel"
                                    placeholder="Select an existing office..."
                                />
                            </div>
                            <Button label="Or, Create a New One" icon="pi pi-plus-circle" @click="isNewOfficeDialogVisible = true" />
                        </div>
                    </form>
                </template>
            </Card>
        </div>
        <div v-else>
            <div class="flex flex-col xl:flex-row gap-5 p-8">
                <Card class="flex mb-auto mx-auto">
                    <template #content>
                        <div class="flex justify-center items-center gap-3">
                            <img v-if="form.office.image_url" :src="form.office.image_url" alt="" class="w-36 rounded-lg">                            
                            <div class="flex flex-col">
                                <p class="font-medium text-xl">{{ form.office.parent_company }}</p>
                                <p v-if="form.office.office_name">{{ form.office.office_name }}</p>
                                <a class="text-blue-500" :href="`tel:+1${form.office.office_phone}`">{{ form.office.office_phone }}</a>
                                <div class="font-medium">
                                    <p>{{ form.office.street_address }} {{ form.office.unit_number ? form.office.unit_number : '' }}</p>
                                    <p>{{ form.office.city }}, {{ form.office.state }} {{ form.office.zip_code }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex mt-3 gap-2 justify-center">
                            <div class="flex mb-auto">
                                <Button label="Change" icon="pi pi-arrow-left" @click="onDeselectOffice" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <Button label="Get Directions" icon="pi pi-directions" />
                                <Link
                                    :href="route('office.show', form.office.id)"
                                >
                                    <Button label="View Office Details" icon="pi pi-eye" />
                                </Link>
                            </div>
                        </div>
                    </template>
                </Card>
                <Card class="flex-1 mb-auto">
                    <template #content>
                        <form @submit.prevent="onFormSubmit">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                                <div class="flex flex-col">
                                    <label for="" >Visit Date</label>
                                    <DatePicker :class="{ '!border-red-500': page.props.errors.visit_date }" v-model="form.visit_date" placeholder="Select a date..." dateFormat="mm/dd/yy" />
                                    <Message v-if="page.props.errors.visit_date" severity="error" variant="simple" size="small">{{ page.props.errors.visit_date }}</Message>
                                </div>
                                <div class="flex flex-col">
                                    <label for="" >Purpose of Visit</label>
                                    <Select :class="{ '!border-red-500': page.props.errors.purpose }" v-model="form.purpose" :options="purposeOptions" optionLabel="label" optionValue="value" placeholder="Select an option..." />
                                    <Message v-if="page.props.errors.purpose" severity="error" variant="simple" size="small">{{ page.props.errors.purpose }}</Message>
                                </div>
                                <div class="flex flex-col items-center">
                                    <label for="" >Overall Rating</label>
                                    <Rating class="mt-3" v-model="form.rating" />
                                    <Message v-if="page.props.errors.rating" severity="error" variant="simple" size="small">{{ page.props.errors.rating }}</Message>
                                </div>
                                <div class="flex flex-col">
                                    <label for="" >Estimated Visit Length (minutes)</label>
                                    <Select :class="{ '!border-red-500': page.props.errors.duration }" :options="visitLengthOptions" optionLabel="label" optionValue="value" v-model="form.duration" placeholder="Select a duration..." />
                                    <Message v-if="page.props.errors.duration" severity="error" variant="simple" size="small">{{ page.props.errors.duration }}</Message>

                                </div>
                                <div class="flex flex-col">
                                    <label for="" >Materials Presented (multiple)</label>
                                    <MultiSelect :class="{ '!border-red-500': page.props.errors.materials_presented }" :options="materialsOptions" optionLabel="label" optionValue="value" v-model="form.materials_presented" placeholder="Select materials..." />
                                    <Message v-if="page.props.errors.materials_presented" severity="error" variant="simple" size="small">{{ page.props.errors.materials_presented }}</Message>
                                </div>
                                <div class="flex flex-col items-center">
                                    <label for="" >Interest Level</label>
                                    <Rating class="mt-3" v-model="form.interest_level" />
                                    <Message v-if="page.props.errors.interest_level" severity="error" variant="simple" size="small">{{ page.props.errors.interest_level }}</Message>
                                </div>
                                <div class="flex flex-col">
                                    <label for="">Topics Discussed</label>
                                    <Textarea :class="{ '!border-red-500': page.props.errors.discussion_topics }" placeholder="Describe discussion topics..." v-model="form.discussion_topics" />
                                    <Message v-if="page.props.errors.discussion_topics" severity="error" variant="simple" size="small">{{ page.props.errors.discussion_topics }}</Message>
                                </div>
                                <div class="flex flex-col">
                                    <label for="">Initial Impressions</label>
                                    <Textarea :class="{ '!border-red-500': page.props.errors.initial_impressions }" placeholder="Describe initial impressions..." v-model="form.initial_impressions" />
                                    <Message v-if="page.props.errors.initial_impressions" severity="error" variant="simple" size="small">{{ page.props.errors.initial_impressions }}</Message>
                                </div>
                                <div class="flex flex-col">
                                    <label for="">General Notes</label>
                                    <Textarea :class="{ '!border-red-500': page.props.errors.notes }" placeholder="Add general notes..." v-model="form.notes" />
                                    <Message v-if="page.props.errors.notes" severity="error" variant="simple" size="small">{{ page.props.errors.notes }}</Message>
                                </div>
                                <div class="flex">
                                    <RadioButtonGroup v-model="form.follow_up_required">
                                        <div class="flex flex-col">
                                            <label for="">Follow-Up Required</label>
                                            <div class="inline-flex gap-3 mt-2">
                                                <div class="flex items-center gap-2">
                                                    <RadioButton name="Yes" inputId="follow-up-yes" :value="true" />
                                                    <label for="follow-up-yes">Yes</label>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <RadioButton name="No" inputId="follow-up-no" :value="false" />
                                                    <label for="follow-up-no">No</label>
                                                </div>
                                            </div>
                                            <Message v-if="page.props.errors.follow_up_required" severity="error" variant="simple" size="small">{{ page.props.errors.follow_up_required }}</Message>
                                        </div>
                                    </RadioButtonGroup>
                                </div>
                                <div class="flex flex-col">
                                    <label for="">Next Actions Required</label>
                                    <Textarea :class="{ '!border-red-500': page.props.errors.next_action }" placeholder="Describe next actions..." v-model="form.next_action" />
                                    <Message v-if="page.props.errors.next_action" severity="error" variant="simple" size="small">{{ page.props.errors.next_action }}</Message>
                                </div>
                                <div class="flex flex-col">
                                    <label for="">Next Action Date</label>
                                    <DatePicker :class="{ '!border-red-500': page.props.errors.next_action_date }" placeholder="Select a date..." v-model="form.next_action_date" />
                                    <Message v-if="page.props.errors.next_action_date" severity="error" variant="simple" size="small">{{ page.props.errors.next_action_date }}</Message>
                                </div>
                            </div>
                            <div class="flex justify-end mt-5">
                                <Button label="Submit" type="submit" icon="pi pi-check-circle" />
                            </div>
                        </form>
                    </template>
                </Card>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import NewOfficeForm from '@/Components/NewOfficeForm.vue';
import { useForm, usePage, Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Card, InputText, Rating, MultiSelect, Select, RadioButtonGroup, RadioButton, Textarea, Button, AutoComplete, DatePicker, Dialog, Message } from 'primevue';
import { format } from 'date-fns';

const page = usePage();
const offices = page.props.offices;

/**
 * State variables
 */
const isOfficeSelected = ref(false);
const isNewOfficeDialogVisible = ref(false);

/**
 * Select options
 */
 const purposeOptions = [
    { label: 'Introduction', value: 'introduction' },
    { label: 'Follow-Up', value: 'follow_up' },
    { label: 'Training', value: 'training' },
    { label: 'Material Drop-Off', value: 'material_drop_off' },
    { label: 'Relationship Building', value: 'relationship_building' },
    { label: 'Issue Resolution', value: 'issue_resolution' },
    { label: 'New Product/Service Introduction', value: 'new_product' },
    { label: 'Networking Event', value: 'networking_event' },
    { label: 'Inspection Feedback', value: 'inspection_feedback' },
];
const visitLengthOptions = Array.from({ length: 12 }, (_, i) => ({
    label: `${(i + 1) * 5} minutes`,
    value: (i + 1) * 5,
}));
const materialsOptions = [
    { label: 'Introductory Packet', value: 'introductory_packet' },
    { label: 'Company Brochures', value: 'company_brochures' },
    { label: 'Sample Inspection Reports', value: 'sample_reports' },
    { label: 'Promotional Items', value: 'promotional_items' },
    { label: 'Pricing Sheets', value: 'pricing_sheets' },
    { label: 'Educational Materials', value: 'educational_materials' },
    { label: 'Service Menus', value: 'service_menus' },
    { label: 'Discount or Incentive Flyers', value: 'discount_flyers' },
    { label: 'Case Studies/Testimonials', value: 'case_studies' },
    { label: 'Business Cards', value: 'business_cards' },
    { label: 'Checklists or Maintenance Tips', value: 'checklists' },
    { label: 'Digital Presentation', value: 'digital_presentation' },
    { label: 'Contracts or Agreements', value: 'contracts' },
]

/**
 * Deselect office
 */
const onDeselectOffice = () => {
    isOfficeSelected.value = false;
    form.reset();
}

/**
 * Filter offices for autocomplete
 */
const filteredOffices = ref([]);
const filterOffices = (event) => {
    const query = event.query.toLowerCase();
    filteredOffices.value = offices.filter(office => {
        return (
            office.parent_company.toLowerCase().includes(query) ||
            office.office_name?.toLowerCase().includes(query) || 
            office.street_address.toLowerCase().includes(query) ||
            office.city.toLowerCase().includes(query) ||
            office.zip_code.toLowerCase().includes(query)
        );
    });
}
/**
 * Get the office AutoComplete Label
 */
 const getLabel = (option) => {
    const unitPart = option.unit_number ? ` #${option.unit_number}` : ''; // Include # only if unit_number exists
    const label = option.office_name 
        ? `${option.parent_company} - ${option.office_name} (${option.street_address}${unitPart})`
        : `${option.parent_company} (${option.street_address}${unitPart})`;
    return label;
};

/**
 * Create the form object for a new office visit
 */
const form = useForm({
    office: '',
    user_id: '',
    visit_date: format(new Date(), 'MM/dd/yyyy'),
    duration: '', //
    rating: '', //
    purpose: '', //
    initial_impressions: '', //
    interest_level: '', //
    follow_up_required: '', //
    materials_presented: '', //
    discussion_topics: '', //
    notes: '', //
    next_action: '', //
    next_action_date: '', //
    status: '',
});

/**
 * Watch for office changes in form
 */
 watch(
    () => form.office, // Use a getter function to access the value reactively
    async (newVal) => {
        if (newVal && typeof newVal === 'object' && newVal !== null) {
            isOfficeSelected.value = true;
        }
    }
);

/**
 * Submit the form
 */
const onFormSubmit = () => {
    form.post(route('office-visit.store'), {
        onSuccess: (response) => {
            form.reset();
            // router.visit('sequence.index');
        },
        onError: (error) => {
            console.log(error);
        }
    });
}

</script>

