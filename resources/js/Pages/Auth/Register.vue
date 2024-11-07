<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const visible = ref(false);
const visible1 = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head :title="$t('Login')"/>

    <div>
        <Link :href="route('home')" class="d-flex align-items-center">
            <template v-if="$page.props.configuration.logo_system.value">
                <img
                    :src="'/storage/' + $page.props.configuration.logo_system.value"
                    :alt="$page.props.configuration.application_name.value"
                    class="mx-auto my-6"
                    height="90"
                />
            </template>
            <template v-else-if="$page.props.configuration.application_name.value">
                <h1 class="mx-auto my-6">{{ $page.props.configuration.application_name.value }}</h1>
            </template>
            <template v-else>
                <h1 class="mx-auto my-6">eCargo</h1>
            </template>
        </Link>

        <v-card
            class="mx-auto pa-12 pb-8"
            elevation="8"
            max-width="448"
            rounded="lg"
        >

            <v-card
                v-if="status"
                class="mb-12"
                color="surface-variant"
                variant="tonal"
            >
                <v-card-text class="text-medium-emphasis text-caption">
                    {{ status }}
                </v-card-text>
            </v-card>

            <form @submit.prevent="submit">
                <div class="text-subtitle-1 text-medium-emphasis">{{ $t('Name') }}</div>

                <v-text-field
                    density="compact"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                    v-model="form.name"
                    :error-messages="form.errors.name"
                />
                
                <div class="text-subtitle-1 text-medium-emphasis">{{ $t('Email') }}</div>

                <v-text-field
                    density="compact"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                    type="email"
                    v-model="form.email"
                    :error-messages="form.errors.email"
                />

                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">{{ $t('Password') }}</div>
                <v-text-field :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible ? 'text' : 'password'" density="compact" prepend-inner-icon="mdi-lock-outline"
                    variant="outlined" @click:append-inner="visible = !visible" v-model="form.password"
                    :error-messages="form.errors.password" />

                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">{{ $t('Confirm Password') }}</div>
                <v-text-field :append-inner-icon="visible1 ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible1 ? 'text' : 'password'" density="compact" prepend-inner-icon="mdi-lock-outline"
                    variant="outlined" @click:append-inner="visible1 = !visible1" v-model="form.password_confirmation"
                    :error-messages="form.errors.password_confirmation" />

                <v-btn
                    class="mb-8"
                    color="primary"
                    size="large"
                    variant="outlined"
                    block
                    type="submit"
                    :disabled="form.processing"
                >
                    {{ $t('Register') }}
                </v-btn>
            </form>

            <v-card-text class="text-center">
                <Link
                    class="text-blue text-decoration-none"
                    :href="route('home')"
                >
                    {{ $t('Back to homepage') }} <v-icon icon="mdi-chevron-left"></v-icon>
                </Link>
            </v-card-text>
        </v-card>
    </div>
</template>