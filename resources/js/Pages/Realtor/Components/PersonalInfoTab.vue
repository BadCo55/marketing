<template>
    <form @submit.prevent="onSubmitPersonalInfo" class="space-y-8">
        <div class="flex flex-col gap-1">
            <h3 class="text-xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">
                Personal Information
            </h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">
                Update contact details, acquisition source, BNI information, and online presence.
            </p>
        </div>

        <section class="border-b border-surface-200 pb-8 dark:border-surface-800">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label
                        for="acquired_via"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        Acquired Via
                    </label>
                    <Select
                        id="acquired_via"
                        v-model="personalInfoForm.acquired_via"
                        :options="acquiredViaOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select option..."
                        fluid
                        :invalid="!!personalInfoForm.errors.acquired_via"
                    />
                    <Message
                        v-if="personalInfoForm.errors.acquired_via"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.acquired_via }}
                    </Message>
                </div>

                <div class="lg:col-span-2">
                    <label
                        for="bni_member"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        BNI Member
                    </label>
                    <Select
                        id="bni_member"
                        v-model="personalInfoForm.bni_member"
                        :options="bniMemberOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Select option..."
                        fluid
                        :invalid="!!personalInfoForm.errors.bni_member"
                    />
                    <Message
                        v-if="personalInfoForm.errors.bni_member"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.bni_member }}
                    </Message>
                </div>

                <div class="lg:col-span-2">
                    <label
                        for="bni_chapter"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        BNI Chapter
                    </label>
                    <InputText
                        id="bni_chapter"
                        v-model="personalInfoForm.bni_chapter"
                        fluid
                        placeholder="Enter BNI chapter"
                        :invalid="!!personalInfoForm.errors.bni_chapter"
                    />
                    <Message
                        v-if="personalInfoForm.errors.bni_chapter"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.bni_chapter }}
                    </Message>
                </div>

                <div class="lg:col-span-3">
                    <label
                        for="first_name"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': personalInfoForm.errors.first_name }"
                    >
                        First Name
                    </label>
                    <InputText
                        id="first_name"
                        v-model="personalInfoForm.first_name"
                        fluid
                        placeholder="Enter first name"
                        :invalid="!!personalInfoForm.errors.first_name"
                    />
                    <Message
                        v-if="personalInfoForm.errors.first_name"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.first_name }}
                    </Message>
                </div>

                <div class="lg:col-span-3">
                    <label
                        for="last_name"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': personalInfoForm.errors.last_name }"
                    >
                        Last Name
                    </label>
                    <InputText
                        id="last_name"
                        v-model="personalInfoForm.last_name"
                        fluid
                        placeholder="Enter last name"
                        :invalid="!!personalInfoForm.errors.last_name"
                    />
                    <Message
                        v-if="personalInfoForm.errors.last_name"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.last_name }}
                    </Message>
                </div>

                <div class="lg:col-span-3">
                    <label
                        for="phone_number"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': personalInfoForm.errors.phone_number }"
                    >
                        Phone #
                    </label>
                    <InputMask
                        id="phone_number"
                        v-model="personalInfoForm.phone_number"
                        mask="999-999-9999"
                        fluid
                        placeholder="123-456-7890"
                        :invalid="!!personalInfoForm.errors.phone_number"
                    />
                    <Message
                        v-if="personalInfoForm.errors.phone_number"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.phone_number }}
                    </Message>
                </div>

                <div class="lg:col-span-3">
                    <label
                        for="email"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': personalInfoForm.errors.email }"
                    >
                        Email
                    </label>
                    <InputText
                        id="email"
                        v-model="personalInfoForm.email"
                        fluid
                        placeholder="name@example.com"
                        :invalid="!!personalInfoForm.errors.email"
                    />
                    <Message
                        v-if="personalInfoForm.errors.email"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.email }}
                    </Message>
                </div>

                <div class="lg:col-span-3">
                    <label
                        for="dob"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                        :class="{ 'text-red-500': personalInfoForm.errors.dob }"
                    >
                        Date of Birth
                    </label>
                    <DatePicker
                        id="dob"
                        v-model="personalInfoForm.dob"
                        fluid
                        showIcon
                        placeholder="Select a date"
                        :invalid="!!personalInfoForm.errors.dob"
                    />
                    <Message
                        v-if="personalInfoForm.errors.dob"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.dob }}
                    </Message>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex flex-col gap-1">
                <h4 class="text-base font-semibold text-surface-900 dark:text-surface-0">Social & Web</h4>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Add profile links for research, marketing, and relationship tracking.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <label
                        for="facebook"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        Facebook
                    </label>
                    <InputText
                        id="facebook"
                        v-model="personalInfoForm.facebook"
                        fluid
                        placeholder="Facebook profile URL"
                        :invalid="!!personalInfoForm.errors.facebook"
                    />
                    <Message
                        v-if="personalInfoForm.errors.facebook"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.facebook }}
                    </Message>
                </div>

                <div>
                    <label
                        for="instagram"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        Instagram
                    </label>
                    <InputText
                        id="instagram"
                        v-model="personalInfoForm.instagram"
                        fluid
                        placeholder="Instagram profile URL"
                        :invalid="!!personalInfoForm.errors.instagram"
                    />
                    <Message
                        v-if="personalInfoForm.errors.instagram"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.instagram }}
                    </Message>
                </div>

                <div>
                    <label
                        for="linked_in"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        LinkedIn
                    </label>
                    <InputText
                        id="linked_in"
                        v-model="personalInfoForm.linked_in"
                        fluid
                        placeholder="LinkedIn profile URL"
                        :invalid="!!personalInfoForm.errors.linked_in"
                    />
                    <Message
                        v-if="personalInfoForm.errors.linked_in"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.linked_in }}
                    </Message>
                </div>

                <div>
                    <label
                        for="tiktok"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        TikTok
                    </label>
                    <InputText
                        id="tiktok"
                        v-model="personalInfoForm.tiktok"
                        fluid
                        placeholder="TikTok profile URL"
                        :invalid="!!personalInfoForm.errors.tiktok"
                    />
                    <Message
                        v-if="personalInfoForm.errors.tiktok"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.tiktok }}
                    </Message>
                </div>

                <div class="lg:col-span-2">
                    <label
                        for="website"
                        class="mb-2 block text-sm font-medium text-surface-900 dark:text-surface-100"
                    >
                        Website
                    </label>
                    <InputText
                        id="website"
                        v-model="personalInfoForm.website"
                        fluid
                        placeholder="Website URL"
                        :invalid="!!personalInfoForm.errors.website"
                    />
                    <Message
                        v-if="personalInfoForm.errors.website"
                        class="mt-2"
                        size="small"
                        variant="simple"
                        severity="error"
                    >
                        {{ personalInfoForm.errors.website }}
                    </Message>
                </div>
            </div>
        </section>

        <div class="flex items-center justify-end gap-3 border-t border-surface-200 pt-5 dark:border-surface-800">
            <Button
                v-if="mismatchedPersonalInfo"
                label="Reset"
                severity="secondary"
                type="button"
                text
                @click="resetPersonalInfo"
            />
            <Button
                label="Save Changes"
                type="submit"
                icon="pi pi-check-circle"
                :loading="personalInfoForm.processing"
            />
        </div>
    </form>
</template>

<script setup>
import { Button, InputText, Message, InputMask, Select, DatePicker } from 'primevue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    realtor: Object,
});

const emit = defineEmits(['realtor']);

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

const socialMediaLinks = ref(parseSocialMediaLinks(props.realtor?.social_media_links));

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
    const currentLinks = parseSocialMediaLinks(props.realtor?.social_media_links);

    return (
        (props.realtor?.first_name || '') !== (personalInfoForm.first_name || '') ||
        (props.realtor?.last_name || '') !== (personalInfoForm.last_name || '') ||
        (props.realtor?.phone_number || '') !== (personalInfoForm.phone_number || '') ||
        (props.realtor?.email || '') !== (personalInfoForm.email || '') ||
        (props.realtor?.acquired_via || '') !== (personalInfoForm.acquired_via || '') ||
        (props.realtor?.bni_member ?? undefined) !== (personalInfoForm.bni_member ?? undefined) ||
        (props.realtor?.bni_chapter || '') !== (personalInfoForm.bni_chapter || '') ||
        (props.realtor?.dob || '') !== (personalInfoForm.dob || '') ||
        (currentLinks?.facebook || '') !== (personalInfoForm.facebook || '') ||
        (currentLinks?.instagram || '') !== (personalInfoForm.instagram || '') ||
        (currentLinks?.linked_in || '') !== (personalInfoForm.linked_in || '') ||
        (currentLinks?.tiktok || '') !== (personalInfoForm.tiktok || '') ||
        (currentLinks?.website || '') !== (personalInfoForm.website || '')
    );
});

const resetPersonalInfo = () => {
    const currentLinks = parseSocialMediaLinks(props.realtor?.social_media_links);

    personalInfoForm.first_name = props.realtor?.first_name || '';
    personalInfoForm.last_name = props.realtor?.last_name || '';
    personalInfoForm.phone_number = props.realtor?.phone_number || '';
    personalInfoForm.email = props.realtor?.email || '';
    personalInfoForm.acquired_via = props.realtor?.acquired_via || '';
    personalInfoForm.bni_member = props.realtor?.bni_member ?? undefined;
    personalInfoForm.bni_chapter = props.realtor?.bni_chapter || '';
    personalInfoForm.dob = props.realtor?.dob || '';
    personalInfoForm.facebook = currentLinks?.facebook || '';
    personalInfoForm.instagram = currentLinks?.instagram || '';
    personalInfoForm.linked_in = currentLinks?.linked_in || '';
    personalInfoForm.tiktok = currentLinks?.tiktok || '';
    personalInfoForm.website = currentLinks?.website || '';
    personalInfoForm.clearErrors();
};

watch(
    () => props.realtor,
    (newRealtor) => {
        const currentLinks = parseSocialMediaLinks(newRealtor?.social_media_links);

        personalInfoForm.first_name = newRealtor?.first_name || '';
        personalInfoForm.last_name = newRealtor?.last_name || '';
        personalInfoForm.phone_number = newRealtor?.phone_number || '';
        personalInfoForm.email = newRealtor?.email || '';
        personalInfoForm.acquired_via = newRealtor?.acquired_via || '';
        personalInfoForm.bni_member = newRealtor?.bni_member ?? undefined;
        personalInfoForm.bni_chapter = newRealtor?.bni_chapter || '';
        personalInfoForm.dob = newRealtor?.dob || '';
        personalInfoForm.facebook = currentLinks?.facebook || '';
        personalInfoForm.instagram = currentLinks?.instagram || '';
        personalInfoForm.linked_in = currentLinks?.linked_in || '';
        personalInfoForm.tiktok = currentLinks?.tiktok || '';
        personalInfoForm.website = currentLinks?.website || '';
    },
    { deep: true }
);

const onSubmitPersonalInfo = () => {
    personalInfoForm.put(route('realtor.update', props.realtor), {
        preserveScroll: true,
        onSuccess: (response) => {
            if (response?.props?.realtor) {
                emit('realtor', response.props.realtor);
            } else {
                props.realtor.first_name = personalInfoForm.first_name;
                props.realtor.last_name = personalInfoForm.last_name;
                props.realtor.phone_number = personalInfoForm.phone_number;
                props.realtor.email = personalInfoForm.email;
                props.realtor.acquired_via = personalInfoForm.acquired_via;
                props.realtor.bni_member = personalInfoForm.bni_member;
                props.realtor.bni_chapter = personalInfoForm.bni_chapter;
                props.realtor.dob = personalInfoForm.dob;
                props.realtor.social_media_links = JSON.stringify({
                    facebook: personalInfoForm.facebook,
                    instagram: personalInfoForm.instagram,
                    linked_in: personalInfoForm.linked_in,
                    tiktok: personalInfoForm.tiktok,
                    website: personalInfoForm.website,
                });

                emit('realtor', props.realtor);
            }
        },
        onError: (error) => {
            console.log(error);
        },
    });
};

function parseSocialMediaLinks(value) {
    if (!value) return {};
    if (typeof value === 'object') return value;

    try {
        return JSON.parse(value);
    } catch {
        return {};
    }
}
</script>
