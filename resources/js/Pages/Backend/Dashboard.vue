<script setup>
import Main from '@/Layouts/Backend/Main.vue';
</script>

<template>
    <Main :title="$t('Dashboard')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Dashboard') }}</h2>
                </v-col>
            </v-row>
        </v-container>
    </Main>
</template>
