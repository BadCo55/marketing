<template>
    <div class="flex justify-end">
        <Button label="Create a Log" @click="onLog" />
    </div>

    <div class="flex flex-col gap-3 mt-5 max-h-96 overflow-y-scroll p-3 border rounded dark:border-surface-800 dark:text-surface-400">
        <template v-if="!realtor.communications.length">
            <div class="">
                No previous communications found
            </div>
        </template>
        <template v-else>
            <div v-for="(item, index) in realtor.communications" :key="index" class="border rounded p-3 bg-surface-50 dark:bg-surface-800 dark:border-0">
                <div class="text-lg font-medium mb-3">
                    {{ item.formatted_communication_method }}
                </div>
                <div class="border rounded p-3 mb-3 bg-surface-100 dark:bg-surface-900 dark:border-0">
                    <div>
                        {{ item.summary }}
                    </div>
                    <div v-if="item.details">
                        <Divider />
                        {{ item.details }}
                    </div>
                </div>
                <div class="flex flex-col text-end justify-end">
                    <p>
                        {{ item.created_by.formatted_full_name }}
                    </p>
                    <p>
                        {{ item.formatted_created_at }}
                    </p>
                </div>
            </div>
        </template>
    </div>


    <CommunicationLogDialog v-model:visible="isDialogVisible" :realtor_id="realtor_id" @realtor="onUpdateRealtor(realtor)" />
</template>

<script setup>
import CommunicationLogDialog from './CommunicationLogDialog.vue';
import { ref } from 'vue';
import { Button, Divider } from 'primevue';
const emit = defineEmits(['realtor'])
const props = defineProps({
    realtor_id: Number,
    realtor: Object,
});
const isDialogVisible = ref(false);
const onLog = () => {
    isDialogVisible.value = true;
}
const onUpdateRealtor = (realtor) => {
    emit('realtor', realtor);
}
</script>
