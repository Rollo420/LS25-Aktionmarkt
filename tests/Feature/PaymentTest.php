<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the payment index page loads correctly for authenticated users.
     * This tests the expandable sections: payin, payout, transfer, transaction, orders.
     */
    public function test_payment_index_page_loads(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('payment.index'));

        $response->assertStatus(200);
        $response->assertSee('Pay in');
        $response->assertSee('Pay out');
        $response->assertSee('Transfer');
        $response->assertSee('Alle Transaktionen');
        $response->assertSee('Orders');
    }

    /**
     * Test pay-in form submission.
     * This tests the pay-in form in the expandable section.
     */
    public function test_pay_in_form_submission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('payment.payin'), [
            'payin' => 100.00,
        ]);

        $response->assertRedirect(route('payment.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test pay-out form submission.
     * This tests the pay-out form in the expandable section.
     */
    public function test_pay_out_form_submission(): void
    {
        $user = User::factory()->create();
        $user->bank->update(['balance' => 200.00]);

        $response = $this->actingAs($user)->post(route('payment.payout'), [
            'payout' => 50.00,
        ]);

        $response->assertRedirect(route('payment.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test transfer form submission.
     * This tests the transfer form in the expandable section.
     */
    public function test_transfer_form_submission(): void
    {
        $user = User::factory()->create();
        $user->bank->update(['balance' => 200.00]);

        $recipient = User::factory()->create();
        $recipient->bank->update(['iban' => 'DE61 12345678 2848820727']);

        $response = $this->actingAs($user)->post(route('payment.transfer'), [
            'to_account' => 'DE61 12345678 2848820727',
            'amount' => 50.00,
        ]);

        $response->assertRedirect(route('payment.index'));
        $response->assertSessionHas('success');
    }
}
