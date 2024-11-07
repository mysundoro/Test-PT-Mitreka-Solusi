<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const visible = ref(false);
const visible1 = ref(false);

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head :title="$t('Reset Password')" />

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

                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">{{ $t('Password') }}</div>

                <v-text-field
                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible ? 'text' : 'password'"
                    density="compact"
                    :placeholder="$('Enter your new password')"
                    prepend-inner-icon="mdi-lock-outline"
                    variant="outlined"
                    type="password"
                    autocomplete="new-password"
                    required
                    v-model="form.password"
                    :error-messages="form.errors.password"
                    @click:append-inner="visible = !visible"
                />

                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">{{ $t('Confirm Password') }}</div>

                <v-text-field
                    :append-inner-icon="visible1 ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible1 ? 'text' : 'password'"
                    density="compact"
                    :placeholder="$t('Enter above password')"
                    prepend-inner-icon="mdi-lock-outline"
                    variant="outlined"
                    type="password"
                    autocomplete="new-password"
                    required
                    v-model="form.password_confirmation"
                    :error-messages="form.errors.password_confirmation"
                    @click:append-inner="visible1 = !visible1"
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
                    {{ $('Reset Password') }}
                </v-btn>
            </form>
        </v-card>
    </div>
</template>
