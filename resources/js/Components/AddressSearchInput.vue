<template>
    <div class="flex flex-col">
        <!-- CHANGED: connected label to a unique input id -->
        <label :for="inputId" class="text-sm">Street Address</label>

        <!-- CHANGED: added a template ref so Google Autocomplete binds to the actual input element -->
        <!-- CHANGED: switched to computedInputValue so the field stays in sync with both parent model and selected place -->
        <InputText
            :id="inputId"
            ref="streetAddressInput"
            fluid
            :disabled="disabled"
            :value="computedInputValue"
            @input="onInput"
        />

        <!-- CHANGED: left error message shell in place but made it conditional-ready -->
        <Message
            v-if="errorMessage"
            variant="simple"
            size="small"
            severity="error"
        >
            {{ errorMessage }}
        </Message>
    </div>
</template>

<script setup>
import { InputText, Message } from 'primevue'
import { computed, onMounted, onBeforeUnmount, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { loadGoogleMapsAPI } from '@/lib/loadGoogleMapsApi'

const emit = defineEmits(['address', 'update:modelValue'])

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    inputId: {
        type: String,
        default: 'street_address',
    },
    errorMessage: {
        type: String,
        default: '',
    },
})

/* CHANGED: added a real ref that points to the rendered input component */
const streetAddressInput = ref(null)

/* CHANGED: kept the extracted address payload in a local form object */
const form = useForm({
    street_address: '',
    city: '',
    state: '',
    zip_code: '',
    county: '',
    place_id: '',
    longitude: '',
    latitude: '',
})

/* CHANGED: stored Google autocomplete instance for cleanup/reuse */
let autocomplete = null
let placeChangedListener = null

/* CHANGED: added a safe computed value so the input reflects typed text or selected place text */
const computedInputValue = computed(() => {
    return props.modelValue ?? form.street_address ?? ''
})

/* CHANGED: added explicit input handler instead of inline emit for clarity */
const onInput = (event) => {
    emit('update:modelValue', event.target.value)
}

/* CHANGED: added helper to unwrap PrimeVue InputText to the actual native input element */
const getNativeInputElement = () => {
    const instance = streetAddressInput.value

    if (!instance) return null

    if (instance.$el instanceof HTMLInputElement) return instance.$el

    return instance.$el?.querySelector?.('input') ?? null
}

onMounted(async () => {
    try {
        /* CHANGED: replaced manual polling with the shared Google Maps loader */
        await loadGoogleMapsAPI()

        const input = getNativeInputElement()

        if (!input) {
            console.error('Street address input element was not found.')
            return
        }

        /* CHANGED: initialize Places Autocomplete only after shared loader resolves */
        autocomplete = new window.google.maps.places.Autocomplete(input, {
            types: ['address'],
            componentRestrictions: { country: 'us' },
            fields: ['address_components', 'geometry', 'place_id', 'formatted_address'],
        })

        /* CHANGED: store listener reference so it can be removed on unmount */
        placeChangedListener = autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace()

            if (!place) return

            /* CHANGED: prefer Google formatted address for the visible input/model value */
            const formattedAddress = place.formatted_address || input.value || ''
            form.street_address = formattedAddress
            emit('update:modelValue', formattedAddress)

            /* CHANGED: reset dependent fields before repopulating */
            form.city = ''
            form.state = ''
            form.zip_code = ''
            form.county = ''
            form.place_id = place.place_id || ''
            form.latitude = ''
            form.longitude = ''

            /* CHANGED: safely extract lat/lng when geometry exists */
            if (place.geometry?.location) {
                form.latitude = place.geometry.location.lat()
                form.longitude = place.geometry.location.lng()
            }

            /* CHANGED: populate parsed address fields from Google components */
            if (place.address_components?.length) {
                extractAddressComponents(place.address_components)
            }

            /* CHANGED: emit a plain payload object instead of the Inertia form proxy */
            emit('address', {
                street_address: form.street_address,
                city: form.city,
                state: form.state,
                zip_code: form.zip_code,
                county: form.county,
                place_id: form.place_id,
                longitude: form.longitude,
                latitude: form.latitude,
            })
        })
    } catch (error) {
        console.error('Error loading Google Maps API:', error)
    }
})

onBeforeUnmount(() => {
    /* CHANGED: clean up Google Maps event listener to avoid duplicate bindings */
    if (placeChangedListener && window.google?.maps?.event) {
        window.google.maps.event.removeListener(placeChangedListener)
    }

    autocomplete = null
    placeChangedListener = null
})

const extractAddressComponents = (components) => {
    /* CHANGED: defined explicit component mappings for address parsing */
    let streetNumber = ''
    let route = ''

    form.city = ''
    form.state = ''
    form.zip_code = ''
    form.county = ''

    components.forEach((component) => {
        const types = component.types || []

        if (types.includes('street_number')) {
            streetNumber = component.short_name || ''
        }

        if (types.includes('route')) {
            route = component.long_name || ''
        }

        if (types.includes('locality')) {
            form.city = component.long_name || ''
        }

        if (types.includes('administrative_area_level_1')) {
            form.state = component.short_name || ''
        }

        if (types.includes('postal_code')) {
            form.zip_code = component.short_name || ''
        }

        if (types.includes('administrative_area_level_2')) {
            form.county = component.long_name || ''
        }
    })

    /* CHANGED: build street address from parsed street number + route */
    const parsedStreetAddress = [streetNumber, route].filter(Boolean).join(' ').trim()

    if (parsedStreetAddress) {
        form.street_address = parsedStreetAddress
        emit('update:modelValue', parsedStreetAddress)
    }
}
</script>
