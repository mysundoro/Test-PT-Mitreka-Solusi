<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Auth;

class TransactionController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        // Update balance
        $user->balance += $request->amount;
        $user->save();

        // Log transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'balance_after' => $user->balance,
            'status' => 'success',
        ]);

        return response()->json(['balance' => $user->balance, 'message' => 'Deposit successful!']);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();

        if ($user->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        // Update balance
        $user->balance -= $request->amount;
        $user->save();

        // Log transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdraw',
            'amount' => $request->amount,
            'balance_after' => $user->balance,
            'status' => 'success',
        ]);

        return response()->json(['balance' => $user->balance, 'message' => 'Withdrawal successful!']);
    }
}
