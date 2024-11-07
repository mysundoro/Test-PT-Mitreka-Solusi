<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';

const props = defineProps({
    data: {
        type: Array,
        default: () => ([]),
    },
});

const search = ref('');
const tempTranslations = ref({});

// Transform the data to group by key and locales
const groupedTranslations = computed(() => {
    const grouped = {};
    props.data.forEach(translation => {
        if (!grouped[translation.key]) {
            grouped[translation.key] = {};
        }
        grouped[translation.key][translation.locale] = translation.value;

        // Initialize tempTranslations
        if (!tempTranslations.value[translation.key]) {
            tempTranslations.value[translation.key] = {};
        }
        tempTranslations.value[translation.key][translation.locale] = translation.value;
    });
    return grouped;
});

const filteredTranslations = computed(() => {
    if (!search.value) {
        return groupedTranslations.value;
    }
    const lowerSearch = search.value.toLowerCase();
    const filtered = {};
    Object.keys(groupedTranslations.value).forEach(key => {
        if (key.toLowerCase().includes(lowerSearch)) {
            filtered[key] = groupedTranslations.value[key];
        }
    });
    return filtered;
});

const create = () => {
    router.get(route('language.create'));
};

const updateTranslation = async (key, locale, value) => {
    try {
        const translation = props.data.find(t => t.key === key && t.locale === locale);
        
        // Make AJAX request using Axios
        const response = await axios.put(route('language.update', translation.id), {
            key: key,
            locale: locale,
            value: value
        });

        // Update the main state only after successful AJAX call
        if (response.status === 200) {
            groupedTranslations.value[key][locale] = value;
        }
    } catch (error) {
        console.error('Failed to update translation:', error);
    }
};
</script>

<template>
    <Main :title="$t('Language')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Language') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('dashboard')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                    <v-btn
                        v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('List Language')"
                        color="primary"
                        class="ml-2"
                        @click="create"
                    >
                        <v-icon>mdi-plus</v-icon> {{ $t('Add') }}
                    </v-btn>
                    <Link
                        v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Create Language')"
                        :href="route('language_code.index')"
                        class="ml-2"
                        as="button"
                    >
                        <v-btn color="secondary">
                            <v-icon>mdi-web</v-icon> {{ $t('Language Code') }}
                        </v-btn>
                    </Link>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-text-field density="compact" variant="outlined" prepend-inner-icon="mdi-table-search" v-model="search" :label="$t('Search')" />
                </v-col>
            </v-row>

            <v-card>
                <v-row>
                    <v-col cols="12">
                        <v-table fixed-header height="480px">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Locale</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(locales, key) in filteredTranslations" :key="key">
                                    <td>{{ key }}</td>
                                    <td>
                                        <div v-for="(value, locale) in locales" :key="locale">
                                            {{ locale }}: 
                                            <v-text-field
                                                density="compact"
                                                variant="outlined"
                                                v-model="tempTranslations[key][locale]"
                                                @blur="updateTranslation(key, locale, tempTranslations[key][locale])"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-col>
                </v-row>
            </v-card>
        </v-container>
    </Main>
</template>
