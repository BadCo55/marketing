<template>
    <div class="h-screen overflow-hidden bg-surface-50 dark:bg-surface-950">
        <!-- MOBILE OVERLAY -->
        <div
            v-if="isSidebarVisible && !isLargeBreakpoint"
            class="fixed inset-0 z-30 bg-black/40"
            @click="closeSidebar"
        />

        <div class="flex h-full min-w-0">
            <!-- SIDEBAR -->
            <aside
                :class="[
                    'z-40 flex h-full shrink-0 flex-col border-r border-surface-800 bg-surface-900 text-surface-200 transition-all duration-300 ease-in-out',
                    !isLargeBreakpoint ? 'fixed inset-y-0 left-0' : 'relative',
                    !isLargeBreakpoint && !isSidebarVisible ? '-translate-x-full' : 'translate-x-0',
                ]"
                :style="{ width: `${currentSidebarWidth}px` }"
            >
                <div
                    class="flex h-16 shrink-0 items-center justify-center border-b border-surface-800 px-4"
                >
                    <img
                        src="/images/logo-dark.svg"
                        class="transition-all duration-300"
                        :class="isLargeBreakpoint && isSidebarCollapsed ? 'h-8' : 'h-11'"
                    />
                </div>

                <div class="flex min-h-0 flex-1 flex-col">
                    <nav class="flex-1 overflow-y-auto px-3 py-4">
                        <div
                            v-if="!isLargeBreakpoint || !isSidebarCollapsed"
                            class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-surface-500"
                        >
                            Main
                        </div>

                        <ul class="m-0 list-none space-y-1 p-0">
                            <li v-for="item in mainNavItems" :key="item.route">
                                <Link :href="route(item.route)" :class="getNavClass(item.route)">
                                    <i :class="[item.icon, 'shrink-0 text-sm']" />
                                    <span
                                        v-if="!isLargeBreakpoint || !isSidebarCollapsed"
                                        class="truncate text-sm font-medium"
                                    >
                                        {{ item.label }}
                                    </span>
                                </Link>
                            </li>
                        </ul>

                        <div
                            v-if="!isLargeBreakpoint || !isSidebarCollapsed"
                            class="mb-3 mt-6 px-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-surface-500"
                        >
                            Admin
                        </div>

                        <ul class="m-0 list-none space-y-1 p-0">
                            <li>
                                <Link
                                    :href="route('settings.index')"
                                    :class="getNavClass('settings.index')"
                                >
                                    <i class="pi pi-cog shrink-0 text-sm" />
                                    <span
                                        v-if="!isLargeBreakpoint || !isSidebarCollapsed"
                                        class="truncate text-sm font-medium"
                                    >
                                        Admin
                                    </span>
                                </Link>
                            </li>
                        </ul>
                    </nav>

                    <div class="border-t border-surface-800 p-3">
                        <Link
                            :href="route('profile.edit')"
                            class="flex items-center rounded-xl px-3 py-3 text-surface-300 transition-colors duration-150 hover:bg-surface-800 hover:text-white"
                            :class="
                                isLargeBreakpoint && isSidebarCollapsed ? 'justify-center' : 'gap-3'
                            "
                        >
                            <img
                                :src="page.props.auth.user.avatar_url"
                                class="h-9 w-9 shrink-0 rounded-full object-cover"
                            />
                            <div
                                v-if="!isLargeBreakpoint || !isSidebarCollapsed"
                                class="min-w-0 flex-1"
                            >
                                <p class="truncate text-sm font-medium text-white">
                                    {{ page.props.auth.user.first_name }}
                                    {{ page.props.auth.user.last_name }}
                                </p>
                                <p class="truncate text-xs text-surface-400">
                                    {{ page.props.auth.user.email }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>
            </aside>

            <!-- MAIN SHELL -->
            <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden">
                <!-- TOPBAR -->
                <header
                    class="flex h-16 shrink-0 items-center border-b border-surface-200 bg-surface-0/95 backdrop-blur dark:border-surface-800 dark:bg-surface-900/95"
                >
                    <div
                        class="grid w-full grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-4 px-4 md:px-6"
                    >
                        <!-- LEFT -->
                        <div class="flex items-center">
                            <Button
                                icon="pi pi-bars"
                                text
                                rounded
                                severity="secondary"
                                aria-label="Toggle sidebar"
                                @click="toggleSidebar"
                            />
                        </div>

                        <!-- CENTER -->
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="hidden max-w-[350px] flex-1 lg:block">
                                <span class="flex w-full items-center gap-2">
                                    <i class="pi pi-search" />
                                    <InputText fluid placeholder="Search..." class="w-full" />
                                </span>
                            </div>

                            <Button
                                v-if="isLargeBreakpoint"
                                label="Create Task"
                                icon="pi pi-plus-circle"
                                @click="openTaskDialog"
                            />
                        </div>

                        <!-- RIGHT -->
                        <div class="flex items-center gap-1 sm:gap-2">
                            <Button
                                class="lg:hidden"
                                icon="pi pi-plus-circle"
                                rounded
                                text
                                severity="secondary"
                                aria-label="Create task"
                                @click="openTaskDialog"
                            />

                            <Button
                                icon="pi pi-list-check"
                                rounded
                                text
                                severity="secondary"
                                aria-label="Tasks"
                                @click="toggleTasksPopover"
                            />

                            <button
                                type="button"
                                class="hidden items-center gap-2 rounded-xl px-2 py-2 transition-colors hover:bg-surface-100 dark:hover:bg-surface-800 lg:flex"
                                @click="toggleUserPopover"
                            >
                                <img
                                    :src="page.props.auth.user.avatar_url"
                                    class="h-8 w-8 rounded-full object-cover"
                                />
                                <div class="text-left leading-tight">
                                    <p
                                        class="text-sm font-medium text-surface-900 dark:text-surface-0"
                                    >
                                        {{ page.props.auth.user.first_name }}
                                    </p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">
                                        Account
                                    </p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <Popover ref="tasksPopover">
                        <div class="w-[320px]">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">Open Tasks</p>
                                    <p class="text-sm text-surface-500">
                                        {{ incompleteTasks.length }} pending
                                    </p>
                                </div>
                                <Link :href="route('task.index')">
                                    <Button label="View All" size="small" text />
                                </Link>
                            </div>

                            <div
                                v-if="!incompleteTasks.length"
                                class="rounded-xl bg-surface-50 px-4 py-4 text-sm text-surface-500 dark:bg-surface-800"
                            >
                                No open tasks.
                            </div>

                            <ul
                                v-else
                                class="m-0 flex max-h-[320px] list-none flex-col gap-2 overflow-y-auto p-0"
                            >
                                <li
                                    v-for="task in incompleteTasks"
                                    :key="task.id"
                                    class="rounded-xl border border-surface-200 bg-surface-50 px-3 py-3 dark:border-surface-700 dark:bg-surface-800"
                                >
                                    <Link :href="route('task.show', task.id)" class="block">
                                        <p class="font-medium text-surface-900 dark:text-surface-0">
                                            {{ task.title }}
                                        </p>
                                        <p
                                            v-if="task.formatted_due_date"
                                            class="mt-1 text-xs text-surface-500"
                                        >
                                            Due: {{ task.formatted_due_date }}
                                        </p>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </Popover>

                    <Popover ref="userPopover">
                        <div class="w-[280px]">
                            <div class="mb-3">
                                <p class="font-semibold">
                                    {{ page.props.auth.user.first_name }}
                                    {{ page.props.auth.user.last_name }}
                                </p>
                                <p class="text-sm text-surface-500">
                                    {{ page.props.auth.user.email }}
                                </p>
                            </div>

                            <Divider />

                            <ul class="m-0 flex list-none flex-col gap-1 p-0">
                                <li
                                    v-for="option in popoverOptions"
                                    :key="option.label"
                                    class="cursor-pointer rounded-lg px-3 py-2 transition-colors hover:bg-surface-100 dark:hover:bg-surface-800"
                                    @click="navigateTo(option)"
                                >
                                    <p class="font-medium">{{ option.label }}</p>
                                </li>

                                <Divider />

                                <li
                                    class="cursor-pointer rounded-lg px-3 py-2 text-red-500 transition-colors hover:bg-red-50 dark:hover:bg-red-950/30"
                                    @click="onLogout"
                                >
                                    Logout
                                </li>
                            </ul>
                        </div>
                    </Popover>
                </header>

                <!-- MAIN CONTENT -->
                <!-- CHANGED: main must scroll -->
                <main class="min-h-0 min-w-0 flex-1 overflow-y-auto overflow-x-hidden">
                    <Toast />
                    <NewTaskDialog :users="page.props.users" />

                    <!-- CHANGED: removed h-full so page content can grow naturally -->
                    <div class="min-w-0">
                        <slot :tasks="tasks" />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<script setup>
import InputText from 'primevue/inputtext'
import Popover from 'primevue/popover'
import NewTaskDialog from '@/Components/NewTaskDialog.vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { Toast, Button, Divider, useToast } from 'primevue'
import { watch, reactive, onMounted, ref, computed, onUnmounted } from 'vue'
import { useAppStore } from '@/Store/appStore'

const page = usePage()
const toast = useToast()
const appStore = useAppStore()

const successSound = new Audio('/sounds/success.mp3')
const errorSound = new Audio('/sounds/error.mp3')

const expandedSidebarWidth = 208
const collapsedSidebarWidth = 72

const tasks = computed(() => appStore.tasks ?? [])
const isSidebarVisible = ref(false)
const isLargeBreakpoint = ref(false)
const isSidebarCollapsed = ref(false)

const currentSidebarWidth = computed(() => {
    if (!isLargeBreakpoint.value) return expandedSidebarWidth
    return isSidebarCollapsed.value ? collapsedSidebarWidth : expandedSidebarWidth
})

const mainNavItems = [
    { label: 'Dashboard', route: 'dashboard', icon: 'pi pi-home' },
    { label: 'Tasks', route: 'task.index', icon: 'pi pi-check-circle' },
    { label: 'Routes', route: 'office-route.index', icon: 'pi pi-directions' },
    { label: 'Office Runs', route: 'office-run.index', icon: 'pi pi-play' },
    { label: 'Realtors', route: 'realtor.index', icon: 'pi pi-user' },
    { label: 'Offices', route: 'office.index', icon: 'pi pi-building' },
    { label: 'Visits', route: 'office-visit.index', icon: 'pi pi-map-marker' },
    { label: 'Reports', route: 'reports.index', icon: 'pi pi-chart-bar' },
    { label: 'Sequences', route: 'sequence.index', icon: 'pi pi-sitemap' },
]

const toggleSidebar = () => {
    if (isLargeBreakpoint.value) {
        isSidebarCollapsed.value = !isSidebarCollapsed.value
        return
    }

    isSidebarVisible.value = !isSidebarVisible.value
}

const closeSidebar = () => {
    isSidebarVisible.value = false
}

const navigateTo = (option) => {
    router.visit(route(option.route, option.id ? { user: option.id } : {}))
}

const flashState = reactive({
    success: page.props.flash.success || null,
    error: page.props.flash.error || null,
})

const openTaskDialog = () => {
    appStore.openTaskDialog()
}

const showFlashMessage = (key, severity, summary, sound) => {
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

        flashState[key] = null
    }
}

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

const userPopover = ref()
const toggleUserPopover = (event) => {
    userPopover.value.toggle(event)
}

const popoverOptions = [
    { label: 'Profile', route: 'profile.edit' },
    { label: 'Tasks', route: 'task.index', id: page.props.auth.user.id },
]

const onLogout = () => {
    router.post(route('logout'))
}

const tasksPopover = ref()
const toggleTasksPopover = (event) => {
    tasksPopover.value.toggle(event)
}

const assignedTasks = computed(() => page.props.auth.user.assigned_tasks ?? [])
const incompleteTasks = computed(() =>
    assignedTasks.value.filter((task) => task.status !== 'completed' && task.status !== 'cancelled')
)

const isActiveRoute = (routeName) => {
    return route().current(routeName)
}

const getNavClass = (routeName) => [
    'flex items-center rounded-xl transition-colors duration-150',
    isLargeBreakpoint.value && isSidebarCollapsed.value
        ? 'justify-center px-3 py-3'
        : 'gap-3 px-3 py-3',
    isActiveRoute(routeName)
        ? 'bg-surface-800 text-white'
        : 'text-surface-300 hover:bg-surface-800 hover:text-white',
]

const checkBreakpoint = () => {
    const wasLarge = isLargeBreakpoint.value
    isLargeBreakpoint.value = window.innerWidth >= 1024

    if (!isLargeBreakpoint.value) {
        isSidebarCollapsed.value = false
    }

    if (!wasLarge && isLargeBreakpoint.value) {
        isSidebarVisible.value = false
    }
}

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
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Your email has been verified!',
                life: 3000,
            })
            const newUrl = window.location.pathname
            history.replaceState({}, document.title, newUrl)
        }
    }, 100)

    checkBreakpoint()
    window.addEventListener('resize', checkBreakpoint)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkBreakpoint)
})
</script>
