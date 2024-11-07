<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';
import DeleteDialog from '@/Components/DeleteDialog.vue';

const search = ref('');
const dialogDelete = ref(false);
const selectedItem = ref(null);
const items = ref([]);

const fetchData = async () => {
    try {
        const response = await axios.get(route('language_code.data'));
        items.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

// Fetch data on component mount
onMounted(fetchData);

const filteredData = computed(() => {
    if (!search.value) {
        return items.value;
    }
    return items.value.filter(item => 
        item.code.toLowerCase().includes(search.value.toLowerCase()) ||
        item.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const create = () => {
    router.get(route('language_code.create'));
};

const editItem = (item) => {
    router.get(route('language_code.edit', item.id));
};

const createLangItem = (item) => {
    router.visit(route("language_code.create_language", item.code), { method: 'post' });
};

const confirmDeleteItem = (item) => {
    selectedItem.value = item;
    dialogDelete.value = true;
};

const deleteItemConfirm = () => {
    if (selectedItem.value) {
        router.visit(route("language_code.destroy", selectedItem.value.id), { method: 'delete' });
    }
    dialogDelete.value = false;
    selectedItem.value = null;
};
</script>

<template>
    <Main :title="$t('Language Code')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Language Code') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('language.index')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                    <v-btn
                        v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Create Language')"
                        color="primary"
                        class="ml-2"
                        @click="create"
                    >
                        <v-icon>mdi-plus</v-icon> {{ $t('Add') }}
                    </v-btn>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-text-field density="compact" variant="outlined" prepend-inner-icon="mdi-table-search" v-model="search" :label="$t('Search')" />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="[
                            { title: 'No', value: 'no', width: '50px' },
                            { title: $t('Code'), value: 'code' },
                            { title: $t('Name'), value: 'name' },
                            { title: $t('Actions'), value: 'actions', width: '150px', sortable: false }
                        ]"
                        :items="filteredData"
                        class="elevation-1"
                    >
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                v-if="(item.code !== 'en') && !item.exists_in_language && $page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Create Language')"
                                class="me-2"
                                size="small"
                                @click="createLangItem(item)"
                            >
                                mdi-google-translate
                            </v-icon>
                            <v-icon
                                v-if="(item.code !== 'en') && $page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Edit Language')"
                                class="me-2"
                                size="small"
                                @click="editItem(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                v-if="(item.code !== 'en') && $page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Delete Language')"
                                size="small"
                                @click="confirmDeleteItem(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </template>
                        <template v-slot:item.no="{ index }">
                            {{ index + 1 }}
                        </template>
                    </v-data-table>

                    <DeleteDialog 
                        :show="dialogDelete" 
                        @update:show="dialogDelete = $event" 
                        @confirm="deleteItemConfirm" 
                    />
                </v-col>
            </v-row>
        </v-container>
    </Main>
</template>
