<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


use App\Models\User;
use App\Models\Stock\Transaction;
use App\Models\GameTime;

class UserFarmerTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        //run command: sail artisan test --filter UserFarmerTest 

        $this->seed();
        

        $userFarms = User::factory()->createMany([
            [
                'name' => 'farmer01',
                'email' => 'farmer01@gmail.com',
                'type' => 'farm',
            ],
            [
                'name' => 'farmer02',
                'email' => 'farmer02@gmail.com',
                'type' => 'farm',
            ],
            [
                'name' => 'farmer03',
                'email' => 'farmer03@gmail.com',
                'type' => 'farm',
            ]
        ]);

        
        $user = User::factory()->create([
            'name' => 'user01',
            'email' => 'user01@gmail.com',
            'type' => 'user',
        ]);
        //$user->farms()->attach($userFarms->pluck('id'));

        dd( $user->farms()->get());
        
        if($user->isFarm()){
            $this->assertTrue(true);
            $famerTrans = Transaction::factory()->create([
                'user_id' => $user->farms(), //Mein Gedanke ist, das die Relation so sein müsste: $user->farm()->id. Wie würde man das in Parantal erstellen?
                'stock_id' => 1,
                'quantity' => 10,
                'price_at_buy' => 100.0,
                'type' => 'buy',
            ]);

            $this->assertDatabaseHas('transactions', [
                'id' => $famerTrans->id,
            ]);
        }
        else 
        {
            $this->assertFalse(false, 'User is not recognized as Farm type');

            $userTrans = Transaction::factory()->create([
                'user_id' => $user->id,
                'stock_id' => 1,
                'quantity' => 10,
                'price_at_buy' => 100.0,
                'type' => 'buy',
            ]);

            $this->assertDatabaseHas('transactions', [
                'id' => $userTrans->id,
            ]);
        }

    }
}
