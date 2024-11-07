<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Header from '@/Layouts/Frontend/Header.vue';
import Footer from '@/Layouts/Frontend/Footer.vue';

defineProps({
    title: String,
});
</script>

<template>
    <v-app>
        <Head :title="title" />
        <v-layout>
            <Header />
            <v-main>
                <slot />
                <Footer />
            </v-main>
        </v-layout>
    </v-app>
</template>
