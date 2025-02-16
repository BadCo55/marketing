<template>
    <Head title="Create a Realtor" />
    <Layout>
        <div class="root-container">
            <Card>
                <template #content>
                    <form @submit.prevent="submitForm">
                        <div class="flex">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
                                <div>
                                    <label for="first_name">First Name</label>
                                    <InputText :invalid="page.props.errors.first_name" fluid v-model="form.first_name" id="first_name" />
                                    <Message variant="simple" size="small" class="text-red-500" v-if="page.props.errors.first_name">{{ page.props.errors.first_name }}</Message >
                                </div>
                                <div>
                                    <label for="last_name">Last Name</label>
                                    <InputText :invalid="page.props.errors.last_name" fluid v-model="form.last_name" id="last_name" />
                                    <Message variant="simple" size="small" class="text-red-500" v-if="page.props.errors.last_name">{{ page.props.errors.last_name }}</Message >
                                </div>
                                <div>
                                    <label for="phone_number">Phone #</label>
                                    <InputMask mask="999-999-9999" :invalid="page.props.errors.phone_number" fluid v-model="form.phone_number" id="phone_number" />
                                    <Message variant="simple" size="small" class="text-red-500" v-if="page.props.errors.phone_number">{{ page.props.errors.phone_number }}</Message >
                                </div>
                                <div>
                                    <label for="email">Email</label>
                                    <InputText :invalid="page.props.errors.email" fluid v-model="form.email" id="email" />
                                    <Message variant="simple" size="small" class="text-red-500" v-if="page.props.errors.email">{{ page.props.errors.email }}</Message >
                                </div>
                                <div>
                                    <label for="brokerage">Office Name</label>
                                    <AutoComplete
                                        fluid
                                        v-model="form.office"
                                        dropdown
                                        :suggestions="filteredOffices"
                                        @complete="filterOffices"
                                        :optionLabel="getLabel"
                                    />
                                    <Button
                                        label="Or, Create a New Office"
                                        @click="openAddOfficeModal"
                                        variant="text"
                                        class="mt-1"
                                    />
                                </div>
                                <div>
                                    <label for="title">Title</label>
                                    <Select :invalid="page.props.errors.title" fluid v-model="form.title" id="title" editable :options="titleOptions" optionLabel="label" optionValue="value" placeholder="Select a title..." />
                                    <Message variant="simple" size="small" class="text-red-500" v-if="page.props.errors.title">{{ page.props.errors.title }}</Message >
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center mt-8">
                            <div class="grid grid-cols-2 gap-8">
                                <!-- <FloatLabel>
                                    <AutoComplete fluid v-model="form.office" dropdown :suggestions="filteredOffices" @complete="filterOffices" :optionLabel="getLabel" />
                                </FloatLabel> -->
                            </div>
                        </div>
                        <div class="flex justify-center mt-5">
                            <Button label="Submit" type="submit" class="w-48"></Button>
                        </div>

                        <!-- Add New Office Modal -->
                        <Dialog header="Add New Office" v-model:visible="isAddOfficeModalVisible" class="w-[35rem]">
                            <NewOfficeForm @updatedOffices="onNewOfficeSubmit"  />
                        </Dialog>
                    </form>
                </template>
            </Card>
        </div>
    </Layout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import { Button, FloatLabel, InputText, AutoComplete, Dialog, InputMask, Card, Message, Select } from 'primevue';
import { usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import NewOfficeForm from '@/Components/NewOfficeForm.vue';

const page = usePage();

const form = useForm({
    first_name: '',
    last_name: '',
    phone_number: '',
    email: '',
    office: '',
    title: '',
});

const titleOptions = [
    { value: 'broker', label: 'Broker' },
    { value: 'broker_associate', label: 'Broker Associate' },
    { value: 'realtor', label: 'Realtor' },
];

const submitForm = () => {
    form.post(route('realtor.store'), {
        onSuccess: (response) => {

        },
        onError: (error) => {
            console.log(page.props.errors);
        }
    })
}

const offices = ref(page.props.offices);
const filteredOffices = ref([]);

// Office modal state
const isAddOfficeModalVisible = ref(false);

const getLabel = (option) => {
    return `${option.parent_company} - ${option.street_address}${option.unit_number ? ` #${option.unit_number}` : ''} (${option.city})`;
};

const filterOffices = (event) => {
    const query = event.query.toLowerCase();
    filteredOffices.value = offices.value.filter(office => office.parent_company.toLowerCase().includes(query));
};

// Open "Add Office" modal
const openAddOfficeModal = () => {
    isAddOfficeModalVisible.value = true;
}

/**
 * On new office creation
 */
const onNewOfficeSubmit = (data) => {
    offices.value = data;
    isAddOfficeModalVisible.value = false;
    filterOffices({ query: form.office });
}


</script>
