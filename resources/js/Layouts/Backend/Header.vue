<script setup>
import { router, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { useTheme } from 'vuetify';
import Language from '@/Components/Language.vue';

const notifications = ref([]);
const unreadCount = ref(0);

const logout = () => {
    router.post(route('logout'));
};

const markAsRead = () => {
    router.visit(route('notification.markAsRead'), { method: 'patch' });
};

const clear = () => {
    router.visit(route('notification.clear'), { method: 'delete' });
};

const fetchNotifications = async () => {
    const response = await fetch(route('notification.index'));
    const data = await response.json();
    notifications.value = data;

    unreadCount.value = data.filter(notification => !notification.is_read).length;
};

// Format the date to dd-mm-yyyy H:i
const formatDateTime = (dateString) => {
    const date = new Date(dateString);

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
    const year = date.getFullYear();

    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${day}-${month}-${year} ${hours}:${minutes}`;
};

onMounted(() => {
    fetchNotifications();
});

// Fullscreen logic
const isFullScreen = ref(false);

const toggleFullScreen = () => {
    const doc = document;

    if (!isFullScreen.value) {
        const elem = doc.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen(); // Firefox
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen(); // Safari
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen(); // IE/Edge
        }
    } else {
        if (doc.exitFullscreen) {
            doc.exitFullscreen();
        } else if (doc.mozCancelFullScreen) {
            doc.mozCancelFullScreen(); // Firefox
        } else if (doc.webkitExitFullscreen) {
            doc.webkitExitFullscreen(); // Safari
        } else if (doc.msExitFullscreen) {
            doc.msExitFullscreen(); // IE/Edge
        }
    }

    isFullScreen.value = !isFullScreen.value;
};

// Dark & Light Switcher with Local Storage
const theme = useTheme();

// Initialize theme based on local storage or default
onMounted(() => {
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme) {
        theme.global.name.value = storedTheme;
    }
});

// Computed property to determine if the current theme is dark
const isDarkTheme = computed(() => theme.global.current.value.dark);

const toggleTheme = () => {
    const currentTheme = theme.global.current.value.dark ? 'light' : 'dark';
    theme.global.name.value = currentTheme;
    localStorage.setItem('theme', currentTheme); // Save theme to local storage
};
</script>

<template>
    <v-app-bar>
        <v-app-bar-nav-icon @click="$emit('toggle-drawer')"></v-app-bar-nav-icon>
        <Link :href="route('dashboard')" class="d-flex align-items-center">
            <template v-if="$page.props.configuration.logo_system.value">
                <img
                    :src="'/storage/' + $page.props.configuration.logo_system.value"
                    :alt="$page.props.configuration.application_name.value"
                    height="48"
                />
            </template>
            <template v-else-if="$page.props.configuration.application_name.value">
                {{ $page.props.configuration.application_name.value }}
            </template>
            <template v-else>
                eCargo
            </template>
        </Link>

        <v-spacer></v-spacer>

        <Language />

        <!-- Fullscreen button -->
        <v-btn icon @click="toggleFullScreen">
            <v-icon v-if="!isFullScreen">mdi-fullscreen</v-icon>
            <v-icon v-else>mdi-fullscreen-exit</v-icon>
        </v-btn>

        <!-- Dark / Light Switcher -->
        <v-btn icon @click="toggleTheme">
            <v-icon>{{ isDarkTheme ? 'mdi-weather-night' : 'mdi-weather-sunny' }}</v-icon>
        </v-btn>

        <!-- Notification -->
        <v-menu offset-y>
            <template v-slot:activator="{ props }">
                <v-btn icon v-bind="props">
                    <v-icon>mdi-bell</v-icon>
                    <v-badge
                        v-if="unreadCount > 0"
                        color="red"
                        :content="unreadCount"
                    ></v-badge>
                </v-btn>
            </template>
            <v-card
                class="d-flex flex-column"
                style="width: 400px; height: 500px;"
            >
                <v-card-title class="bg-primaryd-flex justify-space-between align-center">
                    <span>{{ $t('Notification') }}</span>
                    {{ unreadCount }}
                </v-card-title>
                <v-card-text class="flex-grow-1 overflow-y-auto">
                    <v-list lines="three">
                        <v-list-item
                            v-for="notification in notifications"
                            :key="notification.id"
                            :class="{ 'font-weight-bold': !notification.is_read }"
                            :style="!notification.is_read ? 'background-color: #f0f8ff;' : ''"
                        >
                            <v-list-item-title>{{ notification.type }}</v-list-item-title>
                            <v-list-item-subtitle>
                                {{ notification.data }}<br/>
                                {{ formatDateTime(notification.created_at) }}
                            </v-list-item-subtitle>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="primary" @click="markAsRead">
                        {{ $t('Mark As Read') }}
                    </v-btn>

                    <v-spacer></v-spacer>

                    <v-btn color="secondary" @click="clear">
                        {{ $t('Empty Notification') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-menu>

        <v-menu min-width="200px" rounded>
            <template v-slot:activator="{ props }">
                <v-btn icon v-bind="props">
                    <v-avatar color="primary" size="large">
                        <v-img
                            v-if="$page.props.auth.user.profile_photo_path"
                            :src="$page.props.auth.user.profile_photo_path"
                            alt="User Avatar"
                        ></v-img>
                        <v-icon v-else>mdi-account</v-icon>
                    </v-avatar>
                </v-btn>
            </template>
            <v-card>
                <v-card-text>
                    <div class="mx-auto text-center">
                        <v-avatar color="primary" size="large">
                            <v-img
                                v-if="$page.props.auth.user.profile_photo_path"
                                :src="$page.props.auth.user.profile_photo_path"
                                alt="User Avatar"
                            ></v-img>
                            <v-icon v-else>mdi-account</v-icon>
                        </v-avatar>
                        <h3>{{ $page.props.auth.user.name }}</h3>
                        <p class="text-caption mt-1">{{ $page.props.auth.user.email }}</p>
                        <v-divider class="my-3"></v-divider>
                        <!-- <v-btn variant="text" rounded>Edit Akun</v-btn> -->
                        <v-divider class="my-3"></v-divider>
                        <v-form @submit.prevent="logout">
                            <v-btn type="submit" variant="text" rounded>
                                {{ $t('Logout') }}
                            </v-btn>
                        </v-form>
                    </div>
                </v-card-text>
            </v-card>
        </v-menu>
    </v-app-bar>
</template>
