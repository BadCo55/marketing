<template>
    <div class="space-y-4">
        <div
            class="flex flex-col gap-3 rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-700 dark:bg-surface-900 md:flex-row md:items-end md:justify-between"
        >
            <div>
                <h3 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                    Route Radius Planner
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Click the map to set the route center, adjust the radius, and select offices for the route.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="flex flex-col">
                    <label
                        for="radius-miles"
                        class="mb-2 text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        Radius (miles)
                    </label>
                    <InputNumber
                        id="radius-miles"
                        v-model="localRadiusMiles"
                        :min="1"
                        :max="100"
                        :step="1"
                        fluid
                    />
                </div>

                <div
                    class="rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800"
                >
                    <p class="text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                        In Radius
                    </p>
                    <p class="mt-1 text-lg font-semibold text-surface-900 dark:text-surface-0">
                        {{ officesInRadius.length }}
                    </p>
                </div>

                <div
                    class="rounded-xl border border-surface-200 bg-surface-50 px-4 py-3 dark:border-surface-700 dark:bg-surface-800"
                >
                    <p class="text-xs uppercase tracking-wide text-surface-500 dark:text-surface-400">
                        Selected
                    </p>
                    <p class="mt-1 text-lg font-semibold text-surface-900 dark:text-surface-0">
                        {{ selectedIdsArray.length }}
                    </p>
                </div>
            </div>
        </div>

        <div
            v-if="mapError"
            class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300"
        >
            {{ mapError }}
        </div>

        <div
            v-if="isLoadingMap"
            class="rounded-2xl border border-surface-200 bg-surface-50 px-4 py-3 text-sm text-surface-700 dark:border-surface-700 dark:bg-surface-800 dark:text-surface-300"
        >
            Loading map...
        </div>

        <div
            class="overflow-hidden rounded-2xl border border-surface-200 bg-surface-100 shadow-sm dark:border-surface-700 dark:bg-surface-800"
        >
            <div
                ref="mapElement"
                class="h-[28rem] w-full md:h-[34rem]"
            ></div>
        </div>

        <div
            class="flex flex-wrap items-center gap-4 rounded-2xl border border-surface-200 bg-surface-0 px-4 py-3 text-sm dark:border-surface-700 dark:bg-surface-900"
        >
            <div class="inline-flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                <span class="text-surface-700 dark:text-surface-300">Center</span>
            </div>

            <div class="inline-flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-red-500"></span>
                <span class="text-surface-700 dark:text-surface-300">Outside Radius</span>
            </div>

            <div class="inline-flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-yellow-500"></span>
                <span class="text-surface-700 dark:text-surface-300">Inside Radius</span>
            </div>

            <div class="inline-flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-green-500"></span>
                <span class="text-surface-700 dark:text-surface-300">Selected for Route</span>
            </div>
        </div>

        <div
            class="rounded-2xl border border-surface-200 bg-surface-0 p-4 shadow-sm dark:border-surface-700 dark:bg-surface-900"
        >
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h4 class="text-base font-semibold text-surface-900 dark:text-surface-0">
                        Offices in Radius
                    </h4>
                    <p class="text-sm text-surface-500 dark:text-surface-400">
                        Click a row or marker to add or remove an office from the route.
                    </p>
                </div>
            </div>

            <div
                v-if="officesInRadius.length"
                class="max-h-72 space-y-2 overflow-y-auto"
            >
                <button
                    v-for="office in officesInRadius"
                    :key="office.id"
                    type="button"
                    class="flex w-full items-start justify-between rounded-xl border px-4 py-3 text-left transition-colors"
                    :class="
                        selectedOfficeIdsSet.has(office.id)
                            ? 'border-green-300 bg-green-50 dark:border-green-700 dark:bg-green-950/30'
                            : 'border-surface-200 bg-surface-50 hover:bg-surface-100 dark:border-surface-700 dark:bg-surface-800 dark:hover:bg-surface-700'
                    "
                    @click="toggleOfficeSelection(office)"
                >
                    <div class="min-w-0">
                        <p class="font-medium text-surface-900 dark:text-surface-0">
                            {{ office.parent_company || 'Unknown Office' }}
                        </p>

                        <p
                            v-if="office.office_name"
                            class="text-sm text-surface-600 dark:text-surface-300"
                        >
                            {{ office.office_name }}
                        </p>

                        <p class="text-sm text-surface-500 dark:text-surface-400">
                            {{ formatOfficeAddress(office) }}
                        </p>
                    </div>

                    <div class="shrink-0 pl-3">
                        <span
                            class="inline-flex rounded-full px-3 py-1 text-xs font-medium"
                            :class="
                                selectedOfficeIdsSet.has(office.id)
                                    ? 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-300'
                                    : 'bg-surface-200 text-surface-700 dark:bg-surface-700 dark:text-surface-200'
                            "
                        >
                            {{ selectedOfficeIdsSet.has(office.id) ? 'Selected' : 'Add' }}
                        </span>
                    </div>
                </button>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800"
            >
                <p class="font-medium text-surface-700 dark:text-surface-200">
                    No offices found in the current radius.
                </p>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    Increase the radius or move the center marker to include more offices.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { InputNumber } from 'primevue'
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { loadGoogleMapsAPI } from '@/lib/loadGoogleMapsApi'

const props = defineProps({
    offices: {
        type: Array,
        default: () => [],
    },
    centerLatitude: {
        type: Number,
        default: 25.7617,
    },
    centerLongitude: {
        type: Number,
        default: -80.1918,
    },
    radiusMiles: {
        type: Number,
        default: 10,
    },
    selectedOfficeIds: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits([
    'update:centerLatitude',
    'update:centerLongitude',
    'update:radiusMiles',
    'update:selectedOfficeIds',
    'update:selectedOffices',
    'update:officesInRadius',
])

const mapElement = ref(null)
const mapError = ref('')
const isLoadingMap = ref(false)
const localRadiusMiles = ref(props.radiusMiles)
const selectedIdsArray = ref([...(props.selectedOfficeIds || [])])
const officesInRadius = ref([])

let googleMapsApi = null
let map = null
let centerMarker = null
let radiusCircle = null
let officeMarkers = []
let isDraggingShape = false

const selectedOfficeIdsSet = computed(() => new Set(selectedIdsArray.value))

const normalizedOffices = computed(() => {
    return (props.offices || [])
        .filter((office) => office?.latitude != null && office?.longitude != null)
        .map((office) => ({
            ...office,
            latitude: Number(office.latitude),
            longitude: Number(office.longitude),
        }))
        .filter((office) => !Number.isNaN(office.latitude) && !Number.isNaN(office.longitude))
})

watch(
    () => props.radiusMiles,
    (newValue) => {
        localRadiusMiles.value = Number(newValue || 0)
    }
)

watch(
    () => props.centerLatitude,
    (newValue) => {
        if (!map || !centerMarker || !radiusCircle) return
        const currentPosition = centerMarker.getPosition()
        const currentLng = currentPosition?.lng?.() ?? props.centerLongitude
        setCenter(Number(newValue), Number(currentLng), false, false)
    }
)

watch(
    () => props.centerLongitude,
    (newValue) => {
        if (!map || !centerMarker || !radiusCircle) return
        const currentPosition = centerMarker.getPosition()
        const currentLat = currentPosition?.lat?.() ?? props.centerLatitude
        setCenter(Number(currentLat), Number(newValue), false, false)
    }
)

watch(
    () => props.selectedOfficeIds,
    (newValue) => {
        selectedIdsArray.value = [...(newValue || [])]
        updateMarkerStyles()
    },
    { deep: true }
)

watch(localRadiusMiles, (newValue) => {
    const safeValue = Math.max(1, Number(newValue || 0))

    if (radiusCircle) {
        radiusCircle.setRadius(milesToMeters(safeValue))
    }

    emit('update:radiusMiles', safeValue)
    updateOfficesInRadius()
})

watch(
    normalizedOffices,
    async () => {
        if (!map || !googleMapsApi) return
        await nextTick()
        renderOfficeMarkers()
        updateOfficesInRadius()
    },
    { deep: true }
)

onMounted(async () => {
    await nextTick()

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const lat = position.coords.latitude
                const lng = position.coords.longitude

                emit('update:centerLatitude', lat)
                emit('update:centerLongitude', lng)

                localStorage.setItem(
                    'route_center',
                    JSON.stringify({ lat, lng })
                )

                await initializeMap(lat, lng)
            },
            async () => {
                const savedCenter = getSavedCenter()
                await initializeMap(
                    savedCenter?.lat ?? props.centerLatitude,
                    savedCenter?.lng ?? props.centerLongitude
                )
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0,
            }
        )
    } else {
        const savedCenter = getSavedCenter()
        await initializeMap(
            savedCenter?.lat ?? props.centerLatitude,
            savedCenter?.lng ?? props.centerLongitude
        )
    }
})

onBeforeUnmount(() => {
    clearOfficeMarkers()
    centerMarker = null
    radiusCircle = null
    map = null
    googleMapsApi = null
})

const initializeMap = async (lat = null, lng = null) => {
    try {
        isLoadingMap.value = true
        mapError.value = ''

        const loadedApi = await loadGoogleMapsAPI()
        googleMapsApi = resolveGoogleMapsNamespace(loadedApi)

        if (!googleMapsApi?.Map) {
            mapError.value = 'Google Maps loaded, but the Maps namespace was not available.'
            return
        }

        if (!mapElement.value) {
            mapError.value = 'The map container could not be found.'
            return
        }

        const initialLat = lat ?? props.centerLatitude
        const initialLng = lng ?? props.centerLongitude

        createMap(initialLat, initialLng)
    } catch (error) {
        console.error(error)
        mapError.value =
            'Google Maps could not be loaded. Please verify the API key and loader configuration.'
    } finally {
        isLoadingMap.value = false
    }
}

const resolveGoogleMapsNamespace = (loadedApi) => {
    if (loadedApi?.Map) return loadedApi
    if (loadedApi?.maps?.Map) return loadedApi.maps
    if (window?.google?.maps?.Map) return window.google.maps
    return null
}

const createMap = (lat, lng) => {
    clearOfficeMarkers()
    centerMarker = null
    radiusCircle = null
    map = null

    const initialCenter = {
        lat: Number(lat),
        lng: Number(lng),
    }

    map = new googleMapsApi.Map(mapElement.value, {
        center: initialCenter,
        zoom: 11,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
    })

    centerMarker = new googleMapsApi.AdvancedMarkerElement({
        position: initialCenter,
        map,
        draggable: true,
        title: 'Route Center',
        icon: buildMarkerIcon('#3b82f6'),
    })

    radiusCircle = new googleMapsApi.Circle({
        map,
        center: initialCenter,
        radius: milesToMeters(localRadiusMiles.value),
        fillColor: '#3b82f6',
        fillOpacity: 0.12,
        strokeColor: '#3b82f6',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        draggable: true,
        editable: true,
    })

    radiusCircle.addListener('dragstart', () => {
        isDraggingShape = true
    })

    radiusCircle.addListener('dragend', () => {
        isDraggingShape = false
        const center = radiusCircle.getCenter()
        if (!center) return

        if (centerMarker) {
            centerMarker.setPosition(center)
        }

        emitCenter(center.lat(), center.lng())
        updateOfficesInRadius()
    })

    radiusCircle.addListener('center_changed', () => {
        const center = radiusCircle.getCenter()
        if (!center) return

        if (centerMarker) {
            centerMarker.setPosition(center)
        }

        emitCenter(center.lat(), center.lng())
        updateOfficesInRadius()
    })

    radiusCircle.addListener('radius_changed', () => {
        const radiusMiles = Math.max(1, Math.round(metersToMiles(radiusCircle.getRadius())))
        if (localRadiusMiles.value !== radiusMiles) {
            localRadiusMiles.value = radiusMiles
        }
    })

    map.addListener('click', (event) => {
        if (!event.latLng) return
        setCenter(event.latLng.lat(), event.latLng.lng(), true, false)
    })

    centerMarker.addListener('dragstart', () => {
        isDraggingShape = true
    })

    centerMarker.addListener('dragend', () => {
        isDraggingShape = false
        const position = centerMarker.getPosition()
        if (!position) return
        setCenter(position.lat(), position.lng(), true, false)
    })

    renderOfficeMarkers()
    updateOfficesInRadius()
}

const renderOfficeMarkers = () => {
    clearOfficeMarkers()

    officeMarkers = normalizedOffices.value.map((office) => {
        const marker = new googleMapsApi.AdvancedMarkerElement({
            position: {
                lat: office.latitude,
                lng: office.longitude,
            },
            map,
            title: office.parent_company || office.office_name || 'Office',
            icon: buildMarkerIcon('#ef4444'),
        })

        marker.__office = office

        marker.addListener('click', () => {
            toggleOfficeSelection(office)
        })

        return marker
    })

    updateMarkerStyles()
}

const clearOfficeMarkers = () => {
    officeMarkers.forEach((marker) => marker.setMap(null))
    officeMarkers = []
}

const setCenter = (latitude, longitude, shouldEmit = true, shouldPan = false) => {
    const newCenter = {
        lat: Number(latitude),
        lng: Number(longitude),
    }

    if (Number.isNaN(newCenter.lat) || Number.isNaN(newCenter.lng)) return

    if (centerMarker) {
        centerMarker.setPosition(newCenter)
    }

    if (radiusCircle) {
        radiusCircle.setCenter(newCenter)
    }

    if (map && shouldPan && !isDraggingShape) {
        map.panTo(newCenter)
    }

    if (shouldEmit) {
        emitCenter(newCenter.lat, newCenter.lng)
    }

    updateOfficesInRadius()
}

const emitCenter = (latitude, longitude) => {
    emit('update:centerLatitude', latitude)
    emit('update:centerLongitude', longitude)

    localStorage.setItem(
        'route_center',
        JSON.stringify({ lat: latitude, lng: longitude })
    )
}

const updateOfficesInRadius = () => {
    if (!radiusCircle || !googleMapsApi?.geometry?.spherical) {
        officesInRadius.value = []
        emit('update:officesInRadius', [])
        return
    }

    const center = radiusCircle.getCenter()
    const radiusMeters = radiusCircle.getRadius()

    if (!center) {
        officesInRadius.value = []
        emit('update:officesInRadius', [])
        return
    }

    officesInRadius.value = normalizedOffices.value.filter((office) => {
        const officeLatLng = new googleMapsApi.LatLng(office.latitude, office.longitude)
        const distance = googleMapsApi.geometry.spherical.computeDistanceBetween(
            center,
            officeLatLng
        )
        return distance <= radiusMeters
    })

    emit('update:officesInRadius', officesInRadius.value)
    updateMarkerStyles()
}

const updateMarkerStyles = () => {
    if (!officeMarkers.length || !googleMapsApi) return

    officeMarkers.forEach((marker) => {
        const office = marker.__office
        const isSelected = selectedOfficeIdsSet.value.has(office.id)
        const isInside = officesInRadius.value.some((item) => item.id === office.id)

        let color = '#ef4444'

        if (isInside) {
            color = '#eab308'
        }

        if (isSelected) {
            color = '#22c55e'
        }

        marker.setIcon(buildMarkerIcon(color))
    })
}

const toggleOfficeSelection = (office) => {
    const currentIds = new Set(selectedIdsArray.value)

    if (currentIds.has(office.id)) {
        currentIds.delete(office.id)
    } else {
        currentIds.add(office.id)
    }

    selectedIdsArray.value = [...currentIds]
    updateMarkerStyles()

    const selectedOffices = normalizedOffices.value.filter((item) =>
        selectedIdsArray.value.includes(item.id)
    )

    emit('update:selectedOfficeIds', selectedIdsArray.value)
    emit('update:selectedOffices', selectedOffices)
}

const formatOfficeAddress = (office) => {
    const unit = office.unit_number ? ` #${office.unit_number}` : ''
    return `${office.street_address || ''}${unit}, ${office.city || ''}, ${office.state || ''} ${office.zip_code || ''}`.trim()
}

const milesToMeters = (miles) => {
    return Number(miles || 0) * 1609.34
}

const metersToMiles = (meters) => {
    return Number(meters || 0) / 1609.34
}

const buildMarkerIcon = (color) => {
    if (!googleMapsApi) return null

    return {
        url:
            'data:image/svg+xml;charset=UTF-8,' +
            encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 28 40">
                    <path fill="${color}" stroke="#ffffff" stroke-width="2" d="M14 1C6.82 1 1 6.82 1 14c0 9.75 13 25 13 25s13-15.25 13-25C27 6.82 21.18 1 14 1z"/>
                    <circle cx="14" cy="14" r="5" fill="#ffffff"/>
                </svg>
            `),
        scaledSize: new googleMapsApi.Size(28, 40),
        anchor: new googleMapsApi.Point(14, 40),
    }
}

const getSavedCenter = () => {
    try {
        const raw = localStorage.getItem('route_center')
        if (!raw) return null

        const parsed = JSON.parse(raw)

        if (typeof parsed?.lat === 'number' && typeof parsed?.lng === 'number') {
            return parsed
        }

        return null
    } catch {
        return null
    }
}
</script>
