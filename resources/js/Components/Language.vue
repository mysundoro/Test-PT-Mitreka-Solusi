<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const { props } = usePage();
const languages = props.language;

const selectedLanguage = ref(Object.keys(languages)[0]);

const changeLanguage = (code) => {
    selectedLanguage.value = code;
    // Optionally, trigger language change in your app
    document.documentElement.setAttribute('lang', code);
    
    router.visit(route('language.change', { locale: code }), {
        method: 'POST',
        onSuccess: () => {
            // Reload the page after the navigation is complete
            location.reload();
        },
        onError: (errors) => {
            console.error('Navigation error:', errors);
        }
    });
};
</script>

<template>
    <v-menu min-width="200px" rounded>
        <template v-slot:activator="{ props }">
            <v-btn icon v-bind="props">
                <v-icon>mdi-translate</v-icon>
            </v-btn>
        </template>
        <v-list>
            <v-list-item
                v-for="(language, index) in languages"
                :key="index"
                @click="changeLanguage(language.code)"
                :title="language.name"
            />
        </v-list>
    </v-menu>
</template>