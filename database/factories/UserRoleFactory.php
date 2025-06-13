<?php

namespace Database\Factories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class UserRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): void
    {
        //return [
        //    'user_id' => fake()->numberBetween(1, 5),
        //    'role_id' => fake()->numberBetween(1, 4),
        //];

        DB::table('users_roles')->insert(
            [
                'user_id' => fake()->numberBetween(2, 5),
                'role_id' => fake()->numberBetween(1, 4),
            ],
        );

        $exists = DB::table('users_roles')
        ->where('user_id', 1)
        ->where('role_id', 1)
        ->exists();

        if (!$exists) {
           DB::table('users_roles')->insert(
            [
                'user_id' => 1,
                'role_id' => 1,
            ],
        );
        }

    }

    
}
