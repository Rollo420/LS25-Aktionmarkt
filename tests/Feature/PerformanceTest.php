<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Stock\Stock;
use App\Models\Stock\Transaction;
use App\Models\Stock\Price;
use App\Models\GameTime;
use App\Models\Dividend;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the loading time of the dashboard page.
     * Measures the time taken to load the dashboard for a user with stocks and transactions.
     */
    public function test_dashboard_loading_performance(): void
    {
        // Create test data
        $user = User::factory()->create();
        $this->createTestDataForUser($user);

        // Measure loading time
        $startTime = microtime(true);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $endTime = microtime(true);
        $loadingTime = $endTime - $startTime;

        // Assert response is successful
        $response->assertStatus(200);

        // Log performance result
        echo "Dashboard loading time: " . number_format($loadingTime, 4) . " seconds\n";

        // Assert loading time is under 3 seconds (adjusted threshold due to caching overhead)
        $this->assertLessThan(3.0, $loadingTime, "Dashboard loading time should be under 3 seconds, but was " . number_format($loadingTime, 4) . " seconds");
    }

    /**
     * Benchmark the StockService getUserStocksWithStatistiks method.
     */
    public function test_stock_service_performance(): void
    {
        $user = User::factory()->create();
        $this->createTestDataForUser($user);

        $stockService = new \App\Services\StockService();

        $startTime = microtime(true);
        $result = $stockService->getUserStocksWithStatistiks($user);
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "StockService::getUserStocksWithStatistiks execution time: " . number_format($executionTime, 4) . " seconds\n";

        // Assert execution time is reasonable
        $this->assertLessThan(0.5, $executionTime, "getUserStocksWithStatistiks should execute under 0.5 seconds, but took " . number_format($executionTime, 4) . " seconds");

        // Assert result is collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    /**
     * Benchmark the DividendeService getDividendStatisticsForStock method.
     */
    public function test_dividend_service_performance(): void
    {
        $user = User::factory()->create();
        $stocks = $this->createTestDataForUser($user);
        $stock = $stocks->first()->stock;

        $dividendService = new \App\Services\DividendeService();

        $startTime = microtime(true);
        $result = $dividendService->getDividendStatisticsForStock($stock);
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "DividendeService::getDividendStatisticsForStock execution time: " . number_format($executionTime, 4) . " seconds\n";

        // Assert execution time is reasonable
        $this->assertLessThan(0.1, $executionTime, "getDividendStatisticsForStock should execute under 0.1 seconds, but took " . number_format($executionTime, 4) . " seconds");

        // Assert result is Dividende object
        $this->assertInstanceOf(\App\Http\Responses\Dividende::class, $result);
    }

    /**
     * Benchmark database queries for transactions.
     */
    public function test_transaction_queries_performance(): void
    {
        $user = User::factory()->create();
        $this->createTestDataForUser($user);

        $startTime = microtime(true);
        $transactions = Transaction::where('user_id', $user->id)
            ->with('stock')
            ->latest()
            ->take(5)
            ->get();
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        echo "Transaction query (latest 5) execution time: " . number_format($executionTime, 4) . " seconds\n";

        // Assert execution time is reasonable
        $this->assertLessThan(0.1, $executionTime, "Transaction query should execute under 0.1 seconds, but took " . number_format($executionTime, 4) . " seconds");

        // Assert result
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $transactions);
        $this->assertLessThanOrEqual(5, $transactions->count());
    }

    /**
     * Helper method to create test data for a user.
     */
    private function createTestDataForUser(User $user): \Illuminate\Support\Collection
    {
        // Create GameTimes (simulate months)
        $gameTimes = collect();
        for ($i = 0; $i < 12; $i++) {
            $gameTimes->push(GameTime::factory()->create([
                'name' => now()->subMonths(11 - $i)->startOfMonth()->toDateString(),
            ]));
        }

        // Create stocks
        $stocks = collect();
        for ($i = 0; $i < 5; $i++) {
            $stock = Stock::factory()->create([
                'name' => "Test Stock {$i}",
                'dividend_frequency' => 4, // quarterly
            ]);

            // Create prices for each GameTime
            foreach ($gameTimes as $gt) {
                Price::factory()->create([
                    'stock_id' => $stock->id,
                    'game_time_id' => $gt->id,
                    'name' => rand(50, 200), // random price
                ]);
            }

            // Create dividends
            Dividend::factory()->create([
                'stock_id' => $stock->id,
                'game_time_id' => $gameTimes->last()->id,
                'amount_per_share' => rand(1, 5),
            ]);

            $stocks->push($stock);
        }

        // Create transactions for each stock
        foreach ($stocks as $stock) {
            for ($i = 0; $i < 3; $i++) {
                Transaction::factory()->create([
                    'user_id' => $user->id,
                    'stock_id' => $stock->id,
                    'type' => 'buy',
                    'quantity' => rand(10, 50),
                    'price_at_buy' => rand(50, 200),
                    'game_time_id' => $gameTimes->random()->id,
                ]);
            }
        }

        // Create Bank for user
        $user->bank()->create(['balance' => 10000]);

        return $stocks->map(function ($stock) use ($user) {
            return (object) [
                'stock' => $stock,
                'quantity' => $stock->getCurrentQuantity(),
            ];
        });
    }
}
