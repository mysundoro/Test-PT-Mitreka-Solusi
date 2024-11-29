<script setup>
import Main from '@/Layouts/Backend/Main.vue';
import { ref, onMounted, watch, onBeforeUnmount } from 'vue';

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

// User balance-related data
const depositAmount = ref(null);
const withdrawAmount = ref(null);
const balance = ref(0);  // Balance in USD
const ethPrice = ref(null); // ETH price in USD
const balanceInETH = ref(0); // Converted ETH balance

// Add loading states
const loadingDeposit = ref(false);
const loadingWithdraw = ref(false);

// Swap stock-related data (Buying stock using USD)
const stockAmount = ref(null); // Amount in USD the user wants to spend on the stock
const loadingBuyStock = ref(false); // Loading state for buy stock operation

let intervalId;

// Function to fetch stock price
const fetchPrice = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('realtime.getRealtimePrice', symbol.value));
        price.value = data.price;
        change.value = data.change;
        percentChange.value = data.percentChange;
        previousClose.value = data.previousClose;
        companyName.value = data.companyName;
        error.value = null; // Clear any previous errors
    } catch (err) {
        error.value = 'Failed to fetch stock data.';
    } finally {
        loading.value = false;
    }
};

// Function to fetch user balance
const fetchBalance = async () => {
    try {
        const { data } = await axios.get(route('user.balance'));
        balance.value = data.balance;
        calculateETHBalance(); // Update ETH balance after fetching
    } catch (err) {
        console.error('Failed to fetch balance:', err);
        alert('Failed to fetch balance.');
    }
};

// Function to fetch ETH price
const fetchETHPrice = async () => {
    try {
        const { data } = await axios.get('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=usd');
        ethPrice.value = data.ethereum.usd;
        calculateETHBalance(); // Update ETH balance after fetching
    } catch (err) {
        console.error('Failed to fetch ETH price:', err);
        ethPrice.value = null;
    }
};

// Calculate balance in ETH
const calculateETHBalance = () => {
    if (ethPrice.value && balance.value) {
        balanceInETH.value = (balance.value / ethPrice.value).toFixed(8); // Convert and format to 8 decimals
    }
};

// Function to start auto-fetching stock prices
const startFetchingPrice = () => {
    fetchPrice();
    intervalId = setInterval(fetchPrice, 30000); // Refresh every 30 seconds
};

// Stop interval when component is unmounted
const stopFetchingPrice = () => {
    if (intervalId) clearInterval(intervalId);
};

// Handle deposit
const handleDeposit = async () => {
    if (depositAmount.value && depositAmount.value > 0) {
        loadingDeposit.value = true; // Start loading
        try {
            const response = await axios.post(route('transaction.deposit'), { amount: depositAmount.value });
            balance.value = response.data.balance;
            calculateETHBalance();
            depositAmount.value = null;
            alert('Deposit successful!');
        } catch (err) {
            console.error('Deposit failed:', err);
            alert('Failed to deposit.');
        } finally {
            loadingDeposit.value = false; // Stop loading
        }
    } else {
        alert('Please enter a valid deposit amount.');
    }
};

// Handle withdrawal
const handleWithdraw = async () => {
    if (withdrawAmount.value && withdrawAmount.value > 0 && withdrawAmount.value <= balance.value) {
        loadingWithdraw.value = true; // Start loading
        try {
            const response = await axios.post(route('transaction.withdraw'), { amount: withdrawAmount.value });
            balance.value = response.data.balance;
            calculateETHBalance();
            withdrawAmount.value = null;
            alert('Withdrawal successful!');
        } catch (err) {
            console.error('Withdrawal failed:', err);
            alert('Failed to withdraw.');
        } finally {
            loadingWithdraw.value = false; // Stop loading
        }
    } else {
        alert('Insufficient balance or invalid amount.');
    }
};

// Handle buying stock (swap stock with USD)
const handleBuyStock = async () => {
    if (!stockAmount.value || stockAmount.value <= 0) {
        alert('Please enter a valid amount in USD to buy the stock.');
        return;
    }

    const totalCostInUSD = stockAmount.value; // Cost in USD
    if (totalCostInUSD > balance.value) {
        alert('Insufficient USD balance to buy this stock.');
        return;
    }

    // Calculate the quantity of stocks the user can afford to buy
    const quantity = Math.floor(totalCostInUSD / price.value); // Determine quantity of shares
    const totalAmountSpent = quantity * price.value; // Total amount spent for the calculated quantity

    if (quantity <= 0) {
        alert('You cannot afford even a single share with the entered amount.');
        return;
    }

    loadingBuyStock.value = true; // Start loading for buying stock
    try {
        const response = await axios.post(route('stock.buy'), {
            symbol: symbol.value,
            quantity: quantity, // Send the calculated quantity of shares
            amount_in_usd: totalAmountSpent, // Total amount spent
        });

        balance.value -= totalCostInUSD; // Deduct the balance for the purchase
        calculateETHBalance(); // Update ETH balance
        stockAmount.value = null;
        alert('Stock purchased successfully!');
    } catch (err) {
        console.error('Failed to buy stock:', err);
        alert('Failed to buy stock.');
    } finally {
        loadingBuyStock.value = false; // Stop loading
    }
};

// Watch for symbol changes
watch(symbol, fetchPrice);

// Lifecycle hooks
onMounted(() => {
    startFetchingPrice();
    fetchBalance();
    fetchETHPrice();
});
onBeforeUnmount(stopFetchingPrice);
</script>

<template>
    <Main title="Dashboard">
      <v-container class="pa-4">
        <!-- Realtime Price Card (Full Width) -->
        <v-row>
          <v-col cols="12">
            <v-card class="mx-auto" max-width="100%">
              <v-card-title>
                <v-icon class="mr-2">mdi-finance</v-icon>
                {{ companyName || 'Select a stock' }}
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
              <v-card-subtitle v-if="error" class="text-negative">
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
          </v-col>
        </v-row>
  
        <!-- Deposit, Withdraw, and Balance (3 Cards in Row) -->
        <v-row>
          <v-col cols="12" sm="4">
            <v-card class="mx-auto" max-width="400">
              <v-card-title>Deposit</v-card-title>
              <v-form @submit.prevent="handleDeposit">
                <v-text-field
                  v-model="depositAmount"
                  label="Deposit Amount"
                  type="number"
                  min="1"
                  class="mb-4"
                />
                <v-btn 
                  @click="handleDeposit" 
                  :loading="loadingDeposit" 
                  :disabled="loadingDeposit" 
                  color="primary" 
                  block
                >
                  <template #default>
                    <span v-if="!loadingDeposit">Deposit</span>
                  </template>
                </v-btn>
              </v-form>
            </v-card>
          </v-col>
  
          <v-col cols="12" sm="4">
            <v-card class="mx-auto" max-width="400">
              <v-card-title>Withdraw</v-card-title>
              <v-form @submit.prevent="handleWithdraw">
                <v-text-field
                  v-model="withdrawAmount"
                  label="Withdraw Amount"
                  type="number"
                  min="1"
                  class="mb-4"
                />
                <v-btn 
                  @click="handleWithdraw" 
                  :loading="loadingWithdraw" 
                  :disabled="loadingWithdraw" 
                  color="primary" 
                  block
                >
                  <template #default>
                    <span v-if="!loadingWithdraw">Withdraw</span>
                  </template>
                </v-btn>
              </v-form>
            </v-card>
          </v-col>
  
          <v-col cols="12" sm="4">
            <v-card class="mx-auto" max-width="400">
              <v-card-title>Your Balance</v-card-title>
              <v-card-subtitle>
                USD: ${{ balance }}
                <br />
                ETH: {{ balanceInETH }}
              </v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
  
        <!-- Buy Stock (Full Width) -->
        <v-row>
          <v-col cols="12">
            <v-card class="mx-auto" max-width="100%">
              <v-card-title>Buy Stock</v-card-title>
              <v-form @submit.prevent="handleBuyStock">
                <v-select
                  v-model="symbol"
                  :items="symbols"
                  label="Select Stock Symbol"
                  class="mb-4"
                />
                <v-text-field
                  v-model="stockAmount"
                  label="Amount to Spend"
                  type="number"
                  min="0.01"
                  step="0.01"
                  class="mb-4"
                />
                <v-btn 
                  @click="handleBuyStock" 
                  :loading="loadingBuyStock" 
                  :disabled="loadingBuyStock" 
                  color="primary" 
                  block
                >
                  <template #default>
                    <span v-if="!loadingBuyStock">Buy Stock</span>
                  </template>
                </v-btn>
              </v-form>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </Main>
</template>
  