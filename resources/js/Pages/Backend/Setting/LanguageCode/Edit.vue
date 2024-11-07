<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ([]),
    }
});

const form = useForm({
    _method: 'PUT',
    code: props.data.code,
    name: props.data.name,
});


const update = () => {
    form.post(route('language_code.update', props.data.id), {
        errorBag: 'update',
        preserveScroll: true,
        resetOnSuccess: false,
    });
};
</script>

<template>
    <Main :title="$t('Edit Language Code')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Edit Language Code') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('language_code.index')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                </v-col>
            </v-row>

            <v-form @submit.prevent="update">
                <v-row>
                    <v-col cols="12">
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.code"
                                    :error-messages="form.errors.code"
                                    :label="$t('Code') + '*'"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.name"
                                    :error-messages="form.errors.name"
                                    :label="$t('Name') + '*'"
                                />
                            </v-col>
                        </v-row>
                    </v-col>
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
