<template>
    <form @submit.prevent="onSubmit">
        <div class="grid grid-cols-3 gap-5">
            <template v-if="!isCompanySearchVisible">
                <div class="flex w-full gap-5 items-center col-span-3">
                    <div class="col-span-2 w-full">
                        <div class="inline-flex w-full gap-5 items-center dark:bg-surface-950 bg-surface-50 border p-5 rounded">
                            <div class="w-full">
                                <label class="">Affiliated Company</label>
                                <InputText fluid v-model="form.parent_company" class="flex-1" disabled />
                            </div>
                            <div>
                                <Link
                                    :href="route('office.show', realtor.office.id)"
                                >
                                    <Button icon="pi pi-search" rounded />
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="flex w-1/3 flex-col gap-2">
                        <Button fluid label="Change Company" @click="onToggleCompanySearch" />
                        <Button fluid label="Remove Affiliation" severity="warn" @click="onRemoveAffiliation" />
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="col-span-2">
                    <OfficeAutocompleteInput label="Affiliated Company" v-model="officeForm.office" />
                </div>
                <div class="flex flex-col gap-3">
                    <Button fluid label="Save" @click="onSetAffiliation"  v-if="officeForm.office" />
                    <Button fluid label="Cancel" @click= onToggleCompanySearch v-if="officeForm.office" />
                </div>
            </template>
            <div class="">
                <label>Title</label>
                <InputText fluid v-model="form.title" />
            </div>
            <div class="">
                <label>License #</label>
                <InputText fluid v-model="form.license_number" />
            </div>
            <div class="">
                <label># Years In Business</label>
                <InputNumber fluid v-model="form.years_in_business" />
            </div>
            <div class="col-span-3">
                <label class="">Specializations</label>
                <MultiSelect fluid :options="specializationOptions" optionLabel="label" optionValue="value" placeholder="Select specializations..." v-model="form.specializations" />
            </div>
            <div class="col-span-3">
                <label class="">Primary Region(s)</label>
                <MultiSelect :options="regionOptions" optionLabel="label" optionValue="value" fluid v-model="form.primary_region" placeholder="Select option..." />
            </div>
            <div class="col-span-3">
                <label>Preferred Inspector(s)</label>
                <MultiSelect fluid v-model="form.preferred_inspector" :options="preferredInspectorOptions" optionLabel="label" optionValue="value" placeholder="Select option..." />
            </div>
            <div class="col-span-2">
                <label>Preferred Inspection Company</label>
                <InputText fluid v-model="form.preferred_inspection_company" placeholder="Other preferred company..." />
                <Message size="small" variant="simple" severity="secondary">Enter a company they use over us</Message>
            </div>
        </div>
        <div class="flex justify-end mt-5">
            <Button label="Save" type="submit" />
        </div>
    </form>
</template>

<script setup>
import { regionOptions, specializationOptions, preferredInspectorOptions } from '@/constants';
import OfficeAutocompleteInput from '@/Components/OfficeAutocompleteInput.vue';
import { ref, onMounted } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import { MultiSelect, InputText, Button, InputNumber, Message } from 'primevue';
const props = defineProps({
    realtor: Object,
});
const realtor = ref(props.realtor);
// State Management
onMounted(() => {
    setCompanySearchState();
})
const isCompanySearchVisible = ref(false);
const setCompanySearchState = () => {
    if (realtor.value.office_id) {
        isCompanySearchVisible.value = false;
    } else {
        isCompanySearchVisible.value = true;
    }
}
const onToggleCompanySearch = () => {
    isCompanySearchVisible.value = !isCompanySearchVisible.value;
    officeForm.office = '';
}
const form = useForm({
    specializations: realtor.value?.specializations,
    license_number: realtor.value?.license_number,
    years_in_business: realtor.value?.years_in_business,
    parent_company: realtor.value?.office?.formatted_company_name,
    title: realtor.value?.title,
    primary_region: realtor.value?.primary_region,
    preferred_inspection_company: realtor.value?.preferred_inspection_company,
    preferred_inspector: realtor.value?.preferred_inspector,
});
const officeForm = useForm({
    office: realtor.value?.office_id,
    request_type: 'set_affiliation',
});
const onSubmit = () => {
    form.transform((data) => ({
        ...data, // Include existing form data
        request_type: 'update_professional_info', // Add request_type to the payload
    })).put(route('realtor.update', realtor.value.id), {
        preserveScroll: true,
        onSuccess: () => {

        },
        onError: (error) => {
            console.error("Error updating professional info:", error);
        }
    });
}
const onSetAffiliation = ()=> {
    officeForm.put(route('realtor.update', realtor.value.id), {
        only: ['flash', 'realtor'],
        onSuccess: (response) => {
            realtor.value = response.props.realtor;
            isCompanySearchVisible.value = false;
            form.parent_company = response.props.realtor.office.formatted_company_name;
        },
        onError: (error) => {
            console.error(error);
        }
    });
}
const onRemoveAffiliation = () => {
    router.put(route('realtor.update', realtor.value.id),
    {
        request_type: 'remove_affiliation'
    },
    {
        only: ['flash', 'realtor'],
        onSuccess: (response) => {
            realtor.value = response.props.realtor;
            isCompanySearchVisible.value = true;
            officeForm.office = '';
        },
        onError: (error) => {
            console.error(error);
        }
    });
}
</script>
