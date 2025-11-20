<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Config;
use App\Models\ProductType;
use App\Models\Stock\Stock;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        // Create role and user, attach role
        $role = Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        return $user;
    }

    public function test_admin_routes_are_accessible_and_edit_pages_show_buttons()
    {
        $admin = $this->createAdminUser();

        // create supporting records
        $productType = ProductType::create(['name' => 'TestType']);
        $config1 = Config::create([/* minimal attributes */
            'name' => 'cfg1',
            'volatility_range' => 0.01,
            'seasonal_effect_strength' => 0.01,
            'crash_probability_monthly' => 1,
            'crash_interval_months' => 120,
            'rally_probability_monthly' => 1,
            'rally_interval_months' => 240,
        ]);

        $stock = Stock::create([
            'product_type_id' => $productType->id,
            'name' => 'ACME',
            'firma' => 'ACME GmbH',
            'sektor' => 'Technology',
            'land' => 'DE',
            'description' => 'Test stock',
            'net_income' => 1000,
            'dividend_frequency' => 4,
        ]);

        // Attach config to stock using pivot
        $stock->configs()->attach($config1->id);

        // Act as admin
        $response = $this->actingAs($admin)->get(route('admin.stocks.index'));
        $response->assertStatus(200);

        // Edit page should contain Manage Time link and Löschen button
        $response = $this->actingAs($admin)->get(route('admin.stocks.edit', $stock));
        $response->assertStatus(200);
        $response->assertSee('Manage Time');
        $response->assertSee('Löschen');
        $response->assertSee('Configs zuweisen');
        $response->assertSee('Speichern');

        // Users edit page
        $editedUser = User::factory()->create();
        $response = $this->actingAs($admin)->get(route('admin.users.edit', $editedUser));
        $response->assertStatus(200);
        $response->assertSee('Aktuelle Rolle:');
        $response->assertSee('User löschen');
        $response->assertSee('Rolle');
    }
}
