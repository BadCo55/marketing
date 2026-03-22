<template>
    <Dialog
        :visible="visible"
        modal
        :style="{ width: '36rem', maxWidth: '95vw' }"
        :breakpoints="{ '960px': '92vw', '640px': '96vw' }"
        :pt="{
            root: { class: 'overflow-hidden rounded-2xl' }, /* CHANGED: softer modal shell */
            header: { class: 'border-b border-surface-200 px-6 py-5' }, /* CHANGED: cleaner header spacing */
            content: { class: 'px-6 py-6' }, /* CHANGED: improved body padding */
        }"
        @update:visible="emit('update:visible', false)"
        @hide="onDialogHide"
    >
        <!-- CHANGED: custom header for better hierarchy -->
        <template #header>
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <i class="pi pi-upload text-lg" /> <!-- CHANGED: added upload icon -->
                </div>

                <div>
                    <h2 class="text-xl font-semibold leading-tight">Upload Offices</h2>
                    <p class="mt-1 text-sm text-surface-500">
                        Upload a CSV file to import office records into the system.
                    </p>
                </div>
            </div>
        </template>

        <form @submit.prevent="submitFile" class="space-y-6">
            <!-- CHANGED: added upload instructions / dropzone style wrapper -->
            <div class="rounded-2xl border border-dashed border-surface-300 bg-surface-50 p-5">
                <div class="mb-4">
                    <h3 class="text-base font-semibold">CSV File</h3>
                    <p class="mt-1 text-sm text-surface-500">
                        Select a valid <span class="font-medium">.csv</span> file containing office data.
                    </p>
                </div>

                <!-- CHANGED: FileUpload made clearer and full width -->
                <FileUpload
                    mode="basic"
                    accept=".csv,text/csv"
                    chooseLabel="Choose CSV File"
                    customUpload
                    @select="onFileSelect"
                    class="w-full"
                />

                <!-- CHANGED: selected file summary shown after choosing -->
                <div
                    v-if="upload.file"
                    class="mt-4 rounded-xl border border-surface-200 bg-surface-0 px-4 py-3"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium">Selected File</p>
                            <p class="truncate text-sm text-surface-500">
                                {{ upload.file.name }}
                            </p>
                            <p class="mt-1 text-xs text-surface-400">
                                {{ formatFileSize(upload.file.size) }}
                            </p>
                        </div>

                        <!-- CHANGED: allow clearing selected file -->
                        <Button
                            icon="pi pi-times"
                            severity="secondary"
                            text
                            rounded
                            type="button"
                            @click="clearFile"
                        />
                    </div>
                </div>

                <!-- CHANGED: added inline error feedback -->
                <Message
                    v-if="upload.errors.file"
                    severity="error"
                    variant="simple"
                    size="small"
                    class="mt-3"
                >
                    {{ upload.errors.file }}
                </Message>
            </div>

            <!-- CHANGED: cleaner footer actions -->
            <div class="flex flex-col-reverse gap-3 border-t border-surface-200 pt-5 sm:flex-row sm:justify-end">
                <Button
                    label="Cancel"
                    severity="secondary"
                    outlined
                    type="button"
                    @click="emit('update:visible', false)"
                />
                <Button
                    label="Upload"
                    icon="pi pi-check-circle"
                    type="submit"
                    :disabled="!upload.file"
                    :loading="upload.processing"
                />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { Dialog, FileUpload, Button, Message } from 'primevue'; // CHANGED: added Message import
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['update:visible', 'uploaded']);

defineProps({
    visible: Boolean,
});

const upload = useForm({
    file: null,
});

// CHANGED: store selected file and clear old validation
const onFileSelect = (event) => {
    upload.file = event.files?.[0] || null;
    upload.clearErrors('file');
};

// CHANGED: helper to clear file selection
const clearFile = () => {
    upload.file = null;
    upload.clearErrors('file');
};

// CHANGED: reset modal state when dialog hides
const onDialogHide = () => {
    upload.reset();
    upload.clearErrors();
};

// CHANGED: submit keeps scroll stable and clears state on success
const submitFile = () => {
    upload.post(route('offices.import'), {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:visible', false);
            emit('uploaded');
            upload.reset();
            upload.clearErrors();
        },
        onError: (error) => {
            console.error('File upload failed:', error);
        },
    });
};

// CHANGED: helper for human-readable file size
const formatFileSize = (bytes) => {
    if (!bytes && bytes !== 0) return '';
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
};
</script>
