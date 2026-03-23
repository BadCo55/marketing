<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <!-- CHANGED: Kept the top error block and left it tied to latitude/longitude validation -->
        <div class="mt-1" v-if="form.errors.latitude || form.errors.longitude">
            <Message severity="error" :closable="false">
                <p
                    class="mb-2 font-medium text-surface-900 dark:text-surface-0"
                    v-if="form.errors.latitude === 'The latitude has already been taken.' || form.errors.longitude === 'The longitude has already been taken.'"
                >
                    An office with this address already exists.
                </p>
                <p v-else class="mb-2 font-medium text-surface-900 dark:text-surface-0">
                    Please use the address autocomplete by selecting a suggested address from the street address field.
                </p>

                <ul class="ms-5 list-disc text-sm text-surface-700 dark:text-surface-200">
                    <li v-if="form.errors.latitude">{{ form.errors.latitude }}</li>
                    <li v-if="form.errors.longitude">{{ form.errors.longitude }}</li>
                </ul>
            </Message>
        </div>

        <!-- CHANGED: Kept office information card layout unchanged -->
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 p-5 shadow-sm">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">Office Information</h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Enter the main office details for this brokerage location.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <label for="parent_company" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Parent Company</label>
                    <InputText
                        id="parent_company"
                        v-model="form.parent_company"
                        fluid
                        placeholder="e.g. RE/MAX, Century 21, Compass"
                        :class="{ '!border-red-500': form.errors.parent_company }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.parent_company"
                    >
                        {{ form.errors.parent_company }}
                    </Message>
                </div>

                <div>
                    <label for="office_name" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Office Name</label>
                    <InputText
                        id="office_name"
                        v-model="form.office_name"
                        fluid
                        placeholder="e.g. Coral Springs Office"
                        :class="{ '!border-red-500': form.errors.office_name }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.office_name"
                    >
                        {{ form.errors.office_name }}
                    </Message>
                </div>

                <div>
                    <label for="office_phone" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Office Phone #</label>
                    <InputMask
                        id="office_phone"
                        v-model="form.office_phone"
                        mask="999-999-9999"
                        fluid
                        placeholder="123-456-7890"
                        :class="{ '!border-red-500': form.errors.office_phone }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.office_phone"
                    >
                        {{ form.errors.office_phone }}
                    </Message>
                </div>

                <div>
                    <label for="office_email" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Office Email</label>
                    <InputText
                        id="office_email"
                        v-model="form.office_email"
                        fluid
                        placeholder="office@example.com"
                        :class="{ '!border-red-500': form.errors.office_email }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.office_email"
                    >
                        {{ form.errors.office_email }}
                    </Message>
                </div>

                <div class="lg:col-span-2">
                    <label for="website" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Website</label>
                    <InputText
                        id="website"
                        v-model="form.website"
                        fluid
                        placeholder="https://www.example.com"
                        :class="{ '!border-red-500': form.errors.website }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.website"
                    >
                        {{ form.errors.website }}
                    </Message>
                </div>
            </div>
        </div>

        <!-- CHANGED: Kept office address section but wired Street Address input to a ref for Google autocomplete -->
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 p-5 shadow-sm">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">Office Address</h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Start typing the street address and select the matching suggestion from Google.
                </p>
            </div>

            <div class="mb-5 rounded-xl border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-950/30 px-4 py-3 text-sm text-blue-900 dark:text-blue-200">
                Selecting a suggested address will auto-fill the city, state, county, ZIP code, latitude, longitude, and help prevent duplicates.
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <label for="street_address" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Street Address</label>
                    <InputText
                        id="street_address"
                        ref="streetAddressInput"
                        v-model="form.street_address"
                        fluid
                        placeholder="Start typing address..."
                        :class="{ '!border-red-500': form.errors.street_address }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.street_address"
                    >
                        {{ form.errors.street_address }}
                    </Message>
                </div>

                <div>
                    <label for="unit_number" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Unit #</label>
                    <InputText
                        id="unit_number"
                        v-model="form.unit_number"
                        fluid
                        placeholder="Optional"
                        :class="{ '!border-red-500': form.errors.unit_number }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.unit_number"
                    >
                        {{ form.errors.unit_number }}
                    </Message>
                </div>

                <div>
                    <label for="city" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">City</label>
                    <InputText
                        id="city"
                        v-model="form.city"
                        fluid
                        :class="{ '!border-red-500': form.errors.city }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.city"
                    >
                        {{ form.errors.city }}
                    </Message>
                </div>

                <div>
                    <label for="state" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">State</label>
                    <InputText
                        id="state"
                        v-model="form.state"
                        fluid
                        :class="{ '!border-red-500': form.errors.state }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.state"
                    >
                        {{ form.errors.state }}
                    </Message>
                </div>

                <div>
                    <label for="county" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">County</label>
                    <InputText
                        id="county"
                        v-model="form.county"
                        fluid
                        :class="{ '!border-red-500': form.errors.county }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.county"
                    >
                        {{ form.errors.county }}
                    </Message>
                </div>

                <div>
                    <label for="zip_code" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Zip Code</label>
                    <InputText
                        id="zip_code"
                        v-model="form.zip_code"
                        fluid
                        :class="{ '!border-red-500': form.errors.zip_code }"
                    />
                    <Message
                        variant="simple"
                        size="small"
                        severity="error"
                        class="mt-2"
                        v-if="form.errors.zip_code"
                    >
                        {{ form.errors.zip_code }}
                    </Message>
                </div>
            </div>
        </div>

        <!-- CHANGED: Kept additional details section unchanged -->
        <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 p-5 shadow-sm">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">Additional Details</h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Store internal notes that may help with future office visits or outreach.
                </p>
            </div>

            <div>
                <label for="notes" class="mb-2 block text-sm font-medium text-surface-700 dark:text-surface-200">Notes</label>
                <Textarea
                    id="notes"
                    v-model="form.notes"
                    fluid
                    rows="4"
                    autoResize
                    placeholder="Example: receptionist name, best visit time, parking instructions, prefers email, etc."
                    :class="{ '!border-red-500': form.errors.notes }"
                />
                <Message
                    variant="simple"
                    size="small"
                    severity="error"
                    class="mt-2"
                    v-if="form.errors.notes"
                >
                    {{ form.errors.notes }}
                </Message>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <Button
                label="Reset"
                type="button"
                severity="secondary"
                outlined
                @click="resetForm"
            />
            <Button
                label="Save Office"
                type="submit"
                icon="pi pi-check-circle"
                class="mt-0"
                :loading="form.processing"
            />
        </div>
    </form>
</template>

<script setup>
import { InputText, Button, InputMask, Message, Textarea } from 'primevue'
import { onMounted, onBeforeUnmount, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { loadGoogleMapsAPI } from '@/lib/loadGoogleMapsApi'

const emit = defineEmits(['updatedOffices'])

const form = useForm({
    parent_company: '',
    office_name: '',
    office_phone: '',
    office_email: '',
    website: '',
    street_address: '',
    unit_number: '',
    city: '',
    state: '',
    county: '',
    zip_code: '',
    latitude: '',
    longitude: '',
    place_id: '',
    notes: '',
})

/* CHANGED: Added a template ref so autocomplete attaches to the actual rendered input */
const streetAddressInput = ref(null)

/* CHANGED: Kept references for autocomplete instance and listener cleanup */
let autocomplete = null
let placeChangedListener = null

const submitForm = () => {
    form.post(route('office.store'), {
        only: ['offices', 'flash'],
        preserveScroll: true,
        onSuccess: (response) => {
            const newOffices = response.props.offices
            emit('updatedOffices', newOffices)
            form.reset()
            form.clearErrors()
        },
        onError: () => {
        }
    })
}

/* CHANGED: Added helper to safely resolve the real input element from PrimeVue InputText */
const getNativeInputElement = () => {
    const instance = streetAddressInput.value

    if (!instance) return null

    if (instance.$el instanceof HTMLInputElement) {
        return instance.$el
    }

    return instance.$el?.querySelector?.('input') ?? null
}

onMounted(async () => {
    try {
        /* CHANGED: Replaced the manual polling loop with the shared Google Maps loader */
        await loadGoogleMapsAPI()

        const input = getNativeInputElement()

        if (!input) {
            console.error('Street address input element is not rendered in the DOM.')
            return
        }

        /* CHANGED: Initialized Google Places Autocomplete using the shared loader result */
        autocomplete = new window.google.maps.places.Autocomplete(input, {
            types: ['address'],
            componentRestrictions: { country: 'us' },
            fields: ['address_components', 'geometry', 'place_id', 'formatted_address'],
        })

        /* CHANGED: Stored listener reference so it can be removed on unmount */
        placeChangedListener = autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace()

            if (!place) return

            /* CHANGED: Reset derived location fields before repopulating from selected place */
            form.latitude = ''
            form.longitude = ''
            form.place_id = place.place_id ?? ''
            form.street_address = ''
            form.city = ''
            form.state = ''
            form.county = ''
            form.zip_code = ''

            /* CHANGED: Captured geometry when present */
            if (place.geometry?.location) {
                form.latitude = place.geometry.location.lat()
                form.longitude = place.geometry.location.lng()
            }

            /* CHANGED: Populated address fields from Google address components */
            if (place.address_components?.length) {
                extractAddressComponents(place.address_components)
            }

            /* CHANGED: Fell back to formatted_address when street parsing does not produce a value */
            if (!form.street_address && place.formatted_address) {
                form.street_address = place.formatted_address
            }
        })
    } catch (error) {
        console.error('Error loading Google Maps API:', error)
    }
})

onBeforeUnmount(() => {
    /* CHANGED: Cleaned up the Google Maps event listener */
    if (placeChangedListener && window.google?.maps?.event) {
        window.google.maps.event.removeListener(placeChangedListener)
    }

    autocomplete = null
    placeChangedListener = null
})

const extractAddressComponents = (components) => {
    /* CHANGED: Reworked parsing so street number + route are combined more safely */
    let streetNumber = ''
    let route = ''

    form.street_address = ''
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

    /* CHANGED: Combined street number and route after the loop to avoid partial ordering issues */
    form.street_address = [streetNumber, route].filter(Boolean).join(' ').trim()
}

const resetForm = () => {
    form.reset()
    form.clearErrors()
}
</script>

<style>
/* CHANGED: Kept pac-container on top of dialogs and overlays */
.pac-container {
    z-index: 9999;
}
</style>
