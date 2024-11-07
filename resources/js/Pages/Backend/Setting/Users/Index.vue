<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';
import DeleteDialog from '@/Components/DeleteDialog.vue';

const props = defineProps({
    roles: {
        type: Array,
        default: () => ([]),
    },
});

const search = ref('');
const selectedRole = ref('');
const dialogDelete = ref(false);
const selectedItem = ref(null);
const items = ref([]);

// Fetch data from server based on current page
const fetchData = async () => {
    try {
        const response = await axios.get(route('users.data'));
        items.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

// Fetch data on component mount
onMounted(fetchData);

const filteredData = computed(() => {
    return items.value.filter(item => {
        const matchesSearch = item.name.toLowerCase().includes(search.value.toLowerCase());
        const matchesRole = selectedRole.value ? item.grup === selectedRole.value : true;
        return matchesSearch && matchesRole;
    });
});

const create = () => {
    router.get(route('users.create'));
};

const editItem = (item) => {
    router.get(route('users.edit', item.id));
};

const confirmDeleteItem = (item) => {
    selectedItem.value = item;
    dialogDelete.value = true;
};

const deleteItemConfirm = () => {
    if (selectedItem.value) {
        router.visit(route("users.destroy", selectedItem.value.id), { method: 'delete' });
    }
    dialogDelete.value = false;
    selectedItem.value = null;
};
</script>

<template>
    <Main :title="$t('Users')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Users') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('dashboard')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                    <v-btn
                        v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Create Users')"
                        color="primary"
                        class="ml-2"
                        @click="create"
                    >
                        <v-icon>mdi-plus</v-icon> {{ $t('Add') }}
                    </v-btn>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="6">
                    <v-text-field 
                        density="compact" 
                        variant="outlined" 
                        prepend-inner-icon="mdi-table-search" 
                        v-model="search" 
                        :label="$t('Search')" 
                    />
                </v-col>
                <v-col cols="12" md="6">
                    <v-select
                        density="compact"
                        variant="outlined"
                        v-model="selectedRole" 
                        :items="props.roles.map(role => role.name)"
                        :label="$t('Filter by Roles')"
                        :clearable="true"
                    />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-data-table
                        :headers="[
                            { title: 'No', value: 'no', width: '50px' },
                            { title: $t('Photo'), value: 'profile_photo_url', width: '70' },
                            { title: $t('Name'), value: 'name' },
                            { title: $t('Email'), value: 'email' },
                            { title: $t('Roles'), value: 'grup' },
                            { title: $t('Actions'), value: 'actions', width: '100px', sortable: false }
                        ]"
                        :items="filteredData"
                        :sort-by="[{ key: 'name', order: 'asc' }]"
                        class="elevation-1"
                    >
                        <template v-slot:item.actions="{ item }">
                            <v-icon
                                v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Edit Users')"
                                class="me-2"
                                size="small"
                                @click="editItem(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Delete Users')"
                                size="small"
                                @click="confirmDeleteItem(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </template>
                        <template v-slot:item.profile_photo_url="{ item }">
                            <v-avatar color="primary" size="large">
                                <img :src="item.profile_photo_url" :alt="item.name">
                            </v-avatar>
                        </template>
                        <template v-slot:item.no="{ index, item }">
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
