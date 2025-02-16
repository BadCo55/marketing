<template>
    <form @submit.prevent="onSubmitPersonalInfo">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 items-start">
            <div class="flex flex-col">
                <label for="acquired_via">Acquired Via</label>
                <Select v-model="personalInfoForm.acquired_via" :options="acquiredViaOptions" optionLabel="label" optionValue="value" placeholder="Select option..." />
            </div>
            <div class="flex flex-col">
                <label for="bni_member">BNI Member</label>
                <Select :options="bniMemberOptions" optionLabel="label" optionValue="value" placeholder="Select option..." v-model="personalInfoForm.bni_member" />
            </div>
            <div class="flex flex-col lg:col-span-2">
                <label for="bni_chapter">BNI Chapter</label>
                <InputText v-model="personalInfoForm.bni_chapter" />
            </div>
            <div class="flex flex-col col-span-2">
                <label :class="{'text-red-500': page.props.errors.first_name}">First Name</label>
                <InputText 
                    :class="{ '!border-red-500': page.props.errors.first_name }"
                    v-model="personalInfoForm.first_name" 
                />
                <Message class="mt-1" size="small" variant="simple" severity="error" v-if="page.props.errors.first_name">{{ page.props.errors.first_name }}</Message>
            </div>
            <div class="flex flex-col col-span-2">
                <label :class="{'text-red-500': page.props.errors.last_name}">Last Name</label>
                <InputText 
                    :class="{ '!border-red-500': page.props.errors.last_name }"
                    v-model="personalInfoForm.last_name" 
                />
                <Message class="mt-1" size="small" variant="simple" severity="error" v-if="page.props.errors.last_name">{{ page.props.errors.last_name }}</Message>
            </div>
            <div class="flex flex-col col-span-2">
                <label :class="{'text-red-500': page.props.errors.phone_number}">Phone #</label>
                <InputMask 
                    mask="999-999-9999"
                    :class="{ '!border-red-500': page.props.errors.phone_number }"
                    v-model="personalInfoForm.phone_number" 
                />
                <Message class="mt-1" size="small" variant="simple" severity="error" v-if="page.props.errors.phone_number">{{ page.props.errors.phone_number }}</Message>
            </div>
            <div class="flex flex-col col-span-2">
                <label :class="{'text-red-500': page.props.errors.email}">Email</label>
                <InputText 
                    :class="{ '!border-red-500': page.props.errors.email }"
                    v-model="personalInfoForm.email" 
                />
                <Message class="mt-1" size="small" variant="simple" severity="error" v-if="page.props.errors.email">{{ page.props.errors.email }}</Message>
            </div>
            <div class="flex flex-col col-span-2">
                <label :class="{'text-red-500': page.props.errors.date_of_birth}">Date of Birth</label>
                <DatePicker 
                    :class="{ '!border-red-500': page.props.errors.date_of_birth }"
                    v-model="personalInfoForm.dob" 
                />
                <Message class="mt-1" size="small" variant="simple" severity="error" v-if="page.props.errors.date_of_birth">{{ page.props.errors.date_of_birth }}</Message>
            </div>
            <div class="col-span-2">
                <label class="font-medium">Social Media Links</label>
                <div class="grid grid-cols-4">
                    <table class="col-span-4">
                        <tbody>
                            <tr>
                                <td class="pe-3">
                                    <label for="">Facebook</label>
                                </td>
                                <td>
                                    <InputText v-model="personalInfoForm.facebook" fluid />
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-3">
                                    <label for="">Instagram</label>
                                </td>
                                <td>
                                    <InputText v-model="personalInfoForm.instagram" fluid />
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-3">
                                    <label for="">LinkedIn</label>
                                </td>
                                <td>
                                    <InputText v-model="personalInfoForm.linked_in" fluid />
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-3">
                                    <label for="">TikTok</label>
                                </td>
                                <td>
                                    <InputText v-model="personalInfoForm.tiktok" fluid />
                                </td>
                            </tr>
                            <tr>
                                <td class="pe-3">
                                    <label for="">Website</label>
                                </td>
                                <td>
                                    <InputText v-model="personalInfoForm.website" fluid />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- <div>
                <ul class="list-disc ms-5">
                    <li>Preferred Contact Method</li>
                    <li>Date of Birth</li>
                    <li>Social Media Tags</li>
                </ul>
            </div> -->
        </div>
        <div class="flex justify-end mt-5 gap-3">
            <Button label="Cancel" severity="secondary" type="button" v-if="mismatchedPersonalInfo" @click="resetPersonalInfo" />
            <Button label="Save" type="submit" />
        </div>
    </form>
</template>

<script setup>
import { Button, InputText, Message, InputMask, Select, DatePicker } from 'primevue';
import { usePage, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const props = defineProps({
    realtor: Object,
});

const bniMemberOptions = [
    { label: 'Yes', value: true },
    { label: 'No', value: false },
];

const acquiredViaOptions = [
    { label: 'Office Visit', value: 'office_visit' },
    { label: 'BNI', value: 'bni' },
    { label: 'Previous Inspection', value: 'previous_inspection' },
    { label: 'Referral', value: 'referral' },
    { label: 'Other', value: 'other' },
];

// const socialMediaLinks = JSON.parse(props.realtor.social_media_links);
const socialMediaLinks = ref(JSON.parse(props.realtor.social_media_links));

const personalInfoForm = useForm({
    first_name: props.realtor?.first_name || '',
    last_name: props.realtor?.last_name || '',
    phone_number: props.realtor?.phone_number || '',
    email: props.realtor?.email || '',
    acquired_via: props.realtor?.acquired_via || '',
    bni_member: props.realtor?.bni_member ?? undefined,
    bni_chapter: props.realtor?.bni_chapter || '',
    dob: props.realtor?.dob || '',
    facebook: socialMediaLinks.value?.facebook || '',
    instagram: socialMediaLinks.value?.instagram || '',
    linked_in: socialMediaLinks.value?.linked_in || '',
    tiktok: socialMediaLinks.value?.tiktok || '',
    website: socialMediaLinks.value?.website || '',
});

const mismatchedPersonalInfo = computed(() => {
    if (
        props.realtor.first_name !== personalInfoForm.first_name || 
        props.realtor.last_name !== personalInfoForm.last_name || 
        props.realtor.phone_number !== personalInfoForm.phone_number || 
        props.realtor.email !== personalInfoForm.email
    ) {
        return true; 
    } else {
        return false; 
    }
});

const resetPersonalInfo = () => {
    personalInfoForm.first_name = props.realtor.first_name
    personalInfoForm.last_name = props.realtor.last_name 
    personalInfoForm.phone_number = props.realtor.phone_number
    personalInfoForm.email = props.realtor.email
}

const onSubmitPersonalInfo = () => {
    personalInfoForm.put(route('realtor.update', props.realtor), 
    {
        onSuccess: (response) => {
            // Update the local realtor object with the submitted form data
            props.realtor.first_name = personalInfoForm.first_name;
            props.realtor.last_name = personalInfoForm.last_name;
            props.realtor.phone_number = personalInfoForm.phone_number;
            props.realtor.email = personalInfoForm.email;
        },
        onError: (error) => {
            console.log(error);
        }
    });
}

</script>