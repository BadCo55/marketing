<template>
    <div class="flex flex-col">
        <label for="" class="text-sm">Street Address</label>
        <!-- :class="{ '!border-red-500': form.errors.street_address }" -->
        <InputText fluid id="street_address" :disabled="disabled" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"  />
        <Message variant="simple" size="small" severity="error" >{{ }}</Message>
    </div>
</template>

<script setup>
import { InputText, Message } from 'primevue';
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
const emit = defineEmits(['address', 'update:modelValue']);
const props = defineProps({
    modelValue: String,
    disabled: Boolean,
})
const form = useForm({
    street_address: '',
    city: '',
    state: '',
    zip_code: '',
    county: '',
    place_id: '',
    longitude: '',
    latitude: '',
});
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

                emit('address', form);

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
