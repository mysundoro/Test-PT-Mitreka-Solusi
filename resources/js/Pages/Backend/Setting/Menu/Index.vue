<script setup>
import { ref, watch, onMounted, computed } from 'vue'; // Import onMounted
import { router, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';
import DeleteDialog from '@/Components/DeleteDialog.vue';

const dialogDelete = ref(false);
const search = ref('backend');
const selectedItem = ref(null);
const parentOptions = ref([]);

const fetchParentOptions = async (searchTerm) => {
    if (!searchTerm) {
        parentOptions.value = [];
        return;
    }

    try {
        const response = await axios.get(route('menu.getMenu', searchTerm));
        parentOptions.value = response.data;
    } catch (error) {
        parentOptions.value = [];
    }
};

watch(search, (newSearch) => {
    fetchParentOptions(newSearch);
});

onMounted(() => {
    fetchParentOptions(search.value);
});

const create = () => {
    router.get(route('menu.create'));
};

const editItem = (item) => {
    router.get(route('menu.edit', item.id));
};

const confirmDeleteItem = (item) => {
    selectedItem.value = item;
    dialogDelete.value = true;
};

const deleteItemConfirm = () => {
    if (selectedItem.value) {
        router.visit(route("menu.destroy", selectedItem.value.id), { method: 'delete' });
    }
    dialogDelete.value = false;
    selectedItem.value = null;
};

// Function to organize parent options based on parent_id
const organizedOptions = computed(() => {
    const map = {};
    const roots = [];

    parentOptions.value.forEach(option => {
        map[option.id] = { ...option, children: [] }; // Add a children property
        if (option.parent_id === null) {
            roots.push(map[option.id]); // Top-level items (no parent_id)
        } else {
            // Push child options into the parent's children array
            map[option.parent_id].children.push(map[option.id]);
        }
    });

    return roots; // Return top-level items with their children
});
</script>

<template>
    <Main :title="$t('Menu')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Menu') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('dashboard')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
                    <v-btn
                        v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Create Menu')"
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
                    <v-select
                        density="compact"
                        variant="outlined"
                        v-model="search" 
                        :label="$t('Filter by Category')"
                        :clearable="true"
                        :items="[
                            { title: 'frontend' },
                            { title: 'backend' }
                        ]"
                    />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-list>
                        <template v-if="organizedOptions.length">
                            <template v-for="option in organizedOptions" :key="option.id">
                                <v-list-item>
                                    <span class="mr-3">{{ $t(option.name) }}</span>
                                    <v-icon
                                        v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Edit Menu')"
                                        class="me-2"
                                        size="small"
                                        variant="text"
                                        @click="editItem(option)"
                                    >
                                        mdi-pencil
                                    </v-icon>
                                    <v-icon
                                        v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Delete Menu')"
                                        size="small"
                                        variant="text"
                                        @click="confirmDeleteItem(option)"
                                    >
                                        mdi-delete
                                    </v-icon>
                                </v-list-item>
                                <template v-if="option.children.length">
                                    <v-list>
                                        <template v-for="child in option.children" :key="child.id">
                                            <v-list-item class="ml-4">
                                                <span class="mr-3">{{ $t(child.name) }}</span>
                                                <v-icon
                                                    v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Edit Menu')"
                                                    class="me-2"
                                                    size="small"
                                                    variant="text"
                                                    @click="editItem(child)"
                                                >
                                                    mdi-pencil
                                                </v-icon>
                                                <v-icon
                                                    v-if="$page.props.user.roles.includes('Superadmin') || $page.props.user.permissions.includes('Delete Menu')"
                                                    size="small"
                                                    variant="text"
                                                    @click="confirmDeleteItem(child)"
                                                >
                                                    mdi-delete
                                                </v-icon>
                                            </v-list-item>
                                        </template>
                                    </v-list>
                                </template>
                            </template>
                        </template>
                    </v-list>

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
