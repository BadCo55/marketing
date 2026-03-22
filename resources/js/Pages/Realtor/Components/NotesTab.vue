<template>
    <!-- CHANGED: Added dark mode text colors to section header -->
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <!-- CHANGED: Added dark mode heading color -->
                <h3 class="text-xl font-semibold tracking-tight text-surface-900 dark:text-surface-0">Notes</h3>

                <!-- CHANGED: Added dark mode helper text color -->
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Capture important context, reminders, assignments, and follow-up details for this realtor.
                </p>
            </div>

            <Button
                label="Create Note"
                icon="pi pi-plus-circle"
                @click="isDialogVisible = true"
            />
        </div>

        <!-- CHANGED: Added dark mode background and border styles to notes container -->
        <div class="max-h-[600px] overflow-y-auto rounded-2xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900">
            <template v-if="sortedNotes.length">
                <div class="flex flex-col gap-4">
                    <!-- CHANGED: Added dark mode card styling -->
                    <div
                        v-for="note in sortedNotes"
                        :key="note.id"
                        class="rounded-2xl border border-surface-200 bg-surface-50 p-4 shadow-sm dark:border-surface-700 dark:bg-surface-800"
                    >
                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex flex-wrap items-center gap-2">
                                <Message
                                    v-if="note.is_pinned"
                                    severity="success"
                                    size="small"
                                    icon="pi pi-thumbtack"
                                >
                                    Pinned
                                </Message>

                                <Message
                                    v-if="note.formatted_priority"
                                    :severity="noteSeverity(note.formatted_priority)"
                                    size="small"
                                >
                                    {{ note.formatted_priority }}
                                </Message>

                                <Message
                                    v-if="note.requires_follow_up"
                                    severity="info"
                                    size="small"
                                >
                                    Follow Up Required
                                </Message>

                                <Message
                                    v-if="note.assigned_to"
                                    severity="secondary"
                                    size="small"
                                >
                                    Assigned: {{ note.assigned_to.formatted_full_name }}
                                </Message>
                            </div>

                            <div class="flex items-center gap-2">
                                <Button
                                    v-if="note.is_pinned"
                                    icon="pi pi-times"
                                    severity="secondary"
                                    text
                                    rounded
                                    v-tooltip.top="'Remove Pin'"
                                    @click="onRemovePin(note.id)"
                                />
                                <Button
                                    v-else
                                    label="Pin"
                                    icon="pi pi-thumbtack"
                                    severity="secondary"
                                    outlined
                                    size="small"
                                    @click="onAddPin(note.id)"
                                />
                            </div>
                        </div>

                        <!-- CHANGED: Added dark mode inner note panel styling -->
                        <div class="rounded-xl border border-surface-200 bg-surface-0 p-4 dark:border-surface-700 dark:bg-surface-900">
                            <!-- CHANGED: Added dark mode note body text color -->
                            <div class="whitespace-pre-line text-sm leading-6 text-surface-800 dark:text-surface-200">
                                {{ note.note }}
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <Button
                                    label="Remove Note"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    outlined
                                    size="small"
                                    @click="onDeleteNote(note.id)"
                                />
                            </div>

                            <!-- CHANGED: Added dark mode footer text colors -->
                            <div class="text-sm text-surface-500 dark:text-surface-400 sm:text-right">
                                <p class="font-medium text-surface-700 dark:text-surface-200">
                                    {{ note.created_by?.name || 'Unknown User' }}
                                </p>
                                <p>
                                    {{ note.formatted_created_at }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template v-else>
                <!-- CHANGED: Added dark mode empty state styling -->
                <div class="flex min-h-52 flex-col items-center justify-center rounded-xl border border-dashed border-surface-300 bg-surface-50 px-6 py-10 text-center dark:border-surface-700 dark:bg-surface-800">
                    <i class="pi pi-file-edit mb-3 text-2xl text-surface-400 dark:text-surface-500" />
                    <p class="font-medium text-surface-700 dark:text-surface-200">No notes have been found.</p>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Create a note to store important details, follow-up items, or internal reminders.
                    </p>
                </div>
            </template>
        </div>
    </div>

    <NotesDialog
        v-model:visible="isDialogVisible"
        :realtor="props.realtor"
        @realtor="onUpdateRealtor"
    />
</template>

<script setup>
import NotesDialog from './NotesDialog.vue';
import { router } from '@inertiajs/vue3';
import { Button, Message } from 'primevue';
import { computed, ref } from 'vue';

const props = defineProps({
    users: Object,
    realtor: Object,
});

const emit = defineEmits(['realtor']);

const onUpdateRealtor = (realtor) => {
    emit('realtor', realtor);
};

const noteSeverity = (priorityLevel) => {
    switch (priorityLevel) {
        case 'Low':
            return 'info';
        case 'Medium':
            return 'warn';
        case 'High':
            return 'danger';
        default:
            return 'info';
    }
};

const sortedNotes = computed(() => {
    const notes = [...(props.realtor?.notes || [])];

    return notes.sort((a, b) => {
        if (a.is_pinned !== b.is_pinned) {
            return Number(b.is_pinned) - Number(a.is_pinned);
        }

        return new Date(b.created_at) - new Date(a.created_at);
    });
});

const onRemovePin = (noteId) => {
    router.put(
        route('realtor-note.update', { realtor_note: noteId }),
        { request_type: 'remove_pin', is_pinned: false },
        {
            preserveScroll: true,
            onSuccess: () => {
                const note = props.realtor.notes.find((note) => note.id === noteId);
                if (note) {
                    note.is_pinned = false;
                }
            },
            onError: (error) => {
                console.error('Error removing pin from note:', error);
            },
        }
    );
};

const onDeleteNote = (noteId) => {
    router.delete(route('realtor-note.destroy', { realtor_note: noteId }), {
        preserveScroll: true,
        onSuccess: () => {
            const noteIndex = props.realtor.notes.findIndex((note) => note.id === noteId);
            if (noteIndex !== -1) {
                props.realtor.notes.splice(noteIndex, 1);
            }
        },
        onError: (error) => {
            console.error('Error deleting note:', error);
        },
    });
};

const onAddPin = (noteId) => {
    router.put(
        route('realtor-note.update', { realtor_note: noteId }),
        { request_type: 'add_pin', is_pinned: true },
        {
            preserveScroll: true,
            onSuccess: () => {
                const note = props.realtor.notes.find((note) => note.id === noteId);
                if (note) {
                    note.is_pinned = true;
                }
            },
            onError: (error) => {
                console.error('Error pinning the note:', error);
            },
        }
    );
};

const isDialogVisible = ref(false);
</script>
