<template>
    <Message severity="error" variant="simple">
        {{ message }}
    </Message>
</template>

<script setup>
import { Message } from 'primevue'
const props = defineProps({
    message: String,
});
</script>
