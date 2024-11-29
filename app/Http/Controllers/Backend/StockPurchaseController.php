<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockPurchase;
use App\Models\Stock;
use App\Models\Transaction;
use Auth;

class StockPurchaseController extends Controller
{
    public function getUserStockPurchases(Request $request)
    {
        $userId = Auth::id();
        $stockPurchases = StockPurchase::where('user_id', $userId)->get();

        return response()->json($stockPurchases);
    }

    public function buyStock(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'symbol' => 'required|string|exists:stocks,symbol',
            'quantity' => 'required|integer|min:1', // Quantity of stock to buy
            'amount_in_usd' => 'required|numeric|min:1', // Amount in USD the user wants to spend
        ]);

        // Get the authenticated user
        $user = Auth::user();
        // Find the stock by its symbol
        $stock = Stock::where('symbol', $request->symbol)->first();

        // Check if the user has enough balance
        if ($user->balance < $request->amount_in_usd) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        // Deduct balance
        $user->balance -= $request->amount_in_usd;
        $user->save();

        // Record the stock purchase in the stock_purchases table
        $purchase = StockPurchase::create([
            'user_id' => $user->id,
            'symbol' => $stock->symbol,
            'quantity' => $request->quantity,
            'price_at_purchase' => $stock->current_price,
            'total_amount_spent' => $request->amount_in_usd, // Total USD spent
        ]);

        // Optionally record the transaction in the transactions table
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = 'buy_stock';
        $transaction->amount = $request->amount_in_usd;
        $transaction->balance_after = $user->balance;
        $transaction->status = 'completed';
        $transaction->stock_symbol = $stock->symbol;
        $transaction->stock_amount = $request->quantity;
        $transaction->stock_price = $stock->current_price;
        $transaction->save();

        // Return a JSON response indicating success
        return response()->json([
            'success' => true,
            'message' => 'Stock purchased successfully!',
            'balance' => $user->balance,
            'purchase' => $purchase
        ], 200);
    }
}
