<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Bank;
use App\Models\Stock\Transaction;

class PaymentTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_moves_funds_and_creates_transaction()
    {
        // Create sender and receiver users (factories create banks)
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();

        // Ensure deterministic balances
        $fromBank = Bank::where('user_id', $fromUser->id)->first();
        $toBank = Bank::where('user_id', $toUser->id)->first();

        $fromBank->balance = 1000.00;
        $fromBank->save();

        $toBank->balance = 100.00;
        $toBank->save();

        $amount = 200.00;

        // Act as the sender and post to the transfer route
        $response = $this->actingAs($fromUser)
            ->post(route('payment.transfer'), [
                'to_account' => $toBank->iban,
                'amount' => $amount,
            ]);

        // Expect redirect back to payment index
        $response->assertRedirect(route('payment.index'));

        // Refresh balances
        $fromBank->refresh();
        $toBank->refresh();

        $this->assertEquals(800.00, (float) $fromBank->balance);
        $this->assertEquals(300.00, (float) $toBank->balance);

        // Transaction record was created for the transfer
        $this->assertDatabaseHas('transactions', [
            'user_id' => $fromUser->id,
            'type' => 'transfer',
            'quantity' => $amount,
        ]);
    }
}
