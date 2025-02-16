<template>
    <Dialog
        header="Select a Date Range"
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        :style="{ width: '35rem' }"
    >
        <form @submit.prevent="onDateSubmit">
            <div class="flex w-full gap-3">
                <div class="flex flex-col w-full">
                    <label for="start_date">Start Date</label>
                    <DatePicker :maxDate="maxDate" showIcon v-model="dateForm.start_date" fluid />
                </div>
                <div class="flex flex-col w-full">
                    <label for="end_date">End Date</label>
                    <DatePicker :maxDate="maxDate" showIcon v-model="dateForm.end_date" fluid />
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-5">
                <Button label="Close" severity="secondary" @click="emit('update:visible', false)" />
                <Button label="Submit" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, Button, DatePicker } from 'primevue';
import { useForm } from '@inertiajs/vue3';

defineProps({ visible: Boolean });
const emit = defineEmits(['update:visible', 'newReports']);
const maxDate = new Date();

const dateForm = useForm({
    start_date: '',
    end_date: '',
});

const onDateSubmit = () => {
    dateForm.post(route('reports.generate-report'), {
        only: ['flash', 'reports'],
        onSuccess: ({ props }) => {
            emit('newReports', props.reports);
            emit('update:visible', false);
        },
        onError: (error) => {
            console.error(error);
        }
    });
}

</script>
