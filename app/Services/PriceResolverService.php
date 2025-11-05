<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Stock\Stock;

class PriceResolverService
{
    /**
     * Resolve a price for a stock for a given monthDate using the same fallback order
     * as before: exact GameTime month -> latest before -> earliest after -> tx.price_at_buy -> current stock price
     *
     * @param int $stockId
     * @param Carbon $monthDate
     * @param \Illuminate\Support\Collection|array $pricesByStock grouped by stock_id
     * @param mixed $tx optional transaction object with price_at_buy
     * @return float
     */
    public function resolvePriceForStockMonth(int $stockId, Carbon $monthDate, $pricesByStock, $tx = null): float
    {
        $prices = collect($pricesByStock->get($stockId) ?? []);

        // exact same gameTime month
        $exact = $prices->first(function ($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::parse($p->gameTime->name ?? now()->toDateString());
                return $d->eq($monthDate);
            }
            return false;
        });
        if ($exact) {
            $raw = $exact->name ?? 0;
            return is_string($raw) ? floatval(str_replace(',', '.', $raw)) : (float)$raw;
        }

        // latest price <= month
        $latestBefore = $prices->filter(function ($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::parse($p->gameTime->name ?? now()->toDateString());
                return $d->lte($monthDate);
            }
            return Carbon::parse($p->created_at ?? now())->startOfMonth()->lte($monthDate);
        })->last();
        if ($latestBefore) {
            $raw = $latestBefore->name ?? 0;
            return is_string($raw) ? floatval(str_replace(',', '.', $raw)) : (float)$raw;
        }

        // earliest price > month
        $earliestAfter = $prices->filter(function ($p) use ($monthDate) {
            if (isset($p->gameTime) && $p->gameTime) {
                $d = Carbon::parse($p->gameTime->name ?? now()->toDateString());
                return $d->gt($monthDate);
            }
            return Carbon::parse($p->created_at ?? now())->startOfMonth()->gt($monthDate);
        })->first();
        if ($earliestAfter) {
            $raw = $earliestAfter->name ?? 0;
            return is_string($raw) ? floatval(str_replace(',', '.', $raw)) : (float)$raw;
        }

        // transaction price_at_buy if provided
        if ($tx && isset($tx->price_at_buy) && $tx->price_at_buy !== null) {
            return (float)$tx->price_at_buy;
        }

        // fallback to current stock price
        $stock = Stock::find($stockId);
        if ($stock) {
            return (float) $stock->getCurrentPrice();
        }

        return 0.0;
    }
}
