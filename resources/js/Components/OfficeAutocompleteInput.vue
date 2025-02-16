<template>
    <label for="office_autocomplete">{{ label }}</label>
    <AutoComplete
        fluid
        dropdown
        v-model="modelValue"
        :suggestions="filteredOffices"
        @complete="filterOffices"
        optionLabel="formatted_company_name"
        placeholder="Choose a company..."
        id="office_autocomplete"
    />
</template>
<script setup>
import { AutoComplete } from 'primevue';
import axios from 'axios';
import { ref, onMounted, defineModel } from 'vue';
defineProps({
    label: String,
});
const modelValue = defineModel();
const offices = ref();
onMounted(async () => {
    try {
        const response = await axios.get(route('api.offices.all'));
        offices.value = response.data.offices; // Store data in reactive variable
    } catch (error) {
        console.error('Error fetching offices:', error);
    }
});
const filteredOffices = ref([]);
const filterOffices = (event) => {
    const query = event.query.toLowerCase();
    filteredOffices.value = offices.value.filter(office => office.parent_company.toLowerCase().includes(query));
};
const getLabel = (option) => {
    return `${option.parent_company} - ${option.street_address}${option.unit_number ? ` #${option.unit_number}` : ''} (${option.city})`;
};
</script>