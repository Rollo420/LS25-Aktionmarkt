<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DividendeService;
use App\Models\Stock\Stock;
use App\Models\Dividend;
use App\Models\GameTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class DividendeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DividendeService $dividendeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dividendeService = new DividendeService();
    }

    public function test_dividende_is_calculated_correctly_for_stock_with_dividends()
    {
        $stock = Stock::factory()->create(['dividend_frequency' => 4]);

        $gameTime = GameTime::factory()->create(['name' => '2023-06-01']);
        $dividend = Dividend::factory()->create([
            'stock_id' => $stock->id,
            'game_time_id' => $gameTime->id,
            'amount_per_share' => 2.50,
        ]);

        // Test dividend stats for stock with specific gameTime
        $result = $this->dividendeService->getDividendStatisticsForStock($stock);

        $this->assertEquals(round(2.50, 2), $result->dividendPerShare);
        $this->assertEquals(4, $result->frequency_per_year);
        $this->assertEquals($dividend->amount_per_share, $result->last_amount);
        $this->assertNotNull($result->next_date);
    }

    public function test_dividende_returns_empty_for_stock_without_dividends()
    {
        $stock = Stock::factory()->create(['dividend_frequency' => 1]);

        $result = $this->dividendeService->getDividendStatisticsForStock($stock);

        $this->assertEquals(0, $result->dividendPerShare);
        $this->assertEquals(0, $result->frequency_per_year);
        $this->assertEquals(0, $result->last_amount);
        $this->assertNull($result->next_date);
    }

    public function test_get_dividende_for_stock_id_returns_null_for_invalid_id()
    {
        $result = $this->dividendeService->getDividendeForStockID(999999);
        $this->assertNull($result);
    }
}
