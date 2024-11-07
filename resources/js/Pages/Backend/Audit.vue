<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import Main from '@/Layouts/Backend/Main.vue';

const search = ref('');
const items = ref([]);

const fetchData = async () => {
    try {
        const response = await axios.get(route('audit.data'));
        items.value = response.data;
    } catch (error) {
        console.error(error);
    }
};

onMounted(fetchData);

const filteredData = computed(() => {
    if (!search.value) {
        return items.value;
    }
    return items.value.filter(item => 
        item.name.toLowerCase().includes(search.value.toLowerCase())
    );
});
</script>

<template>
    <Main :title="$t('Audit')">
        <v-container class="elevation-4 mt-16 mb-16">
            <v-row class="mb-10 align-center">
                <v-col cols="12" md="6">
                    <h2>{{ $t('Audit') }}</h2>
                </v-col>
                <v-col cols="12" md="6" class="d-flex justify-end">
                    <Link :href="route('dashboard')" as="button">
                        <v-btn>
                            <v-icon>mdi-arrow-left</v-icon> {{ $t('Back') }}
                        </v-btn>
                    </Link>
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
                            { title: $t('User'), value: 'user.name' },
                            { title: $t('Event'), value: 'event' },
                            { title: $t('Old'), value: 'old_values' },
                            { title: $t('New'), value: 'new_values' },
                            { title: $t('Url'), value: 'url' },
                            { title: $t('IP Address'), value: 'ip_address' },
                            { title: $t('User Agent'), value: 'user_agent' },
                            { title: $t('Created'), value: 'created_at' },
                            { title: $t('Updated'), value: 'updated_at' },
                        ]"
                        :items="filteredData"
                        class="elevation-1"
                    >
                        <template v-slot:item.no="{ index }">
                            {{ index + 1 }}
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-container>
    </Main>
</template>
