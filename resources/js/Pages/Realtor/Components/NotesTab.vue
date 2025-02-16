<template>
    <div class="flex justify-end mb-5">
        <Button label="Create a Note" @click="isDialogVisible = true"></Button>
    </div>
    <div class="flex flex-col gap-5 overflow-y-scroll max-h-[600px] border p-3 rounded-lg">
        <div v-if="props.realtor.notes.length" v-for="note in props.realtor.notes" :key="note.id" class="bg-surface-50 border dark:bg-surface-950 rounded-lg p-3">
            <div class="flex flex-row justify-between items-center mb-3">
                <!-- <p class="inline-flex items-center gap-2">
                    <span class="font-medium">Priority Level: </span>
                    <span>
                        <Message :severity="noteSeverity(note.formatted_priority)" size="small">{{ note.formatted_priority }}</Message>    
                        {{ note }}
                    </span>
                </p> -->
                <div></div>
                <div class="inline-flex gap-2" v-if="note.is_pinned">
                    <Message severity="success" size="small" icon="pi pi-thumbtack">{{ note.is_pinned ? 'Pinned' : '' }}</Message>
                    <Button icon="pi pi-times" @click="onRemovePin(note.id)" />
                </div>
                <div v-else>
                    <Button label="Add Pin" @click="onAddPin(note.id)" />
                </div>
            </div>
            <div class="flex flex-row justify-between items-center mb-3">
                <p class="inline-flex items-center gap-2" v-if="note.requires_follow_up">
                    <span class="font-medium">Requires Follow Up: </span>
                    <Message severity="info" size="small">{{ note.requires_follow_up ? 'Yes' : 'No' }}</Message>
                </p>
                <p v-if="note.assigned_to" class="inline-flex items-center gap-2">
                    <span class="font-medium">Assigned To: </span>
                    <span>
                        <Message severity="info" size="small">{{ note.assigned_to.formatted_full_name }}</Message>
                    </span>
                </p>
            </div>
            <div class="bg-surface-100 border dark:bg-surface-800 rounded p-2 mb-3">
                {{ note.note }}
            </div>
            <div class="flex flex-row items-center justify-between">
                <div>
                    <Button label="Remove Note" icon="pi pi-trash" @click="onDeleteNote(note.id)" />
                </div>
                <div>
                    <p>
                        {{ note.created_by.name }}
                    </p>
                    <p>
                        {{ note.formatted_created_at }}
                    </p>
                </div>
            </div>
        </div>
        <div v-else>
            No notes have been found.
        </div>
    </div>
    <NotesDialog v-model:visible="isDialogVisible" :realtor="props.realtor" @realtor="onUpdateRealtor" />
</template>

<script setup>
import NotesDialog from './NotesDialog.vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import { Button, Message, Textarea, RadioButtonGroup, RadioButton, Select,  } from 'primevue';
import { computed, ref } from 'vue';

const props = defineProps({
    users: Object,
    realtor: Object,
});

const page = usePage();
const emit = defineEmits(['realtor'])


const onUpdateRealtor = (realtor) => {
    emit('realtor', realtor);
}

const noteSeverity = (priorityLevel) => {
    switch (priorityLevel) {
        case 'Low':
            return 'info';
            break;
        case 'Medium':
            return 'warn';
            break;
        case 'High':
            return 'error';
            break;
        default: 
            return 'info';
    }
}

const sortNotes = () => {
  props.realtor.notes.sort((a, b) => {
    // Pinned notes first
    if (a.is_pinned !== b.is_pinned) {
      return b.is_pinned - a.is_pinned; // `true` (1) comes before `false` (0)
    }
    // Newest notes first (compare created_at timestamps)
    return new Date(b.created_at) - new Date(a.created_at);
  });
};

const onRemovePin = (noteId) => {
    router.put(route('realtor-note.update', { realtor_note: noteId }), 
    { request_type: 'remove_pin', is_pinned: false },
    {
        onSuccess: () => {
            // Find the note and update its `is_pinned` status
            const note = props.realtor.notes.find(note => note.id === noteId);
            if (note) {
                note.is_pinned = false;
            }

            // Sort the notes dynamically
            sortNotes();
        }, 
        onError: (error) => {
            console.error('Error pinning the note', error);
        }
    })
}

const onDeleteNote = (noteId) => {
    router.delete(route('realtor-note.destroy', { realtor_note: noteId }), {
        onSuccess: () => {
            const noteIndex = props.realtor.notes.findIndex(note => note.id === noteId);
            if (noteIndex !== -1) {
                // Remove the note from the array
                props.realtor.notes.splice(noteIndex, 1);
            }
            sortNotes();
        },
        onError: (error) => {
            console.error('Error deleting note:', error);
        }
    })
}

const onAddPin = async (noteId) => {
    router.put(route('realtor-note.update', { realtor_note: noteId }), 
    { request_type: 'add_pin', is_pinned: true },
    {
        onSuccess: () => {
            // Find the note and update its `is_pinned` status
            const note = props.realtor.notes.find(note => note.id === noteId);
            if (note) {
                note.is_pinned = true;
            }

            // Sort the notes dynamically
            sortNotes();
        }, 
        onError: (error) => {
            console.error('Error pinning the note', error);
        }
    })
}

const statusOptions = {
    in_person_visit: [
        { label: 'Scheduled', description: 'The visit has been planned, but not yet completed.' },
        { label: 'Completed', description: 'The step was successfully completed.' },
        { label: 'Canceled', description: 'The visit was canceled.' },
        { label: 'Rescheduled', description: 'The visit was rescheduled for another time.' },
    ],
    email: [
        { label: 'Drafted', description: 'The email is written but not sent yet.' },
        { label: 'Sent', description: 'The email was sent successfully.' },
        { label: 'Completed', description: 'The step was successfully completed.' },
        { label: 'Bounced', description: 'The email failed to deliver.' },
    ],
    phone_call: [
        { label: 'Scheduled', description: 'The call is planned, but not yet made.' },
        { label: 'Completed', description: 'The call was completed.' },
        { label: 'No Answer', description: 'The recipient did not answer the call.' },
        { label: 'Voicemail Left', description: 'A voicemail was left.' },
        { label: 'Follow-Up Needed', description: 'Further action is required.' },
        { label: 'Rescheduled', description: 'The call was rescheduled for a different time.' },
        { label: 'Declined', description: 'The recipient declined the call.' }
    ],
    snail_mail: [
        { label: 'Prepared', description: 'The mail has been prepared, but not yet sent.' },
        { label: 'Sent', description: 'The mail has been sent out.' },
        { label: 'Completed', description: 'The step was successfully completed.' },
    ],
    social_media_interaction: [
        { label: 'Messaged', description: 'The realtor was messaged on Social Media' },
        { label: 'Liked', description: 'We liked one of the realtors posts.' },
        { label: 'Commented', description: 'We commented on one of the realtors posts.' },
        { label: 'Completed', description: 'The step was successfully completed.' },
    ]
}

const dynamicStatusOptions = computed(() => {
    if (!selectedActiveSequence.value || !selectedActiveSequence.value.current_step) {
        return []; // Return an empty array when there is no active sequence or current step
    }

    const stepType = selectedActiveSequence.value.current_step.type;
    if (stepType === 'In-Person Visit') {
        return statusOptions.in_person_visit;
    } else if (stepType === 'Email') {
        return statusOptions.email;
    } else if (stepType === 'Phone Call') {
        return statusOptions.phone_call;
    } else if (stepType === 'Snail Mail') {
        return statusOptions.snail_mail;
    } else if (stepType === 'Social Media Interaction') {
        return statusOptions.social_media_interaction;
    }

    return []; // Return an empty array for any unexpected step types
});

const isDialogVisible = ref(false);

</script>