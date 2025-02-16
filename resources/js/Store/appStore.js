import { defineStore } from "pinia";
import { ref } from "vue";

export const useAppStore = defineStore('appStore', {
    state: () => ({
        taskDialogIsVisible: false,
        taskData: {
            title: '',
            description: '',
            assigned_to: '',
            due_date: '',
            status: '',
        },
        tasks: [],
    }),
    actions: {
        openTaskDialog(initialData = null) {
            this.taskDialogIsVisible = true;
            if (initialData) {
                this.taskData = { ...this.taskData, ...initialData };
            }
        },
        closeTaskDialog() {
            this.taskDialogIsVisible = false;
            this.resetTaskData();
        },
        setTasks(data) {
            this.tasks = data;
        },
        addTask(newTask) {
            this.tasks.unshift(newTask);
        },
        resetTaskData() {
            this.taskData = {
                title: '',
                description: '',
                assigned_to: '',
                due_date: '',
                status: '',
            };
        },
    },
});