<script setup>
import { Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Frontend/Main.vue';
</script>

<template>
    <Main :title="$t('Simplifying Air, Sea, and Land Cargo Management for Global Businesses')">
        <v-container fluid fill-height class="pa-0 bg-blue-lighten-1">
            <v-row class="fill-height align-center pa-16">
                <v-col cols="12" md="6" class="text-white d-flex flex-column justify-center">
                    <h1 class="display-1 mb-4">{{ $page.props.configuration.application_name.value }} - {{ $t('The Ultimate Solution for Your Cargo and Logistics Business') }}</h1>
                    <p class="subtitle-1 mb-4">
                        {{ $t('home_desc') }}
                    </p>
                    <p class="subtitle-1 mb-4">
                        {{ $t('home_desc_2') }}
                    </p>
                </v-col>

                <v-col cols="12" md="6" class="text-center">
                    <v-img
                      src="/uploads/ecargo-mockup.png"
                      alt="eCargo Logistics Software Mockup"
                      height="100%"
                      contain
                    ></v-img>
                </v-col>
            </v-row>
        </v-container>
    </Main>
</template>
