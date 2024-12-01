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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('symbol'); // Stock symbol
            $table->float('c')->nullable(); // Current price ('c' from API)
            $table->float('h')->nullable(); // Daily high ('h' from API)
            $table->float('l')->nullable(); // Daily low ('l' from API)
            $table->float('o')->nullable(); // Opening price ('o' from API)
            $table->float('pc')->nullable(); // Previous close ('pc' from API)
            $table->float('d')->nullable(); // Change in price ('d' from API)
            $table->float('dp')->nullable(); // Percentage change ('dp' from API)
            $table->timestamp('t')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
