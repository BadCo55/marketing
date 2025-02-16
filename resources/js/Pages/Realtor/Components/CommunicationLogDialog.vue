<template>
    <Dialog
        :visible="visible"
        header="Log Communication"
        :style="{ width: '35rem' }"
        @update:visible="emit('update:visible', false)"
    >
        <form @submit.prevent="onFormSubmit">
            <div class="mb-5">
                <label for="communication_method">Communication Method</label>
                <Select
                    id="communication_method"
                    v-model="form.communication_method"
                    :options="communicationOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select an option..."
                    fluid
                />
                <Message
                    severity="error"
                    variant="simple"
                    size="small"
                    v-if="form.errors.communication_method"
                    >{{ form.errors.communication_method }}</Message
                >
            </div>
            <div class="flex flex-col mb-5">
                <label for="summary">Summary</label>
                <InputText id="summary" v-model="form.summary" placeholder="Enter a summary..." />
                <Message
                    severity="error"
                    variant="simple"
                    size="small"
                    v-if="form.errors.summary"
                    >{{ form.errors.summary }}</Message
                >
            </div>
            <div class="flex flex-col mb-5">
                <label for="details">Details</label>
                <Textarea id="details" v-model="form.details" placeholder="Enter details..." />
                <Message
                    severity="error"
                    variant="simple"
                    size="small"
                    v-if="form.errors.details"
                    >{{ form.errors.details }}</Message
                >
            </div>
            <div class="flex justify-end">
                <Button label="Submit" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, Select, InputText, Textarea, Button, Message } from 'primevue'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
const props = defineProps({
    realtor_id: Number,
    visible: Boolean,
})
const emit = defineEmits(['update:visible', 'realtor'])
const form = useForm({
    realtor_id: props.realtor_id,
    communication_method: '',
    summary: '',
    details: '',
})
const communicationOptions = [
    { label: 'Phone Call', value: 'phone_call' },
    { label: 'Email', value: 'email' },
    { label: 'Text Message', value: 'text_message' },
    { label: 'Social Media Interaction', value: 'social_media' },
]
const onFormSubmit = () => {
    form.post(route('communication-log.store'), {
        only: ['realtor', 'flash'],
        onSuccess: (response) => {
            emit('update:visible', false)
            emit('realtor', response.props.realtor)
            form.reset()
        },
        onError: (error) => {
            console.log(error)
        },
    })
}
</script>
