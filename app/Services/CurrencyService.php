<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    public function getUSDTtoVND(): float
    {
        try {
            $response = Http::get(config('currency.url_usdt_to_vnd'), [
                'ids' => 'tether',
                'vs_currencies' => 'vnd',
            ]);

            if ($response->successful()) {
                return $response->json()['tether']['vnd'] ?? 0;
            }
        } catch (\Throwable $e) {
            Log::error('CurrencyService Error: ' . $e->getMessage());
        }

        return config('currency.default_usdt_to_vnd', 24500); // fallback
    }
}
