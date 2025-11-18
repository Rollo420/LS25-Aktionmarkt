<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Carbon\Carbon;
use App\Services\PriceResolverService;

class PriceResolverServiceTest extends TestCase
{
    public function test_exact_game_time_match_returns_price()
    {
        $resolver = new PriceResolverService();

        $month = Carbon::createFromDate(2004, 3, 1);

        $priceObj = (object)[
            'name' => '15.72',
            'gameTime' => (object)['current_year' => 2004, 'month_id' => 3],
            'created_at' => '2025-11-03 00:00:00',
        ];

        $pricesByStock = collect([1 => collect([$priceObj])]);

        $res = $resolver->resolvePriceForStockMonth(1, $month, $pricesByStock);
        $this->assertEquals(15.72, $res);
    }

    public function test_latest_before_returns_previous_price()
    {
        $resolver = new PriceResolverService();

        $month = Carbon::createFromDate(2004, 5, 1);

        $p1 = (object)[ 'name' => '10.00', 'gameTime' => (object)['name'=>'2004-03-01'], 'created_at'=>'2025-11-01 00:00:00' ];
        $p2 = (object)[ 'name' => '12.50', 'gameTime' => (object)['name'=>'2004-04-01'], 'created_at'=>'2025-11-02 00:00:00' ];

        $pricesByStock = collect([1 => collect([$p1, $p2])]);

        $res = $resolver->resolvePriceForStockMonth(1, $month, $pricesByStock);
        $this->assertEquals(12.5, $res);
    }

    public function test_tx_price_at_buy_used_when_no_prices()
    {
        $resolver = new PriceResolverService();

        $month = Carbon::createFromDate(2030, 1, 1);

        $pricesByStock = collect([1 => collect([])]);
        $tx = (object)['price_at_buy' => 9.99];

        $res = $resolver->resolvePriceForStockMonth(1, $month, $pricesByStock, $tx);
        $this->assertEquals(9.99, $res);
    }
}
