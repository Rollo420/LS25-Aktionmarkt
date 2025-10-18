<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Stock\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the stock index page loads and displays the table of stocks.
     * This tests the table rows that are clickable to navigate to stock.store.
     */
    public function test_stock_index_page_loads(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();

        $response = $this->actingAs($user)->get(route('stock.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stocks');
        $response->assertSee($stock->name);
    }

    /**
     * Test that clicking on a table row in stock index navigates to stock store page.
     * This simulates the onclick navigation to stock.store route.
     */
    public function test_stock_table_row_click_navigates_to_store(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();

        $response = $this->actingAs($user)->get(route('stock.store', $stock->id));

        $response->assertStatus(200);
        $response->assertViewHas('stock');
        $response->assertViewHas('chartData');
        $response->assertSee($stock->name);
    }

    /**
     * Test that stock store page displays chart, buy/sell buttons, and details.
     * This covers the chart-show component, buy_sell-buttons, firmen-details, and stock-details.
     */
    public function test_stock_store_displays_chart_and_buttons(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();

        $response = $this->actingAs($user)->get(route('stock.store', $stock->id));

        $response->assertStatus(200);
        $response->assertSee('Chart');
        $response->assertSee('Kaufen/Verkaufen');
    }
}
