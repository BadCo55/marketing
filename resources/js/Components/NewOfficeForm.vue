<template>
    <form @submit.prevent="submitForm">
        <div class="flex flex-col gap-3">
            <div class=mt-1>
                <Message severity="error" v-if="form.errors.latitude || form.errors.longitude">
                    <p class="mb-2" v-if="form.errors.latitude === 'The latitude has already been taken.' || form.errors.longitude === 'The longitude has already been taken.'">An office with this address already exists!</p>
                    <p v-else class="mb-2">Please use the address autocomplete feature by entering an address in the street address field.</p>
                    <ul class="list-disc ms-5">
                        <li>{{ form.errors.latitude }}</li>
                        <li>{{ form.errors.longitude }}</li>
                    </ul>
                </Message>
            </div>
            <div class="grid gird-cols-1 lg:grid-cols-2 gap-3">
                <div>
                    <label for="parent_company" class="text-sm">Parent Company</label>
                    <InputText :class="{ '!border-red-500': form.errors.parent_company }" fluid id="parent_company" placeholder="e.g. Re/Max, Century 21, etc." v-model="form.parent_company" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.parent_company">{{ form.errors.parent_company }}</Message>
                </div>
                <div>
                    <label for="office_name" class="text-sm">Office Name</label>
                    <InputText :class="{ '!border-red-500': form.errors.office_name }" fluid id="office_name" v-model="form.office_name" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.office_name">{{ form.errors.office_name }}</Message>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label for="office_phone" class="text-sm">Office Phone #</label>
                    <InputMask :class="{ '!border-red-500': form.errors.office_phone }" fluid id="office_phone" mask="999-999-9999" placeholder="123-456-7890" v-model="form.office_phone" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.office_phone">{{ form.errors.office_phone }}</Message>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                <div>
                    <label for="" class="text-sm">Street Address</label>
                    <InputText :class="{ '!border-red-500': form.errors.street_address }" fluid id="street_address" v-model="form.street_address" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.street_address">{{ form.errors.street_address }}</Message>
                </div>
                <div>
                    <label for="" class="text-sm">Unit #</label>
                    <InputText :class="{ '!border-red-500': form.errors.unit_number }" fluid id="unit_number" v-model="form.unit_number" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.unit_number">{{ form.errors.unit_number }}</Message>
                </div>
                <div>
                    <label for="" class="text-sm">City</label>
                    <InputText :class="{ '!border-red-500': form.errors.city }" fluid id="city" v-model="form.city" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.city">{{ form.errors.city }}</Message>
                </div>
                <div>
                    <label for="" class="text-sm">State</label>
                    <InputText :class="{ '!border-red-500': form.errors.state }" fluid id="state" v-model="form.state" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.state">{{ form.errors.state }}</Message>
                </div>
                <div>
                    <label for="" class="text-sm">County</label>
                    <InputText :class="{ '!border-red-500': form.errors.county }" fluid id="county" v-model="form.county" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.county">{{ form.errors.county }}</Message>
                </div>
                <div>
                    <label for="" class="text-sm">Zip Code</label>
                    <InputText :class="{ '!border-red-500': form.errors.zip_code }" fluid id="zip_code" v-model="form.zip_code" />
                    <Message variant="simple" size="small" severity="error" v-if="form.errors.zip_code">{{ form.errors.zip_code }}</Message>
                </div>
            </div>
            <Button label="Save Office" type="submit" class="mt-4" />
        </div>
    </form>
</template>

<script setup>
import { InputText, Button, InputMask, Message } from 'primevue';
import { onMounted, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';


const emit = defineEmits(['updatedOffices']);

const form = useForm({
    parent_company: '',
    office_name: '',
    office_phone: '',
    street_address: '',
    unit_number: '',
    city: '',
    state: '',
    county: '',
    zip_code: '',
    latitude: '',
    longitude: '',
    place_id: '',
});

const submitForm = () => {
    form.post(route('office.store', form), 
    {
        // preserveState: true,
        only: ['offices', 'flash'],
        onSuccess: (response) => {
            const newOffices = response.props.offices;
            emit('updatedOffices', newOffices);
        }, 
        onError: (error) => {
            // console.log(error);
        }
    });
}

onMounted(() => {
    const autocompleteInput = ref(null);
    let autocomplete;

    // Check if Google Maps API is loaded
    const waitForGoogleMaps = new Promise((resolve, reject) => {
        const checkGoogleMaps = () => {
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                resolve();
            } else {
                setTimeout(checkGoogleMaps, 100); // Retry after 100ms
            }
        };
        checkGoogleMaps();
    });

    waitForGoogleMaps
        .then(() => {
            const input = document.getElementById('street_address');
            if (!input) {
                console.error('Input element is not rendered in the DOM.');
                return;
            }

            autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['address'],
                componentRestrictions: { country: 'us' },
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();

                // Extract lat, lng, and place_id
                if (place.geometry) {
                    form.latitude = place.geometry.location.lat();
                    form.longitude = place.geometry.location.lng();
                }
                form.place_id = place.place_id;

                // Extract address components
                if (place.address_components) {
                    extractAddressComponents(place.address_components);
                }
            });
        })
        .catch((error) => {
            console.error('Error loading Google Maps API:', error);
        });
});

const extractAddressComponents = (components) => {
    const componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name', // City
        administrative_area_level_1: 'short_name', // State
        postal_code: 'short_name', // ZIP Code
        administrative_area_level_2: 'long_name',
    };

    form.street_address = '';
    form.city = '';
    form.state = '';
    form.zip_code = '';
    form.county = '';
    
    components.forEach((component) => {
        const addressType = component.types[0];
        if (componentForm[addressType]) {
            const value = component[componentForm[addressType]];
            switch (addressType) {
                case 'street_number':
                    form.street_address = value + ' ' + form.street_address;
                    break;
                case 'route':
                    form.street_address += value;
                    break;
                case 'locality':
                    form.city = value;
                    break;
                case 'administrative_area_level_1':
                    form.state = value;
                    break;
                case 'postal_code':
                    form.zip_code = value;
                    break;
                case 'administrative_area_level_2':
                    form.county = value;
                    break;
            }
        }
    });

}


</script>

<style>
    .pac-container {
        z-index: 9999;
    }
</style>