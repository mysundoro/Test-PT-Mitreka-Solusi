<script setup>
import { ref, watch, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    drawer: Boolean,
});

const drawer = ref(props.drawer);
const { menu } = usePage().props;

// Filter menu to show only 'backend' category
const backendMenu = computed(() => {
    return menu.filter(item => item.category === 'backend');
});

// Check if an item has children
const hasChildren = (parentId) => {
    return backendMenu.value.some(item => item.parent_id === parentId);
};

// Check if the menu item is active based on the current route
const isActive = (menuRoute) => {
    return route().current(menuRoute);
};

// Navigation function with route check
const navigateTo = (menuUrl) => {
    try {
        router.visit(route(menuUrl));
    } catch (error) {
        window.location.href = menuUrl;
    }
};

// Logout function
const logout = () => {
    router.post(route('logout'));
};

watch(() => props.drawer, (newVal) => {
    drawer.value = newVal;
});
</script>

<template>
    <v-navigation-drawer
        permanent
        v-model="drawer"
        elevation="10"
    >
        <v-list open-strategy="single" nav>
            <!-- Loop through the backendMenu -->
            <template v-for="(item, index) in backendMenu" :key="index">
                <!-- Single Menu (No children) -->
                <v-list-item
                    v-if="item.parent_id === null && !hasChildren(item.id) && 
                        ($page.props.user.roles.includes('Superadmin') || 
                        $page.props.user.permissions_id.includes(item.id_permissions) || 
                        item.id_permissions === null)"
                    @click="navigateTo(item.url)"
                    :prepend-icon="item.icon"
                    :title="$t(item.name)"
                    :class="{
                        'bg-primary white--text': isActive(item.url)
                    }"
                />

                <!-- Dropdown Menu (Has children) -->
                <v-list-group
                    v-if="item.parent_id === null && hasChildren(item.id) && 
                        ($page.props.user.roles.includes('Superadmin') || 
                        $page.props.user.permissions_id.includes(item.id_permissions) || 
                        item.id_permissions === null)"
                    :value="item.name"
                >
                    <template v-slot:activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            :prepend-icon="item.icon"
                            :title="$t(item.name)"
                        />
                    </template>

                    <!-- Render submenus (children) -->
                    <v-list-item
                        v-for="subItem in backendMenu.filter(sub => 
                            sub.parent_id === item.id && 
                            ($page.props.user.roles.includes('Superadmin') || 
                            $page.props.user.permissions_id.includes(sub.id_permissions) || 
                            sub.id_permissions === null))"
                        :key="subItem.id"
                        @click="navigateTo(subItem.url)"
                        :title="$t(subItem.name)"
                        :class="{
                            'bg-primary white--text': isActive(subItem.url)
                        }"
                    />
                </v-list-group>
            </template>
        </v-list>

        <!-- Logout button -->
        <template v-slot:append>
            <div class="pa-2">
                <v-form @submit.prevent="logout">
                    <v-btn block color="primary" type="submit">{{ $t('Logout') }}</v-btn>
                </v-form>
            </div>
        </template>
    </v-navigation-drawer>
</template>
