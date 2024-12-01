<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

use App\Models\StockPurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Auth;

class StockPurchaseController extends Controller
{
    public function buyStock(Request $request)
    {
        $user = Auth::user();

        // Validate request (ensure enough Ethereum and valid stock symbol)
        $request->validate([
            'stock_symbol' => 'required|string',
            'eth_amount' => 'required|numeric|min:0.01',
        ]);

        // Get the Ethereum to USD rate (for display purposes)
        $ethToUsd = $this->getEthereumToUsdRate();
        if (!$ethToUsd) {
            return response()->json(['error' => 'Failed to fetch Ethereum price'], 500);
        }

        // Check if user has enough Ethereum (not USD)
        if ($user->balance < $request->eth_amount) {
            return response()->json(['error' => 'Insufficient Ethereum balance'], 400);
        }

        // Calculate the USD equivalent of the Ethereum to show to the user (for UI)
        $usdValue = $request->eth_amount * $ethToUsd;

        // Create the transaction record
        $transaction = StockPurchase::create([
            'user_id' => $user->id,
            'stock_symbol' => $request->stock_symbol,
            'eth_amount' => $request->eth_amount,
            'usd_value' => $usdValue,  // This is for tracking the USD equivalent of the purchase
            'transaction_id' => Str::uuid()->toString(),
        ]);

        // Deduct Ethereum from user balance (actual Ethereum balance, not USD)
        $user->balance -= $request->eth_amount;
        $user->save();

        return response()->json(['message' => 'Stock purchase successful', 'transaction' => $transaction]);
    }

    // Get Ethereum to USD rate from CoinGecko API
    private function getEthereumToUsdRate()
    {
        try {
            // Make request to CoinGecko API to get Ethereum price in USD
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'ethereum',
                'vs_currencies' => 'usd',
            ]);

            // If response is successful, return the price
            if ($response->successful()) {
                return $response->json()['ethereum']['usd'];
            }

            return null; // If request fails, return null
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return null;
        }
    }

    public function getUserPurchases(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Retrieve all stock purchases related to the current user
        $purchases = StockPurchase::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')  // Order by the most recent purchases
            ->get();

        // Return the purchases as a JSON response
        return response()->json(['purchases' => $purchases]);
    }
}
