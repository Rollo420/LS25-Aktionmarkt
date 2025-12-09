<?php

namespace Tests\Feature;

use App\Models\Farm;
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


        $userFarms = Farm::factory()->createMany([
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

        
        $users = User::factory()->createMany([
            [
                'name' => 'user01',
                'email' => 'user01@gmail.com',
                'type' => 'user',
            ],
            [
                'name' => 'user02',
                'email' => 'user02@gmail.com',
                'type' => 'user',
            ],
        ]);
        
        // Im Test-File (UserFarmerTest.php):
        $secondFarm = $userFarms->get(1);

        $firstUser = $users->get(0);
        
        
        if($firstUser->getFarmMode() == false){
            $firstUser->setFarmMode(true);
        }

        //invite user to farm
        // Statt:
        // $inviteUser = $secondFarm->users()->attach($firstUser->id, ['invite_acception' => false]);
        // Machen Sie nur den Aufruf und prüfen Sie das Ergebnis:

        $secondFarm->users()->attach($firstUser->id, ['invite_acception' => false]);

        // Prüfen, ob der Eintrag in der Pivot-Tabelle existiert
        $this->assertDatabaseHas('farm_user', [
            'farm_id' => $secondFarm->id,
            'user_id' => $firstUser->id,
            'invite_acception' => false, // Optional: Prüfen Sie die Pivot-Daten
        ]);

        // Oder prüfen Sie, ob die Beziehung korrekt geladen wird:
        $this->assertTrue(
            $secondFarm->users()->where('user_id', $firstUser->id)->exists()
        );
        

      
    }
}
