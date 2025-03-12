<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'bank', 'farmer', 'forst'];
        foreach ($roles as $role) {
            Role::factory()->create(['name' => $role]);
        }
    }
}
