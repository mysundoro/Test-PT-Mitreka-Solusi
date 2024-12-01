<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stock_symbol',
        'eth_amount',
        'usd_value',
        'transaction_date',
        'transaction_id',
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
