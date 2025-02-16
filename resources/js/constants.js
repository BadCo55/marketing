import axios from "axios";
import { ref, computed } from "vue";

// Define a reactive users variable
const users = ref([]);

// Function to fetch users once
const fetchUsers = async () => {
    if (users.value.length === 0) { // Prevent duplicate API calls
        try {
            const response = await axios.get(route("api.users.all"));
            users.value = response.data.users;
        } catch (error) {
            console.error("Error fetching users:", error);
        }
    }
};

fetchUsers();

// Region options for realtors professional info tab
export const regionOptions = [
    { label: 'Broward County', value: 'broward_county' },
    { label: 'Miami-Dade County', value: 'miami_dade_county' },
    { label: 'Palm Beach County', value: 'palm_beach_county' },
];

// Specialization options for realtors professional info tab
export const specializationOptions = [
    { label: 'Luxury Homes', value: 'luxury' },
    { label: 'First-Time Buyers', value: 'first_time_buyers' },
    { label: 'Commercial', value: 'commercial' },
];

export const preferredInspectorOptions = [
    { label: 'Chris Ansbaugh', value: 'Chris Ansbaugh' },
    { label: 'Jason Szpyra', value: 'Jason Szpyra' },
    { label: 'Rick Ansbaugh', value: 'Rick Ansbaugh' },
];

export const generalNotePriorityLevelOptions = [
    { label: 'Low', value: 'low' },
    { label: 'Medium', value: 'medium' },
    { label: 'High', value: 'high' },
];

export const usersWithNobodyOption = computed(() => [
    { formatted_full_name: 'Nobody', id: 0 }, // Add the "Nobody" option
    ...users.value
]);