<template>
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">
                    Communication Log
                </h3>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Track calls, texts, emails, visits, and other contact history with this realtor.
                </p>
            </div>

            <Button
                label="Create Log"
                icon="pi pi-plus-circle"
                @click="onLog"
            />
        </div>

        <div class="max-h-[32rem] overflow-y-auto rounded-2xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900">
            <template v-if="!realtor?.communications?.length">
                <div class="flex min-h-40 flex-col items-center justify-center rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800">
                    <i class="pi pi-comments mb-3 text-2xl text-surface-400 dark:text-surface-500" />
                    <p class="font-medium text-surface-700 dark:text-surface-100">
                        No previous communications found
                    </p>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Create a log entry to start building this realtor’s communication history.
                    </p>
                </div>
            </template>

            <template v-else>
                <div class="flex flex-col gap-4">
                    <div
                        v-for="(item, index) in realtor.communications"
                        :key="item.id || index"
                        class="rounded-2xl border border-surface-200 bg-surface-50 p-4 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                    >
                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="text-base font-semibold text-surface-900 dark:text-surface-0">
                                    {{ item.formatted_communication_method }}
                                </div>

                                <div class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                                    Logged by {{ item.created_by?.formatted_full_name || 'Unknown User' }}
                                </div>
                            </div>

                            <div class="text-sm text-surface-500 dark:text-surface-400 sm:text-right">
                                {{ item.formatted_created_at }}
                            </div>
                        </div>

                        <div class="rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900">
                            <div class="whitespace-pre-line text-sm leading-6 text-surface-800 dark:text-surface-200">
                                {{ item.summary }}
                            </div>

                            <template v-if="item.details">
                                <Divider class="!my-4" />

                                <div class="whitespace-pre-line text-sm leading-6 text-surface-600 dark:text-surface-300">
                                    {{ item.details }}
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <CommunicationLogDialog
        v-model:visible="isDialogVisible"
        :realtor_id="realtor_id"
        @realtor="onUpdateRealtor"
    />
</template>

<script setup>
import CommunicationLogDialog from './CommunicationLogDialog.vue';
import { ref } from 'vue';
import { Button, Divider } from 'primevue';

const emit = defineEmits(['realtor']);

const props = defineProps({
    realtor_id: Number,
    realtor: Object,
});

const isDialogVisible = ref(false);

const onLog = () => {
    isDialogVisible.value = true;
};

const onUpdateRealtor = (realtor) => {
    emit('realtor', realtor);
};
</script>
