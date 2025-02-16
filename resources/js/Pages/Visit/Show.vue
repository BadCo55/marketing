<template>
    <Layout>
        <div class="flex gap-5 p-8">
            <div class="flex">
                <Card class="flex mb-auto">
                    <template #content>
                        <div class="flex items-start gap-5">
                            <div class="flex flex-col gap-5">
                                <div class="w-48 overflow-hidden rounded-lg">
                                    <Image :src="office.image_url" class="w-36 " />
                                </div>
                                <Link
                                    :href="route('office.show', office.id)"
                                >
                                    <Button label="View Office Details" @click="showImageDialog = true" />
                                </Link>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold">{{ office.parent_company }}</h2>
                                <p class="text-blue-500">{{ office.office_phone }}</p>
                                <p>{{ office.street_address }} {{ office.unit_number ? '#' + office.unit_number : '' }}</p>
                                <p>{{ office.city }}, {{ office.state }} {{ office.zip_code }}</p>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
            <div class="flex-1">
                <Card>
                    <template #content>
                        <p><strong>Visit Date: </strong>{{ visit.formatted_visit_date }}</p>
                        <p><strong>Visitor: </strong>{{ visit.created_by.name }}</p>
                        <p><strong>Visit Duration: </strong>{{ visit.formatted_duration }}</p>
                        <div class="flex items-center gap-2">
                            <strong>Overall Rating: </strong>
                            <Rating v-model="visit.rating" readonly />
                        </div>
                        <p><strong>Purpose: </strong>{{ visit.formatted_purpose }}</p>
                        <p><strong>Initial Impressions: </strong>{{ visit.initial_impressions }}</p>
                        <div class="flex items-center gap-2">
                            <strong>Interest Level: </strong>
                            <Rating v-model="visit.interest_level" readonly />
                        </div>
                        <p><strong>Follow-Up Required: </strong>{{ visit.formatted_follow_up_required }}</p>
                        <div class="flex gap-2">
                            <strong>Materials Presented: </strong>
                            <ul class="">
                                <li v-for="(material, index) in visit.formatted_materials_presented" :key="index">{{ index + 1 }}. {{ material }}</li>
                            </ul>
                        </div>
                        <p><strong>Discussion Topics: </strong>{{ visit.discussion_topics }}</p>
                        <p><strong>General Notes: </strong>{{ visit.notes }}</p>
                        <p><strong>Next Action: </strong>{{ visit.next_action }}</p>
                        <p><strong>Next Action Date: </strong>{{ visit.formatted_next_action_date }}</p>

                    </template>
                </Card>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '@/Layouts/Layout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Card, Image, Button, Rating } from 'primevue';

const page = usePage();
const visit = page.props.visit;
const office = visit.office;

</script>