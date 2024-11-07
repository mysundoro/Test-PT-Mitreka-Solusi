<script setup>
import { computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';

const props = defineProps({
    permissions: {
        type: Array,
        default: () => ([]),
    },
});

// Form state
const form = useForm({
    name: '',
    permission: [],
});

// Group permissions by id_permissions
const groupedPermissions = computed(() => {
    const groups = {};

    // Iterate through each permission
    props.permissions.forEach(permission => {
        const { id_permissions, name } = permission;
        if (id_permissions === null) {
            groups[permission.id] = { ...permission, children: [] }; // Create group
        } else {
            // Add permission to its corresponding group
            const parentGroup = groups[id_permissions];
            if (parentGroup) {
                parentGroup.children.push(permission);
            }
        }
    });

    return Object.values(groups); // Return groups as an array
});

// Computed property to check if all children are selected
const isAllChildrenSelected = (group) => {
    return group.children.every(child => form.permission.includes(child.id));
};

// Function to handle Select All
const toggleSelectAll = (group) => {
    const allSelected = isAllChildrenSelected(group);
    if (allSelected) {
        // Deselect all children
        group.children.forEach(child => {
            const index = form.permission.indexOf(child.id);
            if (index !== -1) {
                form.permission.splice(index, 1);
            }
        });
    } else {
        // Select all children
        group.children.forEach(child => {
            if (!form.permission.includes(child.id)) {
                form.permission.push(child.id);
            }
        });
    }
};

// Store function to handle form submission
const store = () => {
    form.post(route('roles.store'), {
        errorBag: 'store',
        preserveScroll: true,
        resetOnSuccess: false,
    });
};
</script>

<template>
    <Main :title="$t('Create Role')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Create Role') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('roles.index')" as="button">
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
                            <v-col>
                                <v-text-field
                                    density="compact"
                                    variant="outlined"
                                    v-model="form.name"
                                    :error-messages="form.errors.name"
                                    :label="$t('Name')"
                                />
                            </v-col>
                        </v-row>
                    </v-col>
                    <v-col cols="12">
                        <h3>{{ $t('Permissions') }}</h3>
                        <v-list>
                            <v-list-item v-for="group in groupedPermissions" :key="group.id">
                                <!-- Group Checkbox -->
                                <v-checkbox
                                    :label="group.name"
                                    :value="group.id"
                                    v-model="form.permission"
                                    :indeterminate="!isAllChildrenSelected(group) && form.permission.includes(group.id)"
                                    @change="toggleSelectAll(group)"
                                />

                                <!-- Row of Child Checkboxes (Left to Right) -->
                                <v-row>
                                    <v-col
                                        v-for="child in group.children"
                                        :key="child.id"
                                        cols="auto"
                                    >
                                        <v-checkbox
                                            :label="child.name"
                                            :value="child.id"
                                            v-model="form.permission"
                                            :disabled="!form.permission.includes(group.id)"
                                        />
                                    </v-col>
                                </v-row>
                            </v-list-item>
                        </v-list>
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
