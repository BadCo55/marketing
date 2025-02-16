<template>
    <div
        class="min-h-screen flex max-w-screen relative lg:static bg-surface-50 dark:bg-surface-950"
    >
        <!-- SIDEBAR -->
        <div
            id="app-sidebar-3"
            :class="[
                'w-[200px] bg-surface-900 h-screen flex-shrink-0 fixed top-0 left-0 z-20 border-r border-surface-800 select-none overflow-y-auto transition-transform duration-300',
                isSidebarVisible ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0', // Always visible on large screens
            ]"
        >
            <!-- SIDEBAR OPTIONS -->
            <div class="flex flex-col h-full">
                <div class="flex items-center px-8 flex-shrink-0 h-[80px]">
                    <img src="/images/logo-dark.svg" class="h-[60px] mx-auto" />
                </div>
                <div class="overflow-y-auto">
                    <ul class="list-none p-4 m-0 space-y-1">
                        <li>
                            <Link
                                :href="route('dashboard')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('dashboard')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                                class="flex items-center cursor-pointer p-4 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                            >
                                <i class="pi pi-home mr-2" />
                                <span class="font-medium">Dashboard</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('task.index')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('task.index')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                                class="flex items-center cursor-pointer p-4 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                            >
                                <i class="pi pi-check-circle mr-2" />
                                <span class="font-medium">Tasks</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('realtor.index')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('realtor.index')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                                class="flex items-center cursor-pointer p-4 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                            >
                                <i class="pi pi-user mr-2" />
                                <span class="font-medium">Realtors</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('office.index')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('office.index')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                                class="flex items-center cursor-pointer p-4 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                            >
                                <i class="pi pi-building mr-2" />
                                <span class="font-medium">Offices</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('office-visit.index')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('office-visit.index')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                                class="flex items-center cursor-pointer p-4 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                            >
                                <i class="pi pi-check-circle mr-2" />
                                <span class="font-medium">Visits</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.index')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('reports.index')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                            >
                                <i class="pi pi-chart-bar mr-2" />
                                <span class="font-medium">Reports</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('sequence.index')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('sequence.index')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                                class="flex items-center cursor-pointer p-4 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                            >
                                <i class="pi pi-sitemap mr-2" />
                                <span class="font-medium">Sequences</span>
                            </Link>
                        </li>
                    </ul>
                </div>
                <div class="mt-auto">
                    <ul class="list-none p-4 m-0">
                        <li>
                            <Link
                                :href="route('settings.index')"
                                :class="[
                                    'flex items-center cursor-pointer p-4 rounded-border duration-150 transition-colors',
                                    isActiveRoute('settings.index')
                                        ? 'bg-surface-800 text-white'
                                        : 'hover:bg-surface-800 text-surface-300 hover:text-white',
                                ]"
                                class="flex items-center cursor-pointer p-4 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                            >
                                <i class="pi pi-cog mr-2" />
                                <span class="font-medium">Admin</span>
                            </Link>
                        </li>
                    </ul>
                    <hr class="mb-4 mx-4 border-t border-0 border-surface-800" />
                    <Link
                        class="mx-2 my-4 flex items-center cursor-pointer py-4 px-2 rounded-border hover:bg-surface-800 text-surface-300 hover:text-white duration-150 transition-colors"
                        :href="route('profile.edit')"
                    >
                        <img
                            :src="page.props.auth.user.avatar_url"
                            class="mr-2 w-7 h-7 rounded-full"
                        />
                        <span class="font-medium"
                            >{{ page.props.auth.user.first_name }}
                            {{ page.props.auth.user.last_name }}</span
                        >
                    </Link>
                </div>
            </div>
        </div>
        <div class="min-h-screen flex flex-col flex-auto lg:ms-[200px]">
            <div
                class="h-[80px] flex justify-between items-center px-8 bg-surface-0 dark:bg-surface-900 border-b border-surface fixed top-0 left-0 lg:left-[200px] right-0 z-20"
            >
                <div class="flex gap-2 items-center">
                    <a
                        @click="toggleSidebar"
                        class="cursor-pointer block lg:hidden text-surface-700 dark:text-surface-100 mr-4 mt-1 hover:bg-surface-300 hover:text-surface-50 p-2 rounded"
                    >
                        <i class="pi pi-bars text-4xl" />
                    </a>
                    <InputText placeholder="Search..." />
                    <div v-if="isLargeBreakpoint">
                        <Button
                            label="Create Task"
                            icon="pi pi-plus-circle"
                            @click="openTaskDialog"
                        />
                    </div>
                </div>
                <a
                    class="cursor-pointer block lg:hidden text-surface-700 dark:text-surface-100"
                    @click="toggleUserPopover"
                >
                    <i
                        class="pi pi-ellipsis-v text-2xl p-2 hover:bg-surface-300 hover:text-surface-50 rounded"
                    />
                </a>
                <ul
                    class="list-none p-0 m-0 hidden lg:flex lg:items-center select-none lg:flex-row border lg:border-0 border-surface right-0 top-full z-10 shadow lg:shadow-none absolute lg:static bg-surface-0 dark:bg-surface-900"
                >
                    <li>
                        <a
                            class="flex p-4 lg:px-4 lg:py-2 items-center text-surface-600 dark:text-surface-200 hover:text-surface-900 dark:hover:text-surface-0 hover:bg-surface-100 dark:hover:bg-surface-700 font-medium rounded-border cursor-pointer duration-150 transition-colors"
                            @click="toggleTasksPopover"
                        >
                            <OverlayBadge
                                severity="danger"
                                class="mr-2 lg:mr-0"
                                :value="incompleteTasksCount"
                            >
                                <i
                                    class="pi pi-list-check text-base lg:!text-2xl leading-none mr-2 lg:mr-0"
                                />
                            </OverlayBadge>
                            <span class="block lg:hidden font-medium">Tasks</span>
                            <Popover ref="tasksPopover">
                                <div class="flex flex-col gap-4">
                                    <div>
                                        <ul class="list-none p-0 m-0 gap-1 flex flex-col">
                                            <li
                                                v-if="incompleteTasks"
                                                class="flex items-center gap-2 px-2 py-2 hover:bg-emphasis cursor-pointer rounded bg-surface-100/50"
                                            >
                                                <p class="font-medium">No tasks!</p>
                                            </li>
                                            <li
                                                v-else
                                                class="flex items-center gap-2 px-2 py-2 hover:bg-emphasis cursor-pointer rounded bg-surface-100/50"
                                                v-for="(task, index) in incompleteTasks"
                                                :key="index"
                                            >
                                                <Link :href="route('task.show', task.id)">
                                                    <p class="font-medium">{{ task.title }}</p>
                                                    <p
                                                        class="text-xs text-end w-36"
                                                        v-if="task.formatted_due_date"
                                                    >
                                                        Due: {{ task.formatted_due_date }}
                                                    </p>
                                                </Link>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </Popover>
                        </a>
                    </li>
                    <!-- <li>
                        <a
                            class="flex p-4 lg:px-4 lg:py-2 items-center text-surface-600 dark:text-surface-200 hover:text-surface-900 dark:hover:text-surface-0 hover:bg-surface-100 dark:hover:bg-surface-700 font-medium rounded-border cursor-pointer duration-150 transition-colors"
                        >
                            <i
                                class="pi pi-inbox text-base lg:!text-2xl leading-none mr-2 lg:mr-0"
                            />
                            <span class="block lg:hidden font-medium">Inbox</span>
                        </a>
                    </li> -->
                    <!-- <li>
                        <a
                            class="flex p-4 lg:px-4 lg:py-2 items-center text-surface-600 dark:text-surface-200 hover:text-surface-900 dark:hover:text-surface-0 hover:bg-surface-100 dark:hover:bg-surface-700 font-medium rounded-border cursor-pointer duration-150 transition-colors"
                        >
                            <OverlayBadge severity="danger" class="mr-2 lg:mr-0">
                                <i class="pi pi-bell text-base lg:!text-2xl leading-none" />
                            </OverlayBadge>
                            <span class="block lg:hidden font-medium">Notifications</span>
                        </a>
                    </li> -->
                    <li class="border-t border-surface lg:border-t-0">
                        <a
                            class="flex p-4 lg:px-4 lg:py-2 items-center hover:bg-surface-100 dark:hover:bg-surface-700 font-medium rounded-border cursor-pointer duration-150 transition-colors"
                            @click="toggleUserPopover"
                        >
                            <img
                                :src="page.props.auth.user.avatar_url"
                                class="mr-4 lg:mr-0 w-8 h-8 rounded-full"
                            />
                            <div class="block lg:hidden">
                                <div class="text-surface-900 dark:text-surface-0 font-medium">
                                    Josephine Lillard
                                </div>
                                <span
                                    class="text-surface-600 dark:text-surface-200 font-medium text-sm"
                                    >Marketing Specialist</span
                                >
                            </div>
                        </a>
                        <Popover ref="userPopover">
                            <div class="flex flex-col gap-4">
                                <div>
                                    <div class="flex flex-col dark:text-surface-400 font-medium">
                                        <p>
                                            {{ page.props.auth.user.first_name }}
                                            {{ page.props.auth.user.last_name }}
                                        </p>
                                        <p>
                                            {{ page.props.auth.user.email }}
                                        </p>
                                    </div>
                                    <Divider />
                                    <ul class="list-none p-0 m-0 flex flex-col">
                                        <!-- <li
                                            class="flex items-center gap-2 px-2 py-2 hover:bg-emphasis cursor-pointer rounded-border"
                                            v-for="(option, index) in popoverOptions"
                                            :key="index"
                                        >
                                            <Link
                                                :href="
                                                    route(option.route, option.id ? { user: option.id } : '')
                                                "
                                            >
                                                <p class="font-medium">{{ option.label }}</p>
                                            </Link>
                                        </li> -->
                                        <li
                                            v-for="(option, index) in popoverOptions"
                                            :key="index"
                                            class="flex items-center gap-2 px-2 py-2 hover:bg-emphasis cursor-pointer rounded-border"
                                            @click="navigateTo(option)"
                                        >
                                            <p class="font-medium">{{ option.label }}</p>
                                        </li>
                                        <Divider />
                                        <li
                                            class="flex items-center gap-2 px-2 py-2 hover:bg-emphasis cursor-pointer rounded-border"
                                            @click="onLogout"
                                        >
                                            Logout
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </Popover>
                    </li>
                </ul>
            </div>
            <main class="mt-[80px]">
                <Toast />
                <NewTaskDialog :users="page.props.users" />
                <slot :tasks="tasks" />
            </main>
        </div>
    </div>
</template>
<script setup>
import InputText from 'primevue/inputtext'
import OverlayBadge from 'primevue/overlaybadge'
import Popover from 'primevue/popover'
import NewTaskDialog from '@/Components/NewTaskDialog.vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { useToast, Toast, Button, Divider, Drawer } from 'primevue'
import { watch, reactive, onMounted, ref, computed } from 'vue'
import { useAppStore } from '@/Store/appStore'

const page = usePage()
const toast = useToast()
const appStore = useAppStore()
const successSound = new Audio('/storage/sounds/success.mp3')
const errorSound = new Audio('/storage/sounds/error.mp3')
const tasks = computed(() => appStore.tasks)
const isSidebarVisible = ref(false)
const toggleSidebar = () => {
    isSidebarVisible.value = !isSidebarVisible.value
}

const assignedTasksCount = ref(page.props.auth.user.assigned_tasks_count)

const navigateTo = (option) => {
    router.visit(route(option.route, option.id ? { user: option.id } : {}));
};

// Local state for flash messages
const flashState = reactive({
    success: page.props.flash.success || null,
    error: page.props.flash.error || null,
})

/**
 * Handle task dialog
 */
const openTaskDialog = () => {
    appStore.openTaskDialog()
}

// Helper to handle displaying flash messages
const showFlashMessage = (key, severity, summary, sound) => {
    console.log('calling function');
    const message = flashState[key]
    if (message) {
        toast.add({
            severity,
            summary,
            detail: message,
            life: 3000,
        })

        if (sound) {
            sound.play().catch((err) => console.error('Error playing sound:', err))
        }

        // Clear local flash state
        flashState[key] = null
    }
}

// Watch for flash.success changes
watch(
    () => page.props.flash.success,
    (newVal) => {
        if (newVal) {
            flashState.success = newVal
            showFlashMessage('success', 'success', 'Success', successSound)
            page.props.flash.success = null
        }
    }
)

// Watch for flash.error changes
watch(
    () => page.props.flash.error,
    (newVal) => {
        if (newVal) {
            flashState.error = newVal
            showFlashMessage('error', 'error', 'Error', errorSound)
            page.props.flash.error = null
        }
    }
)

onMounted(() => {
    setTimeout(() => {
        if (flashState.success) {
            showFlashMessage('success', 'success', 'Success', successSound)
            page.props.flash.success = null
        }

        if (flashState.error) {
            showFlashMessage('error', 'error', 'Error', errorSound)
            page.props.flash.error = null
        }

        const urlParams = new URLSearchParams(window.location.search)
        if (urlParams.get('verified') == '1') {
            toast.add({ severity: 'success', summary: 'Success', detail: 'Your email has been verified!', life: 3000 });
            // ✅ Remove 'verified' from the URL without reloading the page
            const newUrl = window.location.pathname
            history.replaceState({}, document.title, newUrl)
        }

    }, 100) // Adjust delay as needed
})


/**
 * Handle User Popover
 */
const userPopover = ref()
const toggleUserPopover = (event) => {
    userPopover.value.toggle(event)
}
const popoverOptions = [
    { label: 'Profile', route: 'profile.edit' },
    // { label: 'Notifications', route: 'realtor.index', id: null },
    { label: 'Tasks', route: 'task.index', id: page.props.auth.user.id, },
]
const onLogout = () => {
    router.post(route('logout'))
}

/**
 * Handle tasks popover
 */
const tasksPopover = ref()
const toggleTasksPopover = (event) => {
    tasksPopover.value.toggle(event)
}
const assignedTasks = ref(page.props.auth.user.assigned_tasks)
const incompleteTasks = assignedTasks.value.filter(
    (task) => task.status != 'completed' && task.status != 'cancelled'
)
const incompleteTasksCount = incompleteTasks.length

/**
 * Check active route for hover classes
 */
const isActiveRoute = (routeName) => {
    return route().current(routeName)
}
</script>
