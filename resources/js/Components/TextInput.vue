<template>
    <div class="flex flex-col">
        <label :for="id">{{ label }}</label>
        <InputText
            :id="id"
            v-model="modelValue"
            :class="{
                '!border-red-500': isError,
                '!border-green-500': isValid,
            }"
            :type="type"
        />
        <Message v-if="isError" variant="simple" severity="error" size="small">{{ error }}</Message>
    </div>
</template>

<script setup>
import { InputText, Message } from 'primevue'
import { computed } from 'vue';
const props = defineProps({
    id: String,
    label: String,
    isError: Boolean,
    error: String,
    type: { type: String, default: 'text' },
});
const modelValue = defineModel()
const emit = defineEmits(['update:modelValue']);
// Check if the input is **valid** (i.e., modified but no errors)
const isValid = computed(() => props.modelValue && !props.isError);
</script>
