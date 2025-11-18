<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Stock\Stock;
use App\Models\BuyTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepotTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the depot index page loads correctly for authenticated users.
     * This tests the depot overview table and ensures it displays stock data.
     */
    public function test_depot_index_page_loads(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        BuyTransaction::factory()->create(['user_id' => $user->id, 'stock_id' => $stock->id]);

        $response = $this->actingAs($user)->get(route('depot.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stocks');
        $response->assertSee($stock->name);
    }

    /**
     * Test that clicking on a table row in depot index navigates to depot stock details.
     * This simulates the onclick navigation to depot.buyDetails route.
     */
    public function test_depot_table_row_click_navigates_to_details(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        BuyTransaction::factory()->create(['user_id' => $user->id, 'stock_id' => $stock->id]);

        $response = $this->actingAs($user)->get(route('depot.buyDetails', $stock->id));

        $response->assertStatus(200);
        $response->assertViewHas('stock');
        $response->assertViewHas('stockData');
        $response->assertSee($stock->name);
    }

    /**
     * Test that depot stock details page displays statistics and buy/sell buttons.
     * This covers the cards, table, and buy_sell-buttons component.
     */
    public function test_depot_stock_details_displays_data(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create();
        BuyTransaction::factory()->create(['user_id' => $user->id, 'stock_id' => $stock->id]);

        $response = $this->actingAs($user)->get(route('depot.buyDetails', $stock->id));

        $response->assertStatus(200);
        $response->assertSee('Durchschnittlicher Einkaufspreis');
        $response->assertSee('Kaufen/Verkaufen');
    }
}
