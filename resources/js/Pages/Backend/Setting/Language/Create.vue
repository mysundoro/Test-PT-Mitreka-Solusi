<script setup>
import { useForm, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Main from '@/Layouts/Backend/Main.vue';

const { props } = usePage();
const languages = props.language;

const form = useForm({
    key: '',
    translations: languages.map(lang => ({ locale: lang.code, value: '' })),
});

// Function to store the form
const store = () => {
    form.post(route('language.store'), {
        errorBag: 'store',
        preserveScroll: true,
        resetOnSuccess: false,
    });
};

// Function to add a new translation
const addTranslation = () => {
    form.translations.push({ locale: '', value: '' });
};

// Function to remove a translation by index
const removeTranslation = (index) => {
    form.translations.splice(index, 1);
};

// Computed function to generate error messages and labels for dynamic inputs
const getTranslationError = (index, field) => {
    return computed(() => form.errors[`translations.${index}.${field}`]);
};

const getTranslationLabel = (locale) => {
    return locale ? `${locale.toUpperCase()} Value` : 'Value';
};
</script>

<template>
    <Main :title="$t('Create Language')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h1>{{ $t('Create Language') }}</h1>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('language.index')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                </v-col>
            </v-row>

            <!-- Form to submit translations -->
            <v-form @submit.prevent="store">
                <v-row>
                    <v-col cols="12">
                        <v-text-field
                            density="compact"
                            variant="outlined"
                            v-model="form.key"
                            :error-messages="form.errors.key"
                            label="Key"
                        />
                    </v-col>
                    
                    <!-- Loop through dynamic translations -->
                    <v-col cols="12" v-for="(translation, index) in form.translations" :key="index">
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    density="compact"
                                    variant="outlined"
                                    v-model="translation.locale"
                                    :items="languages.map(lang => lang.code)"
                                    :error-messages="getTranslationError(index, 'locale').value"
                                    label="Locale"
                                    disabled
                                />
                            </v-col>
                            <v-col cols="12" md="8">
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    v-model="translation.value"
                                    :error-messages="getTranslationError(index, 'value').value"
                                    :label="getTranslationLabel(translation.locale)"
                                />
                            </v-col>
                        </v-row>
                    </v-col>
                </v-row>

                <!-- Submit button -->
                <v-row>
                    <v-col cols="12">
                        <v-btn type="submit" color="primary" :disabled="form.processing">
                            {{ $t('Save') }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-container>
    </Main>
</template>
