<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    }
});

const tab = ref('one');

// Define a ref for timezones
const timezones = ref([]);

const form = useForm({
    application_name: props.data.application_name?.value,
    logo_system: null,
    login_logo: null,
    favicon: null,
    maintenance_mode: props.data.maintenance_mode?.value == 0 ? false : true,
    timezone: props.data.timezone?.value,
});

// Fetch timezones from the WorldTimeAPI
const fetchTimezones = async () => {
    try {
        const response = await fetch('https://worldtimeapi.org/api/timezone');
        const data = await response.json();
        timezones.value = data; // Assign fetched timezones to the ref
    } catch (error) {
        console.error('Error fetching timezones:', error);
    }
};

// Call fetchTimezones when component mounts
onMounted(() => {
    fetchTimezones();
});

const store = () => {
    router.visit(route('configuration.store'), {
        method: 'post',
        data: form,
        preserveScroll: true,
        errorBag: 'store',
    });
};
</script>

<template>
    <Main :title="$t('Configuration')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Configuration') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('dashboard')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                </v-col>
            </v-row>

            <v-tabs
                v-model="tab"
                bg-color="primary"
                show-arrows
            >
                <v-tab value="one">{{ $t('Basic Configuration') }}</v-tab>
            </v-tabs>

            <v-tabs-window v-model="tab">
                <v-form @submit.prevent="store">
                    <v-tabs-window-item value="one">
                        <v-row class="mt-10">
                            <v-col cols="12">
                                <v-row>
                                    <v-col cols="12" md="4" >
                                        <v-text-field
                                            density="compact"
                                            variant="outlined"
                                            v-model="form.application_name"
                                            :error-messages="form.errors.application_name"
                                            :label="$t('Application Name') + '*'"
                                            required
                                        />
                                    </v-col>

                                    <v-col cols="12" md="4" >
                                        <v-file-input
                                            density="compact"
                                            variant="outlined"
                                            show-size
                                            v-model="form.logo_system"
                                            :error-messages="form.errors.logo_system"
                                            :label="$t('System Logo')"
                                            accept="image/*"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="4" >
                                        <v-file-input
                                            density="compact"
                                            variant="outlined"
                                            show-size
                                            v-model="form.login_logo"
                                            :error-messages="form.errors.login_logo"
                                            :label="$t('Login Logo')"
                                            accept="image/*"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="4" >
                                        <v-file-input
                                            density="compact"
                                            variant="outlined"
                                            show-size
                                            v-model="form.favicon"
                                            :error-messages="form.errors.favicon"
                                            :label="$t('Favicon')"
                                            accept="image/*"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="4" >
                                        <v-switch
                                            v-model="form.maintenance_mode"
                                            color="primary"
                                            :label="$t('Maintenance Mode')"
                                        />
                                    </v-col>

                                    <v-col cols="12" md="4" >
                                        <v-autocomplete
                                            v-model="form.timezone"
                                            :items="timezones"
                                            density="compact"
                                            variant="outlined"
                                            :error-messages="form.errors.timezone"
                                            :label="$t('Time Zone') + '*'"
                                            required
                                            item-title="name"
                                            item-value="value"
                                        />
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                    </v-tabs-window-item>

                    <v-row class="mt-10">
                        <v-col cols="12" class="d-flex justify-end">
                            <v-btn type="submit" color="primary" :disabled="form.processing">
                                {{ $t('Save') }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-form>
            </v-tabs-window>
        </v-container>
    </Main>
</template>
