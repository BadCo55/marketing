<template>
    <Head title="Create a Realtor" />

    <Layout>
        <div class="mx-auto w-full max-w-6xl px-4 py-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">
                    Create Realtor
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Add a new realtor to the marketing portal and capture the information needed for follow-up, office visits, and future campaigns.
                </p>
            </div>

            <Card
                :pt="{
                    root: { class: 'overflow-hidden rounded-2xl shadow-sm border border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900' },
                    content: { class: 'p-0 bg-surface-0 dark:bg-surface-900' },
                }"
            >
                <template #content>
                    <form @submit.prevent="submitForm">
                        <div class="divide-y divide-surface-200 dark:divide-surface-800">
                            <section class="p-6">
                                <div class="mb-5">
                                    <h2 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                        Basic Information
                                    </h2>
                                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                        Main contact details for the realtor.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                                    <div>
                                        <label
                                            for="first_name"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            First Name
                                        </label>
                                        <InputText
                                            id="first_name"
                                            v-model="form.first_name"
                                            fluid
                                            placeholder="Enter first name"
                                            :invalid="!!form.errors.first_name"
                                        />
                                        <Message
                                            v-if="form.errors.first_name"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.first_name }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="last_name"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Last Name
                                        </label>
                                        <InputText
                                            id="last_name"
                                            v-model="form.last_name"
                                            fluid
                                            placeholder="Enter last name"
                                            :invalid="!!form.errors.last_name"
                                        />
                                        <Message
                                            v-if="form.errors.last_name"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.last_name }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="title"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Title
                                        </label>
                                        <Select
                                            id="title"
                                            v-model="form.title"
                                            fluid
                                            editable
                                            :options="titleOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Select a title..."
                                            :invalid="!!form.errors.title"
                                        />
                                        <Message
                                            v-if="form.errors.title"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.title }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="phone_number"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Phone Number
                                        </label>
                                        <InputMask
                                            id="phone_number"
                                            v-model="form.phone_number"
                                            mask="999-999-9999"
                                            fluid
                                            placeholder="305-555-1234"
                                            :invalid="!!form.errors.phone_number"
                                        />
                                        <Message
                                            v-if="form.errors.phone_number"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.phone_number }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="secondary_phone"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Secondary Phone
                                        </label>
                                        <InputMask
                                            id="secondary_phone"
                                            v-model="form.secondary_phone"
                                            mask="999-999-9999"
                                            fluid
                                            placeholder="Optional secondary number"
                                            :invalid="!!form.errors.secondary_phone"
                                        />
                                        <Message
                                            v-if="form.errors.secondary_phone"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.secondary_phone }}
                                        </Message>
                                    </div>

                                    <div class="md:col-span-2 lg:col-span-1">
                                        <label
                                            for="email"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Email
                                        </label>
                                        <InputText
                                            id="email"
                                            v-model="form.email"
                                            fluid
                                            placeholder="name@company.com"
                                            :invalid="!!form.errors.email"
                                        />
                                        <Message
                                            v-if="form.errors.email"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.email }}
                                        </Message>
                                    </div>
                                </div>
                            </section>

                            <section class="p-6">
                                <div class="mb-5">
                                    <h2 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                        Office & Relationship
                                    </h2>
                                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                        Connect this realtor to an office and define how they fit into your outreach pipeline.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="lg:col-span-2">
                                        <label
                                            for="office"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Office
                                        </label>
                                        <AutoComplete
                                            id="office"
                                            v-model="form.office"
                                            fluid
                                            dropdown
                                            :suggestions="filteredOffices"
                                            @complete="filterOffices"
                                            :optionLabel="getLabel"
                                            placeholder="Search for an office..."
                                            :invalid="!!form.errors.office"
                                        />
                                        <Message
                                            v-if="form.errors.office"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.office }}
                                        </Message>

                                        <div class="mt-2">
                                            <Button
                                                label="Create New Office"
                                                icon="pi pi-plus"
                                                type="button"
                                                variant="text"
                                                size="small"
                                                @click="openAddOfficeModal"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            for="relationship_status"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Relationship Status
                                        </label>
                                        <Select
                                            id="relationship_status"
                                            v-model="form.relationship_status"
                                            fluid
                                            :options="relationshipStatusOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Select status"
                                            :invalid="!!form.errors.relationship_status"
                                        />
                                        <Message
                                            v-if="form.errors.relationship_status"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.relationship_status }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="lead_source"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Lead Source
                                        </label>
                                        <Select
                                            id="lead_source"
                                            v-model="form.lead_source"
                                            fluid
                                            editable
                                            :options="leadSourceOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="How did they enter the system?"
                                            :invalid="!!form.errors.lead_source"
                                        />
                                        <Message
                                            v-if="form.errors.lead_source"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.lead_source }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="preferred_contact_method"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Preferred Contact Method
                                        </label>
                                        <Select
                                            id="preferred_contact_method"
                                            v-model="form.preferred_contact_method"
                                            fluid
                                            :options="contactMethodOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Select contact method"
                                            :invalid="!!form.errors.preferred_contact_method"
                                        />
                                        <Message
                                            v-if="form.errors.preferred_contact_method"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.preferred_contact_method }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="market_area"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Market Area
                                        </label>
                                        <InputText
                                            id="market_area"
                                            v-model="form.market_area"
                                            fluid
                                            placeholder="Example: Broward, East Boca, Coral Gables"
                                            :invalid="!!form.errors.market_area"
                                        />
                                        <Message
                                            v-if="form.errors.market_area"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.market_area }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="license_number"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            License Number
                                        </label>
                                        <InputText
                                            id="license_number"
                                            v-model="form.license_number"
                                            fluid
                                            placeholder="Optional"
                                            :invalid="!!form.errors.license_number"
                                        />
                                        <Message
                                            v-if="form.errors.license_number"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.license_number }}
                                        </Message>
                                    </div>
                                </div>
                            </section>

                            <section class="p-6">
                                <div class="mb-5">
                                    <h2 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                        Outreach Preferences
                                    </h2>
                                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                        Store preferences that will help with follow-up, campaigns, and relationship management.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                                    <div class="flex items-center gap-3 rounded-xl border border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-4 py-3">
                                        <Checkbox
                                            id="email_opt_in"
                                            v-model="form.email_opt_in"
                                            binary
                                        />
                                        <label
                                            for="email_opt_in"
                                            class="text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Email Opt-In
                                        </label>
                                    </div>

                                    <div class="flex items-center gap-3 rounded-xl border border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-4 py-3">
                                        <Checkbox
                                            id="sms_opt_in"
                                            v-model="form.sms_opt_in"
                                            binary
                                        />
                                        <label
                                            for="sms_opt_in"
                                            class="text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            SMS / Text Opt-In
                                        </label>
                                    </div>

                                    <div class="flex items-center gap-3 rounded-xl border border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-4 py-3">
                                        <Checkbox
                                            id="is_active"
                                            v-model="form.is_active"
                                            binary
                                        />
                                        <label
                                            for="is_active"
                                            class="text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Active Contact
                                        </label>
                                    </div>

                                    <div>
                                        <label
                                            for="last_contacted_at"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Last Contacted
                                        </label>
                                        <DatePicker
                                            id="last_contacted_at"
                                            v-model="form.last_contacted_at"
                                            fluid
                                            showIcon
                                            placeholder="Select date"
                                            :invalid="!!form.errors.last_contacted_at"
                                        />
                                        <Message
                                            v-if="form.errors.last_contacted_at"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.last_contacted_at }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="next_follow_up_date"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Next Follow-Up Date
                                        </label>
                                        <DatePicker
                                            id="next_follow_up_date"
                                            v-model="form.next_follow_up_date"
                                            fluid
                                            showIcon
                                            placeholder="Select date"
                                            :invalid="!!form.errors.next_follow_up_date"
                                        />
                                        <Message
                                            v-if="form.errors.next_follow_up_date"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.next_follow_up_date }}
                                        </Message>
                                    </div>

                                    <div>
                                        <label
                                            for="tags"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Tags
                                        </label>
                                        <InputText
                                            id="tags"
                                            v-model="form.tags"
                                            fluid
                                            placeholder="Example: luxury, investor-friendly, broward"
                                            :invalid="!!form.errors.tags"
                                        />
                                        <Message
                                            v-if="form.errors.tags"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.tags }}
                                        </Message>
                                    </div>

                                    <div class="md:col-span-2 lg:col-span-3">
                                        <label
                                            for="notes"
                                            class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200"
                                        >
                                            Notes
                                        </label>
                                        <Textarea
                                            id="notes"
                                            v-model="form.notes"
                                            fluid
                                            rows="4"
                                            autoResize
                                            placeholder="Add relationship notes, referral details, personality notes, office visit context, etc."
                                            :invalid="!!form.errors.notes"
                                        />
                                        <Message
                                            v-if="form.errors.notes"
                                            variant="simple"
                                            size="small"
                                            severity="error"
                                            class="mt-2"
                                        >
                                            {{ form.errors.notes }}
                                        </Message>
                                    </div>
                                </div>
                            </section>

                            <section class="flex flex-col-reverse gap-3 p-6 sm:flex-row sm:items-center sm:justify-end">
                                <Button
                                    label="Reset"
                                    type="button"
                                    severity="secondary"
                                    outlined
                                    @click="resetForm"
                                />
                                <Button
                                    label="Create Realtor"
                                    type="submit"
                                    icon="pi pi-check-circle"
                                    :loading="form.processing"
                                />
                            </section>
                        </div>

                        <Dialog
                            header="Add New Office"
                            v-model:visible="isAddOfficeModalVisible"
                            modal
                            :style="{ width: '42rem', maxWidth: '95vw' }"
                            :breakpoints="{ '960px': '92vw', '640px': '96vw' }"
                            :pt="{
                                root: { class: 'overflow-hidden rounded-2xl' },
                                header: { class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 text-surface-900 dark:text-surface-0' },
                                content: { class: 'bg-surface-0 dark:bg-surface-900 text-surface-900 dark:text-surface-0' },
                            }"
                        >
                            <NewOfficeForm @updatedOffices="onNewOfficeSubmit" />
                        </Dialog>
                    </form>
                </template>
            </Card>
        </div>
    </Layout>
</template>

<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import {
    Button,
    InputText,
    AutoComplete,
    Dialog,
    InputMask,
    Card,
    Message,
    Select,
    Textarea,
    DatePicker,
    Checkbox,
} from 'primevue';
import { ref } from 'vue';
import NewOfficeForm from '@/Components/NewOfficeForm.vue';

const page = usePage();

const form = useForm({
    first_name: '',
    last_name: '',
    phone_number: '',
    secondary_phone: '',
    email: '',
    office: '',
    title: '',
    relationship_status: 'new',
    lead_source: '',
    preferred_contact_method: '',
    market_area: '',
    license_number: '',
    email_opt_in: true,
    sms_opt_in: false,
    is_active: true,
    last_contacted_at: null,
    next_follow_up_date: null,
    tags: '',
    notes: '',
});

const titleOptions = [
    { value: 'broker', label: 'Broker' },
    { value: 'broker_associate', label: 'Broker Associate' },
    { value: 'realtor', label: 'Realtor' },
    { value: 'team_lead', label: 'Team Lead' },
    { value: 'sales_associate', label: 'Sales Associate' },
];

const relationshipStatusOptions = [
    { value: 'new', label: 'New' },
    { value: 'contacted', label: 'Contacted' },
    { value: 'engaged', label: 'Engaged' },
    { value: 'active_partner', label: 'Active Partner' },
    { value: 'inactive', label: 'Inactive' },
];

const leadSourceOptions = [
    { value: 'office_visit', label: 'Office Visit' },
    { value: 'bni', label: 'BNI' },
    { value: 'referral', label: 'Referral' },
    { value: 'website', label: 'Website' },
    { value: 'instagram', label: 'Instagram' },
    { value: 'facebook', label: 'Facebook' },
    { value: 'email_campaign', label: 'Email Campaign' },
    { value: 'cold_outreach', label: 'Cold Outreach' },
    { value: 'other', label: 'Other' },
];

const contactMethodOptions = [
    { value: 'call', label: 'Call' },
    { value: 'text', label: 'Text' },
    { value: 'email', label: 'Email' },
    { value: 'office_visit', label: 'Office Visit' },
];

const submitForm = () => {
    form.post(route('realtor.store'), {
        preserveScroll: true,
        onSuccess: () => {},
        onError: () => {
            console.log(form.errors);
        },
    });
};

const offices = ref(page.props.offices ?? []);
const filteredOffices = ref([]);
const isAddOfficeModalVisible = ref(false);

const getLabel = (option) => {
    if (!option) return '';

    const company = option.parent_company ?? 'Unknown Office';
    const street = option.street_address ?? '';
    const unit = option.unit_number ? ` #${option.unit_number}` : '';
    const city = option.city ? ` (${option.city})` : '';

    return `${company}${street ? ` - ${street}${unit}` : ''}${city}`;
};

const filterOffices = (event) => {
    const query = (event.query || '').toLowerCase().trim();

    filteredOffices.value = offices.value.filter((office) => {
        const parentCompany = (office.parent_company || '').toLowerCase();
        const streetAddress = (office.street_address || '').toLowerCase();
        const city = (office.city || '').toLowerCase();

        return (
            parentCompany.includes(query) ||
            streetAddress.includes(query) ||
            city.includes(query)
        );
    });
};

const openAddOfficeModal = () => {
    isAddOfficeModalVisible.value = true;
};

const onNewOfficeSubmit = (data) => {
    offices.value = data;
    isAddOfficeModalVisible.value = false;
    filterOffices({ query: typeof form.office === 'string' ? form.office : '' });
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
};
</script>
