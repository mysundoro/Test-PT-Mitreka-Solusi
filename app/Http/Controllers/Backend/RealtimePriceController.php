<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\{Stock};

class RealtimePriceController extends Controller
{
    public function getRealtimePrice($symbol)
    {
        $apiKey = 'ct3gv11r01qkff71874gct3gv11r01qkff718750';

        // Fetch stock quote data
        $response = Http::get('https://finnhub.io/api/v1/quote', [
            'symbol' => $symbol,
            'token' => $apiKey,
        ]);

        if ($response->successful()) {
            // Fetch company profile information
            $companyResponse = Http::get('https://finnhub.io/api/v1/stock/profile2', [
                'symbol' => $symbol,
                'token' => $apiKey,
            ]);

            $companyName = null;
            if ($companyResponse->successful() && isset($companyResponse->json()['name'])) {
                $companyName = $companyResponse->json()['name'];
            }

            // Update or create stock entry in the database
            $stock = Stock::updateOrCreate(
                ['symbol' => $symbol],
                [
                    'current_price' => $response->json()['c'],
                    'previous_close' => $response->json()['pc'],
                    'change' => $response->json()['d'],
                    'percent_change' => $response->json()['dp'],
                ]
            );

            return response()->json([
                'price' => $stock->current_price,
                'change' => $stock->change,
                'percentChange' => $stock->percent_change,
                'previousClose' => $stock->previous_close,
                'companyName' => $companyName,
            ]);
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
}
