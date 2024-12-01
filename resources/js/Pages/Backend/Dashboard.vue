<script setup>
import Main from '@/Layouts/Backend/Main.vue';
import { ref, onMounted, watch } from 'vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale } from 'chart.js';
import { usePage } from '@inertiajs/vue3';

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

// Chart-related data
const chartData = ref({
    labels: [],
    datasets: [
        {
            label: 'Stock Price (USD)',
            data: [],
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
        },
    ],
});

const chartOptions = {
    responsive: true,
    plugins: {
        legend: {
            display: true,
        },
        tooltip: {
            mode: 'index',
            intersect: false,
        },
    },
    scales: {
        x: {
            title: {
                display: true,
                text: 'Time',
            },
        },
        y: {
            title: {
                display: true,
                text: 'Price (USD)',
            },
        },
    },
};

// Stock-related data
const symbol = ref('AAPL');
const price = ref(null);
const change = ref(null);
const percentChange = ref(null);
const previousClose = ref(null);
const companyName = ref(null);
const symbols = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA'];
const loading = ref(true);
const error = ref(null);

const ethAmount = ref(0); // User's input for Ethereum amount
const purchaseError = ref(null); // Error for stock purchase
const purchaseSuccess = ref(null); // Success message for purchase
const buying = ref(false); // State to track purchase loading

const userBalance = usePage().props.auth.user.balance;

// Transaction history
const userPurchases = ref([]);
const purchaseHistoryLoading = ref(true);

// Function to fetch stock price
const fetchPrice = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('stock.RealtimePrice', symbol.value));
        price.value = data.current_price;
        change.value = data.change;
        percentChange.value = data.percentage_change;
        previousClose.value = data.previous_close;
        companyName.value = data.company_profile.name;
        const history = data.history;

        // Map the history data to update chart labels and data
        chartData.value = {
            labels: history.map(entry => new Date(entry.t).toLocaleString()), // Format timestamps
            datasets: [
                {
                    label: 'Stock Price (USD)',
                    data: history.map(entry => entry.c),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                },
            ],
        };

        error.value = null; // Clear any previous errors
    } catch (err) {
        error.value = 'Failed to fetch stock data.';
    } finally {
        loading.value = false;
    }
};

// Function to start auto-fetching stock prices
const startFetchingPrice = () => {
    fetchPrice();
    setInterval(fetchPrice, 30000);
};

// Handle stock purchase
const buyStock = async () => {
    if (ethAmount.value <= 0) {
        purchaseError.value = 'Please enter a valid amount of Ethereum.';
        return;
    }

    if (ethAmount.value > userBalance) {
        purchaseError.value = 'Insufficient Ethereum balance.';
        return;
    }

    buying.value = true; // Set loading state to true when purchase starts

    try {
        const response = await axios.post(route('stock_purchase.buyStock'), {
            stock_symbol: symbol.value,
            eth_amount: ethAmount.value,
        });
        purchaseSuccess.value = 'Stock purchase successful!';
        purchaseError.value = null;
        ethAmount.value = 0;
        fetchPurchaseHistory(); // Fetch updated history after purchase
    } catch (err) {
        purchaseError.value = 'Failed to purchase stock. Please try again.';
        purchaseSuccess.value = null;
    } finally {
        buying.value = false; // Reset loading state after purchase attempt
    }
};

// Fetch user purchase history
const fetchPurchaseHistory = async () => {
    purchaseHistoryLoading.value = true;
    try {
        const { data } = await axios.post(route('stock_purchase.getUserPurchases'));
        userPurchases.value = data.purchases;
    } catch (err) {
        console.error('Failed to fetch purchase history:', err);
    } finally {
        purchaseHistoryLoading.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    startFetchingPrice();
    fetchPurchaseHistory();
});

// Watch for symbol changes
watch(symbol, fetchPrice);
</script>

<template>
    <Main title="Dashboard">
        <v-container class="pa-4">
            <v-row>
                <v-col cols="8">
                    <v-row>
                        <v-col cols="12">
                            <v-card class="mx-auto" max-width="100%">
                                <v-card-title>
                                    <v-icon class="mr-2">mdi-finance</v-icon>
                                    {{ companyName || 'Select a stock' }}
                                </v-card-title>
                                <v-select v-model="symbol" :items="symbols" label="Select Stock Symbol" class="mb-4" />
                                
                                <!-- Conditionally rendered card subtitles -->
                                <v-card-subtitle v-if="loading">
                                    <v-skeleton-loader type="card"></v-skeleton-loader>
                                </v-card-subtitle>
                                <v-card-subtitle v-if="error" class="text-negative">
                                    {{ error }}
                                </v-card-subtitle>

                                <v-card-text v-else>
                                    <v-row>
                                        <!-- Current Price Section -->
                                        <v-col cols="12" md="4">
                                            <div><strong>Current Price</strong></div>
                                            <div>${{ price }}</div>
                                        </v-col>

                                        <!-- Change Section -->
                                        <v-col cols="12" md="4">
                                            <div><strong>Change</strong></div>
                                            <div :class="change < 0 ? 'text-negative' : 'text-positive'">
                                                ${{ change }} ({{ percentChange }}%)
                                            </div>
                                        </v-col>

                                        <!-- Previous Close Section -->
                                        <v-col cols="12" md="4">
                                            <div><strong>Previous Close</strong></div>
                                            <div>${{ previousClose }}</div>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <v-card class="mx-auto" max-width="100%">
                                <v-card-text>
                                    <v-skeleton-loader v-if="loading" type="list-item" />
                                    <Line :data="chartData" :options="chartOptions" />
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-col>

                <v-col cols="4">
                    <v-card class="pa-4">
                        <v-card-text>
                            <v-row>
                                <!-- Box 1: Crypto Asset -->
                                <v-col cols="6">
                                    <v-card class="pa-3 text-center">
                                        <v-icon color="blue" class="mb-2" size="36">
                                            mdi-bitcoin
                                        </v-icon>
                                        <div class="text-h6">{{ $page.props.auth.user.balance }}</div>
                                        <div class="text-subtitle-2">Crypto Asset</div>
                                    </v-card>
                                </v-col>

                                <!-- Box 2: Stock Asset -->
                                <v-col cols="6">
                                    <v-card class="pa-3 text-center">
                                        <v-icon color="green" class="mb-2" size="36">
                                            mdi-chart-bar
                                        </v-icon>
                                        <div class="text-h6">$15,230</div>
                                        <div class="text-subtitle-2">Stock Asset</div>
                                    </v-card>
                                </v-col>

                                <!-- Box 3: Total Transaction -->
                                <v-col cols="6">
                                    <v-card class="pa-3 text-center">
                                        <v-icon color="orange" class="mb-2" size="36">
                                            mdi-swap-horizontal
                                        </v-icon>
                                        <div class="text-h6">$120</div>
                                        <div class="text-subtitle-2">Total Transaction</div>
                                    </v-card>
                                </v-col>

                                <!-- Box 4: Total Referral -->
                                <v-col cols="6">
                                    <v-card class="pa-3 text-center">
                                        <v-icon color="purple" class="mb-2" size="36">
                                            mdi-account-multiple
                                        </v-icon>
                                        <div class="text-h6">0</div>
                                        <div class="text-subtitle-2">Total Referral</div>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>

                    <v-card class="pa-4 mt-5">
                        <v-card-title class="text-h6">Buy Stock {{ companyName }}</v-card-title>
                        <v-card-text>
                            <v-text-field
                                v-model="ethAmount"
                                label="Ethereum Amount"
                                type="number"
                                :min="0.01"
                                required
                            ></v-text-field>
                            <v-btn @click="buyStock" color="primary" :loading="buying" :disabled="loading || ethAmount <= 0">
                                Buy Stock
                            </v-btn>

                            <v-alert v-if="purchaseError" type="error" class="mt-3">{{ purchaseError }}</v-alert>
                            <v-alert v-if="purchaseSuccess" type="success" class="mt-3">{{ purchaseSuccess }}</v-alert>
                        </v-card-text>
                    </v-card>

                    <v-card class="pa-4 mt-5">
                        <v-card-title class="text-h6">Transaction</v-card-title>
                        <v-card-text>
                            <!-- Loading skeleton loader -->
                            <v-skeleton-loader v-if="purchaseHistoryLoading" type="list-item" />

                            <!-- List of purchases -->
                            <v-list v-else>
                                <v-list-item-group>
                                    <v-list-item v-for="(purchase, index) in userPurchases" :key="index">
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                {{ purchase.stock_symbol }} - {{ purchase.eth_amount }} ETH
                                            </v-list-item-title>
                                            <v-list-item-subtitle>
                                                USD Value: ${{ purchase.usd_value }} | Date: {{ new Date(purchase.created_at).toLocaleString() }}
                                            </v-list-item-subtitle>
                                        </v-list-item-content>
                                    </v-list-item>
                                </v-list-item-group>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </Main>
</template>