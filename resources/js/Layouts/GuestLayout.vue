<template>
    <div class="flex min-h-screen flex-col items-center bg-surface-100 dark:bg-surface-950 pt-6 sm:justify-center sm:pt-0">
        <div>
            <img src="/images/logo-dark.svg" class="w-[350px] mb-10 dark:block hidden" />
            <img src="/images/logo-light.svg" class="w-[350px] mb-10 dark:hidden" />
        </div>

        <!-- <div
        class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div> -->
        <slot />
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
</script>