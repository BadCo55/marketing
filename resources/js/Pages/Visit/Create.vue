<template>
    <Head title="Log a Visit" />

    <Dialog
        header="Add New Office"
        v-model:visible="isNewOfficeDialogVisible"
        modal
        :style="{ width: '48rem', maxWidth: '95vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-xl' },
            header: {
                class: 'border-b border-surface-200 dark:border-surface-800 bg-surface-0 dark:bg-surface-900 px-5 py-4 text-surface-900 dark:text-surface-0',
            },
            content: {
                class: 'bg-surface-0 dark:bg-surface-900 px-5 py-5 text-surface-900 dark:text-surface-0',
            },
        }"
    >
        <NewOfficeForm />
    </Dialog>

    <Layout>
        <!-- CHANGED: removed mx-auto/max-w-7xl so layout expands naturally with sidebar -->
        <div class="px-4 py-5 md:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-3">
                <div>
                    <Link :href="route('office-visit.index')">
                        <Button
                            label="Back to Visits"
                            icon="pi pi-arrow-left"
                            severity="secondary"
                            text
                        />
                    </Link>
                </div>

                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-surface-900 dark:text-surface-0">
                        Log a Visit
                    </h1>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Select an office, review its details, and record a new visit entry.
                    </p>
                </div>
            </div>

            <div v-if="!isOfficeSelected" class="mx-auto max-w-2xl">
                <!-- CHANGED: normalized radius to rounded-xl -->
                <Card class="rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                    <template #content>
                        <div class="space-y-6">
                            <div>
                                <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                    Choose an Office
                                </h2>
                                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                    Search for an existing office or create a new one before logging
                                    the visit.
                                </p>
                            </div>

                            <form @submit.prevent class="space-y-5">
                                <div class="flex flex-col">
                                    <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                        Existing Office
                                    </label>

                                    <AutoComplete
                                        fluid
                                        :suggestions="filteredOffices"
                                        v-model="selectedOffice"
                                        dropdown
                                        @complete="filterOffices"
                                        :optionLabel="getLabel"
                                        placeholder="Search by company, office name, address, city, or ZIP..."
                                    />
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                    <Button
                                        label="Create New Office"
                                        icon="pi pi-plus-circle"
                                        type="button"
                                        severity="secondary"
                                        outlined
                                        @click="isNewOfficeDialogVisible = true"
                                    />
                                </div>
                            </form>
                        </div>
                    </template>
                </Card>
            </div>

            <div v-else class="grid grid-cols-12 items-start gap-5">
                <div class="col-span-12 xl:col-span-4 xl:self-start">
                    <div class="space-y-5 xl:sticky xl:top-8">
                        <!-- CHANGED: normalized radius to rounded-xl -->
                        <Card
                            class="overflow-hidden rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900"
                        >
                            <template #content>
                                <div class="flex flex-col gap-5">
                                    <div
                                        class="flex flex-col items-center gap-5 lg:flex-row lg:items-start"
                                    >
                                        <!-- CHANGED: normalized inner image radius to rounded-xl -->
                                        <div
                                            class="flex h-32 w-32 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-surface-200 bg-surface-100 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <img
                                                v-if="selectedOffice.image_url"
                                                :src="selectedOffice.image_url"
                                                alt="Office"
                                                class="h-32 w-32 object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-full w-full items-center justify-center text-surface-400 dark:text-surface-500"
                                            >
                                                <i class="pi pi-building text-3xl" />
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1 text-center lg:text-left">
                                            <h2 class="text-2xl font-bold leading-tight text-surface-900 dark:text-surface-0">
                                                {{ selectedOffice.parent_company }}
                                            </h2>

                                            <p
                                                v-if="selectedOffice.office_name"
                                                class="mt-1 text-sm text-surface-500 dark:text-surface-400"
                                            >
                                                {{ selectedOffice.office_name }}
                                            </p>

                                            <div class="mt-4 space-y-3">
                                                <div
                                                    v-if="selectedOffice.office_phone"
                                                    class="flex items-start justify-center gap-3 text-sm lg:justify-start"
                                                >
                                                    <div class="mt-0.5 text-surface-400 dark:text-surface-500">
                                                        <i class="pi pi-phone" />
                                                    </div>
                                                    <a
                                                        class="break-all text-primary hover:underline"
                                                        :href="`tel:+1${selectedOffice.office_phone}`"
                                                    >
                                                        {{ selectedOffice.office_phone }}
                                                    </a>
                                                </div>

                                                <div
                                                    class="flex items-start justify-center gap-3 text-sm lg:justify-start"
                                                >
                                                    <div class="mt-0.5 text-surface-400 dark:text-surface-500">
                                                        <i class="pi pi-map-marker" />
                                                    </div>
                                                    <p class="text-surface-700 dark:text-surface-300">
                                                        {{ officeAddress }}
                                                    </p>
                                                </div>

                                                <div
                                                    v-if="
                                                        selectedOffice.office_name ||
                                                        selectedOffice.parent_company
                                                    "
                                                    class="flex items-start justify-center gap-3 text-sm lg:justify-start"
                                                >
                                                    <div class="mt-0.5 text-surface-400 dark:text-surface-500">
                                                        <i class="pi pi-id-card" />
                                                    </div>
                                                    <p class="text-surface-500 dark:text-surface-400">
                                                        Office profile selected
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <Divider />

                                    <div
                                        class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2"
                                    >
                                        <Button
                                            label="Change Office"
                                            icon="pi pi-arrow-left"
                                            severity="secondary"
                                            outlined
                                            type="button"
                                            @click="onDeselectOffice"
                                        />

                                        <Link :href="route('office.show', selectedOffice.id)">
                                            <Button
                                                label="View Office"
                                                icon="pi pi-eye"
                                                class="w-full"
                                                type="button"
                                            />
                                        </Link>
                                    </div>

                                    <Button
                                        label="Get Directions"
                                        icon="pi pi-directions-right"
                                        severity="secondary"
                                        outlined
                                        type="button"
                                        @click="openDirections"
                                    />
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <div class="col-span-12 min-w-0 xl:col-span-8">
                    <!-- CHANGED: removed xl:max-w-4xl so right side expands with available layout width -->
                    <div class="w-full">
                        <!-- CHANGED: normalized radius to rounded-xl -->
                        <Card class="rounded-xl border border-surface-200 shadow-sm dark:border-surface-800 dark:bg-surface-900">
                            <template #content>
                                <form @submit.prevent="onFormSubmit" class="space-y-6">
                                    <div>
                                        <h2 class="text-xl font-semibold text-surface-900 dark:text-surface-0">
                                            Visit Details
                                        </h2>
                                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                            Record the purpose, impressions, ratings, and follow-up
                                            details from this visit.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                                        <div class="flex flex-col">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Visit Date
                                            </label>
                                            <DatePicker
                                                v-model="form.visit_date"
                                                placeholder="Select a date..."
                                                dateFormat="mm/dd/yy"
                                                fluid
                                                :class="{
                                                    '!border-red-500': form.errors.visit_date,
                                                }"
                                            />
                                            <Message
                                                v-if="form.errors.visit_date"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.visit_date }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Purpose of Visit
                                            </label>
                                            <Select
                                                v-model="form.purpose"
                                                :options="purposeOptions"
                                                optionLabel="label"
                                                optionValue="value"
                                                placeholder="Select an option..."
                                                fluid
                                                :class="{ '!border-red-500': form.errors.purpose }"
                                            />
                                            <Message
                                                v-if="form.errors.purpose"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.purpose }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Visit Length (minutes)
                                            </label>
                                            <Select
                                                :options="visitLengthOptions"
                                                optionLabel="label"
                                                optionValue="value"
                                                v-model="form.duration"
                                                placeholder="Select a duration..."
                                                fluid
                                                :class="{ '!border-red-500': form.errors.duration }"
                                            />
                                            <Message
                                                v-if="form.errors.duration"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.duration }}
                                            </Message>
                                        </div>

                                        <!-- CHANGED: normalized radius to rounded-xl -->
                                        <div
                                            class="flex flex-col rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <label class="mb-3 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Overall Rating
                                            </label>
                                            <Rating v-model="form.rating" />
                                            <Message
                                                v-if="form.errors.rating"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.rating }}
                                            </Message>
                                        </div>

                                        <!-- CHANGED: normalized radius to rounded-xl -->
                                        <div
                                            class="flex flex-col rounded-xl border border-surface-200 bg-surface-50 p-4 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <label class="mb-3 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Interest Level
                                            </label>
                                            <Rating v-model="form.interest_level" />
                                            <Message
                                                v-if="form.errors.interest_level"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.interest_level }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Materials Presented
                                            </label>
                                            <MultiSelect
                                                :options="materialsOptions"
                                                optionLabel="label"
                                                optionValue="value"
                                                v-model="form.materials_presented"
                                                placeholder="Select materials..."
                                                fluid
                                                display="chip"
                                                :class="{
                                                    '!border-red-500':
                                                        form.errors.materials_presented,
                                                }"
                                            />
                                            <Message
                                                v-if="form.errors.materials_presented"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.materials_presented }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col lg:col-span-3">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Topics Discussed
                                            </label>
                                            <Textarea
                                                placeholder="Describe discussion topics..."
                                                v-model="form.discussion_topics"
                                                rows="4"
                                                autoResize
                                                fluid
                                                :class="{
                                                    '!border-red-500':
                                                        form.errors.discussion_topics,
                                                }"
                                            />
                                            <Message
                                                v-if="form.errors.discussion_topics"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.discussion_topics }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col lg:col-span-3">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Initial Impressions
                                            </label>
                                            <Textarea
                                                placeholder="Describe initial impressions..."
                                                v-model="form.initial_impressions"
                                                rows="4"
                                                autoResize
                                                fluid
                                                :class="{
                                                    '!border-red-500':
                                                        form.errors.initial_impressions,
                                                }"
                                            />
                                            <Message
                                                v-if="form.errors.initial_impressions"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.initial_impressions }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col lg:col-span-3">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                General Notes
                                            </label>
                                            <Textarea
                                                placeholder="Add general notes..."
                                                v-model="form.notes"
                                                rows="4"
                                                autoResize
                                                fluid
                                                :class="{ '!border-red-500': form.errors.notes }"
                                            />
                                            <Message
                                                v-if="form.errors.notes"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.notes }}
                                            </Message>
                                        </div>

                                        <!-- CHANGED: normalized radius to rounded-xl -->
                                        <div
                                            class="flex flex-col rounded-xl border border-surface-200 bg-surface-50 p-4 lg:col-span-1 dark:border-surface-700 dark:bg-surface-800"
                                        >
                                            <label class="mb-3 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Follow-Up Required
                                            </label>
                                            <RadioButtonGroup v-model="form.follow_up_required">
                                                <div class="flex flex-col gap-3">
                                                    <div class="flex items-center gap-2">
                                                        <RadioButton
                                                            inputId="follow-up-yes"
                                                            :value="true"
                                                        />
                                                        <label for="follow-up-yes" class="text-surface-700 dark:text-surface-300">Yes</label>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <RadioButton
                                                            inputId="follow-up-no"
                                                            :value="false"
                                                        />
                                                        <label for="follow-up-no" class="text-surface-700 dark:text-surface-300">No</label>
                                                    </div>
                                                </div>
                                            </RadioButtonGroup>
                                            <Message
                                                v-if="form.errors.follow_up_required"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.follow_up_required }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col lg:col-span-1">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Next Action Required
                                            </label>
                                            <Textarea
                                                placeholder="Describe next actions..."
                                                v-model="form.next_action"
                                                rows="4"
                                                autoResize
                                                fluid
                                                :class="{
                                                    '!border-red-500': form.errors.next_action,
                                                }"
                                            />
                                            <Message
                                                v-if="form.errors.next_action"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.next_action }}
                                            </Message>
                                        </div>

                                        <div class="flex flex-col lg:col-span-1">
                                            <label class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100">
                                                Next Action Date
                                            </label>
                                            <DatePicker
                                                placeholder="Select a date..."
                                                v-model="form.next_action_date"
                                                fluid
                                                :class="{
                                                    '!border-red-500': form.errors.next_action_date,
                                                }"
                                            />
                                            <Message
                                                v-if="form.errors.next_action_date"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                                class="mt-2"
                                            >
                                                {{ form.errors.next_action_date }}
                                            </Message>
                                        </div>
                                    </div>

                                    <div
                                        class="flex justify-end gap-3 border-t border-surface-200 dark:border-surface-800 pt-5"
                                    >
                                        <Button
                                            label="Cancel"
                                            severity="secondary"
                                            outlined
                                            type="button"
                                            @click="onDeselectOffice"
                                        />
                                        <Button
                                            label="Submit Visit"
                                            type="submit"
                                            icon="pi pi-check-circle"
                                            :loading="form.processing"
                                        />
                                    </div>
                                </form>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue'
import NewOfficeForm from '@/Components/NewOfficeForm.vue'
import { useForm, usePage, Head, Link } from '@inertiajs/vue3'
import { ref, computed, watch, onMounted } from 'vue'
import {
    Card,
    Rating,
    MultiSelect,
    Select,
    RadioButtonGroup,
    RadioButton,
    Textarea,
    Button,
    AutoComplete,
    DatePicker,
    Dialog,
    Message,
    Divider,
} from 'primevue'
import { format } from 'date-fns'

const page = usePage()
const offices = page.props.offices ?? []

const isOfficeSelected = ref(false)
const isNewOfficeDialogVisible = ref(false)
const selectedOffice = ref(null)

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
]

const visitLengthOptions = Array.from({ length: 12 }, (_, i) => ({
    label: `${(i + 1) * 5} minutes`,
    value: (i + 1) * 5,
}))

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

const onDeselectOffice = () => {
    isOfficeSelected.value = false
    selectedOffice.value = null
    form.reset()
    form.visit_date = format(new Date(), 'MM/dd/yyyy')
    form.office_id = null
}

const filteredOffices = ref([])

const filterOffices = (event) => {
    const query = (event.query || '').toLowerCase()

    filteredOffices.value = offices.filter((office) => {
        return (
            office.parent_company?.toLowerCase().includes(query) ||
            office.office_name?.toLowerCase().includes(query) ||
            office.street_address?.toLowerCase().includes(query) ||
            office.city?.toLowerCase().includes(query) ||
            office.zip_code?.toLowerCase().includes(query)
        )
    })
}

const getLabel = (option) => {
    const unitPart = option.unit_number ? ` #${option.unit_number}` : ''
    return option.office_name
        ? `${option.parent_company} - ${option.office_name} (${option.street_address}${unitPart})`
        : `${option.parent_company} (${option.street_address}${unitPart})`
}

const form = useForm({
    office_id: null,
    visit_date: format(new Date(), 'MM/dd/yyyy'),
    duration: '',
    rating: '',
    purpose: '',
    initial_impressions: '',
    interest_level: '',
    follow_up_required: '',
    materials_presented: [],
    discussion_topics: '',
    notes: '',
    next_action: '',
    next_action_date: '',
    status: '',
})

watch(selectedOffice, (newVal) => {
    if (newVal && typeof newVal === 'object') {
        isOfficeSelected.value = true
        form.office_id = newVal.id
    }
})

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search)
    const officeIdParam = urlParams.get('office_id')

    if (!officeIdParam) return

    const officeId = Number(officeIdParam)

    if (Number.isNaN(officeId)) return

    const matchedOffice = offices.find((office) => Number(office.id) === officeId)

    if (matchedOffice) {
        selectedOffice.value = matchedOffice
        isOfficeSelected.value = true
        form.office_id = matchedOffice.id
    }
})

const officeAddress = computed(() => {
    if (!selectedOffice.value) return ''

    const unit = selectedOffice.value.unit_number ? ` #${selectedOffice.value.unit_number}` : ''

    return `${selectedOffice.value.street_address || ''}${unit}, ${selectedOffice.value.city || ''}, ${selectedOffice.value.state || ''} ${selectedOffice.value.zip_code || ''}`.trim()
})

const onFormSubmit = () => {
    form.post(route('office-visit.store'), {
        onSuccess: () => {
            form.reset()
            form.visit_date = format(new Date(), 'MM/dd/yyyy')
            form.materials_presented = []
        },
        onError: (error) => {
            console.log(error)
        },
    })
}

const openDirections = () => {
    if (!selectedOffice.value?.latitude || !selectedOffice.value?.longitude) return

    const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${selectedOffice.value.latitude},${selectedOffice.value.longitude}`
    window.open(googleMapsUrl, '_blank')
}
</script>
