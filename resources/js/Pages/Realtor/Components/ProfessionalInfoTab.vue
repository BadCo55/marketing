<template>
    <form @submit.prevent="onSubmit" class="space-y-8">
        <!-- CHANGED: Added dark text support for section heading -->
        <div class="flex flex-col gap-1">
            <h3 class="text-xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">
                Professional Information
            </h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">
                Manage brokerage affiliation, licensing, experience, service areas, and preferred partners.
            </p>
        </div>

        <!-- CHANGED: Added dark border support for section divider -->
        <section class="border-b border-surface-200 pb-8 dark:border-surface-800">
            <div class="mb-4">
                <!-- CHANGED: Added dark text support for subsection heading -->
                <h4 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                    Company Affiliation
                </h4>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Link this realtor to an office or change their current company relationship.
                </p>
            </div>

            <template v-if="!isCompanySearchVisible">
                <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
                    <div class="xl:col-span-9">
                        <!-- CHANGED: Added dark surface and border styles for affiliation card -->
                        <div class="flex h-full flex-col gap-3 rounded-2xl border border-surface-200 bg-surface-50 p-4 md:flex-row md:items-end md:justify-between dark:border-surface-700 dark:bg-surface-800">
                            <div class="flex-1">
                                <!-- CHANGED: Added dark text support for label -->
                                <label class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                                    Affiliated Company
                                </label>
                                <InputText
                                    fluid
                                    v-model="form.parent_company"
                                    disabled
                                    placeholder="No affiliated company"
                                />
                            </div>

                            <div v-if="realtor.office?.id" class="shrink-0">
                                <Link :href="route('office.show', realtor.office.id)">
                                    <Button
                                        icon="pi pi-search"
                                        label="View Office"
                                        outlined
                                    />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="xl:col-span-3">
                        <div class="flex h-full flex-col gap-3">
                            <Button
                                fluid
                                label="Change Company"
                                icon="pi pi-pencil"
                                type="button"
                                @click="onToggleCompanySearch"
                            />
                            <Button
                                fluid
                                label="Remove Affiliation"
                                icon="pi pi-times-circle"
                                severity="warn"
                                outlined
                                type="button"
                                @click="onRemoveAffiliation"
                            />
                        </div>
                    </div>
                </div>
            </template>

            <template v-else>
                <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">
                    <div class="xl:col-span-9">
                        <!-- CHANGED: Added dark text support for label -->
                        <label class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                            Affiliated Company
                        </label>
                        <OfficeAutocompleteInput
                            label=""
                            v-model="officeForm.office"
                        />
                        <!-- CHANGED: Added dark text support to helper message via utility classes -->
                        <Message
                            size="small"
                            variant="simple"
                            severity="secondary"
                            class="mt-2 !text-surface-500 dark:!text-surface-400"
                        >
                            Search for the office this realtor is affiliated with.
                        </Message>
                    </div>

                    <div class="xl:col-span-3">
                        <div class="flex h-full flex-col gap-3 xl:pt-7">
                            <Button
                                v-if="officeForm.office"
                                fluid
                                label="Save Affiliation"
                                icon="pi pi-check-circle"
                                type="button"
                                @click="onSetAffiliation"
                            />
                            <Button
                                fluid
                                label="Cancel"
                                icon="pi pi-times"
                                severity="secondary"
                                outlined
                                type="button"
                                @click="onToggleCompanySearch"
                            />
                        </div>
                    </div>
                </div>
            </template>
        </section>

        <!-- CHANGED: Added dark border support for section divider -->
        <section class="border-b border-surface-200 pb-8 dark:border-surface-800">
            <div class="mb-4">
                <!-- CHANGED: Added dark text support for subsection heading -->
                <h4 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                    Credentials & Experience
                </h4>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Record title, licensing, and experience details.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <!-- CHANGED: Added dark text support for label -->
                    <label for="title" class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                        Title
                    </label>
                    <InputText
                        id="title"
                        fluid
                        v-model="form.title"
                        placeholder="e.g. Realtor, Broker Associate"
                        :invalid="!!form.errors.title"
                    />
                    <Message
                        v-if="form.errors.title"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ form.errors.title }}
                    </Message>
                </div>

                <div class="lg:col-span-2">
                    <!-- CHANGED: Added dark text support for label -->
                    <label for="license_number" class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                        License #
                    </label>
                    <InputText
                        id="license_number"
                        fluid
                        v-model="form.license_number"
                        placeholder="Enter license number"
                        :invalid="!!form.errors.license_number"
                    />
                    <Message
                        v-if="form.errors.license_number"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ form.errors.license_number }}
                    </Message>
                </div>

                <div class="lg:col-span-2">
                    <!-- CHANGED: Added dark text support for label -->
                    <label for="years_in_business" class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                        Years In Business
                    </label>
                    <InputNumber
                        id="years_in_business"
                        fluid
                        v-model="form.years_in_business"
                        placeholder="Enter years"
                        :invalid="!!form.errors.years_in_business"
                    />
                    <Message
                        v-if="form.errors.years_in_business"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ form.errors.years_in_business }}
                    </Message>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div>
                <!-- CHANGED: Added dark text support for subsection heading -->
                <h4 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                    Markets & Preferences
                </h4>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Track specialties, service regions, and inspection preferences.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5">
                <div>
                    <!-- CHANGED: Added dark text support for label -->
                    <label class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                        Specializations
                    </label>
                    <MultiSelect
                        fluid
                        :options="specializationOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select specializations..."
                        v-model="form.specializations"
                        :invalid="!!form.errors.specializations"
                        display="chip"
                    />
                    <Message
                        v-if="form.errors.specializations"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ form.errors.specializations }}
                    </Message>
                </div>

                <div>
                    <!-- CHANGED: Added dark text support for label -->
                    <label class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                        Primary Region(s)
                    </label>
                    <MultiSelect
                        fluid
                        :options="regionOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select region(s)..."
                        v-model="form.primary_region"
                        :invalid="!!form.errors.primary_region"
                        display="chip"
                    />
                    <Message
                        v-if="form.errors.primary_region"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ form.errors.primary_region }}
                    </Message>
                </div>

                <div>
                    <!-- CHANGED: Added dark text support for label -->
                    <label class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                        Preferred Inspector(s)
                    </label>
                    <MultiSelect
                        fluid
                        v-model="form.preferred_inspector"
                        :options="preferredInspectorOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select preferred inspector(s)..."
                        :invalid="!!form.errors.preferred_inspector"
                        display="chip"
                    />
                    <Message
                        v-if="form.errors.preferred_inspector"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ form.errors.preferred_inspector }}
                    </Message>
                </div>

                <div class="max-w-2xl">
                    <!-- CHANGED: Added dark text support for label -->
                    <label class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100">
                        Preferred Inspection Company
                    </label>
                    <InputText
                        fluid
                        v-model="form.preferred_inspection_company"
                        placeholder="Enter another company they commonly use..."
                        :invalid="!!form.errors.preferred_inspection_company"
                    />
                    <!-- CHANGED: Added dark text support to helper message -->
                    <Message
                        size="small"
                        variant="simple"
                        severity="secondary"
                        class="mt-2 !text-surface-500 dark:!text-surface-400"
                    >
                        Enter a competing company they use instead of or alongside your company.
                    </Message>
                    <Message
                        v-if="form.errors.preferred_inspection_company"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ form.errors.preferred_inspection_company }}
                    </Message>
                </div>
            </div>
        </section>

        <!-- CHANGED: Added dark border support for footer divider -->
        <div class="flex items-center justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
            <Button
                label="Save Changes"
                type="submit"
                icon="pi pi-check-circle"
                :loading="form.processing"
            />
        </div>
    </form>
</template>

<script setup>
import { regionOptions, specializationOptions, preferredInspectorOptions } from '@/constants';
import OfficeAutocompleteInput from '@/Components/OfficeAutocompleteInput.vue';
import { ref, onMounted, watch } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { MultiSelect, InputText, Button, InputNumber, Message } from 'primevue';

const props = defineProps({
    realtor: Object,
});

const emit = defineEmits(['realtor']);

const realtor = ref(props.realtor);

// State Management
const isCompanySearchVisible = ref(false);

onMounted(() => {
    setCompanySearchState();
});

const setCompanySearchState = () => {
    isCompanySearchVisible.value = !realtor.value?.office_id;
};

const onToggleCompanySearch = () => {
    isCompanySearchVisible.value = !isCompanySearchVisible.value;
    officeForm.office = '';
};

const form = useForm({
    specializations: realtor.value?.specializations || [],
    license_number: realtor.value?.license_number || '',
    years_in_business: realtor.value?.years_in_business ?? null,
    parent_company: realtor.value?.office?.formatted_company_name || '',
    title: realtor.value?.title || '',
    primary_region: realtor.value?.primary_region || [],
    preferred_inspection_company: realtor.value?.preferred_inspection_company || '',
    preferred_inspector: realtor.value?.preferred_inspector || [],
});

const officeForm = useForm({
    office: realtor.value?.office_id || '',
    request_type: 'set_affiliation',
});

watch(
    () => props.realtor,
    (newRealtor) => {
        realtor.value = newRealtor;

        form.specializations = newRealtor?.specializations || [];
        form.license_number = newRealtor?.license_number || '';
        form.years_in_business = newRealtor?.years_in_business ?? null;
        form.parent_company = newRealtor?.office?.formatted_company_name || '';
        form.title = newRealtor?.title || '';
        form.primary_region = newRealtor?.primary_region || [];
        form.preferred_inspection_company = newRealtor?.preferred_inspection_company || '';
        form.preferred_inspector = newRealtor?.preferred_inspector || [];

        officeForm.office = newRealtor?.office_id || '';
        setCompanySearchState();
    },
    { deep: true }
);

const onSubmit = () => {
    form
        .transform((data) => ({
            ...data,
            request_type: 'update_professional_info',
        }))
        .put(route('realtor.update', realtor.value.id), {
            preserveScroll: true,
            onSuccess: (response) => {
                if (response?.props?.realtor) {
                    realtor.value = response.props.realtor;
                    form.parent_company = response.props.realtor?.office?.formatted_company_name || '';
                    emit('realtor', response.props.realtor);
                }
            },
            onError: (error) => {
                console.error('Error updating professional info:', error);
            },
        });
};

const onSetAffiliation = () => {
    officeForm.put(route('realtor.update', realtor.value.id), {
        only: ['flash', 'realtor'],
        preserveScroll: true,
        onSuccess: (response) => {
            realtor.value = response.props.realtor;
            isCompanySearchVisible.value = false;
            form.parent_company = response.props.realtor?.office?.formatted_company_name || '';
            officeForm.office = response.props.realtor?.office_id || '';
            emit('realtor', response.props.realtor);
        },
        onError: (error) => {
            console.error(error);
        },
    });
};

const onRemoveAffiliation = () => {
    router.put(
        route('realtor.update', realtor.value.id),
        {
            request_type: 'remove_affiliation',
        },
        {
            only: ['flash', 'realtor'],
            preserveScroll: true,
            onSuccess: (response) => {
                realtor.value = response.props.realtor;
                isCompanySearchVisible.value = true;
                officeForm.office = '';
                form.parent_company = '';
                emit('realtor', response.props.realtor);
            },
            onError: (error) => {
                console.error(error);
            },
        }
    );
};
</script>
