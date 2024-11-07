<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const passwordInput = ref(null);
const visible = ref(false);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();

            passwordInput.value.focus();
        },
    });
};
</script>

<template>
    <Head :title="$t('Area Dilindungi')" />

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
                    {{ $t('confirm_pass_desc') }}
                </v-card-text>
            </v-card>

            <form @submit.prevent="submit">
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
                    ref="passwordInput"
                    autocomplete="current-password"
                    required
                    v-model="form.password"
                    :error-messages="form.errors.password"
                    @click:append-inner="visible = !visible"
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
                    {{ $t('Confirm') }}
                </v-btn>
            </form>
        </v-card>
    </div>
</template>
