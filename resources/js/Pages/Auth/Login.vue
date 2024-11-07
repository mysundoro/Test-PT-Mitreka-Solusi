<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const visible = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
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

                <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
                    {{ $t('Password') }}
                    <Link
                    v-if="canResetPassword"
                        class="text-caption text-decoration-none text-blue"
                        :href="route('password.request')"
                    >
                        {{ $t('Forgot Password?') }}
                    </Link>
                </div>

                <v-text-field
                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    :type="visible ? 'text' : 'password'"
                    density="compact"
                    :placeholder="$t('Enter your password')"
                    prepend-inner-icon="mdi-lock-outline"
                    variant="outlined"
                    type="password"
                    v-model="form.password"
                    :error-messages="form.errors.password"
                    @click:append-inner="visible = !visible"
                />

                <v-checkbox :label="$t('Remember me')" v-model="form.remember" />

                <v-btn
                    class="mb-8"
                    color="primary"
                    size="large"
                    variant="outlined"
                    block
                    type="submit"
                    :disabled="form.processing"
                >
                    {{ $t('Login') }}
                </v-btn>
            </form>

            <v-card-text class="text-center">
                <Link
                    class="text-blue text-decoration-none"
                    :href="route('register')"
                >
                    {{ $t('Register') }} <v-icon icon="mdi-chevron-right"></v-icon>
                </Link>
            </v-card-text>
            
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