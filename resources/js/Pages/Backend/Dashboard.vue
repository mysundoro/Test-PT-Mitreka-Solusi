<script setup>
import Main from '@/Layouts/Backend/Main.vue';
import { ref, onMounted, watch, onBeforeUnmount } from 'vue';

const symbol = ref('AAPL');
const price = ref(null);
const change = ref(null);
const percentChange = ref(null);
const previousClose = ref(null);
const companyName = ref(null);
const loading = ref(true);
const error = ref(null);

const symbols = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA'];

const fetchPrice = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('realtime.getRealtimePrice', symbol.value));
        price.value = data.price;
        change.value = data.change;
        percentChange.value = data.percentChange;
        previousClose.value = data.previousClose;
        companyName.value = data.companyName;
    } catch (err) {
        error.value = 'Failed to fetch data.';
    } finally {
        loading.value = false;
    }
};

// Run the price fetch every 30 seconds
let intervalId;

const startFetchingPrice = () => {
    fetchPrice();
    intervalId = setInterval(fetchPrice, 30000); // 30 seconds interval
};

const stopFetchingPrice = () => {
    if (intervalId) {
        clearInterval(intervalId); // Stop the interval when the component is destroyed
    }
};

watch(symbol, fetchPrice); // Fetch when symbol changes
onMounted(startFetchingPrice);
onBeforeUnmount(stopFetchingPrice); // Clean up the interval on unmount
</script>

<template>
    <Main title="Dashboard"></Main>

    <v-container class="pa-4">
        <v-card max-width="400" class="mx-auto">
            <v-card-title>
                <v-icon class="mr-2">mdi-finance</v-icon>
                {{ companyName }}
            </v-card-title>

            <v-select
                v-model="symbol"
                :items="symbols"
                label="Select Stock Symbol"
                class="mb-4"
            />

            <v-card-subtitle v-if="loading">
                <v-skeleton-loader type="card"></v-skeleton-loader>
            </v-card-subtitle>

            <v-card-subtitle v-if="error" type="error" outlined>
                {{ error }}
            </v-card-subtitle>

            <v-card-text v-else>
                <v-list dense>
                    <v-list-item>
                        <v-list-item-title>Current Price</v-list-item-title>
                        <v-list-item-subtitle>${{ price }}</v-list-item-subtitle>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title>Change</v-list-item-title>
                        <v-list-item-subtitle :class="change < 0 ? 'text-negative' : 'text-positive'">
                            ${{ change }} ({{ percentChange }}%)
                        </v-list-item-subtitle>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title>Previous Close</v-list-item-title>
                        <v-list-item-subtitle>${{ previousClose }}</v-list-item-subtitle>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </v-card>
    </v-container>
</template>
