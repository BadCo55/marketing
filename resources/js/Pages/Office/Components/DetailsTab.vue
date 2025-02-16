<template>
    <div class="bg-surface-100 border dark:border-surface-900 dark:bg-surface-800 rounded p-5 mb-5 flex justify-between items-center">
        <div>
            <p><span class="font-bold">Google Place ID:</span> {{ form.place_id }}</p>
            <p><span class="font-bold">Latitude:</span> {{ form.latitude }}</p>
            <p><span class="font-bold">Longitude:</span> {{ form.longitude }}</p>
        </div>
        <div>
            <Button label="View on Google" @click="openGoogleMaps"></Button>
        </div>
    </div>
    <form @submit.prevent="onFormSubmit">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="flex flex-col">
                <label>Parent Company</label>
                <InputText v-model="form.parent_company" />
                <Message v-if="form.errors.parent_company" size="small" severity="error" variant="simple">{{ form.errors.parent_company }}</Message>
            </div>
            <div class="flex flex-col">
                <label>Office Name</label>
                <InputText v-model="form.office_name" />
                <Message v-if="form.errors.office_name" size="small" severity="error" variant="simple">{{ form.errors.office_name }}</Message>
            </div>
            <!-- <div class="flex flex-col">
                <label>Street Address</label>
                <InputText v-model="form.street_address" :disabled="!isDetailsEditable" />
            </div> -->
            <div class="flex flex-col">
                <AddressSearchInput @address="onNewAddress" :disabled="!isDetailsEditable" v-model="form.street_address" />
                <Message v-if="form.errors.street_address" size="small" severity="error" variant="simple">{{ form.errors.street_address }}</Message>
            </div>
            <div class="flex flex-col">
                <label>Unit #</label>
                <InputText v-model="form.unit_number" disabled />
                <Message v-if="form.errors.unit_number" size="small" severity="error" variant="simple">{{ form.errors.unit_number }}</Message>
            </div>
            <div class="flex flex-col">
                <label>City</label>
                <InputText v-model="form.city" disabled />
                <Message v-if="form.errors.city" size="small" severity="error" variant="simple">{{ form.errors.city }}</Message>
            </div>
            <div class="flex flex-col">
                <label>State</label>
                <InputText v-model="form.state" disabled />
                <Message v-if="form.errors.state" size="small" severity="error" variant="simple">{{ form.errors.state }}</Message>

            </div>
            <div class="flex flex-col">
                <label>Zip Code</label>
                <InputText v-model="form.zip_code" disabled />
                <Message v-if="form.errors.zip_code" size="small" severity="error" variant="simple">{{ form.errors.zip_code }}</Message>
            </div>
        </div>
        <div class="flex justify-end mt-5 gap-3">
            <Button label="Edit" v-if="!isDetailsEditable" @click="onToggleDetailsVisible" />
            <Button label="Cancel" severity="secondary" v-if="isDetailsEditable" @click="onCancelDetailsEdit" />
            <Button label="Save" v-if="isDetailsEditable" type="submit" />
        </div>
    </form>
</template>
<script setup>
import { useForm } from '@inertiajs/vue3';
import { InputText, Button, Message } from 'primevue';
import { ref, watch } from 'vue';
import AddressSearchInput from '@/Components/AddressSearchInput.vue';
const props = defineProps({
    office: Object,
});
const emit = defineEmits(['office']);
const office = ref(props.office);
const form = useForm({
    parent_company: office.value.parent_company,
    office_name: office.value.office_name,
    street_address: office.value.street_address,
    unit_number: office.value.unit_number,
    city: office.value.city,
    state: office.value.state,
    zip_code: office.value.zip_code,
    place_id: office.value.place_id,
    longitude: office.value.longitude,
    latitude: office.value.latitude,
});
watch(() => form.parent_company, (newValue, oldValue) => {
    if (newValue != oldValue) {
        isDetailsEditable.value = true;
    }
})
const onFormSubmit = () => {
    form.put(route('office.update', { office: office.value, request_type: 'update_details' }), 
    {
        preserveState: true, 
        preserveScroll: true,
        only: ['flash', 'office'],
        onSuccess: (response) => {
            office.value = response.props.office;

            // Update form fields manually
            form.parent_company = office.value.parent_company;
            form.office_name = office.value.office_name;
            form.street_address = office.value.street_address;
            form.unit_number = office.value.unit_number;
            form.city = office.value.city;
            form.state = office.value.state;
            form.zip_code = office.value.zip_code;
            form.place_id = office.value.place_id;
            form.longitude = office.value.longitude;
            form.latitude = office.value.latitude;

            isDetailsEditable.value = false;
            emit('office', office.value);
        },
        onError: (error) => {
            console.log(error);
        }
    })
}
// State management
const isDetailsEditable = ref(false);
const onToggleDetailsVisible = () => {
    isDetailsEditable.value = !isDetailsEditable.value;
}
const onCancelDetailsEdit = () => {
    form.reset();
    onToggleDetailsVisible();
}
const onNewAddress = (address) => {
    form.street_address = address.street_address;
    form.city = address.city;
    form.state = address.state;
    form.zip_code = address.zip_code;
    form.place_id = address.place_id;
    form.longitude = address.longitude;
    form.latitude = address.latitude;
}
const openGoogleMaps = () => {
    if (form.latitude && form.longitude) {
        const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${form.latitude},${form.longitude}`;
        window.open(googleMapsUrl, '_blank');
    } else {
        alert('Latitude and Longitude are required to view on Google Maps.');
    }
}
</script>