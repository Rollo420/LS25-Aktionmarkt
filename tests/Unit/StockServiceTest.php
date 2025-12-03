<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Services\StockService;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions_since_last_zero_ignores_previous_periods()
    {
        // Arrange: create user and stock
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        $gt = \App\Models\GameTime::factory()->create();

        // T1: buy 5
        Transaction::factory()->create([
            'user_id' => $user->id,
            'stock_id' => $stock->id,
            'game_time_id' => $gt->id,
            'type' => 'buy',
            'quantity' => 5,
            'created_at' => now()->subMonths(5),
        ]);

        // T2: sell 5 -> balance zero
        Transaction::factory()->create([
            'user_id' => $user->id,
            'stock_id' => $stock->id,
            'game_time_id' => $gt->id,
            'type' => 'sell',
            'quantity' => 5,
            'created_at' => now()->subMonths(4),
        ]);

        // T3: buy 3 (new period)
        $t3 = Transaction::factory()->create([
            'user_id' => $user->id,
            'stock_id' => $stock->id,
            'game_time_id' => $gt->id,
            'type' => 'buy',
            'quantity' => 3,
            'created_at' => now()->subMonths(1),
        ]);

        $svc = new StockService();

        // Act
        $txns = $svc->getTransactionsSinceLastZero($user, $stock->id);

        // Assert: only T3 present
        $this->assertCount(1, $txns);
        $this->assertEquals($t3->id, $txns->first()->id);
    }

    public function test_get_user_buy_transactions_for_stock_returns_only_buys_after_reset()
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        $gt = \App\Models\GameTime::factory()->create();

        Transaction::factory()->create(['user_id' => $user->id, 'stock_id' => $stock->id, 'game_time_id' => $gt->id, 'type' => 'buy', 'quantity' => 2, 'created_at' => now()->subMonths(6)]);
        Transaction::factory()->create(['user_id' => $user->id, 'stock_id' => $stock->id, 'game_time_id' => $gt->id, 'type' => 'sell', 'quantity' => 2, 'created_at' => now()->subMonths(5)]);
        $tNew = Transaction::factory()->create(['user_id' => $user->id, 'stock_id' => $stock->id, 'game_time_id' => $gt->id, 'type' => 'buy', 'quantity' => 4, 'created_at' => now()->subMonths(1)]);

        $svc = new StockService();

        $buys = $svc->getUserBuyTransactionsForStock($user, $stock->id);

        $this->assertCount(1, $buys);
        $this->assertEquals($tNew->id, $buys->first()->id);
    }

    public function test_overview_and_detail_use_same_values()
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();

        $gt = \App\Models\GameTime::factory()->create();

        // buy after reset
        Transaction::factory()->create(['user_id' => $user->id, 'stock_id' => $stock->id, 'game_time_id' => $gt->id, 'type' => 'buy', 'quantity' => 3, 'price_at_buy' => 10]);

        $svc = new StockService();

        $overviewStats = $svc->getUserStocksWithStatistiks($user);
        $detailStats = $svc->getStockStatistiks($stock, $user);

        // find corresponding stat in overview
        $stat = $overviewStats->firstWhere('stock.id', $stock->id);

        $this->assertNotNull($stat, 'Stock stat must appear in overview');
        $this->assertEquals($stat->profit_loss, $detailStats->profit_loss);
        $this->assertEquals($stat->avg_buy_price, $detailStats->avg_buy_price);
        $this->assertEquals($stat->quantity, $detailStats->quantity);
    }
}
