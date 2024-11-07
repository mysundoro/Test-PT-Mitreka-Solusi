<script setup>
import { ref, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';

const props = defineProps({
    permissions: {
        type: Array,
        default: () => ([]),
    },
});

const form = useForm({
    category: '',
    parent_id: '',
    name: '',
    icon: '',
    url: '',
    id_permissions: ''
});

const parentOptions = ref([]);

const fetchParentOptions = async (category) => {
    if (!category) {
        parentOptions.value = [];
        return;
    }

    try {
        const response = await axios.get(route('menu.getMenu', category));
        parentOptions.value = response.data;
    } catch (error) {
        parentOptions.value = [];
    }
};

watch(() => form.category, (newCategory) => {
    fetchParentOptions(newCategory);
});

const store = () => {
    form.post(route('menu.store'), {
        errorBag: 'store',
        preserveScroll: true,
        resetOnSuccess: false,
    });
};
</script>

<template>
    <Main :title="$t('Create Menu')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Create Menu') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('menu.index')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                </v-col>
            </v-row>

            <v-form @submit.prevent="store">
                <v-row>
                    <v-col cols="12">
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-select
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.category"
                                    :error-messages="form.errors.category"
                                    :label="$t('Category') + '*'"
                                    :clearable="true"
                                    :items="[ { title: 'frontend' }, { title: 'backend' } ]"
                                    required
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.parent_id"
                                    :error-messages="form.errors.parent_id"
                                    :label="$t('Parent')"
                                    :clearable="true"
                                    :items="parentOptions"
                                    item-title="name"
                                    item-value="id"
                                    :disabled="!form.category"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.name"
                                    :error-messages="form.errors.name"
                                    :label="$t('Name') + '*'"
                                    required
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.icon"
                                    :error-messages="form.errors.icon"
                                    :label="$t('Icon')"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.url"
                                    :error-messages="form.errors.url"
                                    label="URL"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-autocomplete
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.id_permissions"
                                    :error-messages="form.errors.id_permissions"
                                    :label="$t('Permission')"
                                    :clearable="true"
                                    :items="props.permissions"
                                    item-title="name"
                                    item-value="id"
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
