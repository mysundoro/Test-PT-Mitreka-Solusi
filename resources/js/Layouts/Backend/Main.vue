<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import Header from '@/Layouts/Backend/Header.vue';
import Sidebar from '@/Layouts/Backend/Sidebar.vue';
import Footer from '@/Layouts/Backend/Footer.vue';

defineProps({
    title: String,
});

// Create a ref to manage the state of the navigation drawer
const drawer = ref(false);

// Create a ref to manage the state of the snackbar
const snackbar = ref(false);
const flashMessage = ref('');

// Function to load the drawer state from local storage
const loadDrawerState = () => {
    const savedState = localStorage.getItem('drawerState');
    if (savedState !== null) {
        drawer.value = JSON.parse(savedState);
    }
};

// Function to save the drawer state to local storage
const saveDrawerState = (state) => {
    localStorage.setItem('drawerState', JSON.stringify(state));
};

// Function to toggle the drawer state
const toggleDrawer = () => {
    drawer.value = !drawer.value;
};

// Load the drawer state and flash message when the component is mounted
onMounted(() => {
    loadDrawerState();
    const flash = usePage().props.flash.message;
    if (flash) {
        flashMessage.value = flash;
        snackbar.value = true;
    }
});

// Watch the drawer state and save it to local storage whenever it changes
watch(drawer, (newState) => {
    saveDrawerState(newState);
});
</script>

<template>
    <Head :title="title" />
    <v-layout>
        <Header @toggle-drawer="toggleDrawer" />
        <Sidebar :drawer="drawer" />
        <v-main class="d-flex align-center justify-center" style="min-height: 300px;">
            <slot />
        </v-main>
        <!-- Snackbar for flash messages -->
        <v-snackbar v-model="snackbar" :timeout="6000" top>
            {{ flashMessage }}
            <template v-slot:actions>
                <v-btn color="pink" variant="text" @click="snackbar = false">
                    Tutup
                </v-btn>
            </template>
        </v-snackbar>
        <Footer />
    </v-layout>
</template>
