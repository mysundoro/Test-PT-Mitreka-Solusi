<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\{Price, PriceHistory};

class StockController extends Controller
{
    public function RealtimePrice($symbol)
    {
        $apiKey = 'ct3gv11r01qkff71874gct3gv11r01qkff718750';

        // Fetch stock quote data
        $quoteResponse = Http::get('https://finnhub.io/api/v1/quote', [
            'symbol' => $symbol,
            'token' => $apiKey,
        ]);

        // Fetch company profile data
        $profileResponse = Http::get('https://finnhub.io/api/v1/stock/profile2', [
            'symbol' => $symbol,
            'token' => $apiKey,
        ]);

        if ($quoteResponse->successful() && $profileResponse->successful()) {
            // Extract stock quote data
            $quoteData = $quoteResponse->json();
            $currentPrice = $quoteData['c']; // Current price ('c' from API)
            $high = $quoteData['h'];        // Daily high ('h' from API)
            $low = $quoteData['l'];         // Daily low ('l' from API)
            $open = $quoteData['o'];        // Opening price ('o' from API)
            $previousClose = $quoteData['pc']; // Previous close ('pc' from API)
            $change = $quoteData['d'];      // Price change ('d' from API)
            $percentageChange = $quoteData['dp']; // Percentage change ('dp' from API)
            $timestamp = $quoteData['t'];   // Timestamp ('t' from API)

            // Convert the timestamp to a Laravel Carbon instance
            $recordedAt = Carbon::createFromTimestamp($timestamp)->toDateTimeString();

            // Extract company profile data
            $profileData = $profileResponse->json();
            $companyName = $profileData['name'] ?? 'N/A';  // Company name
            $sector = $profileData['finnhubIndustry'] ?? 'N/A'; // Industry (Finnhub industry)
            $logo = $profileData['logo'] ?? null;          // Company logo URL
            $website = $profileData['weburl'] ?? 'N/A';    // Company website URL
            $country = $profileData['country'] ?? 'N/A';   // Country
            $currency = $profileData['currency'] ?? 'N/A'; // Currency
            $exchange = $profileData['exchange'] ?? 'N/A'; // Exchange
            $ipo = $profileData['ipo'] ?? 'N/A';           // IPO date
            $marketCap = $profileData['marketCapitalization'] ?? 'N/A'; // Market capitalization
            $phone = $profileData['phone'] ?? 'N/A';       // Phone number
            $sharesOutstanding = $profileData['shareOutstanding'] ?? 'N/A'; // Outstanding shares
            $ticker = $profileData['ticker'] ?? 'N/A';     // Ticker symbol

            // Save or update the latest price to the 'prices' table
            $price = Price::updateOrCreate(
                ['symbol' => $symbol],
                [
                    'c' => $currentPrice,         // Current price ('c' from API)
                    'h' => $high,                 // Daily high ('h' from API)
                    'l' => $low,                  // Daily low ('l' from API)
                    'o' => $open,                 // Opening price ('o' from API)
                    'pc' => $previousClose,       // Previous close ('pc' from API)
                    'd' => $change,               // Change in price ('d' from API)
                    'dp' => $percentageChange,    // Percentage change ('dp' from API)
                    't' => $recordedAt,            // Timestamp ('t' from API)
                ]
            );

            // Check if price has changed and save the historical data if so
            if ($price->wasRecentlyCreated || $price->c != $currentPrice) {
                // Save the historical data to the 'price_history' table
                PriceHistory::create([
                    'symbol' => $symbol,
                    'c' => $currentPrice,         // Current price ('c' from API)
                    'h' => $high,                 // Daily high ('h' from API)
                    'l' => $low,                  // Daily low ('l' from API)
                    'o' => $open,                 // Opening price ('o' from API)
                    'pc' => $previousClose,       // Previous close ('pc' from API)
                    'd' => $change,               // Change in price ('d' from API)
                    'dp' => $percentageChange,    // Percentage change ('dp' from API)
                    't' => $recordedAt,           // Timestamp ('t' from API)
                ]);
            }

            // Get Price History By Symbol
            $history = PriceHistory::where('symbol', $symbol)->orderBy('id', 'DESC')->get();

            // Return stock data along with company profile in the requested format
            return response()->json([
                'symbol' => $symbol,
                'current_price' => $currentPrice,
                'high' => $high,
                'low' => $low,
                'open' => $open,
                'previous_close' => $previousClose,
                'change' => $change,
                'percentage_change' => $percentageChange,
                'company_profile' => [
                    'name' => $companyName,
                    'country' => $country,
                    'currency' => $currency,
                    'exchange' => $exchange,
                    'ipo' => $ipo,
                    'marketCapitalization' => $marketCap,
                    'phone' => $phone,
                    'shareOutstanding' => $sharesOutstanding,
                    'ticker' => $ticker,
                    'weburl' => $website,
                    'logo' => $logo,
                    'finnhubIndustry' => $sector,
                ],
                'history' => $history,
            ]);
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
}
