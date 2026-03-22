<template>
    <div class="mb-5">
        <h3 class="text-xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">
            Location Details
        </h3>
        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
            Review the office location, Google mapping data, and update the address when needed.
        </p>
    </div>

    <div class="mb-6 rounded-2xl border border-surface-200 bg-surface-50 p-5 dark:border-surface-700 dark:bg-surface-800">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-3">
                <div class="rounded-xl border border-surface-200 bg-white p-3 dark:border-surface-700 dark:bg-surface-900">
                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                        Google Place ID
                    </p>
                    <p class="mt-1 break-all text-sm text-surface-800 dark:text-surface-200">
                        {{ form.place_id || 'Not available' }}
                    </p>
                </div>

                <div class="rounded-xl border border-surface-200 bg-white p-3 dark:border-surface-700 dark:bg-surface-900">
                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                        Latitude
                    </p>
                    <p class="mt-1 text-sm text-surface-800 dark:text-surface-200">
                        {{ form.latitude || 'Not available' }}
                    </p>
                </div>

                <div class="rounded-xl border border-surface-200 bg-white p-3 dark:border-surface-700 dark:bg-surface-900">
                    <p class="text-xs font-medium uppercase tracking-wide text-surface-500 dark:text-surface-400">
                        Longitude
                    </p>
                    <p class="mt-1 text-sm text-surface-800 dark:text-surface-200">
                        {{ form.longitude || 'Not available' }}
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                <Button
                    label="View on Google"
                    icon="pi pi-map-marker"
                    outlined
                    :disabled="!form.latitude || !form.longitude"
                    @click="openGoogleMaps"
                />
            </div>
        </div>
    </div>

    <form @submit.prevent="onFormSubmit" class="space-y-6">
        <div class="rounded-2xl border border-surface-200 bg-surface-0 p-5 shadow-sm dark:border-surface-700 dark:bg-surface-900">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="flex flex-col">
                    <label
                        for="parent_company"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.parent_company }"
                    >
                        Parent Company
                    </label>
                    <InputText
                        id="parent_company"
                        v-model="form.parent_company"
                        fluid
                        :disabled="!isDetailsEditable"
                        placeholder="Enter parent company"
                        :invalid="!!form.errors.parent_company"
                    />
                    <Message
                        v-if="form.errors.parent_company"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.parent_company }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        for="office_name"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.office_name }"
                    >
                        Office Name
                    </label>
                    <InputText
                        id="office_name"
                        v-model="form.office_name"
                        fluid
                        :disabled="!isDetailsEditable"
                        placeholder="Enter office name"
                        :invalid="!!form.errors.office_name"
                    />
                    <Message
                        v-if="form.errors.office_name"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.office_name }}
                    </Message>
                </div>

                <div class="flex flex-col lg:col-span-2">
                    <label
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.street_address }"
                    >
                        Street Address
                    </label>

                    <AddressSearchInput
                        v-model="form.street_address"
                        :disabled="!isDetailsEditable"
                        @address="onNewAddress"
                    />

                    <Message
                        v-if="form.errors.street_address"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.street_address }}
                    </Message>

                    <Message
                        v-if="isDetailsEditable"
                        size="small"
                        severity="secondary"
                        variant="simple"
                        class="mt-2 text-surface-500 dark:text-surface-400"
                    >
                        Search and select an address to update the Google Place ID and coordinates.
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        for="unit_number"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.unit_number }"
                    >
                        Unit #
                    </label>
                    <InputText
                        id="unit_number"
                        v-model="form.unit_number"
                        fluid
                        :disabled="!isDetailsEditable"
                        placeholder=""
                        :invalid="!!form.errors.unit_number"
                    />
                    <Message
                        v-if="form.errors.unit_number"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.unit_number }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        for="city"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.city }"
                    >
                        City
                    </label>
                    <InputText
                        id="city"
                        v-model="form.city"
                        fluid
                        disabled
                        placeholder="Auto-filled"
                        :invalid="!!form.errors.city"
                    />
                    <Message
                        v-if="form.errors.city"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.city }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        for="state"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.state }"
                    >
                        State
                    </label>
                    <InputText
                        id="state"
                        v-model="form.state"
                        fluid
                        disabled
                        placeholder="Auto-filled"
                        :invalid="!!form.errors.state"
                    />
                    <Message
                        v-if="form.errors.state"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.state }}
                    </Message>
                </div>

                <div class="flex flex-col">
                    <label
                        for="zip_code"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': form.errors.zip_code }"
                    >
                        Zip Code
                    </label>
                    <InputText
                        id="zip_code"
                        v-model="form.zip_code"
                        fluid
                        disabled
                        placeholder="Auto-filled"
                        :invalid="!!form.errors.zip_code"
                    />
                    <Message
                        v-if="form.errors.zip_code"
                        size="small"
                        severity="error"
                        variant="simple"
                        class="mt-2"
                    >
                        {{ form.errors.zip_code }}
                    </Message>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-surface-200 pt-5 dark:border-surface-700 sm:flex-row sm:justify-end">
            <Button
                v-if="!isDetailsEditable"
                label="Edit Details"
                icon="pi pi-pencil"
                type="button"
                @click="onToggleDetailsVisible"
            />

            <template v-else>
                <Button
                    label="Cancel"
                    severity="secondary"
                    outlined
                    type="button"
                    @click="onCancelDetailsEdit"
                />
                <Button
                    label="Save Changes"
                    icon="pi pi-check-circle"
                    type="submit"
                    :loading="form.processing"
                />
            </template>
        </div>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { InputText, Button, Message } from 'primevue';
import { ref, watch, computed } from 'vue';
import AddressSearchInput from '@/Components/AddressSearchInput.vue';

const props = defineProps({
    office: Object,
});

const emit = defineEmits(['office']);

const office = ref(props.office);

const getInitialValues = () => ({
    parent_company: office.value.parent_company || '',
    office_name: office.value.office_name || '',
    street_address: office.value.street_address || '',
    unit_number: office.value.unit_number || '',
    city: office.value.city || '',
    state: office.value.state || '',
    zip_code: office.value.zip_code || '',
    place_id: office.value.place_id || '',
    longitude: office.value.longitude || '',
    latitude: office.value.latitude || '',
});

const form = useForm(getInitialValues());

const hasChanges = computed(() => {
    const initial = getInitialValues();

    return (
        form.parent_company !== initial.parent_company ||
        form.office_name !== initial.office_name ||
        form.street_address !== initial.street_address ||
        form.unit_number !== initial.unit_number ||
        form.city !== initial.city ||
        form.state !== initial.state ||
        form.zip_code !== initial.zip_code ||
        form.place_id !== initial.place_id ||
        form.longitude !== initial.longitude ||
        form.latitude !== initial.latitude
    );
});

watch(hasChanges, (changed) => {
    if (changed) {
        isDetailsEditable.value = true;
    }
});

const onFormSubmit = () => {
    form
        .transform((data) => ({
            ...data,
            request_type: 'update_details',
        }))
        .put(route('office.update', { office: office.value.id }), {
            preserveState: true,
            preserveScroll: true,
            only: ['flash', 'office'],
            onSuccess: (response) => {
                office.value = response.props.office;

                const initial = getInitialValues();
                form.parent_company = initial.parent_company;
                form.office_name = initial.office_name;
                form.street_address = initial.street_address;
                form.unit_number = initial.unit_number;
                form.city = initial.city;
                form.state = initial.state;
                form.zip_code = initial.zip_code;
                form.place_id = initial.place_id;
                form.longitude = initial.longitude;
                form.latitude = initial.latitude;

                form.clearErrors();
                isDetailsEditable.value = false;
                emit('office', office.value);
            },
            onError: (error) => {
                console.log(error);
            },
        });
};

const isDetailsEditable = ref(false);

const onToggleDetailsVisible = () => {
    isDetailsEditable.value = true;
};

const onCancelDetailsEdit = () => {
    const initial = getInitialValues();

    form.parent_company = initial.parent_company;
    form.office_name = initial.office_name;
    form.street_address = initial.street_address;
    form.unit_number = initial.unit_number;
    form.city = initial.city;
    form.state = initial.state;
    form.zip_code = initial.zip_code;
    form.place_id = initial.place_id;
    form.longitude = initial.longitude;
    form.latitude = initial.latitude;

    form.clearErrors();
    isDetailsEditable.value = false;
};

const onNewAddress = (address) => {
    form.street_address = address.street_address || '';
    form.city = address.city || '';
    form.state = address.state || '';
    form.zip_code = address.zip_code || '';
    form.place_id = address.place_id || '';
    form.longitude = address.longitude || '';
    form.latitude = address.latitude || '';
};

const openGoogleMaps = () => {
    if (form.latitude && form.longitude) {
        const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${form.latitude},${form.longitude}`;
        window.open(googleMapsUrl, '_blank');
    } else {
        alert('Latitude and longitude are required to view this location on Google Maps.');
    }
};
</script>
