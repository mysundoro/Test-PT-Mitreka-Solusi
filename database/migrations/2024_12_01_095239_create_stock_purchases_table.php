<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to users table
            $table->string('stock_symbol'); // Stock symbol (e.g., AAPL, MSFT)
            $table->decimal('eth_amount', 18, 8); // Amount of Ethereum used
            $table->decimal('usd_value', 18, 2); // Equivalent value in USD
            $table->timestamp('transaction_date')->useCurrent(); // Date and time of the transaction
            $table->string('transaction_id')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_purchases');
    }
};
