<template>
    <Dialog
        :visible="visible"
        @update:visible="emit('update:visible', false)"
        :style="{ width: '35rem' }"
        header="Upload Offices"
    >
        <form @submit.prevent="submitFile">
            <FileUpload mode="basic" accept=".csv" customUpload @select="onFileSelect" />
            <div class="flex justify-end mt-4 gap-3">
                <Button label="Cancel" severity="secondary" @click="emit('update:visible', false)" />
                <Button label="Upload" type="submit" :disabled="!upload.file" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, FileUpload, Button } from 'primevue'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
const emit = defineEmits(['update:visible', 'uploaded'])
const props = defineProps({
    visible: Boolean,
})

const upload = useForm({
    file: null,
})

// Handle file selection
const onFileSelect = (event) => {
    upload.file = event.files[0] // Store the first selected file
}

// Submit file to Laravel
const submitFile = () => {
    upload.post(route('offices.import'), {
        onSuccess: () => {
            emit('update:visible', false) // Close modal
            emit('uploaded') // Trigger update in parent component
        },
        onError: (error) => {
            console.error('File upload failed:', error)
        },
    })
}
</script>
