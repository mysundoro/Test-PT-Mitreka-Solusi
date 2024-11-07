<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head :title="$t('Forgot Password')"/>

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
                class="mb-12"
                color="surface-variant"
                variant="tonal"
            >
                <v-card-text class="text-medium-emphasis text-caption">
                    {{ $t('forgot_pass_desc') }}
                </v-card-text>
            </v-card>

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
                <div class="text-subtitle-1 text-medium-emphasis">{{ $t('Email') }}</div>

                <v-text-field
                    density="compact"
                    :placeholder="$t('Email Address')"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                    type="email"
                    v-model="form.email"
                    :error-messages="form.errors.email"
                />

                <v-btn
                    class="mb-8"
                    color="primary"
                    size="large"
                    variant="outlined"
                    block
                    type="submit"
                    :disabled="form.processing"
                >
                    {{ $t('Reset Password') }}
                </v-btn>
            </form>

            <v-card-text class="text-center">
                <Link
                    class="text-blue text-decoration-none"
                    :href="route('login')"
                >
                    {{ $t('Back to homepage') }} <v-icon icon="mdi-chevron-left"></v-icon>
                </Link>
            </v-card-text>
        </v-card>
    </div>
</template>