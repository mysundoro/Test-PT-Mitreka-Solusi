<template>
    <v-dialog :model-value="show" @update:model-value="updateShow" max-width="500">
        <v-card
            append-icon="$close"
            elevation="16"
            :title="$t('Warning')"
        >
            <template v-slot:append>
                <v-btn icon="$close" variant="text" @click="cancel"></v-btn>
            </template>

            <v-divider></v-divider>

            <div class="py-12 text-center">
                <v-icon
                    class="mb-6"
                    color="warning"
                    icon="mdi-delete-alert"
                    size="128"
                />

                <div v-html="$t('delete_warning')"></div>
            </div>

            <v-divider></v-divider>

            <div class="pa-4 d-flex flex-column align-center">
                <div class="d-flex justify-center flex-column flex-md-row">
                    <v-btn color="warning" class="ml-2 mb-2 mb-md-0" @click="cancel">
                        <v-icon class="mr-2">mdi-cancel</v-icon> {{ $t('Cancel') }}
                    </v-btn>
                    <v-btn color="primary" class="ml-2" @click="confirm">
                        <v-icon class="mr-2">mdi-thumb-up</v-icon> {{ $t('Delete') }}
                    </v-btn>
                </div>
            </div>
        </v-card>
    </v-dialog>
</template>

<script setup>
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:show', 'confirm']);

const updateShow = (value) => {
    emit('update:show', value);
};

const cancel = () => {
    emit('update:show', false);
};

const confirm = () => {
    emit('confirm');
    emit('update:show', false);
};
</script>
