<template>
    <div class="flex gap-5 items-center flex-col md:flex-row">
        <div>
            <img :src="user.avatar_url" class="rounded-full w-48 h-48">
        </div>
        <div>
            <FileUpload
                mode="basic"
                name="avatar"
                accept="image/*"
                customUpload
                @select="onFileSelect"
                :maxFileSize="1000000"
            />
        </div>
    </div>
</template>

<script setup>
import { FileUpload } from 'primevue';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    user: Object,
});
const onFileSelect = async (event) => {
    const file = event.files[0];
    if (!file) {
        console.error("No file selected.");
        return;
    }

    // Create FormData object
    const formData = new FormData();
    formData.append('avatar', file);

    // Send the request using Inertia
    router.post(route('user.upload-avatar', { id: props.user.id }), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
        onSuccess: () => {
            console.log("Upload successful");
        },
        onError: (error) => {
            console.error("Upload error:", error);
        },
    });
};
</script>
