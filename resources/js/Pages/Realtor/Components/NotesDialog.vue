<template>
    <Dialog :visible="visible" header="Create Note" :style="{ width: '35rem' }" @update:visible="emit('update:visible', false)">
        <form @submit.prevent="onGeneralNoteSubmit">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="flex flex-col">
                    <label for="" class="font-medium mb-1" :class="{ 'text-red-500': form.errors.priority }">Priority Level</label>
                    <Select :class="{ '!border-red-500': form.errors.priority }" v-model="form.priority" :options="generalNotePriorityLevelOptions" optionLabel="label" optionValue="value" placeholder="Select priority..."></Select>
                    <Message v-if="form.errors.priority" size="small" severity="error" variant="simple">{{ form.errors.priority }}</Message>
                </div>
                <div class="flex flex-col ms-10">
                    <label for="" class="font-medium mb-1">Pinned</label>
                    <RadioButtonGroup v-model="form.is_pinned">
                        <div class="flex gap-5 mt-2">
                            <div class="flex items-center gap-2">
                                <RadioButton inputId="pinned-yes" :value="true" />
                                <label for="pinned-yes">Yes</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <RadioButton inputId="pinned-no" :value="false" />
                                <label for="pinned-no">No</label>
                            </div>
                        </div>
                        <Message v-if="form.errors.is_pinned" size="small" severity="error" variant="simple">{{ form.errors.is_pinned }}</Message>
    
                    </RadioButtonGroup>
                </div>
                <div class="flex flex-col">
                    <label for="" class="font-medium mb-1" :class="{ 'text-red-500': form.errors.assigned_to }">Assigned To</label>
                    <Select :class="{ '!border-red-500': form.errors.assigned_to }" v-model="form.assigned_to" :options="usersWithNobodyOption" optionLabel="formatted_full_name" optionValue="id" placeholder="Select a user..."></Select>
                    <Message v-if="form.errors.assigned_to" size="small" severity="error" variant="simple">{{ form.errors.assigned_to }}</Message>
                </div>
                <div class="flex flex-col ms-10">
                    <label for="" class="font-medium mb-1">Requires Follow-Up</label>
                    <RadioButtonGroup v-model="form.requires_follow_up">
                        <div class="flex gap-5 mt-2">
                            <div class="flex items-center gap-2">
                                <RadioButton inputId="followup-yes" :value="true" />
                                <label for="followup-yes">Yes</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <RadioButton inputId="followup-no" :value="false" />
                                <label for="followup-no">No</label>
                            </div>
                        </div>
                        <Message v-if="form.errors.requires_follow_up" size="small" severity="error" variant="simple">{{ form.errors.requires_follow_up }}</Message>
                    </RadioButtonGroup>
                </div>
            </div>
            <div class="flex flex-col mt-5">
                <label for="general-note" class="font-medium mb-1" :class="{ 'text-red-500': form.errors.note }">Notes</label>
                <Textarea :class="{ '!border-red-500': form.errors.note }" v-model="form.note" class="h-36" />
                <Message v-if="form.errors.note" size="small" severity="error" variant="simple">{{ form.errors.note }}</Message>
            </div>
            <div class="flex justify-end mt-5 gap-3">
                <Button label="Cancel" type="button" severity="secondary" @click="emit('update:visible', false)" />
                <Button label="Submit" type="submit" />
            </div>
        </form>
    </Dialog>
</template>
<script setup>
import { Dialog, Select, Textarea, Button, Message, RadioButtonGroup, RadioButton } from 'primevue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { generalNotePriorityLevelOptions } from '@/constants';
import { usersWithNobodyOption } from '@/constants';
const page = usePage();
const emit = defineEmits(['update:visible', 'realtor']);
const props = defineProps({
    visible: Boolean,
    realtor: Object,
});
const realtor = ref(props.realtor);
const form = useForm({
    priority: 'medium',
    is_pinned: false,
    assigned_to: null,
    requires_follow_up: false,
    note: '',
});
const onGeneralNoteSubmit = () => {
    form.post(route('realtor-note.store', { realtor_id: props.realtor.id }), {
        preserveState: true,
        only: ['realtor', 'flash'],
        onSuccess: () => {
            realtor.value.notes = page.props.realtor.notes;
            form.reset();
            emit('update:visible', false);
            emit('realtor', realtor);
        },
        onError: (error) => {

        }
    });
}
// const onNoteSubmit = () => {
//     notesForm.post(route('sequence-step-note.store'), {
//         onSuccess: () => {
//             console.log('success');
//         },
//         onError: (error) => {
//             console.log(error);
//             console.log(page);
//         }
//     })
// }
</script>