<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import Language from '@/Components/Language.vue';

// Fetch the menu items from $page.props
const menuItems = usePage().props.menu;

// Define the category you want to filter by (e.g., 'frontend')
const categoryFilter = 'frontend';

// Filter the menu items based on the category and organize them into parent-child structure
const filteredMenuItems = computed(() => {
    const items = menuItems.filter(item => item.category === categoryFilter);

    // Build a parent-child menu structure using parent_id
    const menuTree = items.reduce((acc, item) => {
        if (!item.parent_id) {
            // Parent item
            acc.push({ ...item, subItems: [] });
        } else {
        // Child item, find the parent
            const parent = acc.find(parentItem => parentItem.id === item.parent_id);
            if (parent) {
                parent.subItems.push(item);
            }
        }
        return acc;
    }, []);
    
    return menuTree;
});

// Method to handle navigation
const navigate = (menuUrl) => {
    try {
        router.get(route(menuUrl));
    } catch (error) {
        window.location.href = menuUrl;
    }
};
</script>

<template>
    <v-app-bar class="position-fixed" app flat elevation="4" color="white" dark>
        <!-- Row container for logo, menu, and buttons -->
        <v-row align="center" class="flex-grow-1" no-gutters>
            <!-- Logo Section -->
            <v-col cols="2" class="d-flex justify-center">
                <v-img
                    :src="'/storage/' + $page.props.configuration.logo_system.value"
                    :height="64"
                    contain
                />
            </v-col>
  
            <!-- Spacer between Logo and Menu -->
            <v-spacer />
  
            <!-- Centered menu (responsive) -->
            <v-col cols="auto" class="d-none d-md-flex">
                <v-row>
                    <template v-for="(item, index) in filteredMenuItems" :key="index">
                        <v-col cols="auto">
                            <v-menu
                                v-if="item.subItems && item.subItems.length > 0"
                                :items="item.subItems"
                                :title="$t(item.name)"
                                offset-y
                                :close-on-content-click="false"
                                :prepend-icon="item.icon"
                            >
                                <v-list>
                                    <v-list-item
                                        v-for="(subItem, subIndex) in item.subItems"
                                        :key="subIndex"
                                        @click="navigate(subItem.url)"
                                    >
                                        <v-icon left>{{ subItem.icon }}</v-icon>
                                        {{ $t(subItem.name) }}
                                    </v-list-item>
                                </v-list>
                            </v-menu>

                            <v-btn v-else text
                                color="primary"
                                class="ml-2"
                                @click="navigate(item.url)">
                                <v-icon left>{{ item.icon }}</v-icon> {{ $t(item.name) }}
                            </v-btn>
                        </v-col>
                    </template>
                </v-row>
            </v-col>

            <!-- Spacer between Menu and Buttons -->
            <v-spacer />
  
            <!-- Right Section: Language Button and Auth Button -->
            <v-col cols="auto" class="d-flex justify-end align-center">
                <!-- Mobile menu -->
                <v-col cols="auto" class="d-flex d-md-none">
                    <v-menu>
                        <template v-slot:activator="{ props }">
                            <v-btn icon="mdi-menu" variant="text" v-bind="props"></v-btn>
                        </template>

                        <v-list open-strategy="single">
                            <template v-for="(item, index) in filteredMenuItems" :key="index">
                                <v-list-item
                                    v-if="item.subItems && item.subItems.length > 0"
                                    @click="navigate(item.url)"
                                    :prepend-icon="item.icon"
                                    :title="$t(item.name)"
                                    :class="{
                                        'bg-primary white--text': isActive(item.url)
                                    }"
                                >
                                    <v-menu
                                        :items="item.subItems"
                                        offset-y
                                        :close-on-content-click="false"
                                    >
                                        <template #activator="{ props }">
                                            <v-btn text v-bind="props">{{ $t(item.name) }}</v-btn>
                                        </template>
                                        <v-list>
                                            <v-list-item
                                                v-for="(subItem, subIndex) in item.subItems"
                                                :key="subIndex"
                                                @click="navigate(subItem.url)"
                                            >
                                                {{ $t(subItem.name) }}
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </v-list-item>
                                <v-list-item
                                    v-else
                                    @click="navigate(item.url)"
                                >
                                    {{ $t(item.name) }}
                                </v-list-item>
                            </template>
                        </v-list>
                    </v-menu>
                </v-col>
                <Language />
                <v-btn v-if="$page.props.auth.user" text @click="navigate(route('dashboard'))">
                    {{ $t('Dashboard') }}
                </v-btn>
                <v-btn v-else text @click="navigate(route('login'))">
                    {{ $t('Login') }}
                </v-btn>
            </v-col>
        </v-row>
    </v-app-bar>
</template>
