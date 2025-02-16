<template>
    <Head title="Sequence " />
    <Layout>
        <div class="p-8 flex gap-5">
            <Card class="mb-5 w-1/3 self-start">
                <template #content>
                    <p class="font-bold text-2xl">{{ sequence.name }}</p>
                    <p><span class="font-bold">Target:</span> {{ sequence.target_audience }}</p>
                    <p><span class="font-bold">Description:</span> {{ sequence.description }}</p>
                </template>
            </Card>
            <Card class="w-full">
                <template #content>
                    <div v-for="step in steps" :key="step.id" class="border rounded p-5">
                        <p class="text-2xl">
                            <span class="font-bold">Step {{ step.step_number }}: </span>
                            {{ step.type }}
                        </p>
                        <div class="flex">
                            <div class="flex-1">
                                <p><span class="font-medium">Interval from Initial Contact:</span> {{ step.interval }} day(s)</p>
                                <p>
                                    {{ step.description }}
                                </p>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-xl ms-2 mb-2">Template</p>
                                <div class="border rounded p-3">
                                    {{ step.template ? step.template : 'No template found.' }}
                                </div>
                            </div>
                        </div>
                    </div> 
                </template>
            </Card>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card } from 'primevue';
const page = usePage();
const sequence = ref(page.props.sequence);
const steps = ref(sequence.value.steps);
</script>