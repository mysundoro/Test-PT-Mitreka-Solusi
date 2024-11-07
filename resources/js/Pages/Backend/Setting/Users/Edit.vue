<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ([]),
    },
    roles: {
        type: Array,
        default: () => ([]),
    },
    userRole: {
        type: Object,
        default: () => ({}),
    },
});

const visible = ref(false);

const form = useForm({
    _method: 'PUT',
    name: props.data.name,
    email: props.data.email,
    password: '',
    roles: Object.values(props.userRole),
});


const update = () => {
    form.post(route('users.update', props.data.id), {
        errorBag: 'update',
        preserveScroll: true,
        resetOnSuccess: false,
    });
};
</script>

<template>
    <Main :title="$t('Edit User')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Edit User') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('users.index')" as="button">
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
                                    v-model="form.name"
                                    :error-messages="form.errors.name"
                                    :label="$t('Name')"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    type="email"
                                    v-model="form.email"
                                    :error-messages="form.errors.email"
                                    :label="$t('Email')"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-text-field
                                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                    :type="visible ? 'text' : 'password'"
                                    density="compact"
                                    variant="outlined"
                                    type="password"
                                    v-model="form.password"
                                    :error-messages="form.errors.password"
                                    :label="$t('Password')"
                                    :hint="$t('user_pass_hint')"
                                    @click:append-inner="visible = !visible"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    density="compact"
                                    variant="outlined"
                                    multiple
                                    :label="$t('Roles')"
                                    :items="props.roles"
                                    item-title="name"
                                    item-value="id"
                                    v-model="form.roles"
                                    :error-messages="form.errors.roles"
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
